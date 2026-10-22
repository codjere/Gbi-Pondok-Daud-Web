# BAB IV — SPESIFIKASI SISTEM

## 4.1 Spesifikasi Kebutuhan Pengembangan

Pada bab ini diuraikan spesifikasi kebutuhan yang digunakan dalam pengembangan
Portal GBI Pondok Daud, yaitu sebuah aplikasi berbasis website untuk manajemen
gereja yang mencakup autentikasi (termasuk Face Recognition), jadwal ibadah,
Alkitab digital, permohonan doa, warta/renungan, chatbot AI (RAG), sistem
rekomendasi pelayanan, serta pelaporan. Aplikasi dibangun menggunakan kerangka
kerja Laravel 12 dengan dua layanan pendukung berbasis Python (FastAPI) untuk
pengenalan wajah dan kecerdasan buatan.

Spesifikasi dibagi menjadi tiga bagian: spesifikasi *hardware*, spesifikasi
*software*, dan spesifikasi *tools*.

---

### 4.1.1 Spesifikasi Hardware

Dalam pengembangan aplikasi Portal GBI Pondok Daud diperlukan spesifikasi
*hardware* sebagai berikut:

**Tabel 4.1 Spesifikasi Hardware**

| Hardware  | Spesifikasi                                                    |
|-----------|----------------------------------------------------------------|
| Processor | Intel Core i5 Gen 11 atau yang setara                          |
| GPU       | NVIDIA GeForce RTX 3050 (akselerasi inferensi model FaceNet)    |
| RAM       | 16 GB                                                          |
| Storage   | SSD 512 GB                                                     |

> Catatan: GPU bersifat opsional. Layanan pengenalan wajah (FaceNet/PyTorch)
> dapat berjalan pada CPU, namun ketersediaan GPU mempercepat proses ekstraksi
> *embedding* wajah.

---

### 4.1.2 Spesifikasi Software

Dalam pengembangan aplikasi Portal GBI Pondok Daud diperlukan spesifikasi
*software* sebagai berikut:

**Tabel 4.2 Spesifikasi Software**

| Software         | Spesifikasi                                                    |
|------------------|----------------------------------------------------------------|
| Operating System | Windows 11 (64-Bit) / macOS / Ubuntu 24.04 LTS                 |
| Web Server       | Apache (XAMPP) / PHP Built-in Server                           |
| Bahasa Backend   | PHP 8.2.12                                                     |
| Framework        | Laravel 12                                                     |
| Bahasa AI Service| Python 3.13.5                                                 |
| Database         | PostgreSQL (produksi) / SQLite (pengembangan)                  |
| Code Editor      | Visual Studio Code                                            |
| Web Browser      | Google Chrome / Microsoft Edge versi terbaru                   |

---

### 4.1.3 Spesifikasi Tools

Dalam pengembangan aplikasi Portal GBI Pondok Daud diperlukan spesifikasi
*tools* sebagai berikut:

**Tabel 4.3 Spesifikasi Tools**

| Tools               | Spesifikasi                                   |
|---------------------|-----------------------------------------------|
| PHP                 | Versi 8.2.12                                  |
| Laravel Framework   | Versi 12                                      |
| Composer            | Versi 2.8.10                                  |
| Node.js             | Versi 22.17.1                                 |
| NPM                 | Versi 10.9.2                                  |
| Vite                | Versi 7.x                                     |
| Tailwind CSS        | Versi 4.x                                     |
| Python              | Versi 3.13.5                                  |
| FastAPI             | Versi 0.115 ke atas                           |
| Git                 | Versi 2.50.1                                  |

---

## 4.2 Spesifikasi Teknis Perangkat Lunak

Bagian ini merinci *stack* teknologi dan pustaka (*library*) yang digunakan pada
masing-masing komponen sistem.

### 4.2.1 Backend (Aplikasi Web)

**Tabel 4.4 Dependensi Backend (Laravel)**

