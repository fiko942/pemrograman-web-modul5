# LAPORAN CODELAB MODUL 6 (Middleware & File Storage)

## Ringkasan Perubahan
- Middleware `api.logger` untuk semua route API yang menulis log request (method, path, status, durasi, request_id) ke `storage/logs/api.log` dan menambahkan header `X-Request-ID` di setiap respons.
- Todo kini mendukung lampiran file: kolom `attachment_path`, upload saat create/update (`attachment` pada multipart form-data), opsi hapus lampiran (`remove_attachment`), serta endpoint download khusus.
- Respons JSON menambahkan properti `attachment_url` yang dibangun otomatis dari disk `public`, dan file otomatis dibersihkan saat todo dihapus atau lampiran diganti.

## Persiapan & Cara Menjalankan
1. Salin `.env.example` lalu isi koneksi database.
2. Install dependensi: `composer install`.
3. Jalankan migrasi (termasuk kolom lampiran baru): `php artisan migrate`.
4. Aktifkan akses publik ke storage: `php artisan storage:link`.
5. Jalankan server: `php artisan serve --port=8020` lalu akses API di `http://127.0.0.1:8020/api`.

## Endpoint Utama
- `GET /api/todos` — pagination + filter `search`, `status`, `category`, `priority`, `limit`. Respons menyertakan `attachment_url` bila ada.
- `POST /api/todos` — buat todo. Gunakan `multipart/form-data` bila menyertakan `attachment` (jpg, png, pdf, txt, max 2MB).
- `PATCH /api/todos/{id}` — ubah data. Sertakan `attachment` untuk mengganti file atau `remove_attachment=true` untuk menghapus lampiran lama.
- `GET /api/todos/{id}/attachment` — unduh lampiran milik todo terkait (404 jika tidak ada).
- `DELETE /api/todos/{id}` — hapus todo dan lampiran yang terkait.

## JWT Authentication
- Install paket: `composer require php-open-source-saver/jwt-auth` (butuh PHP >= 8.2).
- Publish konfigurasi: `php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"`.
- Generate secret: `php artisan jwt:secret` lalu pastikan guard default di `config/auth.php` adalah `api` (driver `jwt`).
- Endpoint: `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me`, `POST /api/auth/logout`. Endpoint `me` dan `logout` wajib header `Authorization: Bearer <token>`.
- Guard bawaan diubah menjadi `api` sehingga helper `auth()` otomatis memakai JWT.

## Catatan Middleware Logging
- Middleware terdaftar sebagai alias `api.logger` dan sudah diterapkan ke seluruh route API.
- Setiap respons membawa header `X-Request-ID`; detail request tercatat di `storage/logs/api.log` untuk memudahkan trace/debugging.

## Pengujian
- Koleksi Postman tetap tersedia di `postman/todo-api.postman_collection.json`; tambah request download lampiran sesuai endpoint di atas bila diperlukan.
- Lampiran disimpan di `storage/app/public/attachments` dan diakses publik melalui symlink `public/storage`.
