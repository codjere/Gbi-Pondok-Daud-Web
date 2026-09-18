# 📖 Dokumentasi Sistem Alkitab Digital
## Portal GBI Pondok Daud - Fitur Pembacaan Alkitab

---

## 📋 **DAFTAR ISI**

1. [Overview Sistem](#overview-sistem)
2. [Arsitektur Database](#arsitektur-database)
3. [Flow Sistem Lengkap](#flow-sistem-lengkap)
4. [Komponen-komponen](#komponen-komponen)
5. [Panduan Penggunaan](#panduan-penggunaan)
6. [Technical Documentation](#technical-documentation)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 **OVERVIEW SISTEM**

### **Deskripsi Fitur**
Sistem Alkitab Digital adalah fitur pembacaan firman Tuhan dengan kemampuan:
- 📖 **Baca Alkitab** - Akses ke seluruh kitab, pasal, dan ayat
- 🔊 **Text-to-Speech** - Pembacaan otomatis dengan suara
- 📅 **Reading Plans** - Rencana bacaan terstruktur (1 tahun & 6 bulan)
- 📊 **Progress Tracking** - Monitoring kemajuan bacaan
- 🔍 **Pencarian** - Cari ayat berdasarkan kata kunci

### **Teknologi yang Digunakan**
- **Backend**: Laravel 11 (PHP Framework)
- **Database**: PostgreSQL
- **Frontend**: Blade Templates + Tailwind CSS
- **Audio**: Web Speech API (Browser Native)
- **JavaScript**: Vanilla JS (tanpa framework)

---

## 🗄️ **ARSITEKTUR DATABASE**

### **Struktur Tabel**

#### **1. Tabel `kitab`**
Menyimpan data kitab-kitab Alkitab

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `nama` | VARCHAR(255) | Nama lengkap kitab (Kejadian, Keluaran, dll) |
| `singkatan` | VARCHAR(10) | Singkatan kitab (Kej, Kel, dll) |
| `urutan` | INTEGER | Urutan kitab dalam Alkitab (1-66) |
| `jumlah_pasal` | INTEGER | Total pasal dalam kitab |
| `testament` | VARCHAR(5) | PL (Perjanjian Lama) atau PB (Perjanjian Baru) |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Contoh Data:**
```sql
INSERT INTO kitab (nama, singkatan, urutan, jumlah_pasal, testament) 
VALUES ('Kejadian', 'Kej', 1, 50, 'PL');
```

#### **2. Tabel `pasal`**
Menyimpan data pasal per kitab

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `kitab_id` | BIGINT (FK) | Foreign key ke tabel kitab |
| `nomor_pasal` | INTEGER | Nomor pasal (1, 2, 3, ...) |
| `jumlah_ayat` | INTEGER | Total ayat dalam pasal |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Contoh Data:**
```sql
INSERT INTO pasal (kitab_id, nomor_pasal, jumlah_ayat) 
VALUES (1, 1, 31); -- Kejadian pasal 1
```

#### **3. Tabel `ayat`**
Menyimpan isi ayat per pasal

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `pasal_id` | BIGINT (FK) | Foreign key ke tabel pasal |
| `nomor_ayat` | INTEGER | Nomor ayat (1, 2, 3, ...) |
| `isi_ayat` | TEXT | Isi teks ayat |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Contoh Data:**
```sql
INSERT INTO ayat (pasal_id, nomor_ayat, isi_ayat) 
VALUES (1, 1, 'Pada mulanya Allah menciptakan langit dan bumi.');
```

#### **4. Tabel `reading_plans`**
Menyimpan rencana bacaan (1 tahun, 6 bulan)

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `nama` | VARCHAR(255) | Nama plan (1 Tahun Baca Alkitab) |
| `deskripsi` | TEXT | Deskripsi plan |
| `durasi_hari` | INTEGER | Durasi dalam hari (365 atau 180) |
| `aktif` | BOOLEAN | Status aktif/non-aktif |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

#### **5. Tabel `reading_plan_items`**
Detail bacaan per hari (JSON format)

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `reading_plan_id` | BIGINT (FK) | Foreign key ke reading_plans |
| `hari_ke` | INTEGER | Hari ke-berapa (1, 2, 3, ...) |
| `bacaan` | JSON | Array bacaan hari ini |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Contoh JSON bacaan:**
```json
{
  "hari_ke": 1,
  "bacaan": [
    {
      "kitab_id": 1,
      "pasal_mulai": 1,
      "pasal_selesai": 3
    }
  ]
}
```

#### **6. Tabel `user_reading_progress`**
Progress user per reading plan

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `reading_plan_id` | BIGINT (FK) | Foreign key ke reading_plans |
| `hari_terakhir` | INTEGER | Hari terakhir yang diselesaikan |
| `tanggal_mulai` | DATE | Tanggal mulai plan |
| `tanggal_selesai` | DATE | Tanggal selesai plan |
| `selesai` | BOOLEAN | Status selesai/belum |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

#### **7. Tabel `user_reading_history`**
History setiap kali user membaca pasal

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | BIGINT | Primary Key |
| `user_id` | BIGINT (FK) | Foreign key ke users |
| `pasal_id` | BIGINT (FK) | Foreign key ke pasal |
| `waktu_baca` | TIMESTAMP | Waktu user membaca |
| `selesai_baca` | BOOLEAN | Status selesai dibaca |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

### **Relationship Diagram**
```
kitab (1) ----< (N) pasal (1) ----< (N) ayat
  |                   |
  |                   |
  |                   (N) user_reading_history
  |
(N) reading_plan_items (N) ----< (1) reading_plans (1) ----< (N) user_reading_progress
```

---

## 🔄 **FLOW SISTEM LENGKAP**

### **1. FLOW BACA ALKITAB MANUAL**

```
User Login
    ↓
Menu Alkitab Digital
    ↓
Pilih Kitab (PL/PB Tab)
    ↓
Pilih Pasal (1, 2, 3, ...)
    ↓
Halaman Baca Pasal
    ↓
Opsional: Text-to-Speech (Play/Pause/Stop)
    ↓
Opsional: Tandai Selesai Dibaca
    ↓
Navigation: Pasal Sebelumnya/Selanjutnya
```

### **2. FLOW TEXT-TO-SPEECH**

```
User Klik "Mulai Baca"
    ↓
JavaScript: splitTextIntoChunks()
    ↓
JavaScript: split text into chunks (~150 chars)
    ↓
JavaScript: speakNextChunk(0)
    ↓
Browser: Speak chunk 1
    ↓
Chunk 1 selesai → speakNextChunk(1)
    ↓
Browser: Speak chunk 2
    ↓
...continue sampai selesai semua chunks
    ↓
Update button state → "Mulai Baca"
```

### **3. FLOW READING PLAN**

```
User Login
    ↓
Menu Alkitab Digital → Reading Plans
    ↓
Pilih Plan (1 Tahun / 6 Bulan)
    ↓
Klik "Mulai Plan"
    ↓
Backend: Create user_reading_progress
    ↓
Redirect ke Detail Plan
    ↓
Tampilkan: Bacaan Hari Ini
    ↓
User Klik link pasal untuk membaca
    ↓
User Selesai membaca hari ini
    ↓
Klik "Tandai Hari Ini Selesai"
    ↓
Backend: Increment hari_terakhir
    ↓
Redirect: Tampilkan bacaan hari berikutnya
    ↓
...repeat sampai selesai semua hari
```

### **4. FLOW PROGRESS TRACKING**

```
User Baca Pasal
    ↓
Backend: Create user_reading_history
    ↓
waktu_baca = now()
    ↓
selesai_baca = false
    ↓
User Klik "Tandai Selesai"
    ↓
Backend: Update user_reading_history
    ↓
selesai_baca = true
    ↓
History tersimpan untuk analytics
```

### **5. FLOW PENCARIAN AYAT**

```
User Input kata kunci
    ↓
Backend: LIKE query pada isi_ayat
    ↓
SELECT * FROM ayat WHERE isi_ayat LIKE '%kata%'
    ↓
Limit 50 hasil
    ↓
Tampilkan hasil dengan link ke full chapter
```

---

## 🧩 **KOMPONEN-KOMPONEN**

### **A. MODELS (Laravel Eloquent)**

#### **1. Model Kitab**
```php
class Kitab extends Model
{
    protected $table = 'kitab';
    protected $fillable = ['nama', 'singkatan', 'urutan', 'jumlah_pasal', 'testament'];
    
    // Relationship: Satu kitab memiliki banyak pasal
    public function pasal(): HasMany {
        return $this->hasMany(Pasal::class);
    }
    
    // Scope untuk Perjanjian Lama
    public function scopePerjanjianLama($query) {
        return $query->where('testament', 'PL');
    }
    
    // Scope untuk Perjanjian Baru
    public function scopePerjanjianBaru($query) {
        return $query->where('testament', 'PB');
    }
}
```

**Penjelasan:**
- `$table = 'kitab'` - Set nama tabel secara eksplisit
- `$fillable` - Fields yang bisa di-mass assignment
- `pasal()` - Relationship hasMany ke model Pasal
- `scopePerjanjianLama()` - Query scope untuk filter PL
- `scopePerjanjianBaru()` - Query scope untuk filter PB

#### **2. Model Pasal**
```php
class Pasal extends Model
{
    protected $table = 'pasal';
    protected $fillable = ['kitab_id', 'nomor_pasal', 'jumlah_ayat'];
    
    // Relationship: Pasal milik satu kitab
    public function kitab(): BelongsTo {
        return $this->belongsTo(Kitab::class, 'kitab_id');
    }
    
    // Relationship: Satu pasal memiliki banyak ayat
    public function ayat(): HasMany {
        return $this->hasMany(Ayat::class)->orderBy('nomor_ayat');
    }
    
    // Helper untuk nama lengkap
    public function namaLengkap(): string {
        return $this->kitab->nama . ' ' . $this->nomor_pasal;
    }
}
```

**Penjelasan:**
- `kitab()` - Relationship belongsTo ke model Kitab
- `ayat()` - Relationship hasMany ke model Ayat dengan ordering
- `namaLengkap()` - Helper method untuk format "Kejadian 1"

#### **3. Model Ayat**
```php
class Ayat extends Model
{
    protected $table = 'ayat';
    protected $fillable = ['pasal_id', 'nomor_ayat', 'isi_ayat'];
    
    // Relationship: Ayat milik satu pasal
    public function pasal(): BelongsTo {
        return $this->belongsTo(Pasal::class, 'pasal_id');
    }
    
    // Helper untuk referensi lengkap
    public function referensi(): string {
        return $this->pasal->kitab->singkatan . ' ' . $this->pasal->nomor_pasal . ':' . $this->nomor_ayat;
    }
}
```

**Penjelasan:**
- `referensi()` - Helper untuk format "Kej 1:1"

#### **4. Model ReadingPlan**
```php
class ReadingPlan extends Model
{
    protected $table = 'reading_plans';
    protected $fillable = ['nama', 'deskripsi', 'durasi_hari', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
    
    // Relationship: Satu plan memiliki banyak item bacaan
    public function items(): HasMany {
        return $this->hasMany(ReadingPlanItem::class, 'reading_plan_id')->orderBy('hari_ke');
    }
    
    // Helper untuk format durasi
    public function durasiFormatted(): string {
        if ($this->durasi_hari >= 365) {
            return $this->durasi_hari / 365 . ' Tahun';
        } elseif ($this->durasi_hari >= 30) {
            return round($this->durasi_hari / 30) . ' Bulan';
        }
        return $this->durasi_hari . ' Hari';
    }
}
```

**Penjelasan:**
- `$casts` - Auto-cast boolean untuk field aktif
- `items()` - Relationship ke item bacaan dengan ordering
- `durasiFormatted()` - Helper untuk format "1 Tahun", "6 Bulan"

#### **5. Model ReadingPlanItem**
```php
class ReadingPlanItem extends Model
{
    protected $table = 'reading_plan_items';
    protected $fillable = ['reading_plan_id', 'hari_ke', 'bacaan'];
    protected $casts = ['bacaan' => 'array']; // Cast JSON to array
    
    // Relationship: Item milik satu reading plan
    public function readingPlan(): BelongsTo {
        return $this->belongsTo(ReadingPlan::class, 'reading_plan_id');
    }
    
    // Helper untuk deskripsi bacaan
    public function deskripsiBacaan(): string {
        $deskripsi = [];
        foreach ($this->bacaan as $item) {
            $kitab = Kitab::find($item['kitab_id']);
            if ($kitab) {
                if ($item['pasal_mulai'] == $item['pasal_selesai']) {
                    $deskripsi[] = $kitab->singkatan . ' ' . $item['pasal_mulai'];
                } else {
                    $deskripsi[] = $kitab->singkatan . ' ' . $item['pasal_mulai'] . '-' . $item['pasal_selesai'];
                }
            }
        }
        return implode(', ', $deskripsi);
    }
}
```

**Penjelasan:**
- `$casts = ['bacaan' => 'array']` - Auto-cast JSON field ke PHP array
- `deskripsiBacaan()` - Helper untuk format "Kej 1-3, Maz 23"

#### **6. Model UserReadingProgress**
```php
class UserReadingProgress extends Model
{
    protected $table = 'user_reading_progress';
    protected $fillable = ['user_id', 'reading_plan_id', 'hari_terakhir', 'tanggal_mulai', 'tanggal_selesai', 'selesai'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'selesai' => 'boolean'];
    
    // Helper untuk hitung persentase
    public function persentaseProgress(): float {
        if ($this->selesai) return 100.0;
        
        $totalHari = $this->readingPlan->durasi_hari;
        if ($totalHari == 0) return 0.0;
        
        return round(($this->hari_terakhir / $totalHari) * 100, 1);
    }
    
    // Helper untuk status
    public function status(): string {
        if ($this->selesai) return 'Selesai';
        elseif ($this->hari_terakhir == 0) return 'Belum Mulai';
        else return 'Sedang Berjalan';
    }
}
```

**Penjelasan:**
- `persentaseProgress()` - Hitung progress percentage
- `status()` - Return status text

### **B. CONTROLLERS**

#### **AlkitabController Methods**

##### **1. `index()` - Halaman Utama**
```php
public function index()
{
    $kitabs = Kitab::urutan()->get();
    $readingPlans = ReadingPlan::aktif()->get();
    $userProgress = Auth::check() 
        ? Auth::user()->readingProgress()->with('readingPlan')->get() 
        : collect();
    
    return view('jemaat.alkitab', compact('kitabs', 'readingPlans', 'userProgress'));
}
```

**Flow:**
1. Ambil semua kitab diurutkan
2. Ambil semua reading plans aktif
3. Ambil progress user jika login
4. Kirim ke view

##### **2. `bacaPasal($kitabId, $nomorPasal)` - Baca Pasal**
```php
public function bacaPasal($kitabId, $nomorPasal)
{
    $kitab = Kitab::findOrFail($kitabId);
    $pasal = Pasal::where('kitab_id', $kitabId)
        ->where('nomor_pasal', $nomorPasal)
        ->firstOrFail();
    $ayats = $pasal->ayat;
    
    // Track history jika login
    if (Auth::check()) {
        UserReadingHistory::create([
            'user_id' => Auth::id(),
            'pasal_id' => $pasal->id,
            'waktu_baca' => now(),
            'selesai_baca' => false,
        ]);
    }
    
    return view('jemaat.alkitab-baca', compact('kitab', 'pasal', 'ayats'));
}
```

**Flow:**
1. Cari kitab berdasarkan ID
2. Cari pasal berdasarkan kitab dan nomor
3. Ambil semua ayat dalam pasal
4. Create history bacaan jika user login
5. Kirim ke view

##### **3. `selesaiBaca()` - Mark Pasal Selesai**
```php
public function selesaiBaca(Request $request)
{
    $data = $request->validate(['pasal_id' => ['required', 'exists:pasal,id']]);
    
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Login required'], 401);
    }
    
    $history = UserReadingHistory::where('user_id', Auth::id())
        ->where('pasal_id', $data['pasal_id'])
        ->latest()
        ->first();
    
    if ($history) {
        $history->update(['selesai_baca' => true]);
    }
    
    return response()->json(['success' => true, 'message' => 'Progress tersimpan']);
}
```

**Flow:**
1. Validate input
2. Check auth
3. Update history bacaan terakhir
4. Return JSON response

##### **4. `mulaiPlan()` - Mulai Reading Plan**
```php
public function mulaiPlan(Request $request)
{
    $data = $request->validate(['reading_plan_id' => ['required', 'exists:reading_plans,id']]);
    
    if (!Auth::check()) {
        return back()->with('error', 'Login required');
    }
    
    $existingProgress = UserReadingProgress::where('user_id', Auth::id())
        ->where('reading_plan_id', $data['reading_plan_id'])
        ->first();
    
    if ($existingProgress) {
        return back()->with('info', 'Sudah mulai');
    }
    
    UserReadingProgress::create([
        'user_id' => Auth::id(),
        'reading_plan_id' => $data['reading_plan_id'],
        'hari_terakhir' => 0,
        'tanggal_mulai' => now(),
        'selesai' => false,
    ]);
    
    return redirect()->route('jemaat.alkitab.plan', $data['reading_plan_id'])
        ->with('success', 'Plan dimulai');
}
```

**Flow:**
1. Validate input
2. Check auth
3. Cek apakah sudah ada progress
4. Create progress baru
5. Redirect ke detail plan

##### **5. `detailPlan($planId)` - Detail Reading Plan**
```php
public function detailPlan($planId)
{
    $plan = ReadingPlan::findOrFail($planId);
    $progress = UserReadingProgress::where('user_id', Auth::id())
        ->where('reading_plan_id', $plan->id)
        ->firstOrFail();
    
    $hariBerikutnya = $progress->hari_terakhir + 1;
    $bacaanHariIni = ReadingPlanItem::where('reading_plan_id', $plan->id)
        ->where('hari_ke', $hariBerikutnya)
        ->first();
    
    return view('jemaat.alkitab-plan-detail', compact('plan', 'progress', 'bacaanHariIni', 'hariBerikutnya'));
}
```

**Flow:**
1. Cari plan berdasarkan ID
2. Cari progress user
3. Hitung hari berikutnya
4. Ambil bacaan hari ini
5. Kirim ke view

##### **6. `selesaiHarian()` - Mark Hari Selesai**
```php
public function selesaiHarian(Request $request)
{
    $data = $request->validate(['reading_plan_id' => ['required', 'exists:reading_plans,id']]);
    
    $progress = UserReadingProgress::where('user_id', Auth::id())
        ->where('reading_plan_id', $data['reading_plan_id'])
        ->first();
    
    if ($progress) {
        $progress->increment('hari_terakhir');
        
        if ($progress->hari_terakhir >= $progress->readingPlan->durasi_hari) {
            $progress->update([
                'selesai' => true,
                'tanggal_selesai' => now(),
            ]);
        }
    }
    
    return response()->json(['success' => true, 'message' => 'Progress tersimpan']);
}
```

**Flow:**
1. Validate input
2. Increment hari_terakhir
3. Cek jika sudah selesai semua
4. Update tanggal_selesai jika selesai
5. Return JSON response

### **C. VIEWS**

#### **1. alkitab.blade.php - Halaman Utama**
**Components:**
- Search bar untuk cari ayat
- Reading plans cards dengan progress
- Tab PL/PB untuk kitab
- Grid kitab-kitab

**Key JavaScript:**
```javascript
function showTestament(testament) {
    // Toggle visibility PL/PB books
    document.getElementById('PL-books').classList.add('hidden');
    document.getElementById('PB-books').classList.remove('hidden');
}
```

#### **2. alkitab-baca.blade.php - Baca Pasal**
**Components:**
- Navigation buttons (previous/next chapter)
- Text-to-Speech controls
- Speed slider
- Bible content dengan numbering
- "Tandai Selesai" button

**Key JavaScript (Text-to-Speech):**
```javascript
// Chunking system untuk menghindari interruption
function splitTextIntoChunks(text, maxChunkLength = 200) {
    const chunks = [];
    const sentences = text.split(/[.!?]+/);
    let currentChunk = '';
    
    sentences.forEach(sentence => {
        if (sentence.trim()) {
            const sentenceWithPunctuation = sentence.trim() + '. ';
            if ((currentChunk + sentenceWithPunctuation).length <= maxChunkLength) {
                currentChunk += sentenceWithPunctuation;
            } else {
                if (currentChunk) chunks.push(currentChunk.trim());
                currentChunk = sentenceWithPunctuation;
            }
        }
    });
    
    if (currentChunk) chunks.push(currentChunk.trim());
    return chunks;
}

// Speak chunks secara berurutan
function speakNextChunk() {
    if (currentChunkIndex >= textChunks.length) {
        isSpeaking = false;
        updateSpeechButton(false);
        return;
    }
    
    const chunk = textChunks[currentChunkIndex];
    speechUtterance = new SpeechSynthesisUtterance(chunk);
    speechUtterance.lang = 'id-ID';
    speechUtterance.rate = speechRate;
    
    speechUtterance.onend = () => {
        currentChunkIndex++;
        setTimeout(() => {
            if (isSpeaking && !isPaused) {
                speakNextChunk();
            }
        }, 100);
    };
    
    speechSynthesis.speak(speechUtterance);
}
```

#### **3. alkitab-plans.blade.php - Daftar Plans**
**Components:**
- Grid reading plans
- Progress bar per plan
- "Mulai" atau "Lanjut" button

#### **4. alkitab-plan-detail.blade.php - Detail Plan**
**Components:**
- Progress overview dengan statistik
- Bacaan hari ini dengan links
- "Tandai Hari Ini Selesai" button

#### **5. alkitab-cari.blade.php - Hasil Pencarian**
**Components:**
- Search form
- List ayat yang cocok
- Link ke full chapter

### **D. ROUTES**

```php
// Alkitab Routes
Route::get('/alkitab', [AlkitabController::class, 'index'])->name('alkitab');
Route::get('/alkitab/kitab/{kitabId}/pasal/{nomorPasal}', [...])->name('alkitab.baca');
Route::post('/alkitab/selesai-baca', [...])->name('alkitab.selesai-baca');
Route::get('/alkitab/reading-plans', [...])->name('alkitab.plans');
Route::post('/alkitab/mulai-plan', [...])->name('alkitab.mulai-plan');
Route::get('/alkitab/plan/{planId}', [...])->name('alkitab.plan');
Route::post('/alkitab/selesai-harian', [...])->name('alkitab.selesai-harian');
Route::get('/alkitab/cari', [...])->name('alkitab.cari');
```

---

## 👥 **PANDUAN PENGGUNAAN**

### **1. Cara Baca Alkitab Manual**

1. **Login** ke sistem
2. Buka menu **Alkitab Digital**
3. Pilih tab **Perjanjian Lama** atau **Perjanjian Baru**
4. Klik kitab yang diinginkan (contoh: Kejadian)
5. Klik nomor pasal (contoh: 1)
6. Baca ayat-ayat yang ditampilkan
7. **Opsional**: Klik "Mulai Baca" untuk text-to-speech
8. **Opsional**: Klik "Tandai Selesai Dibaca" untuk menyimpan progress
9. Gunakan tombol navigation untuk pindah pasal

### **2. Cara Menggunakan Text-to-Speech**

1. Di halaman baca pasal, cari bagian **Audio Pembacaan**
2. Klik tombol **"Mulai Baca"**
3. Teks akan dibacakan secara otomatis
4. Atur kecepatan dengan slider (0.5x - 2x)
5. Klik **"Pause"** untuk jeda sementara
6. Klik **"Stop"** untuk menghentikan pembacaan

### **3. Cara Menggunakan Reading Plan**

#### **A. Memulai Reading Plan Baru**

1. Buka menu **Alkitab Digital**
2. Klik **"Lihat Semua"** di bagian Rencana Bacaan
3. Pilih plan yang diinginkan:
   - **1 Tahun Baca Alkitab** (365 hari)
   - **6 Bulan Baca Alkitab** (180 hari)
4. Klik **"Mulai Rencana Ini"**
5. Progress akan dimulai dari hari ke-1

#### **B. Mengikuti Reading Plan**

1. Setelah memulai plan, klik **"Lanjut Baca Hari Ini"**
2. Lihat bacaan hari ini yang ditampilkan
3. Klik link pasal untuk membaca
4. Setelah selesai membaca semua pasal hari ini
5. Klik **"Tandai Hari Ini Selesai"**
6. Sistem akan otomatis menampilkan bacaan hari berikutnya

#### **C. Melihat Progress**

1. Di halaman Reading Plans, progress ditampilkan untuk setiap plan
2. Progress bar menunjukkan persentase penyelesaian
3. Status: "Belum Mulai", "Sedang Berjalan", atau "Selesai"

### **4. Cara Mencari Ayat**

1. Di halaman Alkitab Digital, gunakan search bar
2. Ketik kata kunci (contoh: "kasih", "iman", "harapan")
3. Klik **"Cari"**
4. Hasil pencarian akan ditampilkan
5. Klik **"Baca Selengkapnya"** untuk membaca full chapter

---

## 🛠️ **TECHNICAL DOCUMENTATION**

### **1. Database Migration**

```bash
# Run migration
php artisan migrate

# Rollback migration
php artisan migrate:rollback
```

### **2. Database Seeding**

```bash
# Seed data Alkitab
php artisan db:seed --class=BibleSeeder
```

**Data yang di-seed:**
- 39 kitab Alkitab (PL & PB)
- Sample pasal dan ayat (Kejadian 1-2, Mazmur 23, Yohanes 3)
- 2 reading plans (1 tahun & 6 bulan)
- 7 hari sample bacaan

### **3. Model Relationships**

```php
// One-to-Many
Kitab -> hasMany -> Pasal -> hasMany -> Ayat

// Many-to-Many (via junction)
ReadingPlan -> hasMany -> ReadingPlanItem
User -> hasMany -> UserReadingProgress

// Polymorphic-like
User -> hasMany -> UserReadingHistory
Pasal -> hasMany -> UserReadingHistory
```

### **4. JSON Data Structure**

**Reading Plan Item JSON:**
```json
{
  "hari_ke": 7,
  "bacaan": [
    {
      "kitab_id": 1,
      "pasal_mulai": 19,
      "pasal_selesai": 21
    },
    {
      "kitab_id": 19,
      "pasal_mulai": 1,
      "pasal_selesai": 2
    }
  ]
}
```

### **5. JavaScript API Interactions**

**AJAX Request Pattern:**
```javascript
fetch('/alkitab/selesai-baca', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        pasal_id: pasalId
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Handle success
    }
});
```

### **6. Web Speech API Integration**

**Browser Support:**
- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Opera: ✅ Full support

**Key Methods:**
```javascript
// Initialize
const synthesis = window.speechSynthesis;

// Create utterance
const utterance = new SpeechSynthesisUtterance(text);

// Configure
utterance.lang = 'id-ID';
utterance.rate = 1.0;
utterance.pitch = 1.0;
utterance.volume = 1.0;

// Speak
synthesis.speak(utterance);

// Control
synthesis.pause();
synthesis.resume();
synthesis.cancel();
```

---

## 🔧 **TROUBLESHOOTING**

### **1. Text-to-Speech Tidak Berfungsi**

**Masalah:** Tidak ada suara saat klik "Mulai Baca"

**Solusi:**
- Cek browser settings → Privacy & Security → Speech recognition
- Pastikan volume komputer tidak mute
- Coba di browser lain (Chrome, Edge, Firefox)
- Buka console browser (F12) untuk error messages

**Error umum:**
- `interrupted` - Browser membatalkan speech (gunakan chunking system)
- `not-allowed` - Browser permission tidak diizinkan
- `canceled` - Speech dibatalkan oleh user/system

### **2. Reading Plan Tidak Muncul**

**Masalah:** Reading plans tidak tampil di halaman

**Solusi:**
- Pastikan seeder dijalankan: `php artisan db:seed --class=BibleSeeder`
- Cek database: ada data di tabel `reading_plans`
- Cek model relationship antara `ReadingPlan` dan `ReadingPlanItem`

### **3. Progress Tidak Tersimpan**

**Masalah:** Mark selesai tidak menyimpan progress

**Solusi:**
- Pastikan user sudah login
- Cek CSRF token di form
- Buka console untuk error messages
- Cek network tab di browser dev tools

### **4. Navigation Pasal Error**

**Masalah:** Tombol next/previous tidak berfungsi

**Solusi:**
- Cek route parameters di controller
- Pastikan `nomor_pasal` valid untuk kitab tersebut
- Cek validation di controller

### **5. Pencarian Tidak Berfungsi**

**Masalah:** Hasil pencarian kosong

**Solusi:**
- Cek query di controller menggunakan `LIKE`
- Pastikan ada data di tabel `ayat`
- Cek kata kunci yang dimasukkan

---

## 📊 **STATISTIK & ANALYTICS**

### **Data yang Ditrack:**

1. **Reading Progress:**
   - Total user yang mulai reading plan
   - Progress per user per plan
   - Tingkat penyelesaian (percentage)

2. **Reading History:**
   - Pasal yang paling sering dibaca
   - Waktu baca user
   - Pola bacaan (pagi/siang/malam)

3. **Search Analytics:**
   - Kata kunci yang paling sering dicari
   - Ayat yang paling sering diakses

### **SQL Queries untuk Analytics:**

```sql
-- Pasal paling sering dibaca
SELECT pasal_id, COUNT(*) as total 
FROM user_reading_history 
WHERE selesai_baca = true 
GROUP BY pasal_id 
ORDER BY total DESC;

-- User dengan progress tertinggi
SELECT user_id, reading_plan_id, hari_terakhir 
FROM user_reading_progress 
ORDER BY hari_terakhir DESC;

-- Kata kunci populer (dari search logs jika ada)
-- (Perlu tambah search analytics table)
```

---

## 🎓 **PANDUAN PEMBELAJARAN CODING**

### **1. Laravel Eloquent ORM**

**Concepts yang dipelajari:**
- Models dan Relationships
- Query Scopes
- Mass Assignment ($fillable)
- Attribute Casting ($casts)
- Accessors & Mutators

**Contoh:**
```php
// Relationship
public function pasal(): HasMany {
    return $this->hasMany(Pasal::class);
}

// Scope
public function scopeAktif($query) {
    return $query->where('aktif', true);
}

// Usage
$kitab->pasal()->where('nomor_pasal', 1)->get();
$kitab->aktif()->get();
```

### **2. Laravel Routes**

**Concepts yang dipelajari:**
- Route parameters
- Route groups dengan middleware
- Route naming
- Resource controllers

**Contoh:**
```php
// Parameterized route
Route::get('/alkitab/kitab/{kitabId}/pasal/{nomorPasal}', [...]);

// Route group
Route::middleware(['auth', 'verified.otp'])->group(function () {
    // Routes yang butuh auth dan OTP verification
});
```

### **3. Blade Templates**

**Concepts yang dipelajari:**
- Template inheritance (@extends)
- Section management (@section, @yield)
- Components (@include, @component)
- Loops (@foreach, @forelse)
- Conditionals (@if, @else)
- Directives (@csrf, @method)

**Contoh:**
```blade
@extends('layouts.app')
@section('title', 'Halaman Alkitab')
@section('content')
    @foreach ($ayats as $ayat)
        <p>{{ $ayat->isi_ayat }}</p>
    @endforeach
@endsection
```

### **4. JavaScript & AJAX**

**Concepts yang dipelajari:**
- DOM manipulation
- Event handling
- Fetch API
- JSON handling
- Web Speech API
- Async/await patterns

**Contoh:**
```javascript
// Fetch API
fetch('/api/endpoint', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ data: 'value' })
})
.then(response => response.json())
.then(data => console.log(data));

// Web Speech API
const utterance = new SpeechSynthesisUtterance('Hello World');
speechSynthesis.speak(utterance);
```

### **5. Database Design**

**Concepts yang dipelajari:**
- Normalization (3NF)
- Foreign keys
- Indexes
- JSON fields
- Relationship types (1:1, 1:N, N:M)

**Contoh:**
```sql
-- Foreign key
FOREIGN KEY (kitab_id) REFERENCES kitab(id)

-- Index
CREATE INDEX idx_pasal_kitab ON pasal(kitab_id);

-- JSON field
bacaan JSON -- Store array of objects
```

---

## 🚀 **FUTURE ENHANCEMENTS**

### **1. Fitur yang Bisa Ditambahkan**

- 🎵 **Audio Files** - Tambah audio recording per pasal
- 📝 **Highlight & Notes** - Tambah fitur catatan pribadi
- 📖 **Bookmarks** - Simpan ayat favorit
- 📤 **Share** - Share ayat ke sosial media
- 🌙 **Dark Mode** - Tema gelap untuk membaca malam
- 📱 **Mobile App** - Native app untuk iOS/Android
- 🔔 **Reminders** - Pengingat bacaan harian
- 📊 **Statistics Dashboard** - Analytics untuk admin

### **2. Improvements yang Bisa Dilakukan**

- ✅ Tambah data Alkitab lengkap (66 kitab)
- ✅ Implementasi audio API eksternal (Google TTS)
- ✅ Tambah fitur offline dengan Service Worker
- ✅ Implementasi WebSocket untuk real-time progress
- ✅ Tambah kolaboratif reading (group reading plans)
- ✅ Implementasi multiple languages (English, Indonesian)
- ✅ Tambah comment system per ayat

---

## 📞 **SUPPORT & MAINTENANCE**

### **Debugging Tips**

1. **Laravel Logs:** `storage/logs/laravel.log`
2. **Browser Console:** F12 → Console tab
3. **Database Queries:** `DB::enableQueryLog()`
4. **Network Requests:** F12 → Network tab

### **Maintenance Commands**

```bash
# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Reset database
php artisan migrate:fresh --seed
```

---

## 📝 **KESIMPULAN**

Sistem Alkitab Digital ini menyediakan:

✅ **Baca Alkitab** - Akses lengkap ke seluruh firman Tuhan
✅ **Text-to-Speech** - Pembacaan otomatis dengan chunking system
✅ **Reading Plans** - Rencana bacaan terstruktur (1 tahun & 6 bulan)
✅ **Progress Tracking** - Monitoring kemajuan bacaan
✅ **Pencarian** - Cari ayat berdasarkan kata kunci
✅ **Responsive Design** - Tampilan modern dan user-friendly

**Arsitektur yang solid** dengan:
- Laravel 11 sebagai backend framework
- PostgreSQL sebagai database
- Blade + Tailwind CSS sebagai frontend
- Vanilla JavaScript untuk interaktivitas
- Web Speech API untuk audio

**Dokumentasi lengkap** untuk:
- Pengembangan selanjutnya
- Onboarding developer baru
- Maintenance sistem
- Troubleshooting issues

---

*Dokumentasi ini dibuat untuk memudahkan pemahaman dan pengembangan fitur Alkitab Digital Portal GBI Pondok Daud.*

**Last Updated:** 2026-08-24
**Version:** 1.0
