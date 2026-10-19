from flask import Flask, request, jsonify
import joblib

app = Flask(__name__)

# Memuat "otak" AI yang sudah kamu latih
model = joblib.load('model_sentimen_saham.pkl')

@app.route('/predict', methods=['POST'])
def predict_sentiment():
    # Menerima data dari Laravel
    data = request.get_json()
    teks_berita = data.get('teks', '')
    
    if not teks_berita:
        return jsonify({'error': 'Teks berita kosong'}), 400

    # AI melakukan prediksi
    hasil_prediksi = model.predict([teks_berita])
    
    # Mengembalikan hasil ke Laravel dalam format JSON
    return jsonify({
        'status': 'success',
        'sentimen': hasil_prediksi[0]
    })

if __name__ == '__main__':
    # Menjalankan server mini Python di port 5000
    app.run(port=5000, debug=True)