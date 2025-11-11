# Generaz-Berbakti

Aplikasi Monitoring dan Peringatan Dini (Dashboard + API) untuk data sensor multi-node.

## Konfigurasi Lingkungan

Gunakan environment variables (direkomendasikan via konfigurasi server/web host) untuk kredensial dan pengaturan:

- DB_HOST: host database MySQL (default: localhost)
- DB_USER: user database
- DB_PASS: password database
- DB_NAME: nama database
- DEBUG_MODE: true/false (default: false)
- API_KEY: API key aplikasi (untuk keperluan internal)
- RECEIVER_API_KEY: API key untuk endpoint ingestion (WAJIB diganti di produksi)
- THRESHOLD_VIB_WARN: ambang peringatan getaran (default: 50000)
- THRESHOLD_VIB_DANGER: ambang bahaya getaran (default: 80000)
- THRESHOLD_MPU_WARN: ambang peringatan mpu6050 (default: 50000)
- THRESHOLD_MPU_DANGER: ambang bahaya mpu6050 (default: 80000)

Catatan: Nilai ambang di atas contoh saja—sesuaikan dengan karakteristik sensor real Anda.

## Endpoint Ingest Sensor

- URL: /api/ingest_sensor.php
- Method: POST
- Auth: Header `X-API-Key: <RECEIVER_API_KEY>`
- Content-Type: `application/json` (disarankan) atau `application/x-www-form-urlencoded`

Contoh JSON body:

{
	"nodeID": 1,
	"temperature": 28.5,
	"humidity": 60.2,
	"pressure": 1005.3,
	"accelX": 0.12,
	"accelY": -0.03,
	"accelZ": 9.81,
	"gyroX": 0.01,
	"gyroY": 0.00,
	"gyroZ": -0.02,
	"piezo1": 1200,
	"piezo2": 1180,
	"piezo3": 1210,
	"latitude": -5.55,
	"longitude": 105.33
}

Respons sukses:

{
	"status": "success",
	"node_id": 1,
	"inserted_at": "2025-01-01 10:00:00",
	"reading_status": "NORMAL|PERINGATAN|BAHAYA",
	"reading_status_class": "status-normal|status-warning|status-danger"
}

Field yang diterima (opsional kecuali nodeID):
- nodeID: integer (wajib)
- temperature, humidity, pressure: angka
- accelX, accelY, accelZ: angka (digunakan untuk hitung magnitudo mpu6050)
- gyroX, gyroY, gyroZ: angka (opsional)
- piezo1, piezo2, piezo3: integer (digunakan untuk hitung rata-rata getaran)
- latitude, longitude: angka (opsional)

## API Dashboard

API telah disederhanakan menjadi 3 endpoint aktif:

1. **POST** `/api/ingest_sensor.php` - Ingestion endpoint untuk sensor gateway
2. **GET** `/api/get-latest-data.php` - Status terbaru tiap node (digunakan dashboard)
3. **GET** `/api/get-node-data.php` - Data historis untuk chart/grafik

Dokumentasi lengkap tersedia di `/api/README.md`

Semua endpoint menggunakan fungsi `computeStatus(vibration, mpu)` untuk konsistensi evaluasi status.

## Keamanan

- Headers keamanan aktif: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy, Content-Security-Policy.
- CORS tidak dibuka lebar; dashboard bekerja same-origin. Endpoint ingest dipanggil langsung oleh gateway (bukan browser), sehingga tidak memerlukan CORS.
- Matikan DEBUG_MODE di produksi.
- Ganti RECEIVER_API_KEY bawaan ("changeme").

## Menjalankan Lokal

1. Siapkan database MySQL dan import skema tabel `sensor_data` (kolom umum: node_id, timestamp, vibration, mpu6050, temperature, humidity, pressure, latitude, longitude, accel_x/y/z, gyro_x/y/z, piezo_1/2/3, battery).
2. Set environment variables untuk DB_* minimal.
3. Jalankan aplikasi lewat server PHP/Apache (misal XAMPP) dan akses `http://localhost/Generaz-Berbakti/`.

## Arsitektur Front-end (CSS)

Stylesheet diatur dalam layer untuk memudahkan maintenance:

1. **core.css** - Token & utilitas bersama
   - Variabel warna, spacing, shadow, transition
   - Utility classes: status badges, alert banners, loading spinner, card dasar
   - Digunakan oleh semua halaman

2. **styles.css** - Landing page (`index.php`)
   - Hero section, features, gallery
   - Animasi dekoratif (dolphin float)
   - Layout marketing/front page

3. **bmkg.css** - Halaman BMKG (`bmkg.php`)
   - Magnitude display, shakemap, earthquake table
   - Tab & filter controls
   - Status classification styling

4. **monitoring.css** - Dashboard monitoring (`monitoring.php`)
   - Node grid & sensor cards
   - Chart containers & map
   - Alert panel & real-time indicators

**Penggunaan:**
- Setiap halaman me-load `core.css` terlebih dahulu, kemudian stylesheet page-specific-nya
- Contoh di `index.php`: `<link rel="stylesheet" href="assets/css/core.css">` → `<link rel="stylesheet" href="assets/css/styles.css">`
- Hindari inline `<style>` blocks; semua styling eksternal untuk konsistensi

## Lisensi

MIT
