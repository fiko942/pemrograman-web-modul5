# LAPORAN CODELAB MODUL 5

## Cover
- **Nama**  : WIJI FIKO TEREN
- **NIM**   : 202310370311437
- **Kelas** : Pemrograman Mobile G

## Tujuan Praktikum
1. Menginstal dan menyiapkan proyek Laravel 12 berbasis API.
2. Melakukan konfigurasi database MySQL eksternal serta menjalankan migration.
3. Mengembangkan API Todo List lengkap dengan model, controller, routing, serta endpoint CRUD.
4. Menguji seluruh endpoint menggunakan Postman sesuai panduan modul.

## Tools dan Teknologi
- macOS (zsh)
- PHP 8.4.14 + Composer 2.9.2
- Laravel Installer 5.23
- Node.js 18.20.8 (untuk Vite/dev server)
- MySQL server eksternal (IP 103.150.190.87, DB `praktikum-web-modul5`)
- Postman (workspace & collection Todo API)

## Ringkasan Langkah Implementasi
1. **Persiapan lingkungan**
   - Verifikasi versi PHP, Composer, Node.js, dan Laravel installer.
   - Pastikan akses ke server MySQL eksternal yang disediakan.
2. **Pembuatan proyek Laravel**
   - Menjalankan `laravel new pemrograman-web --no-interaction` dan otomatis menjalankan migration default.
   - Membuka proyek di VS Code dan menginisialisasi `.env`.
3. **Konfigurasi database**
   - Mengubah `.env` menjadi koneksi MySQL remote (`praktikum-web-modul5`).
   - Menjalankan `php artisan migrate` untuk membuat tabel default pada server tersebut.
4. **Membangun schema Todo**
   - Membuat migration `create_todos_table` berisi kolom `title`, `description`, `status`, `due_date`, `priority`, `timestamps`, dan `softDeletes`.
   - Membuat migration alter `add_category_to_todos_table` dengan enum `personal/work/study/others`.
   - Menjalankan kembali `php artisan migrate` agar tabel Todo siap.
5. **Model dan Controller**
   - Generate `Todo` model dan `TodoController --api --model=Todo`.
   - Menambahkan `fillable`, `SoftDeletes`, dan cast di model.
   - Mengimplementasikan fungsi CRUD di controller lengkap dengan validasi serta filter pencarian sesuai screenshot modul.
6. **Routing API**
   - Menjalankan `php artisan install:api`, menambahkan `Route::apiResource('todos', TodoController::class);` pada `routes/api.php`.
   - Memastikan route terdaftar melalui `php artisan route:list`.
7. **Testing dengan Postman**
   - Membuat collection `postman/todo-api.postman_collection.json` berisi request POST (3 data contoh), GET all, GET by ID, PATCH, DELETE.
   - Mengimpor collection ke Postman, menjalankan setiap request pada `http://127.0.0.1:8020/api/todos` sambil menjalankan `php artisan serve --port=8020`.
8. **Verifikasi & Dokumentasi**
   - Memastikan data masuk ke database (termasuk efek soft delete) dan menyiapkan laporan ini beserta placeholder screenshot.

## Bukti / Screenshots
![Tampilan Laravel Berjalan](../screenshots/laravel-serve.png)

![Postman Create Todo](../screenshots/postman-create.png)

![Postman Get All Todos](../screenshots/postman-getall.png)

![Database Todos](../screenshots/database-todos.png)

## Kesimpulan
Seluruh langkah Codelab Modul 5 telah terlaksana: proyek Laravel berhasil dibuat, terhubung dengan database MySQL eksternal, dan disertai API Todo CRUD lengkap beserta koleksi Postman untuk pengujian. Konfigurasi, kode, serta dokumentasi siap ditunjukkan kepada asisten pada pekan materi/demonstrasi.
