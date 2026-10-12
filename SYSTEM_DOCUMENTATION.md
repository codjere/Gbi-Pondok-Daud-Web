# 📚 Dokumentasi Lengkap Sistem GBI Pondok Daud
## Portal Gereja Management System - Laravel 12

---

## 📋 **DAFTAR ISI**

1. [Overview Sistem](#overview-sistem)
2. [Arsitektur Database Lengkap](#arsitektur-database-lengkap)
3. [Controllers dan Flow](#controllers-dan-flow)
4. [Models dan Relationships](#models-dan-relationships)
5. [Services](#services)
6. [Migrations](#migrations)
7. [Routing System](#routing-system)
8. [View Structure](#view-structure)
9. [Seeders](#seeders)
10. [Flow Sistem Lengkap](#flow-sistem-lengkap)
11. [Panduan Penggunaan](#panduan-penggunaan)
12. [Technical Documentation](#technical-documentation)
13. [Fitur Terbaru (v1.2)](#-fitur-terbaru-v12)

---

## 🎯 **OVERVIEW SISTEM**

### **Deskripsi Sistem**
Portal GBI Pondok Daud adalah sistem manajemen gereja komprehensif yang mencakup:
- 🔐 **Autentikasi** - Login dengan email/password, OTP verification, Face Recognition
- 📅 **Jadwal Ibadah** - Management jadwal dan reservasi ibadah
- 📖 **Alkitab Digital** - Pembacaan Alkitab dengan text-to-speech dan reading plans
- 🙏 **Permohonan Doa** - Sistem permohonan doa jemaat
- 📰 **Warta/Renungan** - Content management untuk warta gereja
- 🤖 **AI Chatbot** - Asisten virtual dengan RAG (Retrieval-Augmented Generation)
- 👥 **Kuesioner Profil** - Profiling jemaat untuk rekomendasi pelayanan
- 🎭 **COOL Groups** - Kelompok sel dan management
- 📊 **Laporan** - Analytics dan reporting untuk pengurus
- 📷 **Face Recognition** - Absensi biometrik dengan FaceNet

### **Teknologi Stack**
- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: PostgreSQL
- **Frontend**: Blade Templates + Tailwind CSS 4 (CDN browser build)
- **Authentication**: Laravel Auth + Custom OTP + Face Recognition
- **AI/ML**: OpenAI API + FaceNet (Python service)
- **JavaScript**: Vanilla JS + Web Speech API
- **Email**: Laravel Mailer (Log in development)

### **Role System**
- **jemaat** - Member jemaat biasa
- **pelayan** - Pelayan gereja
- **pengurus** - Admin/manager sistem

---

## 🗄️ **ARSITEKTUR DATABASE LENGKAP**

### **Diagram Relationship**
```
users (1) ----< (N) reservasi ----> (1) jadwal_ibadah
  |                  |
  |                  |
  | (N)              (N)
  v                  v
kehadiran <---- (1) jadwal_ibadah
  |
  (N)
  v
user

users (1) ----< (N) permohonan_doa
users (1) ----< (N) konten_warta
users (1) ----< (N) user_pelayanan_choice ----> (1) tim_pelayanan
users (1) ----< (N) user_cool_choice ----> (1) cool_group
users (1) ----< (N) user_profile
users (1) ----< (N) chat_conversation ----< (N) chat_message
users (1) ----< (N) user_reading_progress ----> (1) reading_plans
users (1) ----< (N) user_reading_history ----> (1) pasal

kitab (1) ----< (N) pasal (1) ----< (N) ayat
reading_plans (1) ----< (N) reading_plan_items
```

### **1. Tabel `users`**
Tabel utama untuk semua user sistem

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `name` | VARCHAR(255) | Nama lengkap user |
| `email` | VARCHAR(255) | Email unik |
| `avatar` | VARCHAR(255) | Path relatif foto profil pada disk `public` (nullable) |
| `password` | VARCHAR(255) | Hashed password |
| `role` | VARCHAR(50) | jemaat/pelayan/pengurus |
| `face_embedding` | JSON | Vector wajah FaceNet |
| `has_biometric` | BOOLEAN | Status registrasi biometrik |
| `email_verified_at` | TIMESTAMP | Email verification timestamp |
| `remember_token` | VARCHAR(100) | Remember me token |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `hasMany(Reservasi)` - Reservasi ibadah
- `hasMany(PermohonanDoa)` - Permohonan doa
- `hasMany(KontenWarta)` - Warta yang dibuat
- `hasMany(Kehadiran)` - History kehadiran
- `hasOne(UserProfile)` - Profil lengkap
- `hasMany(UserPelayananChoice)` - Pilihan pelayanan
- `hasMany(UserCoolChoice)` - Pilihan COOL
- `hasMany(ChatConversation)` - Percakapan chatbot
- `hasMany(UserReadingProgress)` - Progress reading plan
- `hasMany(UserReadingHistory)` - History bacaan Alkitab

### **2. Tabel `jadwal_ibadah`**
Management jadwal ibadah

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `nama_sesi` | VARCHAR(255) | Nama sesi (Ibadah Raya, Ibadah Pemuda) |
| `deskripsi` | TEXT | Deskripsi sesi |
| `tanggal` | DATE | Tanggal ibadah |
| `jam_mulai` | TIME | Jam mulai |
| `jam_selesai` | TIME | Jam selesai |
| `pembicara` | VARCHAR(255) | Nama pembicara |
| `lokasi` | VARCHAR(255) | Lokasi ibadah |
| `batas_kuota` | INTEGER | Maksimal peserta |
| `status` | VARCHAR(50) | terpublikasi/selesai/dibatalkan |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `hasMany(Reservasi)` - Reservasi untuk sesi ini
- `hasMany(Kehadiran)` - Kehadiran untuk sesi ini

**Helper Methods:**
- `jumlahTerisi()` - Hitung reservasi confirmed
- `sisaKuota()` - Hitung sisa kuota
- `isPenuh()` - Cek apakah penuh
- `statusKuota()` - Status kuota (tersedia/hampir/penuh)

**Scopes:**
- `scopeAktif()` - Filter sesi aktif (terpublikasi & tanggal >= hari ini)
- `scopeTerbaru()` - Order by tanggal dan jam

### **3. Tabel `reservasi`**
Reservasi ibadah oleh jemaat

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `jadwal_ibadah_id` | BIGINT (FK) | Foreign key ke jadwal_ibadah |
| `status` | VARCHAR(50) | confirmed/canceled/pending |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User yang reservasi
- `belongsTo(JadwalIbadah)` - Jadwal yang di-reservasi

### **4. Tabel `kehadiran`**
Record kehadiran ibadah

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `jadwal_ibadah_id` | BIGINT (FK) | Foreign key ke jadwal_ibadah |
| `waktu_checkin` | TIMESTAMP | Waktu check-in |
| `metode_verifikasi` | VARCHAR(50) | facenet/manual |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User yang hadir
- `belongsTo(JadwalIbadah)` - Jadwal yang dihadiri

### **5. Tabel `permohonan_doa`**
Permohonan doa jemaat

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users (nullable untuk anonim) |
| `nama` | VARCHAR(255) | Nama (jika anonim) |
| `kategori` | VARCHAR(100) | Kategori permohonan |
| `isi_permohonan` | TEXT | Isi permohonan |
| `status` | VARCHAR(50) | pending/diproses/selesai |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User yang mengirim (nullable)

### **6. Tabel `konten_warta`**
Warta/renungan gereja

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users (author) |
| `judul` | VARCHAR(255) | Judul warta |
| `slug` | VARCHAR(255) | URL-friendly slug |
| `isi` | TEXT | Isi warta (HTML) |
| `kategori` | VARCHAR(100) | Kategori (renungan, pengumuman) |
| `published_at` | TIMESTAMP | Waktu publish |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - Author warta

**Scopes:**
- `scopePublished()` - Filter yang sudah dipublish

### **7. Tabel `email_otps`**
OTP untuk email verification

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `email` | VARCHAR(255) | Email tujuan |
| `code_hash` | VARCHAR(255) | Hashed OTP code |
| `expires_at` | TIMESTAMP | Waktu expired (10 menit) |
| `consumed_at` | TIMESTAMP | Waktu consumed |
| `attempts` | INTEGER | Jumlah percobaan (max 5) |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Helper Methods:**
- `isExpired()` - Cek apakah sudah expired

### **8. Tabel `user_profiles`**
Profil lengkap jemaat

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `tanggal_lahir` | DATE | Tanggal lahir |
| `usia` | INTEGER | Usia (auto-calc) |
| `jenis_kelamin` | VARCHAR(20) | L/P |
| `nomor_telepon` | VARCHAR(50) | Nomor HP |
| `alamat` | TEXT | Alamat lengkap |
| `kecamatan` | VARCHAR(100) | Kecamatan |
| `status_perkawinan` | VARCHAR(50) | Single/Menikah/Janda/Duda |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User terkait

### **9. Tabel `tim_pelayanan`**
Tim pelayanan gereja

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `nama` | VARCHAR(255) | Nama tim |
| `slug` | VARCHAR(255) | URL-friendly slug |
| `deskripsi` | TEXT | Deskripsi tim |
| `minat_tags` | JSON | Array tags minat |
| `bakat_tags` | JSON | Array tags bakat |
| `min_usia` | INTEGER | Usia minimum |
| `max_usia` | INTEGER | Usia maksimum |
| `hari_pelayanan` | JSON | Array hari pelayanan |
| `icon` | VARCHAR(50) | Icon FontAwesome |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

### **10. Tabel `user_pelayanan_choices`**
Pilihan pelayanan user

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `tim_pelayanan_id` | BIGINT (FK) | Foreign key ke tim_pelayanan |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User
- `belongsTo(TimPelayanan)` - Tim pelayanan

### **11. Tabel `cool_groups`**
Kelompok sel (COOL)

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `nama` | VARCHAR(255) | Nama COOL |
| `pemimpin` | VARCHAR(255) | Nama pemimpin |
| `kecamatan` | VARCHAR(100) | Kecamatan lokasi |
| `lokasi` | TEXT | Alamat lengkap |
| `jadwal` | VARCHAR(100) | Jadwal pertemuan |
| `min_usia` | INTEGER | Usia minimum |
| `max_usia` | INTEGER | Usia maksimum |
| `tema_minat` | JSON | Array tema minat |
| `kapasitas` | INTEGER | Kapasitas maksimal |
| `terisi` | INTEGER | Jumlah terisi |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

### **12. Tabel `user_cool_choices`**
Pilihan COOL user

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `cool_group_id` | BIGINT (FK) | Foreign key ke cool_groups |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User
- `belongsTo(CoolGroup)` - COOL group

### **13. Tabel `faq_items`**
FAQ untuk chatbot

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `kategori` | VARCHAR(100) | Kategori FAQ |
| `pertanyaan` | TEXT | Pertanyaan |
| `jawaban` | TEXT | Jawaban |
| `keywords` | JSON | Array keywords untuk matching |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

### **14. Tabel `chat_conversations`**
Percakapan chatbot

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `title` | VARCHAR(255) | Judul percakapan |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(User)` - User
- `hasMany(ChatMessage)` - Messages dalam percakapan

### **15. Tabel `chat_messages`**
Messages dalam percakapan

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `conversation_id` | BIGINT (FK) | Foreign key ke chat_conversations |
| `role` | VARCHAR(50) | user/assistant |
| `content` | TEXT | Isi message |
| `sources` | JSON | Array sources (untuk RAG) |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Relationships:**
- `belongsTo(ChatConversation)` - Percakapan

### **16. Tabel Alkitab (kitab, pasal, ayat)**
Lihat dokumentasi `ALKITAB_DOCUMENTATION.md` untuk detail lengkap.

### **17. Tabel Reading Plans (reading_plans, reading_plan_items, user_reading_progress, user_reading_history)**
Lihat dokumentasi `ALKITAB_DOCUMENTATION.md` untuk detail lengkap.

---

## 🎮 **CONTROLLERS DAN FLOW**

### **A. AUTHCONTROLLER**

**Purpose:** Menangani autentikasi user (login, register, OTP, biometric)

**Location:** `app/Http/Controllers/AuthController.php`

**Dependencies:**
- `FaceRecognitionService` - Untuk registrasi dan verifikasi wajah
- `EmailOtpService` - Untuk OTP verification

#### **Methods:**

##### **1. `showLogin()`**
- **Purpose:** Tampilkan halaman login
- **Route:** `GET /login`
- **Middleware:** `guest`
- **Flow:**
  1. Cek jika user sudah login → redirect ke dashboard
  2. Tampilkan view `auth.login`

##### **2. `login(Request $request)`**
- **Purpose:** Proses login
- **Route:** `POST /login`
- **Middleware:** `guest`, `throttle:5,1`
- **Validation:**
  - `email` required, email format
  - `password` required
- **Flow:**
  1. Validate credentials
  2. Attempt authentication dengan Laravel Auth
  3. Jika gagal → return error
  4. Jika berhasil → regenerate session
  5. **Step 1:** Cek email verification
     - Jika belum verified → kirim OTP → redirect ke OTP page
  6. **Step 2:** Cek biometric registration
     - Jika belum → redirect ke biometric registration
  7. **Step 3:** Semua checks passed → redirect ke dashboard

##### **3. `showRegister()`**
- **Purpose:** Tampilkan halaman register
- **Route:** `GET /register`
- **Middleware:** `guest`
- **Flow:**
  1. Tampilkan view `auth.register`

##### **4. `register(Request $request)`**
- **Purpose:** Proses registrasi
- **Route:** `POST /register`
- **Middleware:** `guest`, `throttle:5,1`
- **Validation:**
  - `nama` required, max 255
  - `email` required, email, unique
  - `status` required, in: jemaat/pelayan
  - `password` required, confirmed, min 8 chars
- **Flow:**
  1. Validate input
  2. Create user baru dengan role dari status
  3. Set `has_biometric = false`
  4. Login user baru
  5. Kirim OTP email
  6. Redirect ke OTP verification page

##### **5. `showOtp()`**
- **Purpose:** Tampilkan halaman OTP verification
- **Route:** `GET /register/otp`
- **Middleware:** `auth`
- **Flow:**
  1. Cek jika user not authenticated → redirect ke login
  2. Cek jika sudah verified:
     - Jika belum biometric → redirect ke biometric
     - Jika sudah biometric → redirect ke dashboard
  3. Tampilkan view `auth.otp`

##### **6. `verifyOtp(Request $request)`**
- **Purpose:** Verifikasi OTP code
- **Route:** `POST /register/otp`
- **Middleware:** `auth`, `throttle:10,1`
- **Validation:**
  - `code` required, digits:6
- **Flow:**
  1. Validate input
  2. Call `EmailOtpService::verify()`
  3. Jika invalid → return error
  4. Jika valid → regenerate session
  5. Update `email_verified_at` di database
  6. Cek biometric:
     - Jika belum → redirect ke biometric
     - Jika sudah → redirect ke dashboard

##### **7. `resendOtp()`**
- **Purpose:** Resend OTP code
- **Route:** `POST /register/otp/resend`
- **Middleware:** `auth`, `throttle:3,1`
- **Flow:**
  1. Cek jika sudah verified → redirect ke dashboard
  2. Call `EmailOtpService::send()`
  3. Return success message

##### **8. `showBiometric()`**
- **Purpose:** Tampilkan halaman registrasi biometrik
- **Route:** `GET /register/biometric`
- **Middleware:** `auth`, `verified.otp`
- **Flow:**
  1. Cek jika sudah biometric → redirect ke dashboard
  2. Cek availability FaceNet service
  3. Tampilkan view `auth.biometric`

##### **9. `storeBiometric(Request $request)`**
- **Purpose:** Simpan data wajah (face embedding)
- **Route:** `POST /register/biometric`
- **Middleware:** `auth`, `verified.otp`, `session.expiration`
- **Validation:**
  - `images` required, array, min samples (default 5)
  - `images.*` required, string (base64)
- **Flow:**
  1. Cek jika sudah biometric → redirect ke dashboard
  2. Validate input
  3. Cek FaceNet service availability
  4. Call `FaceRecognitionService::extractEmbeddingBatch()`
  5. Jika gagal → return error
  6. Jika berhasil → update user:
     - `face_embedding` = embedding vector
     - `has_biometric` = true
  7. Redirect ke dashboard

##### **10. `logout(Request $request)`**
- **Purpose:** Logout user
- **Route:** `POST /logout`
- **Middleware:** `auth`
- **Flow:**
  1. Call `Auth::logout()`
  2. Invalidate session
  3. Regenerate CSRF token
  4. Redirect to landing page

---

### **B. DASHBOARDCONTROLLER**

**Purpose:** Menampilkan dashboard utama dengan statistik

**Location:** `app/Http/Controllers/DashboardController.php`

#### **Methods:**

##### **1. `index()`**
- **Purpose:** Tampilkan dashboard
- **Route:** `GET /dashboard`
- **Middleware:** `auth`, `verified.otp`
- **Flow:**
  1. Ambil user authenticated
  2. Hitung statistik:
     - Total jemaat (role = jemaat)
     - Reservasi minggu ini (confirmed, week range)
     - Permohonan doa pending
     - Persentase kehadiran (jemaat terakhir vs kuota)
  3. Ambil 3 jadwal terdekat (aktif, terbaru)
  4. Ambil renungan terbaru (published, with author)
  5. Kirim ke view `dashboard`

---

### **C. JEMAAT RESERVASICONTROLLER**

**Purpose:** Menangani reservasi ibadah oleh jemaat

**Location:** `app/Http/Controllers/Jemaat/ReservasiController.php`

#### **Methods:**

##### **1. `index()`**
- **Purpose:** Tampilkan halaman reservasi
- **Route:** `GET /jemaat/reservasi`
- **Middleware:** `auth`, `verified.otp`
- **Flow:**
  1. Ambil semua jadwal aktif (terpublikasi, tanggal >= hari ini)
  2. Ambil reservasi user (pluck jadwal_ibadah_id)
  3. Kirim ke view `jemaat.reservasi`

##### **2. `store(Request $request, JadwalIbadah $jadwal)`**
- **Purpose:** Buat reservasi baru
- **Route:** `POST /jemaat/reservasi/{jadwal}`
- **Middleware:** `auth`, `verified.otp`
- **Flow:**
  1. Wrap dalam DB transaction
  2. Lock jadwal row untuk prevent race condition
  3. Validasi:
     - Status = terpublikasi
     - Tidak penuh (sisa kuota > 0)
     - User belum reservasi sesi ini
  4. Create reservasi dengan status = confirmed
  5. Commit transaction
  6. Return success message

---

### **D. JEMAAT CHATBOTCONTROLLER**

**Purpose:** Menangani AI chatbot untuk Q&A gereja

**Location:** `app/Http/Controllers/Jemaat/ChatbotController.php`

**Dependencies:**
- `ChatbotService` - Untuk processing chat dengan AI

#### **Methods:**

##### **1. `index()`**
- **Purpose:** Tampilkan halaman chatbot
- **Route:** `GET /jemaat/chat`
- **Middleware:** `auth`, `verified.otp`
- **Flow:**
  1. Ambil conversation terakhir user
  2. Ambil messages dalam conversation (jika ada)
  3. Cek service availability
  4. Kirim ke view `jemaat.chatbot`

##### **2. `send(Request $request)`**
- **Purpose:** Kirim message dan dapatkan response AI
- **Route:** `POST /jemaat/chat/send`
- **Middleware:** `auth`, `verified.otp`
- **Validation:**
  - `message` required, string, max 1000
  - `conversation_id` nullable, exists
- **Flow:**
  1. Validate input
  2. Get atau create conversation
  3. Save user message ke database
  4. Build history dari 10 messages terakhir
  5. Call `ChatbotService::chat()` dengan message dan history
  6. Jika service unavailable → return 503
  7. Save assistant response ke database
  8. Return JSON response dengan answer, sources, intent

---

### **E. PENGURUS ABSENCONTROLLER**

**Purpose:** Menangani absensi biometrik dan manual

**Location:** `app/Http/Controllers/Pengurus/AbsenController.php`

**Dependencies:**
- `FaceRecognitionService` - Untuk face recognition

#### **Methods:**

##### **1. `index()`**
- **Purpose:** Tampilkan halaman absensi
- **Route:** `GET /pengurus/absen`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Flow:**
  1. Ambil semua jadwal aktif
  2. Cek FaceNet service availability
  3. Hitung user dengan biometric terdaftar
  4. Kirim ke view `pengurus.absen`

##### **2. `verify(Request $request)`**
- **Purpose:** Verifikasi wajah untuk absensi
- **Route:** `POST /pengurus/absen/verify`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`, `session.expiration`
- **Validation:**
  - `image` required, string (base64)
  - `jadwal_id` required, exists
- **Flow:**
  1. Cek FaceNet service availability
  2. Extract embedding dari image
  3. Jika gagal → return error
  4. Ambil semua user dengan biometric terdaftar
  5. Jika tidak ada → return error
  6. Find best match dengan FaceNet
  7. Jika tidak match → return not recognized
  8. Cek sudah absen untuk sesi ini:
     - Jika sudah → return already checked in
  9. Create kehadiran record
  10. Return success dengan nama dan similarity score

##### **3. `todayList(Request $request)`**
- **Purpose:** Get list kehadiran hari ini
- **Route:** `GET /pengurus/absen/list`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`, `session.expiration`
- **Validation:**
  - `jadwal_id` required, exists
- **Flow:**
  1. Ambil kehadiran untuk jadwal tertentu
  2. Include user data
  3. Order by waktu check-in
  4. Return JSON response

##### **4. `manual(Request $request)`**
- **Purpose:** Absensi manual
- **Route:** `POST /pengurus/absen/manual`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Validation:**
  - `jadwal_id` required, exists
  - `user_id` required, exists
- **Flow:**
  1. Cek sudah absen → return error
  2. Create kehadiran dengan metode = manual
  3. Return success

##### **5. `participants(Request $request)`**
- **Purpose:** Tampilkan halaman participants (reservasi + kehadiran)
- **Route:** `GET /pengurus/absen/participants`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Validation:**
  - `jadwal_id` required, exists
- **Flow:**
  1. Ambil jadwal
  2. Ambil semua reservasi dengan user
  3. Ambil semua kehadiran dengan user
  4. Ambil semua users
  5. Kirim ke view `pengurus.participants`

---

### **F. PENGURUS JADWALCONTROLLER**

**Purpose:** Management jadwal ibadah

**Location:** `app/Http/Controllers/Pengurus/JadwalController.php`

#### **Methods:**

##### **1. `index()`**
- **Purpose:** Tampilkan semua jadwal
- **Route:** `GET /pengurus/jadwal`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Flow:**
  1. Ambil semua jadwal (order by tanggal desc, jam)
  2. Kirim ke view `pengurus.jadwal`

##### **2. `store(Request $request)`**
- **Purpose:** Buat jadwal baru
- **Route:** `POST /pengurus/jadwal`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Validation:**
  - `nama_sesi` required, max 255
  - `deskripsi` nullable, max 1000
  - `tanggal` required, date
  - `jam_mulai` required
  - `jam_selesai` required, after:jam_mulai
  - `pembicara` required, max 255
  - `batas_kuota` required, integer, min 1
  - `lokasi` nullable, max 255
- **Flow:**
  1. Validate input
  2. Create jadwal dengan status = terpublikasi
  3. Return success

##### **3. `destroy(JadwalIbadah $jadwal)`**
- **Purpose:** Hapus jadwal
- **Route:** `DELETE /pengurus/jadwal/{jadwal}`
- **Middleware:** `auth`, `verified.otp`, `role:pengurus`
- **Flow:**
  1. Delete jadwal
  2. Return success

---

### **G. JEMAAT ALKITABCONTROLLER**

**Purpose:** Menangani fitur Alkitab Digital

**Location:** `app/Http/Controllers/Jemaat/AlkitabController.php`

**Methods:** Lihat dokumentasi `ALKITAB_DOCUMENTATION.md` untuk detail lengkap.

---

## 🧩 **MODELS DAN RELATIONSHIPS**

### **User Model**

**Location:** `app/Models/User.php`

**Fillable Fields:**
- `name`, `email`, `avatar`, `password`, `role`, `face_embedding`, `has_biometric`, `email_verified_at`

**Hidden Fields:**
- `password`, `remember_token`, `face_embedding`

**Casts:**
- `email_verified_at` → datetime
- `password` → hashed
- `face_embedding` → array
- `has_biometric` → boolean

**Helper Methods:**
- `isPengurus()` - Return true jika role = pengurus
- `isJemaat()` - Return true jika role = jemaat
- `isPelayan()` - Return true jika role = pelayan
- `initials()` - Generate inisial dari nama
- `avatarUrl()` - URL publik foto profil, atau `null` bila belum ada (fallback ke inisial)

**Relationships:**
- `hasMany(Reservasi)` - Reservasi ibadah
- `hasMany(PermohonanDoa)` - Permohonan doa
- `hasMany(KontenWarta)` - Warta yang dibuat
- `hasMany(Kehadiran)` - History kehadiran
- `hasOne(UserProfile)` - Profil lengkap
- `hasMany(UserPelayananChoice)` - Pilihan pelayanan
- `hasMany(UserCoolChoice)` - Pilihan COOL
- `hasMany(ChatConversation)` - Percakapan chatbot
- `hasMany(UserReadingProgress)` - Progress reading plan
- `hasMany(UserReadingHistory)` - History bacaan Alkitab

### **JadwalIbadah Model**

**Location:** `app/Models/JadwalIbadah.php`

**Fillable Fields:**
- `nama_sesi`, `deskripsi`, `tanggal`, `jam_mulai`, `jam_selesai`, `pembicara`, `lokasi`, `batas_kuota`, `status`

**Casts:**
- `tanggal` → date

**Helper Methods:**
- `jamMulaiFormatted()` - Format jam mulai (HH:MM)
- `jamSelesaiFormatted()` - Format jam selesai (HH:MM)
- `jumlahTerisi()` - Hitung reservasi confirmed
- `sisaKuota()` - Hitung sisa kuota
- `isPenuh()` - Cek apakah penuh
- `statusKuota()` - Status kuota (tersedia/hampir/penuh)

**Scopes:**
- `scopeAktif()` - Filter sesi aktif
- `scopeTerbaru()` - Order by tanggal dan jam

**Relationships:**
- `hasMany(Reservasi)` - Reservasi untuk sesi ini
- `hasMany(Kehadiran)` - Kehadiran untuk sesi ini

### **Reservasi Model**

**Location:** `app/Models/Reservasi.php`

**Fillable Fields:**
- `user_id`, `jadwal_ibadah_id`, `status`

**Relationships:**
- `belongsTo(User)` - User yang reservasi
- `belongsTo(JadwalIbadah)` - Jadwal yang di-reservasi

### **Kehadiran Model**

**Location:** `app/Models/Kehadiran.php`

**Fillable Fields:**
- `user_id`, `jadwal_ibadah_id`, `waktu_checkin`, `metode_verifikasi`

**Casts:**
- `waktu_checkin` → datetime

**Relationships:**
- `belongsTo(User)` - User yang hadir
- `belongsTo(JadwalIbadah)` - Jadwal yang dihadiri

### **PermohonanDoa Model**

**Location:** `app/Models/PermohonanDoa.php`

**Fillable Fields:**
- `user_id`, `nama`, `kategori`, `isi_permohonan`, `status`

**Relationships:**
- `belongsTo(User)` - User yang mengirim (nullable)

### **KontenWarta Model**

**Location:** `app/Models/KontenWarta.php`

**Fillable Fields:**
- `user_id`, `judul`, `slug`, `isi`, `kategori`, `published_at`

**Scopes:**
- `scopePublished()` - Filter yang sudah dipublish

**Relationships:**
- `belongsTo(User)` - Author warta

### **EmailOtp Model**

**Location:** `app/Models/EmailOtp.php`

**Fillable Fields:**
- `user_id`, `email`, `code_hash`, `expires_at`, `consumed_at`, `attempts`

**Helper Methods:**
- `isExpired()` - Cek apakah sudah expired

### **UserProfile Model**

**Location:** `app/Models/UserProfile.php`

**Fillable Fields:**
- `user_id`, `tanggal_lahir`, `usia`, `jenis_kelamin`, `nomor_telepon`, `alamat`, `kecamatan`, `status_perkawinan`

**Relationships:**
- `belongsTo(User)` - User terkait

### **TimPelayanan Model**

**Location:** `app/Models/TimPelayanan.php`

**Fillable Fields:**
- `nama`, `slug`, `deskripsi`, `minat_tags`, `bakat_tags`, `min_usia`, `max_usia`, `hari_pelayanan`, `icon`

**Casts:**
- `minat_tags` → array
- `bakat_tags` → array
- `hari_pelayanan` → array

**Relationships:**
- `hasMany(UserPelayananChoice)` - Pilihan user

### **CoolGroup Model**

**Location:** `app/Models/CoolGroup.php`

**Fillable Fields:**
- `nama`, `pemimpin`, `kecamatan`, `lokasi`, `jadwal`, `min_usia`, `max_usia`, `tema_minat`, `kapasitas`, `terisi`

**Casts:**
- `tema_minat` → array

**Relationships:**
- `hasMany(UserCoolChoice)` - Pilihan user

### **FaqItem Model**

**Location:** `app/Models/FaqItem.php`

**Fillable Fields:**
- `kategori`, `pertanyaan`, `jawaban`, `keywords`

**Casts:**
- `keywords` → array

### **ChatConversation Model**

**Location:** `app/Models/ChatConversation.php`

**Fillable Fields:**
- `user_id`, `title`

**Relationships:**
- `belongsTo(User)` - User
- `hasMany(ChatMessage)` - Messages

### **ChatMessage Model**

**Location:** `app/Models/ChatMessage.php`

**Fillable Fields:**
- `conversation_id`, `role`, `content`, `sources`

**Casts:**
- `sources` → array

**Relationships:**
- `belongsTo(ChatConversation)` - Percakapan

---

## 🔧 **SERVICES**

### **A. EMAILOTPSERVICE**

**Location:** `app/Services/EmailOtpService.php`

**Purpose:** Menangani OTP untuk email verification

#### **Methods:**

##### **1. `send(User $user)`**
- **Purpose:** Kirim OTP ke email user
- **Flow:**
  1. Consume OTP lama (set consumed_at)
  2. Generate random 6-digit code
  3. Hash code dengan Hash::make()
  4. Create record EmailOtp dengan:
     - user_id
     - email
     - code_hash
     - expires_at = now + 10 menit
  5. Kirim email dengan EmailOtpMail
  6. Jika mail driver = log, log ke laravel.log

##### **2. `verify(User $user, string $code)`**
- **Purpose:** Verifikasi OTP code
- **Flow:**
  1. Get OTP terakhir yang belum consumed
  2. Cek jika expired → return false
  3. Cek jika attempts >= 5 → return false
  4. Increment attempts
  5. Verify code dengan Hash::check()
  6. Jika invalid → return false
  7. Jika valid:
     - Set consumed_at = now
     - Update user email_verified_at = now
     - Return true

---

### **B. FACERECOGNITIONSERVICE**

**Location:** `app/Services/FaceRecognitionService.php`

**Purpose:** Interface dengan FaceNet Python service untuk face recognition

#### **Methods:**

##### **1. `isAvailable()`**
- **Purpose:** Cek jika FaceNet service aktif
- **Flow:**
  1. Send GET request ke `/health` endpoint
  2. Return true jika response successful

##### **2. `extractEmbeddingBatch(array $images)`**
- **Purpose:** Extract face embedding dari multiple images
- **Parameters:** Array base64 images
- **Flow:**
  1. Send POST request ke `/extract` endpoint
  2. Return {embedding, samples_used} atau null jika gagal

##### **3. `findBestMatch(array $embedding, array $knownUsers)`**
- **Purpose:** Find best match dari known users
- **Parameters:**
  - embedding - Vector dari input image
  - knownUsers - Array {id, name, embedding}
- **Flow:**
  1. Send POST request ke `/match` endpoint
  2. Return {user_id, name, similarity} atau null jika tidak match

---

### **C. CHATBOTSERVICE**

**Location:** `app/Services/ChatbotService.php`

**Purpose:** Interface dengan AI service untuk chatbot dengan RAG

#### **Methods:**

##### **1. `isAvailable()`**
- **Purpose:** Cek jika AI service aktif
- **Flow:**
  1. Send GET request ke `/health` endpoint
  2. Return true jika response successful

##### **2. `chat(string $message, array $history)`**
- **Purpose:** Process chat message dengan AI
- **Parameters:**
  - message - User message
  - history - Array {role, content} dari 10 messages terakhir
- **Flow:**
  1. Build knowledge base dari:
     - FAQ items
     - Jadwal ibadah aktif
     - Konten warta terbaru
     - Info gereja
  2. Send POST request ke `/chat` endpoint dengan:
     - message
     - documents (knowledge base)
     - history
     - openai_api_key
  3. Jika service unavailable → call fallbackChat()
  4. Return {answer, sources, intent}

##### **3. `fallbackChat(string $message)`**
- **Purpose:** Fallback jika AI service unavailable
- **Flow:**
  1. Search FAQ dengan keyword matching
  2. Jika match → return FAQ answer
  3. Jika tidak match → return default greeting

---

### **D. RECOMMENDATIONSERVICE**

**Location:** `app/Services/RecommendationService.php`

**Purpose:** Rekomendasi pelayanan dan COOL berdasarkan profil user

#### **Methods:**

##### **1. `recommendTimPelayanan(User $user)`**
- **Purpose:** Rekomendasi tim pelayanan
- **Flow:**
  1. Get user profile
  2. Get all tim pelayanan
  3. Score setiap tim berdasarkan:
     - Usia range match
     - Minat tags match
     - Bakat tags match
     - Hari pelayanan match
  4. Return top 3 recommendations

##### **2. `recommendCoolGroup(User $user)`**
- **Purpose:** Rekomendasi COOL group
- **Flow:**
  1. Get user profile (kecamatan)
  2. Get all COOL groups
  3. Score setiap COOL berdasarkan:
     - Kecamatan match
     - Usia range match
     - Tema minat match
     - Kapasitas availability
  4. Return top 3 recommendations

---

## 📝 **MIGRATIONS**

### **Migration Files:**

1. **0001_01_01_000000_create_users_table.php**
   - Tabel users dengan auth fields

2. **0001_01_01_000001_create_cache_table.php**
   - Tabel cache Laravel

3. **0001_01_01_000002_create_jobs_table.php**
   - Tabel queue jobs

4. **2026_08_11_000001_add_profile_fields_to_users_table.php**
   - Add face_embedding, has_biometric, email_verified_at

5. **2026_08_11_000002_create_jadwal_ibadah_table.php**
   - Tabel jadwal_ibadah

6. **2026_08_11_000003_create_reservasi_table.php**
   - Tabel reservasi

7. **2026_08_11_000004_create_permohonan_doa_table.php**
   - Tabel permohonan_doa

8. **2026_08_11_000005_create_konten_warta_table.php**
   - Tabel konten_warta

9. **2026_08_11_000006_create_kehadiran_table.php**
   - Tabel kehadiran

10. **2026_08_13_000001_create_ai_features_tables.php**
    - Tabel: tim_pelayanan, cool_groups, user_profiles, user_pelayanan_choices, user_cool_choices, faq_items, chat_conversations, chat_messages

11. **2026_08_24_000001_create_email_otps_table.php**
    - Tabel email_otps

12. **2026_08_24_094612_add_deskripsi_to_jadwal_ibadah_table.php**
    - Add deskripsi field to jadwal_ibadah

13. **2026_08_24_100647_create_bible_structure.php**
    - Tabel: kitab, pasal, ayat, reading_plans, reading_plan_items, user_reading_progress, user_reading_history

---

## 🛣️ **ROUTING SYSTEM**

### **Route Structure:**

```php
// Public Routes
Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/tentang-kami', fn () => view('about'))->name('about');

// Guest Routes (Login/Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

// Auth Routes (OTP Verification)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register/otp', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/register/otp', [AuthController::class, 'verifyOtp'])->name('otp.verify')->middleware('throttle:10,1');
    Route::post('/register/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend')->middleware('throttle:3,1');
});

// Verified Routes (OTP + Biometric)
Route::middleware(['auth', 'verified.otp'])->group(function () {
    // Biometric Registration
    Route::get('/register/biometric', [AuthController::class, 'showBiometric'])->name('register.biometric');
    Route::post('/register/biometric', [AuthController::class, 'storeBiometric'])->name('register.biometric.store')->middleware('session.expiration');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Jemaat Routes
    Route::prefix('jemaat')->name('jemaat.')->group(function () {
        Route::get('/jadwal', [JemaatJadwalController::class, 'index'])->name('jadwal');
        Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi');
        Route::post('/reservasi/{jadwal}', [ReservasiController::class, 'store'])->name('reservasi.store');
        Route::get('/doa', [PermohonanDoaController::class, 'index'])->name('doa');
        Route::post('/doa', [PermohonanDoaController::class, 'store'])->name('doa.store');
        Route::get('/warta', [WartaController::class, 'index'])->name('warta');
        Route::get('/warta/{warta:slug}', [WartaController::class, 'show'])->name('warta.show');
        Route::get('/kuesioner', [KuesionerController::class, 'index'])->name('kuesioner');
        Route::post('/kuesioner', [KuesionerController::class, 'store'])->name('kuesioner.store');
        Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
        Route::post('/rekomendasi/tim', [RekomendasiController::class, 'pilihTim'])->name('rekomendasi.tim');
        Route::post('/rekomendasi/cool', [RekomendasiController::class, 'pilihCool'])->name('rekomendasi.cool');
        Route::get('/chat', [ChatbotController::class, 'index'])->name('chat');
        Route::post('/chat/send', [ChatbotController::class, 'send'])->name('chat.send');
        
        // Alkitab Routes
        Route::get('/alkitab', [AlkitabController::class, 'index'])->name('alkitab');
        Route::get('/alkitab/kitab/{kitabId}/pasal/{nomorPasal}', [AlkitabController::class, 'bacaPasal'])->name('alkitab.baca');
        Route::post('/alkitab/selesai-baca', [AlkitabController::class, 'selesaiBaca'])->name('alkitab.selesai-baca');
        Route::get('/alkitab/reading-plans', [AlkitabController::class, 'readingPlans'])->name('alkitab.plans');
        Route::post('/alkitab/mulai-plan', [AlkitabController::class, 'mulaiPlan'])->name('alkitab.mulai-plan');
        Route::get('/alkitab/plan/{planId}', [AlkitabController::class, 'detailPlan'])->name('alkitab.plan');
        Route::post('/alkitab/selesai-harian', [AlkitabController::class, 'selesaiHarian'])->name('alkitab.selesai-harian');
        Route::get('/alkitab/cari', [AlkitabController::class, 'cari'])->name('alkitab.cari');
    });
    
    // Pengurus Routes (Role: pengurus)
    Route::prefix('pengurus')->name('pengurus.')->middleware('role:pengurus')->group(function () {
        Route::get('/jadwal', [PengurusJadwalController::class, 'index'])->name('jadwal');
        Route::post('/jadwal', [PengurusJadwalController::class, 'store'])->name('jadwal.store');
        Route::delete('/jadwal/{jadwal}', [PengurusJadwalController::class, 'destroy'])->name('jadwal.destroy');
        Route::get('/konten', [KontenController::class, 'index'])->name('konten');
        Route::post('/konten', [KontenController::class, 'store'])->name('konten.store');
        Route::delete('/konten/{konten}', [KontenController::class, 'destroy'])->name('konten.destroy');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/absen', [AbsenController::class, 'index'])->name('absen');
        Route::post('/absen/verify', [AbsenController::class, 'verify'])->name('absen.verify')->middleware('session.expiration');
        Route::post('/absen/manual', [AbsenController::class, 'manual'])->name('absen.manual');
        Route::get('/absen/participants', [AbsenController::class, 'participants'])->name('absen.participants');
        Route::get('/absen/list', [AbsenController::class, 'todayList'])->name('absen.list')->middleware('session.expiration');
    });
});
```

### **Middleware:**

1. **guest** - Hanya untuk user belum login
2. **auth** - Hanya untuk user sudah login
3. **verified.otp** - Email sudah diverifikasi (custom middleware)
4. **role:pengurus** - Hanya user dengan role = pengurus
5. **throttle:X,Y** - Rate limiting (X request per Y menit)
6. **session.expiration** - Handle session expiration (custom middleware)

---

## 🎨 **VIEW STRUCTURE**

### **View Hierarchy:**

```
resources/views/
├── layouts/
│   └── app.blade.php (Main layout)
├── partials/
│   └── flash.blade.php (Flash messages)
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── otp.blade.php
│   └── biometric.blade.php
├── dashboard.blade.php
├── landing.blade.php
├── about.blade.php
├── jemaat/
│   ├── jadwal.blade.php
│   ├── reservasi.blade.php
│   ├── doa.blade.php
│   ├── warta.blade.php
│   ├── warta-show.blade.php
│   ├── kuesioner.blade.php
│   ├── rekomendasi.blade.php
│   ├── chatbot.blade.php
│   ├── alkitab.blade.php
│   ├── alkitab-baca.blade.php
│   ├── alkitab-plans.blade.php
│   ├── alkitab-plan-detail.blade.php
│   └── alkitab-cari.blade.php
├── pengurus/
│   ├── jadwal.blade.php
│   ├── konten.blade.php
│   ├── laporan.blade.php
│   ├── absen.blade.php
│   └── participants.blade.php
└── emails/
    └── otp.blade.php
```

### **Theming & UI (Dark/Light Mode)**

Tema diatur terpusat di dua layout: `resources/views/layouts/app.blade.php` (area login/dashboard) dan `resources/views/layouts/guest.blade.php` (landing & halaman auth).

**Mekanisme dark mode:**
- Menggunakan **dark mode berbasis class** (`.dark` pada elemen `<html>`), bukan `prefers-color-scheme`. Karena Tailwind v4 (CDN) secara default memakai media query, layout mendefinisikan `@custom-variant dark (&:where(.dark, .dark *));` agar semua utility `dark:*` merespon class `.dark`.
- Preferensi disimpan di `localStorage` (`darkMode`) dan diterapkan sebelum halaman render untuk mencegah flash.
- Toggle: fungsi `toggleDarkMode()` menamb/menghapus class `.dark`.

**Palet dark:**
- Diatur melalui override skala `--color-slate-*` di blok `@theme`. Karena seluruh view memakai utility `dark:*-slate-*`, satu perubahan di sini mewarnai ulang tema gelap secara konsisten.
- Blok fallback `:where(.dark) ...` (spesifisitas setara satu class) menjadi cadangan untuk elemen yang memakai utility bare (mis. `bg-white`) tanpa pasangan `dark:*`, sehingga tetap ikut gelap.

**Interaksi & transisi:**
- `.press` (efek tekan tombol), `.tilt-card` (kartu terangkat saat hover), `[data-reveal]` (scroll-reveal via IntersectionObserver), `.page-enter` (animasi masuk halaman), dan fade-out saat pindah menu.
- Semua animasi menghormati `prefers-reduced-motion`.

**Landing page** (`resources/views/landing.blade.php`) memakai container lebar (`max-w-[100rem]`) dengan padding responsif (`px-5 sm:px-8 lg:px-12`) agar konten mengisi layar lebar namun tetap nyaman di perangkat kecil.

---

## 🌱 **SEEDERS**

### **AiSeeder**

**Location:** `database/seeders/AiSeeder.php`

**Purpose:** Seed data untuk fitur AI (Tim Pelayanan, COOL Groups, FAQ)

**Data Seeded:**

1. **Tim Pelayanan (6 tim):**
   - Praise & Worship
   - Multimedia
   - Usher & Hospitality
   - Youth / Pemuda
   - Tim Doa & Intercessory
   - Tim Anak (Sunday School)

2. **COOL Groups (6 COOL):**
   - COOL Medan Kota — Rehoboth
   - COOL Medan Area — Bethel
   - COOL Medan Timur — Efrata
   - COOL Medan Denai — Nazareth
   - COOL Medan Sunggal — Salem
   - COOL Medan Helvetia — Sion

3. **FAQ Items (6 FAQ):**
   - Pembaptisan
   - Pernikahan
   - Jadwal ibadah pemuda
   - COOL Groups
   - Pendaftaran tim pelayanan
   - Permohonan doa

### **BibleSeeder**

**Location:** `database/seeders/BibleSeeder.php`

**Purpose:** Seed data untuk Alkitab Digital

**Data Seeded:**

1. **Kitab (39 kitab):**
   - Perjanjian Lama (Genesis sampai Malachi)
   - Perjanjian Baru (Matthew sampai Revelation)

2. **Pasal (Sample):**
   - Kejadian 1-2
   - Mazmur 23
   - Yohanes 3

3. **Ayat (Sample):**
   - Ayat untuk pasal di atas

4. **Reading Plans (2 plans):**
   - 1 Tahun Baca Alkitab (365 hari)
   - 6 Bulan Baca Alkitab (180 hari)

5. **Reading Plan Items (7 hari sample):**
   - Sample bacaan untuk 7 hari pertama

---

## 🔄 **FLOW SISTEM LENGKAP**

### **1. FLOW REGISTRASI & LOGIN**

```
User buka halaman register
    ↓
Input nama, email, password, status (jemaat/pelayan)
    ↓
Submit form → POST /register
    ↓
AuthController::register()
    ↓
Create user dengan role dari status
    ↓
Login user baru
    ↓
EmailOtpService::send() → Kirim OTP ke email
    ↓
Redirect ke /register/otp
    ↓
User input 6-digit OTP code
    ↓
Submit → POST /register/otp
    ↓
AuthController::verifyOtp()
    ↓
EmailOtpService::verify()
    ↓
Jika valid → Update email_verified_at
    ↓
Redirect ke /register/biometric
    ↓
User upload 5+ foto wajah
    ↓
Submit → POST /register/biometric
    ↓
AuthController::storeBiometric()
    ↓
FaceRecognitionService::extractEmbeddingBatch()
    ↓
Extract face embedding vector
    ↓
Update user face_embedding, has_biometric = true
    ↓
Redirect ke /dashboard
```

### **2. FLOW LOGIN EXISTING USER**

```
User buka halaman login
    ↓
Input email, password
    ↓
Submit → POST /login
    ↓
AuthController::login()
    ↓
Laravel Auth::attempt()
    ↓
Jika berhasil → Check email_verified_at
    ↓
Jika belum verified → Kirim OTP → Redirect ke OTP
    ↓
Jika sudah verified → Check has_biometric
    ↓
Jika belum biometric → Redirect ke biometric registration
    ↓
Jika sudah biometric → Redirect ke dashboard
```

### **3. FLOW RESERVASI IBADAH**

```
User login dan verified
    ↓
Buka menu /jemaat/reservasi
    ↓
Lihat semua jadwal aktif
    ↓
Pilih jadwal ibadah
    ↓
Klik "Reservasi" → POST /jemaat/reservasi/{jadwal}
    ↓
ReservasiController::store()
    ↓
DB transaction + Lock jadwal
    ↓
Validasi: status = terpublikasi, tidak penuh, belum reservasi
    ↓
Create reservasi dengan status = confirmed
    ↓
Return success message
    ↓
User siap untuk verifikasi kamera di hari-H
```

### **4. FLOW ABSENSI BIOMETRIK**

```
Pengurus buka /pengurus/absen
    ↓
Pilih jadwal ibadah
    ↓
Buka kamera untuk face recognition
    ↓
Pelayan/jemaat hadir di depan kamera
    ↓
Capture foto → POST /pengurus/absen/verify
    ↓
AbsenController::verify()
    ↓
FaceRecognitionService::extractEmbeddingBatch()
    ↓
Extract embedding dari foto
    ↓
FaceRecognitionService::findBestMatch()
    ↓
Compare dengan semua user terdaftar
    ↓
Jika match dengan similarity > threshold
    ↓
Create kehadiran record dengan metode = facenet
    ↓
Return success dengan nama dan similarity score
    ↓
Pelayan/jemaat sukses absen
```

### **5. FLOW CHATBOT AI**

```
User buka /jemaat/chat
    ↓
Tampilkan chat interface
    ↓
User ketik pertanyaan
    ↓
Submit → POST /jemaat/chat/send
    ↓
ChatbotController::send()
    ↓
Save user message ke database
    ↓
Build history dari 10 messages terakhir
    ↓
ChatbotService::chat()
    ↓
Build knowledge base (FAQ, jadwal, warta, info gereja)
    ↓
Send ke AI service (/chat endpoint)
    ↓
AI service:
    - Process dengan RAG
    - Generate answer
    - Return sources
    ↓
Save assistant response ke database
    ↓
Return JSON dengan answer, sources, intent
    ↓
Display response di chat interface
```

### **6. FLOW KUESIONER PROFIL & REKOMENDASI**

```
User buka /jemaat/kuesioner
    ↓
Isi profil lengkap:
    - Tanggal lahir
    - Jenis kelamin
    - Nomor telepon
    - Alamat
    - Kecamatan
    - Status perkawinan
    - Minat tags
    - Bakat tags
    - Ketersediaan hari
    ↓
Submit → POST /jemaat/kuesioner
    ↓
KuesionerController::store()
    ↓
Save user profile
    ↓
Redirect ke /jemaat/rekomendasi
    ↓
RecommendationService::recommendTimPelayanan()
    ↓
Score setiap tim berdasarkan match
    ↓
Display top 3 tim pelayanan
    ↓
RecommendationService::recommendCoolGroup()
    ↓
Score setiap COOL berdasarkan kecamatan dan match
    ↓
Display top 3 COOL groups
    ↓
User pilih tim pelayanan → POST /jemaat/rekomendasi/tim
    ↓
Save user_pelayanan_choice
    ↓
User pilih COOL → POST /jemaat/rekomendasi/cool
    ↓
Save user_cool_choice
```

---

## 👥 **PANDUAN PENGGUNAAN**

### **1. Pendaftaran & Login**

**Untuk Jemaat Baru:**
1. Buka halaman register
2. Isi nama, email, password
3. Pilih status: "Jemaat"
4. Submit
5. Cek email untuk OTP code (atau laravel.log di development)
6. Input OTP code
7. Upload 5+ foto wajah untuk biometric
8. Selesai! Login berhasil

**Untuk Pelayan Baru:**
1. Sama seperti jemaat
2. Pilih status: "Pelayan"
3. Setelah login, akan dapat rekomendasi tim pelayanan

**Untuk Pengurus:**
- Tidak bisa daftar melalui public register
- Harus dibuat manual oleh admin database

### **2. Reservasi Ibadah**

1. Login dan verified
2. Buka menu "Jadwal Ibadah"
3. Lihat semua jadwal aktif
4. Klik "Reservasi" pada jadwal yang diinginkan
5. Sistem cek kuota dan status
6. Reservasi berhasil dengan status "confirmed"
7. Di hari-H, gunakan kamera untuk verifikasi

### **3. Absensi (Pengurus)**

1. Login sebagai pengurus
2. Buka menu "Absen Pelayan"
3. Pilih jadwal ibadah hari ini
4. Buka kamera
5. Posisikan pelayan/jemaat di depan kamera
6. Sistem otomatis recognize wajah
7. Jika match → absen berhasil
8. Jika tidak match → minta registrasi biometrik
9. Bisa juga absen manual jika face recognition gagal

### **4. Chatbot AI**

1. Buka menu "Asisten GBI Pondok Daud"
2. Ketik pertanyaan seputar:
   - Jadwal ibadah
   - COOL groups
   - Tim pelayanan
   - Pembaptisan
   - Pernikahan
   - Renungan
3. Sistem AI akan menjawab dengan:
   - Jawaban dari FAQ
   - Informasi dari jadwal ibadah
   - Renungan terbaru
   - Source referensi

### **5. Alkitab Digital**

Lihat dokumentasi `ALKITAB_DOCUMENTATION.md` untuk panduan lengkap.

---

## 🛠️ **TECHNICAL DOCUMENTATION**

### **Environment Setup**

**Required:**
- PHP 8.2+
- Composer
- PostgreSQL
- Node.js (untuk development tools)

**Installation:**
```bash
# Clone repository
git clone <repository-url>
cd gereja-thesis

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### **Configuration Files**

**`.env` Key Settings:**
```env
APP_NAME="GBI Pondok Daud"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gereja_thesis
DB_USERNAME=postgres
DB_PASSWORD=password

MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

AI_SERVICE_URL=http://localhost:5000
AI_OPENAI_API_KEY=sk-...
AI_TIMEOUT=30

FACENET_SERVICE_URL=http://localhost:5001
FACENET_SAMPLES_REQUIRED=5
```

### **Artisan Commands**

```bash
# Database
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Queue
php artisan queue:work
php artisan queue:failed

# Generate keys
php artisan key:generate
```

### **Testing**

Sistem memiliki **automated feature test** (PHPUnit) yang menguji fitur backend end-to-end: autentikasi, alur OTP & biometrik, guard role, serta seluruh fitur jemaat dan pengurus.

#### **Cakupan Test**

| File | Fokus | Jumlah |
|------|-------|--------|
| `tests/Feature/AuthTest.php` | Register, login, OTP, biometrik (FaceNet di-mock), guard role, guest redirect, logout | 21 |
| `tests/Feature/JemaatTest.php` | Dashboard, jadwal, reservasi (termasuk kuota penuh & duplikat), permohonan doa, warta, kuesioner, rekomendasi, chatbot, Alkitab, **profil & foto profil (upload/hapus/validasi)** | 30 |
| `tests/Feature/PengurusTest.php` | Kelola jadwal, kelola konten, laporan (**+ breakdown metode & export PDF/Excel**), absensi (manual/FaceNet/list), permohonan doa | 23 |
| `tests/Feature/SanityTest.php` | Landing page & migrasi database | 2 |

Total: **85 feature test** (termasuk test bawaan). Semua lolos pada pengujian terakhir.

#### **Konfigurasi Test (`phpunit.xml`)**

Test dijalankan pada environment terisolasi yang di-override di `phpunit.xml`, terpisah dari data aplikasi asli:

- **Database**: PostgreSQL, database khusus `gereja_test` (bukan database produksi). Menggunakan trait `RefreshDatabase` sehingga skema dibuat ulang setiap test.
- **Mail**: `array` — email OTP tidak benar-benar terkirim.
- **Queue**: `sync`, **Cache/Session**: `array`, **BCRYPT_ROUNDS**: `4` (mempercepat test).
- **APP_URL**: `http://localhost` — wajib di-set agar route `/` tidak menjadi 404 saat test (karena `.env` memakai subpath `/gereja-thesis/public`).

> **Catatan lingkungan:** Laravel default memakai SQLite in-memory untuk test. Pada lingkungan development ini driver `pdo_sqlite` tidak terpasang (hanya `pdo_pgsql`), sehingga test diarahkan ke PostgreSQL. Database `gereja_test` perlu dibuat sekali sebelum menjalankan test:
> ```sql
> CREATE DATABASE "gereja_test" ENCODING 'UTF8';
> ```

#### **Layanan Eksternal saat Test**

Fitur yang bergantung pada layanan Python eksternal **tidak** menghubungi service sungguhan saat test — panggilan HTTP di-mock dengan `Http::fake()`:

- **FaceNet** (`:5001`): registrasi & verifikasi wajah biometrik, halaman absen.
- **AI service** (`:5002`): chatbot, kuesioner/rekomendasi.

Ini membuat test deterministik dan bisa jalan offline, sekaligus menguji jalur "service aktif" maupun "service mati" (mis. absen mengembalikan 503 saat FaceNet down).

#### **Menjalankan Test**

```bash
# Jalankan seluruh test
php artisan test

# Jalankan satu file / grup
php artisan test --filter=AuthTest
php artisan test --filter=JemaatTest
php artisan test --filter=PengurusTest

# Jalankan satu test spesifik
php artisan test --filter=test_reservasi_full_quota_blocked
```

> Setelah mengubah env di `phpunit.xml`, jalankan `php artisan config:clear` agar perubahan terbaca.

#### **Batasan**

Test ini mencakup **logika backend** (controller, validasi, database, middleware, integrasi service). Test ini **tidak** menguji tampilan/JavaScript di browser (dark mode, animasi, kamera langsung) — hal tersebut perlu pengujian manual atau browser test terpisah.

---

## 📞 **TROUBLESHOOTING**

### **1. OTP Tidak Terkirim**

**Solusi:**
- Cek `config/mail.php` → set `MAIL_MAILER=log`
- Cek `storage/logs/laravel.log` untuk OTP code
- Pastikan mail server configuration benar

### **2. Face Recognition Tidak Berfungsi**

**Solusi:**
- Jalankan FaceNet service: `python face-service/app.py`
- Cek `config/facenet.php` → `service_url`
- Pastikan user sudah registrasi biometrik
- Cek lighting dan kamera quality

### **3. Chatbot Tidak Merespon**

**Solusi:**
- Jalankan AI service
- Cek `config/ai.php` → `service_url` dan `openai_api_key`
- Cek service availability dengan `/health` endpoint
- Fallback ke FAQ matching jika service down

### **4. Session Expired**

**Solusi:**
- Cek `config/session.php` → `lifetime` (default 480 menit)
- Pastikan `HandleSessionExpiration` middleware registered
- Refresh halaman untuk re-auth

---

## 📊 **STATISTIK & ANALYTICS**

### **Metrics yang Ditrack:**

1. **User Metrics:**
   - Total jemaat terdaftar
   - Total pelayan terdaftar
   - User dengan biometric terdaftar
   - User dengan email verified

2. **Ibadah Metrics:**
   - Total reservasi per minggu
   - Kehadiran per sesi
   - Utilisasi kuota
   - Trend reservasi

3. **AI Metrics:**
   - Total chat conversations
   - FAQ matches vs AI responses
   - Most asked questions
   - Chatbot availability

4. **Alkitab Metrics:**
   - Reading plan participants
   - Progress tracking
   - Popular chapters
   - Search queries

---

## 🎓 **PANDUAN PEMBELAJARAN CODING**

### **Laravel Concepts yang Dipelajari:**

1. **Eloquent ORM**
   - Models dan Relationships
   - Query Scopes
   - Mass Assignment
   - Attribute Casting
   - Accessors & Mutators

2. **Authentication**
   - Laravel Auth system
   - Custom middleware
   - OTP verification
   - Biometric integration

3. **Routing**
   - Route groups
   - Route parameters
   - Route naming
   - Middleware application

4. **Blade Templates**
   - Template inheritance
   - Components
   - Directives
   - Conditionals
   - Loops

5. **Services**
   - Service pattern
   - Dependency injection
   - External API integration
   - Error handling

6. **Database**
   - Migrations
   - Seeders
   - Relationships
   - Transactions
   - Locking

---

## 🚀 **FUTURE ENHANCEMENTS**

### **Fitur yang Bisa Ditambahkan:**

1. **Payment Integration** - Donasi online
2. **Event Management** - Event khusus gereja
3. **Mobile App** - Native iOS/Android
4. **Push Notifications** - Notifikasi jadwal, warta
5. **Video Streaming** - Live streaming ibadah
6. **Social Features** - Community forum
7. **Volunteer Management** - Advanced volunteer scheduling
8. **Reporting** - Advanced analytics dashboard
9. **Multi-language** - Support English/Indonesian
10. **SMS Gateway** - SMS OTP dan notifications

---

## 📝 **KESIMPULAN**

Sistem GBI Pondok Daud adalah sistem manajemen gereja komprehensif dengan:

✅ **Autentikasi Multi-factor** - Email OTP + Face Recognition
✅ **Manajemen Jadwal** - Jadwal ibadah dan reservasi
✅ **Absensi Biometrik** - Face recognition dengan FaceNet
✅ **Alkitab Digital** - Baca Alkitab dengan text-to-speech dan reading plans
✅ **AI Chatbot** - RAG-based chatbot untuk Q&A
✅ **Rekomendasi Pelayanan** - AI-powered recommendations
✅ **COOL Groups** - Kelompok sel management
✅ **Permohonan Doa** - Sistem doa jemaat
✅ **Warta/Renungan** - Content management
✅ **Dashboard Analytics** - Statistik dan reporting

**Arsitektur Solid:**
- Laravel 12 sebagai backend framework
- PostgreSQL sebagai database
- Blade + Tailwind CSS sebagai frontend
- Python services untuk AI dan Face Recognition
- OpenAI API untuk AI chatbot
- Web Speech API untuk text-to-speech

**Dokumentasi Lengkap:**
- Semua controllers dengan flow
- Semua models dengan relationships
- Semua services dengan methods
- Semua migrations dengan structure
- Routing system lengkap
- View structure
- Seeders dan sample data
- Flow sistem lengkap
- Panduan penggunaan
- Technical documentation

---

*Dokumentasi ini dibuat untuk memudahkan pemahaman, pengembangan, dan maintenance sistem GBI Pondok Daud Portal Gereja Management System.*

---

## 🆕 **FITUR TERBARU (v1.2)**

Bagian ini mendokumentasikan tiga pekerjaan pada iterasi terakhir: **(1) Foto Profil Jemaat**, **(2) Laporan Kehadiran yang Diperkaya**, dan **(3) Pengayaan Data Demo (termasuk Alkitab lengkap)**. Setiap bagian menjelaskan perubahan backend beserta alasan desainnya.

---

### **1. Fitur Foto Profil (Avatar)**

Menambahkan kemampuan jemaat mengunggah, menampilkan, dan menghapus foto profil pada halaman Profil yang sudah ada.

#### **1.1 Migration — kolom `avatar`**

**File:** `database/migrations/2026_10_05_000001_add_avatar_to_users_table.php`

Menambahkan satu kolom `avatar` (nullable string) ke tabel `users`, ditempatkan setelah kolom `email`. Kolom ini menyimpan **path relatif** file pada disk `public` (mis. `avatars/abc123.jpg`), bukan URL penuh dan bukan data gambar.

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('avatar')->nullable()->after('email');
});
```

**Keputusan desain:** foto disimpan sebagai **file di storage** (disk `public`, folder `avatars/`), bukan base64 di database. Alasan: menyimpan biner gambar di DB membengkakkan ukuran tabel dan memperlambat query. Pola file + path adalah standar Laravel dan memerlukan `php artisan storage:link` (symlink `public/storage` → `storage/app/public`) agar file bisa diakses publik.

#### **1.2 Model `User`**

**File:** `app/Models/User.php`

- `avatar` ditambahkan ke `$fillable`.
- Method baru `avatarUrl()` mengembalikan URL publik foto, atau `null` bila belum ada (view memakai inisial sebagai fallback).

```php
public function avatarUrl(): ?string
{
    if (! $this->avatar) {
        return null;
    }

    return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
}
```

Method lama `initials()` tetap dipakai sebagai fallback ketika `avatarUrl()` bernilai `null`.

#### **1.3 Controller — upload & hapus**

**File:** `app/Http/Controllers/Jemaat/ProfileController.php`

Pada `update()`, validasi file ditambahkan dengan pesan error berbahasa Indonesia:

```php
'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // maks 2 MB
```

Logika penyimpanan: jika ada file ter-upload, hapus avatar lama (bila ada) lalu simpan yang baru. `store('avatars', 'public')` mengembalikan path relatif yang langsung disimpan ke kolom `avatar`.

```php
if ($request->hasFile('avatar')) {
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
    }
    $user->avatar = $request->file('avatar')->store('avatars', 'public');
}
```

Method baru `deleteAvatar()` menghapus file dari disk dan meng-null-kan kolom:

```php
public function deleteAvatar()
{
    $user = Auth::user();
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
        $user->avatar = null;
        $user->save();
    }
    return redirect()->route('jemaat.profil')->with('success', 'Foto profil dihapus.');
}
```

#### **1.4 Route**

**File:** `routes/web.php` (grup `jemaat`)

```php
Route::delete('/profil/foto', [ProfileController::class, 'deleteAvatar'])->name('profil.avatar.delete');
```

Form hapus dibuat **terpisah** (method `DELETE`) dari form utama profil (method `PUT`), karena satu `<form>` HTML hanya bisa punya satu method.

#### **1.5 View**

- `resources/views/jemaat/profil.blade.php`: form utama kini memakai `enctype="multipart/form-data"` (wajib untuk upload file); input file memakai atribut `form="profilForm"` agar tetap satu submit dengan data profil lainnya; ada pratinjau JavaScript (`URL.createObjectURL`) sebelum simpan, serta tombol hapus (form `DELETE` terpisah).
- `resources/views/layouts/app.blade.php`: blok user di sidebar menampilkan avatar kecil bila ada, selain itu ikon generik.

#### **1.6 Catatan lingkungan — ekstensi GD**

Pemrosesan gambar (dan `UploadedFile::fake()->image()` pada test) membutuhkan ekstensi **GD**. Pada XAMPP di mesin ini, `extension=gd` di `C:\xampp\php\php.ini` sempat dinonaktifkan dan telah diaktifkan. **Apache perlu di-restart** agar web server mengenali perubahan ini (CLI sudah mengenalinya).

---

### **2. Laporan Kehadiran yang Diperkaya**

Menu Laporan pengurus diperluas dari sekadar tabel per-sesi menjadi laporan analitik dengan **export PDF & Excel**, **grafik tren kehadiran**, dan **breakdown metode verifikasi (FaceNet vs manual)**.

#### **2.1 Dependensi baru**

```bash
composer require barryvdh/laravel-dompdf maatwebsite/excel
```

- `barryvdh/laravel-dompdf` → render PDF dari Blade.
- `maatwebsite/excel` → export file `.xlsx`.

#### **2.2 Controller**

**File:** `app/Http/Controllers/Pengurus/LaporanController.php`

Dirombak dengan satu method privat `buildReport()` yang dipakai bersama oleh tampilan dan kedua export, agar sumber data konsisten (DRY). Isinya:

- **Statistik per sesi** — total hadir, kuota, persentase kehadiran.
- **Breakdown metode verifikasi** — jumlah & persentase `facenet` vs `manual` untuk sesi terpilih.
- **Tren kehadiran** — mengambil 10 sesi terakhir via `withCount('kehadiran')`, diurutkan menaik berdasarkan tanggal untuk grafik garis.

```php
$metode['facenet'] = $kehadiran->where('metode_verifikasi', 'facenet')->count();
$metode['manual']  = $kehadiran->where('metode_verifikasi', 'manual')->count();

$trenSource = JadwalIbadah::withCount('kehadiran')
    ->orderByDesc('tanggal')->take(10)->get()
    ->sortBy('tanggal')->values();
```

Dua action export:

```php
public function exportPdf(Request $request)   // Pdf::loadView('pengurus.laporan-pdf', $data)->download(...)
public function exportExcel(Request $request) // Excel::download(new KehadiranExport($jadwal), ...)
```

Keduanya memanggil `buildReport()` dan `abort_unless($data['jadwal'], 404)` bila sesi tidak ditemukan.

#### **2.3 Export class**

**File:** `app/Exports/KehadiranExport.php`

Implementasi `FromCollection`, `WithHeadings`, dan `WithMapping` dari maatwebsite/excel. `collection()` mengambil kehadiran satu sesi (eager-load `user`); `map()` memetakan tiap baris menjadi kolom No, Nama, Email, Peran, Waktu Check-in, dan Metode Verifikasi (nilai `facenet` ditampilkan sebagai "Pengenalan Wajah").

#### **2.4 View**

- `resources/views/pengurus/laporan-pdf.blade.php` — template cetak (HTML + CSS inline, font DejaVu Sans untuk dompdf) berisi kop gereja, metadata sesi, kartu statistik, dan tabel daftar hadir.
- `resources/views/pengurus/laporan.blade.php` — tombol PDF/Excel asli (menggantikan tombol dummy), grafik **tren** (Chart.js line) dan **metode verifikasi** (Chart.js doughnut) via CDN, serta badge metode yang lebih manusiawi ("Wajah"/"Manual"). Script Chart.js dibungkus `@push('scripts')` dan sadar dark mode.

#### **2.5 Route**

**File:** `routes/web.php` (grup `pengurus`)

```php
Route::get('/laporan/pdf',   [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
```

---

### **3. Pengayaan Data Demo**

Menambah data agar aplikasi terasa hidup, sekaligus **memperbaiki bug lama**: Alkitab hanya berisi Kejadian 1–2.

#### **3.1 `BibleSeeder` kini dipanggil `DatabaseSeeder`**

**File:** `database/seeders/DatabaseSeeder.php`

Sebelumnya `DatabaseSeeder` **tidak** memanggil `BibleSeeder`, sehingga setiap `migrate:fresh --seed` menghasilkan Alkitab kosong (akar masalah "cuma Kejadian 1–2"). Sekarang dipanggil setelah `AiSeeder`:

```php
$this->call(AiSeeder::class);
$this->call(BibleSeeder::class);   // ← ditambahkan
...
$this->call(ExtraDemoSeeder::class);
$this->call(HistoriDemoSeeder::class); // ← ditambahkan
```

`BibleSeeder` menjalankan command `bible:import` yang mengunduh teks AYT lengkap. Hasil seeding terakhir: **66 kitab, 1.189 pasal, 31.102 ayat**. Bila unduhan gagal (offline), seeder otomatis fallback ke data sample.

#### **3.2 Seeder baru `HistoriDemoSeeder`**

**File:** `database/seeders/HistoriDemoSeeder.php`

Menghidupkan laporan tanpa melanggar aturan desain lama ("daftar hadir jangan terisi otomatis"). **Aturan kunci: kehadiran hanya untuk ibadah yang tanggalnya SUDAH LEWAT (status `selesai`).** Isi seeder:

- **6 jemaat tambahan** beserta `user_profiles` lengkap (minat/bakat/ketersediaan) → menyuburkan dashboard & rekomendasi.
- **6 ibadah raya lampau** (status `selesai`) + kehadiran historis dengan pola jumlah bervariasi (9–13 orang) supaya grafik tren menarik. Metode verifikasi dibuat ~75% `facenet`, ~25% `manual`, mencerminkan fokus sistem pada pengenalan wajah.
- **Reservasi** untuk ibadah **mendatang** (~60% kuota) — wajar karena jemaat memesan tempat; kehadiran tetap kosong karena belum hari-H.

Semua penulisan memakai `firstOrCreate` agar idempoten (aman dijalankan berulang; menghormati unique constraint `(user_id, jadwal_ibadah_id)` pada tabel `kehadiran` dan `reservasi`).

#### **3.3 Hasil akhir database (setelah `migrate:fresh --seed`)**

| Entitas | Jumlah |
|---------|--------|
| Users | 21 (19 jemaat) |
| User profiles | 10 |
| Jadwal ibadah | 14 (7 selesai) |
| Kehadiran | 63 (45 FaceNet / 18 manual) |
| Reservasi | 105 |
| Konten warta | 7 |
| Permohonan doa | 6 |
| Kitab / Pasal / Ayat | 66 / 1.189 / 31.102 |
| Reading plan | 2 |
| Tim pelayanan / COOL / FAQ | 6 / 6 / 8 |

Login demo: `admin@gbipondokdaud.id` (pengurus) & jemaat seperti `andi@email.com`, semua password `password123`.

---

### **4. Verifikasi Fitur Terbaru**

- **Automated test**: ditambah test foto profil (upload/hapus/validasi) di `JemaatTest.php` dan test laporan (breakdown + export PDF/Excel) di `PengurusTest.php`. Total suite menjadi **85 feature test**, semua lolos.
- **Smoke test manual**: 22 route (jemaat & pengurus) diuji terhadap database ber-seed, termasuk laporan + export, Alkitab hingga **Wahyu 22**, dan chatbot — seluruhnya mengembalikan 200/302 (tanpa error 500).

> Catatan: route baca Alkitab adalah `GET /jemaat/alkitab/kitab/{kitabId}/pasal/{nomorPasal}` (name `jemaat.alkitab.baca`).

---

**Last Updated:** 2026-09-10
**Version:** 1.2
**Author:** Devin AI Assistant

### **Changelog**

**v1.2 (2026-09-10)**
- Tambah section **Fitur Terbaru (v1.2)**: Foto Profil (avatar) jemaat, Laporan Kehadiran yang diperkaya (export PDF & Excel, grafik tren, breakdown metode verifikasi), dan Pengayaan Data Demo.
- Dokumentasikan perbaikan bug Alkitab: `BibleSeeder` kini dipanggil `DatabaseSeeder` sehingga Alkitab terisi lengkap (66 kitab / 1.189 pasal / 31.102 ayat), bukan hanya Kejadian 1–2.
- Dokumentasikan seeder baru `HistoriDemoSeeder` (kehadiran historis hanya untuk ibadah yang sudah lewat; reservasi untuk ibadah mendatang).
- Catat penambahan dependensi `barryvdh/laravel-dompdf` & `maatwebsite/excel`, serta pengaktifan ekstensi GD (perlu restart Apache).
- Suite test bertambah menjadi **85 feature test** (dari 68/77), semua lolos.

**v1.1 (2026-09-10)**
- Koreksi versi framework: Laravel 11 → **Laravel 12**.
- Tambah bagian **Testing**: 68 feature test (Auth/Jemaat/Pengurus/Sanity), konfigurasi `phpunit.xml` (PostgreSQL `gereja_test`, `APP_URL`), penggunaan `Http::fake()` untuk FaceNet/AI service, dan cara menjalankan.
- Tambah bagian **Theming & UI**: dark mode berbasis class (`@custom-variant`), palet via `--color-slate-*`, micro-interaction & transisi, container landing responsif.

**v1.0 (2026-08-24)**
- Dokumentasi awal lengkap.
