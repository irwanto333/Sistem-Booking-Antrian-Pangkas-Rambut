# 📋 DESKRIPSI PROYEK WEB SERVICE

---

## 1. Nama Proyek

**API Sistem Booking & Antrian Pangkas Rambut**

> RESTful API untuk manajemen booking, antrian, dan operasional usaha pangkas rambut dengan sistem admin terintegrasi.

---

## 2. Latar Belakang Masalah

Usaha pangkas rambut konvensional sering menghadapi berbagai permasalahan operasional yang menghambat efisiensi dan kepuasan pelanggan:

### Permasalahan yang Dihadapi:

| No  | Masalah                             | Dampak                                                   |
| --- | ----------------------------------- | -------------------------------------------------------- |
| 1   | **Antrian tidak teratur**           | Pelanggan harus menunggu lama tanpa kepastian waktu      |
| 2   | **Tidak ada sistem booking**        | Pelanggan datang sia-sia karena tempat penuh             |
| 3   | **Pencatatan manual**               | Sulit melacak riwayat pelanggan dan pendapatan           |
| 4   | **Jadwal tukang cukur tidak jelas** | Pelanggan tidak tahu kapan tukang cukur favorit tersedia |
| 5   | **Tidak ada laporan bisnis**        | Pemilik kesulitan menganalisis performa usaha            |

### Solusi yang Ditawarkan:

Membangun **Web Service (API)** yang dapat:

-   Mengelola booking secara online tanpa perlu login untuk customer
-   Mengatur antrian secara otomatis dan real-time
-   Menyediakan dashboard admin untuk manajemen operasional
-   Menghasilkan laporan bisnis harian, mingguan, dan bulanan
-   Mencatat semua aktivitas sistem (activity logs)

---

## 3. Tujuan Proyek

### Tujuan Utama:

Membangun **RESTful API** yang handal untuk sistem booking dan antrian pangkas rambut yang dapat diintegrasikan dengan berbagai platform (mobile app, web app, atau sistem lainnya).

### Manfaat API:

| Untuk Customer                          | Untuk Admin/Pemilik              |
| --------------------------------------- | -------------------------------- |
| ✅ Booking mudah tanpa perlu registrasi | ✅ Dashboard statistik real-time |
| ✅ Cek status antrian kapan saja        | ✅ Kelola jadwal tukang cukur    |
| ✅ Pilih tukang cukur favorit           | ✅ Laporan pendapatan otomatis   |
| ✅ Batalkan booking dengan mudah        | ✅ Manajemen layanan & harga     |
| ✅ Lihat estimasi waktu tunggu          | ✅ Activity logs untuk audit     |

---

## 4. Deskripsi Fitur Utama

### 🔓 A. Public Endpoints (Customer - Tanpa Autentikasi)

Customer dapat mengakses API tanpa perlu login/registrasi.

| Method | Endpoint                      | Deskripsi                         |
| ------ | ----------------------------- | --------------------------------- |
| `GET`  | `/api/services`               | Melihat daftar layanan & harga    |
| `GET`  | `/api/tukang-cukurs`          | Melihat daftar tukang cukur aktif |
| `GET`  | `/api/schedules/available`    | Melihat jadwal yang tersedia      |
| `POST` | `/api/bookings`               | Membuat booking baru              |
| `GET`  | `/api/bookings/{code}/status` | Cek status booking                |
| `POST` | `/api/bookings/{code}/cancel` | Batalkan booking                  |
| `GET`  | `/api/queue/today`            | Lihat antrian hari ini            |

### 🔐 B. Admin Endpoints (Memerlukan Autentikasi JWT)

#### B.1 Autentikasi

| Method | Endpoint            | Deskripsi                       |
| ------ | ------------------- | ------------------------------- |
| `POST` | `/api/admin/login`  | Login admin & mendapatkan token |
| `POST` | `/api/admin/logout` | Logout & invalidate token       |

#### B.2 Dashboard

| Method | Endpoint               | Deskripsi                                              |
| ------ | ---------------------- | ------------------------------------------------------ |
| `GET`  | `/api/admin/dashboard` | Data statistik (booking hari ini, pendapatan, antrian) |

#### B.3 Manajemen Layanan (Services)

| Method   | Endpoint                   | Deskripsi           |
| -------- | -------------------------- | ------------------- |
| `GET`    | `/api/admin/services`      | List semua layanan  |
| `POST`   | `/api/admin/services`      | Tambah layanan baru |
| `GET`    | `/api/admin/services/{id}` | Detail layanan      |
| `PUT`    | `/api/admin/services/{id}` | Update layanan      |
| `DELETE` | `/api/admin/services/{id}` | Hapus layanan       |

#### B.4 Manajemen Tukang Cukur

| Method   | Endpoint                        | Deskripsi                |
| -------- | ------------------------------- | ------------------------ |
| `GET`    | `/api/admin/tukang-cukurs`      | List semua tukang cukur  |
| `POST`   | `/api/admin/tukang-cukurs`      | Tambah tukang cukur baru |
| `GET`    | `/api/admin/tukang-cukurs/{id}` | Detail tukang cukur      |
| `PUT`    | `/api/admin/tukang-cukurs/{id}` | Update tukang cukur      |
| `DELETE` | `/api/admin/tukang-cukurs/{id}` | Hapus tukang cukur       |

