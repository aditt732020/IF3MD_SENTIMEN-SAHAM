"""Preprocessing teks bersama, dipakai oleh train.py dan app.py.
Harus berada di modul terpisah agar model .pkl bisa dimuat ulang (pickle menyimpan referensi modul)."""

import re


def clean_text(text: str) -> str:
    """Normalisasi ringan: huruf kecil, buang URL & karakter aneh, rapikan spasi.
    Angka dipertahankan karena sering bermakna (contoh: 'laba naik 20%')."""
    text = str(text).lower()
    text = re.sub(r"https?://\S+", " ", text)
    text = re.sub(r"[^0-9a-z%\s]", " ", text)
    return re.sub(r"\s+", " ", text).strip()

