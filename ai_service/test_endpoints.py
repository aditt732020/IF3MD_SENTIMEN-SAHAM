import json
from app import app

c = app.test_client()


def show(label, resp):
    print(f"\n=== {label} -> HTTP {resp.status_code}")
    print(json.dumps(resp.get_json(), indent=1, ensure_ascii=False)[:900])


show("GET /health", c.get("/health"))
show("POST /predict single", c.post("/predict", json={"teks": "Penjualan perusahaan anjlok parah akibat krisis global."}))
show("POST /predict batch", c.post("/predict", json={"texts": [
    "Laba bersih BBCA kuartal III melonjak 12 persen",
    "BEI umumkan jadwal libur bursa akhir tahun",
    "Saham GOTO ambles setelah rugi membengkak",
]}))
show("POST /predict kosong", c.post("/predict", json={"teks": ""}))
show("GET /prices/BBCA", c.get("/prices/BBCA?period=5d"))
show("GET /prices injeksi", c.get("/prices/BBCA' OR 1=1--"))
show("GET /quotes", c.get("/quotes?tickers=BBCA,BBRI,TLKM,GOTO"))