#### B.5 Manajemen Jadwal

| Method   | Endpoint                    | Deskripsi          |
| -------- | --------------------------- | ------------------ |
| `GET`    | `/api/admin/schedules`      | List semua jadwal  |
| `POST`   | `/api/admin/schedules`      | Tambah jadwal baru |
| `PUT`    | `/api/admin/schedules/{id}` | Update jadwal      |
| `DELETE` | `/api/admin/schedules/{id}` | Hapus jadwal       |

#### B.6 Manajemen Booking

| Method   | Endpoint                          | Deskripsi                          |
| -------- | --------------------------------- | ---------------------------------- |
| `GET`    | `/api/admin/bookings`             | List semua booking (dengan filter) |
| `GET`    | `/api/admin/bookings/{id}`        | Detail booking                     |
| `PUT`    | `/api/admin/bookings/{id}/status` | Update status booking              |
| `DELETE` | `/api/admin/bookings/{id}`        | Hapus booking                      |

#### B.7 Manajemen Antrian

| Method | Endpoint                 | Deskripsi                  |
| ------ | ------------------------ | -------------------------- |
| `GET`  | `/api/admin/queue`       | Lihat antrian saat ini     |
| `POST` | `/api/admin/queue/next`  | Panggil antrian berikutnya |
| `POST` | `/api/admin/queue/reset` | Reset antrian harian       |

#### B.8 Laporan

| Method | Endpoint                     | Deskripsi        |
| ------ | ---------------------------- | ---------------- |
| `GET`  | `/api/admin/reports/daily`   | Laporan harian   |
| `GET`  | `/api/admin/reports/weekly`  | Laporan mingguan |
| `GET`  | `/api/admin/reports/monthly` | Laporan bulanan  |

#### B.9 Activity Logs

| Method   | Endpoint                | Deskripsi                |
| -------- | ----------------------- | ------------------------ |
| `GET`    | `/api/admin/logs`       | List semua activity logs |
| `GET`    | `/api/admin/logs/{id}`  | Detail log               |
| `DELETE` | `/api/admin/logs/{id}`  | Hapus log                |
| `DELETE` | `/api/admin/logs/clear` | Hapus log lama           |

---

## 5. Teknologi yang Digunakan

### Tech Stack:

| Kategori               | Teknologi            | Keterangan                                |
| ---------------------- | -------------------- | ----------------------------------------- |
| **Backend Framework**  | Laravel 11           | Framework PHP modern dengan fitur lengkap |
| **Bahasa Pemrograman** | PHP 8.2+             | Versi terbaru dengan performa optimal     |
| **Database**           | MySQL 8.0            | Relational database yang stabil           |
| **Autentikasi**        | JWT (JSON Web Token) | Token-based authentication untuk API      |
| **API Documentation**  | Postman Collection   | Dokumentasi & testing API                 |
| **Version Control**    | Git                  | Source code management                    |

### Arsitektur API:

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT APPS                             │
│         (Mobile App / Web App / Third Party)                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     RESTful API                              │
│                   (Laravel Backend)                          │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Public     │  │    Admin     │  │   Activity   │      │
│  │  Endpoints   │  │  Endpoints   │  │    Logs      │      │
│  │  (No Auth)   │  │ (JWT Auth)   │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      MySQL Database                          │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │ admins  │ │services │ │bookings │ │  logs   │          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│  ┌───────────────┐ ┌───────────┐ ┌─────────┐              │
│  │ tukang_cukurs │ │ schedules │ │settings │              │
│  └───────────────┘ └───────────┘ └─────────┘              │
└─────────────────────────────────────────────────────────────┘
```

### Database Schema (7 Tabel):

1. **admins** - Data administrator sistem
2. **services** - Daftar layanan & harga
3. **tukang_cukurs** - Data tukang cukur
4. **schedules** - Jadwal operasional
5. **bookings** - Data booking customer
6. **settings** - Pengaturan sistem
7. **logs** - Activity logs untuk audit trail

---

## 6. Peran Anggota Kelompok

### 👨‍💻 Anggota Tim:

| Nama                               | NIM        | Peran             |
| ---------------------------------- | ---------- | ----------------- |
| **Irwanto**                        | 2301010164 | Backend Developer |
| **I Putu Maha Ditya Jeris Atmaja** | 2301010169 | Backend Developer |

### 📋 Pembagian Tugas:

| No  | Tugas                    | Keterangan                                     | PIC                            |
| --- | ------------------------ | ---------------------------------------------- | ------------------------------ |
| 1   | **Perancangan Database** | Desain ERD, migrasi, dan seeder                | Irwanto                        |
| 2   | **Setup Project**        | Konfigurasi Laravel, JWT, dan environment      | Irwanto                        |
| 3   | **Public API Endpoints** | Services, Tukang Cukur, Booking, Queue         | I Putu Maha Ditya Jeris Atmaja |
| 4   | **Admin API Endpoints**  | Auth, Dashboard, CRUD semua entitas            | Irwanto                        |
| 5   | **Reports & Logs**       | Laporan harian/mingguan/bulanan, Activity logs | I Putu Maha Ditya Jeris Atmaja |
| 6   | **Dokumentasi API**      | Postman Collection & Environment               | Irwanto                        |

