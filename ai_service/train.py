"""
StockSense AI - Training model sentimen berita saham (FR-07)

Dataset : dataset/indFinsent.jsonl  (kolom 'text' = judul berita, 'answer' = label)
Output  : model_sentimen_saham.pkl  (Pipeline scikit-learn, mendukung predict_proba)
          model_metrics.json        (akurasi & laporan evaluasi, dipakai endpoint /health)

Jalankan: python train.py
"""

import json
from datetime import datetime
from pathlib import Path

import joblib
import pandas as pd
import sklearn
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report, f1_score
from sklearn.model_selection import StratifiedKFold, cross_val_score, train_test_split
from sklearn.naive_bayes import ComplementNB
from sklearn.pipeline import FeatureUnion, Pipeline
from sklearn.feature_extraction.text import TfidfVectorizer

from text_utils import clean_text

BASE_DIR = Path(__file__).resolve().parent
DATASET_PATH = BASE_DIR / "dataset" / "indFinsent.jsonl"
MODEL_PATH = BASE_DIR / "model_sentimen_saham.pkl"
METRICS_PATH = BASE_DIR / "model_metrics.json"
RANDOM_STATE = 42


def build_features():
    # Gabungan n-gram kata + n-gram karakter: tahan terhadap typo & imbuhan bahasa Indonesia
    return FeatureUnion([
        ("word", TfidfVectorizer(preprocessor=clean_text, ngram_range=(1, 2), min_df=1, sublinear_tf=True)),
        ("char", TfidfVectorizer(preprocessor=clean_text, analyzer="char_wb", ngram_range=(2, 5), min_df=2, sublinear_tf=True)),
    ])


def candidate_models():
    return {
        "ComplementNB": Pipeline([
            ("features", build_features()),
            ("clf", ComplementNB(alpha=0.3)),
        ]),
        "LogisticRegression": Pipeline([
            ("features", build_features()),
            ("clf", LogisticRegression(C=5.0, max_iter=2000, class_weight="balanced")),
        ]),
    }


def main():
    # 1. Load dataset
    df = pd.read_json(DATASET_PATH, lines=True)[["text", "answer"]].dropna()
    df["text"] = df["text"].astype(str).str.strip()
    df = df[df["text"] != ""].drop_duplicates(subset="text")
    print(f"Jumlah data (setelah hapus duplikat): {len(df)}")
    print("Distribusi label:\n" + df["answer"].value_counts().to_string() + "\n")

    X_train, X_test, y_train, y_test = train_test_split(
        df["text"], df["answer"], test_size=0.2, stratify=df["answer"], random_state=RANDOM_STATE
    )

    # 2. Bandingkan kandidat model dengan 5-fold cross validation (macro F1)
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=RANDOM_STATE)
    scores = {}
    for name, model in candidate_models().items():
        f1 = cross_val_score(model, X_train, y_train, cv=cv, scoring="f1_macro").mean()
        scores[name] = f1
        print(f"CV macro-F1 {name:<20}: {f1:.4f}")

    best_name = max(scores, key=scores.get)
    best_model = candidate_models()[best_name]
    print(f"\nModel terbaik: {best_name}")

    # 3. Evaluasi akhir di data uji
    best_model.fit(X_train, y_train)
    y_pred = best_model.predict(X_test)
    acc = accuracy_score(y_test, y_pred)
    macro_f1 = f1_score(y_test, y_pred, average="macro")
    report = classification_report(y_test, y_pred, digits=4)
    print(f"\nAkurasi data uji : {acc:.4f}\nMacro F1 data uji: {macro_f1:.4f}\n")
    print(report)

    # 4. Latih ulang dengan seluruh data lalu simpan
    best_model.fit(df["text"], df["answer"])
    joblib.dump(best_model, MODEL_PATH)

    metrics = {
        "model": best_name,
        "trained_at": datetime.now().isoformat(timespec="seconds"),
        "sklearn_version": sklearn.__version__,
        "dataset": DATASET_PATH.name,
        "n_samples": int(len(df)),
        "label_distribution": df["answer"].value_counts().to_dict(),
        "cv_macro_f1": {k: round(v, 4) for k, v in scores.items()},
        "test_accuracy": round(acc, 4),
        "test_macro_f1": round(macro_f1, 4),
        "classes": list(best_model.classes_),
    }
    METRICS_PATH.write_text(json.dumps(metrics, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"Model disimpan ke  : {MODEL_PATH.name}")
    print(f"Metrik disimpan ke : {METRICS_PATH.name}")


if __name__ == "__main__":
    main()
