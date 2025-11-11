# Sistem Auto-Save Gempa Lampung

## 📋 Deskripsi

Sistem ini secara otomatis menyimpan data gempa bumi yang terjadi di wilayah Lampung dan sekitarnya (radius 300 km) ke database untuk keperluan history dan analisis.

## 🗄️ Setup Database

### 1. Jalankan SQL File
Jalankan file SQL untuk membuat tabel:
```bash
database/gempa_lampung.sql
```

Atau copy-paste query SQL ke phpMyAdmin:
- Login ke phpMyAdmin
- Pilih database `u855675680_mntrpekon`
- Buka tab SQL
- Copy-paste isi file `gempa_lampung.sql`
- Klik Go/Execute

### 2. Struktur Tabel
Tabel `gempa_lampung_history` akan dibuat dengan kolom:
- `id` - Primary key
- `tanggal` - Tanggal gempa
- `jam` - Jam kejadian
- `datetime` - Datetime lengkap
- `coordinates` - Koordinat string
- `lintang` - Latitude (decimal)
- `bujur` - Longitude (decimal)
- `magnitude` - Magnitudo gempa
- `kedalaman` - Kedalaman gempa
- `wilayah` - Lokasi gempa
- `potensi` - Potensi dampak
- `shakemap` - URL shakemap
- `is_lampung` - Boolean (1 jika di Lampung)
- `is_nearby` - Boolean (1 jika dalam radius 300km)
- `distance_from_lampung` - Jarak dari Lampung (km)
- `created_at` - Waktu penyimpanan

## 🚀 Cara Kerja

### Auto-Save Otomatis
Sistem bekerja secara otomatis pada halaman `bmkg.php`:

1. **Saat load gempa terkini** - Menyimpan semua gempa M≥5.0 yang ada di Lampung/sekitarnya
2. **Saat load gempa dirasakan** - Menyimpan gempa yang dirasakan di Lampung/sekitarnya
3. **Saat ada gempa baru** - Langsung tersimpan jika di wilayah Lampung

### Kriteria Penyimpanan
- Gempa dengan wilayah mengandung kata "Lampung" 
- Gempa dalam radius 300 km dari Lampung (koordinat: -5.1099, 105.2253)
- Tidak menyimpan duplikat (cek berdasarkan tanggal, jam, koordinat)

### Mencegah Duplikat
Sistem akan:
- ✅ Skip jika data sudah ada di database
- ✅ Log: "⏭️ Gempa already exists"
- ✅ Hanya simpan gempa baru

## 📊 Mengakses Data

### 1. Melalui Web Interface
Buka halaman: `http://localhost/generaz-berbakti/gempa-history.php`

Fitur:
- ✅ Lihat semua history gempa Lampung
- ✅ Filter berdasarkan tanggal
- ✅ Filter berdasarkan magnitude minimal
- ✅ Statistik lengkap
- ✅ Pagination (50 data per halaman)
- ✅ Export ke CSV

### 2. Melalui API

#### Get History
```
GET /api/get-gempa-history.php
```

**Parameters:**
- `limit` - Jumlah data (default: 100, max: 1000)
- `offset` - Offset untuk pagination
- `min_magnitude` - Magnitude minimal (contoh: 5.0)
- `lampung_only` - true/false (filter Lampung saja)
- `start_date` - Format: YYYY-MM-DD
- `end_date` - Format: YYYY-MM-DD

**Contoh Request:**
```
/api/get-gempa-history.php?limit=20&min_magnitude=5.0&lampung_only=true
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 150,
    "limit": 20,
    "offset": 0,
    "pages": 8
  },
  "statistics": {
    "total_earthquakes": 150,
    "max_magnitude": 6.5,
    "avg_magnitude": 5.2,
    "lampung_earthquakes": 45,
    "nearby_earthquakes": 105
  }
}
```

## 🔧 API Endpoints

### 1. Save Gempa
```
POST /api/save-gempa-lampung.php
Content-Type: application/json
```

**Request Body:**
```json
{
  "Tanggal": "7 Nov 2025",
  "Jam": "15:02:26 WIB",
  "Coordinates": "5.11 LS - 105.23 BT",
  "Magnitude": "5.5",
  "Kedalaman": "17 km",
  "Wilayah": "Lampung Barat",
  "Potensi": "Tidak berpotensi tsunami",
  "Shakemap": "20251107150226.mmi.jpg"
}
```

### 2. Get History
```
GET /api/get-gempa-history.php?limit=50&lampung_only=true
```

## 📱 Console Logs

Saat halaman `bmkg.php` dimuat, akan muncul log di browser console:

```
✅ Gempa saved: 5.5 SR - Lampung Barat
⏭️ Gempa already exists: Lampung Selatan
✅ Gempa saved: 6.0 SR - 45 km Timur Laut Lampung
```

## 🎯 Fitur Utama

1. **Auto-Save Real-time** - Data tersimpan otomatis tanpa perlu klik apapun
2. **Duplikasi Prevention** - Tidak akan menyimpan data yang sama 2x
3. **Distance Calculation** - Hitung jarak otomatis dari Lampung
4. **Rich Statistics** - Statistik lengkap gempa Lampung
5. **Export CSV** - Export data untuk analisis eksternal
6. **Advanced Filtering** - Filter berdasarkan tanggal, magnitude, dll

## 🔍 Monitoring

### Check Console
Buka Developer Tools (F12) → Console untuk melihat:
- Data gempa yang disimpan
- Error (jika ada)
- Status penyimpanan

### Check Database
```sql
-- Lihat 10 gempa terbaru
SELECT * FROM gempa_lampung_history 
ORDER BY datetime DESC 
LIMIT 10;

-- Total gempa di Lampung
SELECT COUNT(*) FROM gempa_lampung_history 
WHERE is_lampung = 1;

-- Gempa terbesar
SELECT * FROM gempa_lampung_history 
ORDER BY magnitude DESC 
LIMIT 1;
```

## 🛠️ Troubleshooting

### Data tidak tersimpan
1. Cek console browser untuk error
2. Pastikan tabel `gempa_lampung_history` sudah dibuat
3. Cek database connection di `config.php`
4. Cek log file di `logs/app.log`

### Database error
1. Jalankan SQL file untuk membuat tabel
2. Cek kredensial database di `config.php`
3. Pastikan MySQL service running

### Duplikat data
- Sistem akan otomatis skip duplikat
- Duplikat dideteksi dari: tanggal + jam + koordinat

## 📝 Notes

- Data otomatis tersimpan setiap kali halaman bmkg.php dimuat/refresh
- History mencakup gempa di Lampung dan dalam radius 300 km
- Auto-refresh setiap 5 menit akan terus menyimpan data baru
- Data lama tetap tersimpan, tidak akan terhapus

## 🎉 Keunggulan

✅ **Lengkap** - Menyimpan semua gempa Lampung, bukan hanya 15 terbaru
✅ **Otomatis** - Tidak perlu input manual
✅ **Cerdas** - Mencegah duplikasi data
✅ **Informatif** - Statistik dan analisis lengkap
✅ **Exportable** - Data bisa di-export untuk analisis lanjutan
