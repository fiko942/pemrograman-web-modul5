# Campus Event API

Implementasi tugas Modul 5 Pemrograman Web yang meminta pembuatan database dan endpoint CRUD menggunakan Laravel API. Studi kasus yang dipilih adalah manajemen acara kampus dengan fitur pencarian dan pagination.

## Fitur utama
- Migrasi tabel `events` lengkap dengan soft delete dan enum status.
- Endpoint CRUD (`GET/POST/PATCH/DELETE`) dengan validator, auto slug, dan filter pencarian (limit, page, search, orderBy, sortBy) sesuai modul.
- Seeder contoh data sehingga API dapat langsung dicoba setelah migrasi.
- Koleksi Postman agar pengujian dapat mengikuti instruksi modul praktikum.

## Menjalankan proyek
1. **Instal dependensi**
   ```bash
   composer install
   ```
2. **Salin file konfigurasi** (opsional jika belum ada)
   ```bash
   cp .env.example .env
   ```
3. **Gunakan SQLite bawaan** (default), atau sesuaikan kredensial database lain pada `.env`.
4. **Generate key & jalankan migrasi + seeder**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
5. **Menjalankan server** sesuai modul
   ```bash
   php artisan serve --port=8020
   ```
   Akses API di `http://localhost:8020/api`.

> Catatan: gunakan `php artisan migrate:fresh --seed` bila ingin mengulang data contoh dari `Database\Seeders\EventSeeder`.

## Endpoint API
| Method | URI | Deskripsi |
| --- | --- | --- |
| `GET` | `/api/events` | Get All + pagination/query (limit, page, search, orderBy, sortBy, category, status)
| `GET` | `/api/events/{id}` | Get Detail berdasarkan ID
| `POST` | `/api/events` | Create event baru
| `PATCH` | `/api/events/{id}` | Update sebagian data menggunakan param ID
| `DELETE` | `/api/events/{id}` | Soft delete event berdasarkan ID

### Query string `GET /api/events`
- `limit` : jumlah data per halaman (1-50, default 10)
- `page` : nomor halaman
- `search` : cari di kolom judul/kota/kategori
- `orderBy` : salah satu dari `start_date`, `ticket_price`, `created_at`, `title`, `city`
- `sortBy` : `asc` atau `desc`
- `category` / `status` : filter tambahan

### Contoh body request
```jsonc
POST /api/events
{
  "title": "Mini Bootcamp Product Design",
  "category": "workshop",
  "description": "Latihan prototype aplikasi dengan mentor industri.",
  "location": "Ruang Inovasi Lt.3",
  "city": "Malang",
  "start_date": "2025-12-20 10:00:00",
  "capacity": 25,
  "available_seats": 22,
  "ticket_price": 10000,
  "status": "scheduled",
  "is_featured": true
}
```

```jsonc
PATCH /api/events/1
{
  "available_seats": 48,
  "status": "scheduled"
}
```

## Koleksi Postman
File `postman/event-api-collection.postman_collection.json` sudah menyertakan kelima request (Create, Get All, Get Detail, Update, Delete) sesuai panduan modul. Import file tersebut lalu ubah variable `base_url` jika tidak menjalankan server di `http://localhost:8000`.

## Pengujian
Gunakan perintah berikut untuk memastikan instalasi berjalan:
```bash
php artisan test
```

## Struktur penting
- `app/Http/Controllers/EventController.php` – seluruh logika CRUD beserta query pagination.
- `app/Models/Event.php` – model dengan `SoftDeletes` dan cast field.
- `database/migrations/*create_events_table.php` – definisi tabel utama.
- `database/seeders/EventSeeder.php` – data contoh untuk tiga event kampus.
- `postman/event-api-collection.postman_collection.json` – request HTTP untuk penilaian tugas.
