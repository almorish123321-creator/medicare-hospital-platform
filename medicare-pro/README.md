<div align="center">

# 🏥 Medicare Pro

**نظام إدارة العيادات والمستشفيات المتكامل**  
*A Comprehensive Clinic & Hospital Management System*

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D?logo=redis)](https://redis.io)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

---

## 📖 Overview / نظرة عامة

**Medicare Pro** is a full-featured, Arabic-first hospital and clinic management API built with Laravel 11. It provides a robust RESTful backend with role-based access control, real-time WebSocket events, multi-language support (Arabic & English), and payment gateway integrations.

> **نظام ميديكير برو** هو نظام شامل لإدارة العيادات والمستشفيات مبني بإطار Laravel 11. يوفر واجهة برمجة تطبيقات RESTful قوية مع تحكم بالصلاحيات، أحداث WebSocket في الوقت الفعلي، دعم متعدد اللغات (العربية والإنجليزية)، وتكامل مع بوابات الدفع.

---

## ✨ Features / المميزات

### Core / الأساسية
- 🩺 **Appointment Management** – Create, update, cancel, and track appointments
- 🗂️ **Patient Records** – Comprehensive patient data with medical history
- 💊 **Prescription Management** – Digital prescriptions with medication tracking
- 🧾 **Medical Records** – Full medical history with lab results and diagnoses
- 💰 **Payment Processing** – Multi-gateway support (Stripe, Paymob)
- 📋 **Queue System** – Automated daily queue management with ticket numbers

### Technical / التقنية
- 🔐 **Authentication** – Laravel Sanctum token-based auth with OTP verification
- 🛡️ **Role-Based Access Control** – Granular permissions for Super Admin, Admin, Doctor, Nurse, Pharmacist, Receptionist, Patient
- 🏥 **Multi-Hospital Support** – Subscription plans and hospital-level data isolation
- 📡 **Real-Time WebSocket** – Soketi (Laravel Echo) for live notifications and queue updates
- 🌍 **Multi-Language** – Arabic (default) & English with translation management
- 📱 **Push Notifications** – Firebase Cloud Messaging (FCM) for mobile apps
- 💬 **SMS & WhatsApp** – Twilio integration for appointment reminders
- 📄 **PDF Generation** – Reports, invoices, and medical documents
- 📊 **Swagger API Docs** – Auto-generated interactive API documentation
- 🎯 **Repository Pattern** – Clean, maintainable, testable architecture

---

## 🛠 Tech Stack / تقنيات المشروع

| Component        | Technology                           |
|------------------|--------------------------------------|
| **Framework**    | Laravel 11                           |
| **Language**     | PHP 8.3                              |
| **Database**     | MySQL 8.0 (utf8mb4)                  |
| **Cache/Queue**  | Redis 7                              |
| **Auth**         | Laravel Sanctum                      |
| **WebSocket**    | Soketi (Laravel Echo compatible)     |
| **API Docs**     | L5-Swagger (OpenAPI 3.0)             |
| **Payments**     | Stripe, Paymob                       |
| **SMS/WhatsApp** | Twilio                               |
| **Push**         | Firebase Cloud Messaging             |
| **Testing**      | Pest PHP                             |
| **Containerization** | Docker, Docker Compose            |

---

## 📋 Requirements / المتطلبات

### Docker (Recommended)
- Docker 20.10+
- Docker Compose 2.0+

### Manual Installation
- PHP >= 8.3 with extensions: `pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `zip`, `redis`
- Composer 2.x
- MySQL >= 8.0
- Redis >= 7.0
- Node.js >= 18.x (optional, for frontend builds)

---

## 🚀 Installation / التثبيت

### Option A: Docker (Recommended) / باستخدام Docker

```bash
# 1. Clone the repository
git clone https://github.com/IslamTaleb11/klinik-laravel-api.git
cd klinik-laravel-api

# 2. Copy environment file
cp .env.example .env

# 3. Build and start all containers
docker-compose up -d --build

# 4. Generate application key (first time only)
docker-compose exec app php artisan key:generate
```

The application will be available at **http://localhost**

The entrypoint script automatically:
- Waits for MySQL to be ready
- Runs database migrations
- Seeds the database (local environment only)
- Generates Swagger documentation
- Clears and caches configs

### Option B: Manual / التثبيت اليدوي

```bash
# 1. Clone and enter directory
git clone https://github.com/IslamTaleb11/klinik-laravel-api.git
cd klinik-laravel-api

# 2. Install dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Configure your .env (database, redis, etc.)
#    Edit .env and set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Run migrations
php artisan migrate

# 6. Seed the database (optional)
php artisan db:seed --env=local

# 7. Generate Swagger docs
php artisan l5-swagger:generate

# 8. Start the development server
php artisan serve
```

---

## 📡 API Documentation / توثيق API

Once running, the interactive Swagger API documentation is available at:

```
http://localhost/api/documentation
```

---

## ⚙️ .env Configuration / إعدادات البيئة

Key environment variables to configure (see `.env.example` for the full list):

| Section | Variable | Description |
|---------|----------|-------------|
| **App** | `APP_TIMEZONE` | Set to `Africa/Cairo` for Egypt |
| **App** | `APP_LOCALE` | Set to `ar` for Arabic default |
| **DB** | `DB_HOST` | `mysql` (Docker) or `127.0.0.1` (local) |
| **Redis** | `REDIS_HOST` | `redis` (Docker) or `127.0.0.1` (local) |
| **Broadcast** | `BROADCAST_DRIVER` | `soketi` for WebSocket events |
| **Queue** | `QUEUE_CONNECTION` | `redis` for background jobs |
| **Twilio** | `TWILIO_ACCOUNT_SID` | Required for SMS & WhatsApp |
| **FCM** | `FCM_SERVER_KEY` | Required for push notifications |
| **Stripe** | `STRIPE_KEY` | Required for Stripe payments |
| **Paymob** | `PAYMOB_API_KEY` | Required for Paymob payments |

---

## 🐳 Docker Commands / أوامر Docker

```bash
# Start all services
docker-compose up -d

# Rebuild after code changes
docker-compose up -d --build

# View logs
docker-compose logs -f app
docker-compose logs -f nginx

# Run artisan commands
docker-compose exec app php artisan <command>

# Run Composer
docker-compose exec app composer <command>

# Stop all services
docker-compose down

# Stop and remove volumes (WARNING: deletes database data)
docker-compose down -v

# Restart a single service
docker-compose restart app

# Access MySQL shell
docker-compose exec mysql mysql -u medicare -p
```

### Available Ports / المنافذ المتاحة

| Service       | Port  | URL                      |
|---------------|-------|--------------------------|
| **App (Nginx)** | 80  | http://localhost          |
| **phpMyAdmin** | 8080 | http://localhost:8080     |
| **Soketi WS**  | 6001 | ws://localhost:6001       |
| **Soketi Metrics** | 9601 | http://localhost:9601 |
| **MySQL**      | 3306 | localhost:3306            |
| **Redis**      | 6379 | localhost:6379            |

---

## 🧪 Testing / الاختبارات

```bash
# Run all tests (Docker)
docker-compose exec app php artisan test

# Run all tests (manual)
php artisan test

# Run specific test file
php artisan test --filter=AuthenticationTest

# Run with coverage
php artisan test --coverage
```

---

## 📋 Queue Management / إدارة الطوابير

```bash
# Start queue worker (Docker runs this automatically)
docker-compose exec app php artisan queue:work redis --sleep=3 --tries=3

# Start queue worker (manual)
php artisan queue:work redis --sleep=3 --tries=3

# Failed jobs
docker-compose exec app php artisan queue:failed
docker-compose exec app php artisan queue:retry all

# Restart queue worker after code changes
docker-compose restart queue-worker
```

---

## 🔌 WebSocket Setup / إعداد WebSocket

Medicare Pro uses [Soketi](https://soketi.app/) as a Pusher-compatible WebSocket server.

### Docker (Automatic)
Soketi is included in `docker-compose.yml` and starts automatically. The Nginx config proxies `/app-events` to Soketi.

### Frontend Integration

```javascript
import Echo from 'laravel-echo';

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: 'medicare-key',
  wsHost: window.location.hostname,
  wsPort: 80,
  wssPort: 443,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
  disabledTransports: ['sockjs', 'polling', 'jsonp'],
});

