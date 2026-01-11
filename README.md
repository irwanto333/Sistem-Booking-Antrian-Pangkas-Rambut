# 💈 Booking Pangkas Rambut API

API Backend untuk Sistem Booking & Antrian Pangkas Rambut.

## 📋 Deskripsi

Aplikasi ini adalah RESTful API untuk mengelola sistem booking dan antrian pada usaha pangkas rambut. Aplikasi mendukung dua jenis pengguna:

-   **Customer** - Dapat melakukan booking tanpa perlu login
-   **Admin** - Memerlukan autentikasi JWT untuk akses penuh

## ✨ Fitur

### Public (Customer)

-   ✅ Melihat daftar layanan
-   ✅ Melihat daftar tukang cukur
-   ✅ Melihat jadwal yang tersedia
-   ✅ Membuat booking baru
-   ✅ Cek status booking
-   ✅ Membatalkan booking
-   ✅ Melihat antrian hari ini

### Admin (Authenticated)

-   ✅ Dashboard dengan statistik
-   ✅ CRUD Layanan (Services)
-   ✅ CRUD Tukang Cukur
-   ✅ CRUD Jadwal (Schedules)
-   ✅ Manajemen Booking
-   ✅ Manajemen Antrian (Queue)
-   ✅ Laporan Harian, Mingguan, Bulanan
-   ✅ Activity Logs

## 🛠️ Tech Stack

-   **Framework:** Laravel 11
-   **Authentication:** JWT (tymon/jwt-auth)
-   **Testing:** Pest PHP
-   **Database:** MySQL

## 📦 Instalasi

### Prerequisites

-   PHP >= 8.2
-   Composer
-   MySQL
-   Laravel Herd (opsional, untuk local development)

### Langkah-langkah

1. **Clone repository**

    ```bash
    git clone https://github.com/irwanto333/Sistem-Booking-Antrian-Pangkas-Rambut.git
    cd Sistem-Booking-Antrian-Pangkas-Rambut
    ```

2. **Install dependencies**

    ```bash
    composer install
    ```

    Atau jika menggunakan Laravel Herd:

    ```bash
    herd composer install
    ```

3. **Copy environment file**

    ```bash
    cp .env.example .env
    ```

4. **Konfigurasi database di `.env`**

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=booking_pangkas_rambut
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. **Generate application key**

    ```bash
    php artisan key:generate
    ```

6. **Generate JWT secret**

    ```bash
    php artisan jwt:secret
    ```

7. **Jalankan migrasi dan seeder**

    ```bash
    php artisan migrate --seed
    ```

    atau jika ingin migras dan seed fresh

    ```bash
    php artisan migrate:fresh --seed
    ```

8. **Jalankan server** (jika tidak menggunakan Herd)
    ```bash
    php artisan serve
    ```

## 🔑 Default Credentials