| Komponen                | Versi / Keterangan                               |
|-------------------------|--------------------------------------------------|
| laravel/framework       | ^12.0                                            |
| PHP                     | ^8.2                                             |
| laravel/tinker          | ^2.10                                            |
| barryvdh/laravel-dompdf | ^3.1 (ekspor laporan ke PDF)                     |
| maatwebsite/excel       | ^3.1 (ekspor data kehadiran ke Excel)            |
| phpunit/phpunit         | ^11.5 (pengujian)                                |
| laravel/pint            | ^1.24 (code style / linting)                     |

### 4.2.2 Frontend

**Tabel 4.5 Dependensi Frontend**

| Komponen             | Versi / Keterangan                               |
|----------------------|--------------------------------------------------|
| Blade Templates      | Bawaan Laravel 12                                |
| Tailwind CSS         | ^4.0                                             |
| Vite                 | ^7.0                                             |
| laravel-vite-plugin  | ^2.0                                             |
| Axios                | ^1.11 (HTTP request di sisi klien)               |
| JavaScript           | Vanilla JS + Web Speech API (text-to-speech)     |

### 4.2.3 Layanan Face Recognition (Python)

Layanan terpisah berbasis FastAPI untuk ekstraksi *embedding* wajah pada fitur
absensi biometrik.

**Tabel 4.6 Dependensi Face Service**

| Komponen         | Versi / Keterangan                      |
|------------------|-----------------------------------------|
| Python           | 3.13.5                                  |
| FastAPI          | >= 0.115                                |
| Uvicorn          | >= 0.32                                 |
| PyTorch (torch)  | >= 2.0                                  |
| torchvision      | >= 0.15                                 |
| facenet-pytorch  | >= 2.6                                  |
| Pillow           | >= 10.0                                 |
| NumPy            | >= 2.0                                  |
| python-multipart | >= 0.0.12                               |

Parameter layanan: ambang kecocokan (*threshold*) 0.60, jumlah sampel wajah yang
dibutuhkan 5, berjalan pada port 5001.

### 4.2.4 Layanan AI (Rekomendasi + Chatbot RAG)

Layanan terpisah berbasis FastAPI untuk sistem rekomendasi pelayanan dan chatbot
berbasis RAG (*Retrieval-Augmented Generation*).

**Tabel 4.7 Dependensi AI Service**

| Komponen      | Versi / Keterangan                                   |
|---------------|------------------------------------------------------|
| Python        | 3.13.5                                               |
| FastAPI       | >= 0.115                                             |
| Uvicorn       | >= 0.32                                              |
| scikit-learn  | >= 1.5 (TF-IDF, cosine similarity)                   |
| NumPy         | >= 2.0                                               |
| Pydantic      | >= 2.9                                               |
| OpenAI API    | Opsional (generasi jawaban chatbot)                  |

Metode rekomendasi menggunakan gabungan *Content-Based Filtering* (bobot: minat
0.30, bakat 0.25, ketersediaan 0.20, usia 0.15, lokasi 0.10) dan *Collaborative
Filtering* dengan kombinasi α = 0.60 dan β = 0.40. Layanan berjalan pada port 5002.

---

## 4.3 Arsitektur Sistem (Ringkas)

Sistem terdiri atas tiga komponen yang saling terhubung melalui HTTP:

1. **Aplikasi Web (Laravel 12)** — inti aplikasi, menangani autentikasi,
   antarmuka pengguna, logika bisnis, dan basis data.
2. **Face Service (FastAPI, port 5001)** — ekstraksi *embedding* wajah untuk
   absensi biometrik menggunakan FaceNet.
3. **AI Service (FastAPI, port 5002)** — sistem rekomendasi pelayanan dan
   chatbot RAG.

Aplikasi web berkomunikasi dengan kedua layanan Python melalui panggilan HTTP
internal (`FACE_SERVICE_URL` dan `AI_SERVICE_URL`).
