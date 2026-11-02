"""
StockSense AI - Python Microservice
-----------------------------------
Endpoint:
  GET  /health                 -> status service & info model (dashboard status endpoint)
  POST /predict                -> klasifikasi sentimen berita (FR-07)
  GET  /prices/<ticker>        -> OHLCV harian dari Yahoo Finance (FR-06)
  GET  /quotes?tickers=A,B,C   -> harga terakhir + perubahan % (live ticker dashboard)

Jalankan: python app.py   (port 5000)
"""

import json
import logging
import os
import re
from pathlib import Path

import joblib
import yfinance as yf
from flask import Flask, jsonify, request

import text_utils  # noqa: F401  (wajib di-import agar model .pkl bisa di-load)

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "model_sentimen_saham.pkl"
METRICS_PATH = BASE_DIR / "model_metrics.json"

# Label model (Bahasa Indonesia) -> label database (enum di tabel news_sentiments)
LABEL_MAP = {"Positif": "positive", "Negatif": "negative", "Netral": "neutral"}

# NFR-04: hanya terima kode emiten IDX yang valid (4 huruf kapital), mencegah input berbahaya
TICKER_PATTERN = re.compile(r"^[A-Z]{4}$")
ALLOWED_PERIODS = {"1d", "5d", "1mo", "3mo", "6mo", "1y"}
MAX_BATCH = 100
MAX_TEXT_LEN = 5000

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
log = logging.getLogger("stocksense-ai")

app = Flask(__name__)

model = joblib.load(MODEL_PATH)
model_info = json.loads(METRICS_PATH.read_text(encoding="utf-8")) if METRICS_PATH.exists() else {}
log.info("Model dimuat: %s", model_info.get("model", type(model).__name__))


# ---------------------------------------------------------------- helpers
def error(message: str, status: int = 400):
    return jsonify({"status": "error", "message": message}), status


def normalize_ticker(raw: str):
    ticker = (raw or "").strip().upper().removesuffix(".JK")
    return ticker if TICKER_PATTERN.match(ticker) else None


def classify(texts):
    probas = model.predict_proba(texts)
    classes = list(model.classes_)
    results = []
    for proba in probas:
        best = int(proba.argmax())
        label_id = classes[best]
        results.append({
            "sentimen": label_id,                       # kompatibel dengan versi lama
            "label": LABEL_MAP.get(label_id, "neutral"),  # siap disimpan ke DB
            "confidence": round(float(proba[best]), 3),
            "probabilities": {LABEL_MAP.get(c, c): round(float(p), 3) for c, p in zip(classes, proba)},
        })
    return results


# ---------------------------------------------------------------- routes
@app.get("/health")
def health():
    return jsonify({
        "status": "ok",
        "model": model_info.get("model"),
        "trained_at": model_info.get("trained_at"),
        "test_accuracy": model_info.get("test_accuracy"),
        "test_macro_f1": model_info.get("test_macro_f1"),
    })


@app.post("/predict")
def predict_sentiment():
    """Body: {"teks": "..."}  atau  {"texts": ["...", "..."]} (batch, maks 100)."""
    data = request.get_json(silent=True) or {}

    if "texts" in data:
        texts = data["texts"]
        if not isinstance(texts, list) or not texts:
            return error("'texts' harus berupa list yang tidak kosong")
        if len(texts) > MAX_BATCH:
            return error(f"Maksimal {MAX_BATCH} teks per request")
        texts = [str(t)[:MAX_TEXT_LEN] for t in texts]
        if any(not t.strip() for t in texts):
            return error("Terdapat teks berita kosong")
        return jsonify({"status": "success", "results": classify(texts)})

    teks = str(data.get("teks", "")).strip()[:MAX_TEXT_LEN]
    if not teks:
        return error("Teks berita kosong")
    return jsonify({"status": "success", **classify([teks])[0]})


@app.get("/prices/<ticker>")
def prices(ticker):
    """OHLCV harian. Query: ?period=5d (1d,5d,1mo,3mo,6mo,1y)."""
    code = normalize_ticker(ticker)
    if not code:
        return error("Kode emiten tidak valid (harus 4 huruf, contoh: BBCA)")
    period = request.args.get("period", "5d")
    if period not in ALLOWED_PERIODS:
        return error(f"Period tidak valid. Pilihan: {', '.join(sorted(ALLOWED_PERIODS))}")

    try:
        hist = yf.Ticker(f"{code}.JK").history(period=period, interval="1d", auto_adjust=False)
    except Exception as exc:  # jaringan / Yahoo error
        log.exception("Gagal mengambil harga %s", code)
        return error(f"Gagal mengambil data dari Yahoo Finance: {exc}", 502)

    if hist.empty:
        return error(f"Data harga untuk {code} tidak ditemukan", 404)

    rows = [{
        "date": idx.strftime("%Y-%m-%d"),
        "open": round(float(r["Open"]), 2),
        "high": round(float(r["High"]), 2),
        "low": round(float(r["Low"]), 2),
        "close": round(float(r["Close"]), 2),
        "volume": int(r["Volume"]),
    } for idx, r in hist.iterrows()]

    return jsonify({"status": "success", "ticker": code, "source": "Yahoo Finance", "prices": rows})


@app.get("/quotes")
def quotes():
    """Harga terakhir + perubahan % untuk beberapa emiten. Query: ?tickers=BBCA,BBRI,TLKM"""
    raw = request.args.get("tickers", "")
    codes = [c for c in (normalize_ticker(t) for t in raw.split(",")) if c]
    if not codes:
        return error("Parameter 'tickers' kosong atau tidak valid")
    codes = list(dict.fromkeys(codes))[:20]

    try:
        data = yf.download([f"{c}.JK" for c in codes], period="5d", interval="1d",
                           group_by="ticker", auto_adjust=False, progress=False, threads=True)
    except Exception as exc:
        log.exception("Gagal mengambil quotes")
        return error(f"Gagal mengambil data dari Yahoo Finance: {exc}", 502)

    result = []
    for code in codes:
        try:
            closes = data[f"{code}.JK"]["Close"].dropna()
        except KeyError:
            continue
        if closes.empty:
            continue
        last = float(closes.iloc[-1])
        prev = float(closes.iloc[-2]) if len(closes) > 1 else last
        change = last - prev
        result.append({
            "ticker": code,
            "price": round(last, 2),
            "change": round(change, 2),
            "change_pct": round(change / prev * 100, 2) if prev else 0.0,
            "date": closes.index[-1].strftime("%Y-%m-%d"),
        })

    if not result:
        return error("Gagal mengambil harga dari Yahoo Finance (cek koneksi internet)", 502)

    return jsonify({"status": "success", "quotes": result})


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=int(os.getenv("PORT", "5000")), debug=os.getenv("FLASK_DEBUG", "0") == "1")