```
Email: admin@pangkasrambut.com
Password: password123
```

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api
```

Atau jika menggunakan Laravel Herd:

```
http://booking_2301010144.test/api
```

---

### 🌐 Public Endpoints (No Auth Required)

#### Services

| Method | Endpoint    | Description             |
| ------ | ----------- | ----------------------- |
| GET    | `/services` | Get all active services |

#### Tukang Cukur

| Method | Endpoint         | Description                 |
| ------ | ---------------- | --------------------------- |
| GET    | `/tukang-cukurs` | Get all active tukang cukur |

#### Schedules

| Method | Endpoint               | Description             |
| ------ | ---------------------- | ----------------------- |
| GET    | `/schedules/available` | Get available schedules |

**Query Parameters:**

-   `date` (optional): Date in YYYY-MM-DD format (default: today)
-   `tukang_cukur_id` (optional): Filter by tukang cukur ID

#### Bookings

| Method | Endpoint                  | Description          |
| ------ | ------------------------- | -------------------- |
| POST   | `/bookings`               | Create new booking   |
| GET    | `/bookings/{code}/status` | Check booking status |
| POST   | `/bookings/{code}/cancel` | Cancel booking       |

**Create Booking Request Body:**

```json
{
    "customer_name": "John Doe",
    "customer_phone": "081234567890",
    "tukang_cukur_id": 1,
    "service_id": 1,
    "booking_date": "2025-12-23",
    "booking_time": "10:00",
    "notes": "Potong pendek"
}
```

**Cancel Booking Request Body:**

```json
{
    "customer_phone": "081234567890"
}
```

#### Queue

| Method | Endpoint       | Description       |
| ------ | -------------- | ----------------- |
| GET    | `/queue/today` | Get today's queue |

---

### 🔐 Admin Endpoints (Auth Required)

Semua endpoint admin memerlukan header:

```
Authorization: Bearer {token}
```

#### Authentication

| Method | Endpoint         | Description            |
| ------ | ---------------- | ---------------------- |
| POST   | `/admin/login`   | Login admin            |
| POST   | `/admin/logout`  | Logout admin           |
| POST   | `/admin/refresh` | Refresh token          |
| GET    | `/admin/me`      | Get current admin info |

**Login Request Body:**

```json
{
    "email": "admin@pangkasrambut.com",
    "password": "password123"
}
```

#### Dashboard

| Method | Endpoint           | Description              |
| ------ | ------------------ | ------------------------ |
| GET    | `/admin/dashboard` | Get dashboard statistics |

#### Services (CRUD)

| Method | Endpoint               | Description        |
| ------ | ---------------------- | ------------------ |
| GET    | `/admin/services`      | Get all services   |
| POST   | `/admin/services`      | Create service     |
| GET    | `/admin/services/{id}` | Get service detail |
| PUT    | `/admin/services/{id}` | Update service     |
| DELETE | `/admin/services/{id}` | Delete service     |

#### Tukang Cukur (CRUD)

| Method | Endpoint                    | Description             |
| ------ | --------------------------- | ----------------------- |
| GET    | `/admin/tukang-cukurs`      | Get all tukang cukur    |
| POST   | `/admin/tukang-cukurs`      | Create tukang cukur     |
| GET    | `/admin/tukang-cukurs/{id}` | Get tukang cukur detail |
| PUT    | `/admin/tukang-cukurs/{id}` | Update tukang cukur     |
| DELETE | `/admin/tukang-cukurs/{id}` | Delete tukang cukur     |

#### Schedules (CRUD)

| Method | Endpoint                | Description       |
| ------ | ----------------------- | ----------------- |
| GET    | `/admin/schedules`      | Get all schedules |
| POST   | `/admin/schedules`      | Create schedule   |
| PUT    | `/admin/schedules/{id}` | Update schedule   |
| DELETE | `/admin/schedules/{id}` | Delete schedule   |

#### Bookings Management

| Method | Endpoint                      | Description           |
| ------ | ----------------------------- | --------------------- |
| GET    | `/admin/bookings`             | Get all bookings      |
| GET    | `/admin/bookings/{id}`        | Get booking detail    |
| PUT    | `/admin/bookings/{id}/status` | Update booking status |
| DELETE | `/admin/bookings/{id}`        | Delete booking        |

**Query Parameters (GET /admin/bookings):**

-   `date`: Filter by date
-   `status`: Filter by status (pending, confirmed, in_progress, completed, cancelled)
-   `tukang_cukur_id`: Filter by tukang cukur

#### Queue Management

| Method | Endpoint             | Description       |
| ------ | -------------------- | ----------------- |
| GET    | `/admin/queue`       | Get current queue |
| POST   | `/admin/queue/next`  | Call next queue   |
| POST   | `/admin/queue/reset` | Reset daily queue |

#### Reports

| Method | Endpoint                 | Description    |
| ------ | ------------------------ | -------------- |
| GET    | `/admin/reports/daily`   | Daily report   |
| GET    | `/admin/reports/weekly`  | Weekly report  |
| GET    | `/admin/reports/monthly` | Monthly report |

**Query Parameters:**

-   `date`: For daily report (default: today)
-   `start_date`, `end_date`: For weekly report
-   `month`, `year`: For monthly report

#### Logs

| Method | Endpoint           | Description    |
| ------ | ------------------ | -------------- |
| GET    | `/admin/logs`      | Get all logs   |
| GET    | `/admin/logs/{id}` | Get log detail |
| DELETE | `/admin/logs/{id}` | Delete log     |
| DELETE | `/admin/logs`      | Clear old logs |

---

## 📊 Database Schema

### Tables

1. **admins** - Admin users
2. **services** - Available services (potong rambut, dll)
3. **tukang_cukurs** - Barbers/staff
4. **schedules** - Working schedules
5. **bookings** - Customer bookings
6. **settings** - Application settings
7. **logs** - Activity logs

### Booking Status

-   `pending` - Waiting for confirmation
-   `confirmed` - Confirmed by admin
-   `in_progress` - Currently being served
-   `completed` - Completed
-   `cancelled` - Cancelled

---

## 🧪 Testing

Jalankan test:

```bash
php artisan test
```

Atau dengan parallel:

```bash
php artisan test --parallel
```

Jalankan test coverage:

```bash
php artisan test --coverage
```

### Test Summary

-   **Total Tests:** 63
-   **Total Assertions:** 251
-   **Status:** ✅ All Passing

---

## 📁 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── BaseController.php
│           ├── ServiceController.php
│           ├── TukangCukurController.php
│           ├── ScheduleController.php
│           ├── BookingController.php
│           ├── QueueController.php
│           └── Admin/
│               ├── AuthController.php
│               ├── DashboardController.php
│               ├── ServiceController.php
│               ├── TukangCukurController.php
│               ├── ScheduleController.php
│               ├── BookingController.php
│               ├── QueueController.php
│               ├── ReportController.php
│               └── LogController.php
├── Models/
│   ├── Admin.php
│   ├── Service.php
│   ├── TukangCukur.php
│   ├── Schedule.php
│   ├── Booking.php
│   ├── Setting.php
│   └── Log.php
database/
├── factories/
├── migrations/
└── seeders/
routes/
└── api.php
tests/
└── Feature/
    └── Api/
        ├── PublicServiceTest.php
        ├── PublicTukangCukurTest.php
        ├── PublicScheduleTest.php
        ├── PublicBookingTest.php
        ├── PublicQueueTest.php
        └── Admin/
            ├── AuthTest.php
            ├── DashboardTest.php
            ├── ServiceTest.php
            ├── TukangCukurTest.php
            ├── ScheduleTest.php
            ├── BookingTest.php
            ├── QueueTest.php
            ├── ReportTest.php
            └── LogTest.php
```

---

## 📬 Postman Collection

File Postman Collection dan Environment tersedia di folder `docs/`:

-   `Booking_Pangkas_Rambut.postman_collection.json`
-   `Booking_Pangkas_Rambut.postman_environment.json`

### Import ke Postman:

1. Buka Postman
2. Click "Import"
3. Pilih file collection dan environment
4. Set environment variable `base_url`

---

## 🔧 Configuration

### JWT Configuration (config/jwt.php)

-   `ttl`: Token time to live (default: 60 minutes)
-   `refresh_ttl`: Refresh token time to live (default: 20160 minutes / 2 weeks)

### Auth Guard (config/auth.php)

```php
'guards' => [
    'admin' => [
        'driver' => 'jwt',
        'provider' => 'admins',
    ],
],

'providers' => [
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],
```

---

## 📝 Response Format

### Success Response

```json
{
    "success": true,
    "message": "Data berhasil diambil",
    "data": { ... }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