// Listen to events
window.Echo.channel('appointments')
  .listen('.AppointmentCreated', (e) => {
    console.log('New appointment:', e.appointment);
  });

window.Echo.channel('queue')
  .listen('.QueueUpdated', (e) => {
    console.log('Queue updated:', e.queueLog);
  });
```

---

## 🏗 Architecture / البنية البرمجية

```
app/
├── Http/
│   ├── Controllers/Api/     # Organized by role
│   │   ├── Admin/
│   │   ├── Doctor/
│   │   ├── Nurse/
│   │   ├── Patient/
│   │   ├── Pharmacist/
│   │   ├── Receptionist/
│   │   └── SuperAdmin/
│   ├── Middleware/           # Role & access checks
│   ├── Requests/             # Organized by role
│   └── Resources/            # API transformers
├── Models/                   # Eloquent models
├── Repositories/             # Repository pattern
├── Services/                 # Business logic
├── Jobs/                     # Queued jobs
├── Events/                   # Broadcast events
└── Listeners/                # Event handlers
docker/
├── nginx/default.conf
├── php/local.ini
├── mysql/my.cnf
└── soketi/config.json
```

---

## 📄 License / الرخصة

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 🌟 Demo / عرض توضيحي

### Demo Video
[Watch on YouTube](https://www.youtube.com/watch?v=2gbiS_S7kPQ)

### Frontend Repository
[Vue.js Frontend](https://github.com/IslamTaleb11/klinik-vuejs)
