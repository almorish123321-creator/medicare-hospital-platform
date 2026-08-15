# MediCare Pro - منصة المستشفى الطبية

نظام إدارة مستشفيات متكامل ثنائي اللغة (عربي/إنجليزي) مبني بتقنيات حديثة.

## المكونات

### 1. الموقع العام + لوحة الإدارة (Next.js 16)
- صفحة رئيسية مع Hero وخدمات طبية
- استعراض المستشفيات والأطباء
- حجز مواعيد إلكتروني
- لوحة تحكم إدارية كاملة
- ثنائي اللغة (عربي RTL / إنجليزي LTR)
- API كامل مع Prisma + SQLite

### 2. نظام المستشفى (Laravel 11 API)
- **7 أدوار**: super_admin, hospital_admin, doctor, receptionist, nurse, pharmacist, patient
- **18 جدول** في قاعدة البيانات
- **80+ Endpoint** API مع Versioning
- نظام طوابير ذكي D{dept_id}-{seq}
- إشعارات: FCM + SMS + Database
- دفع: كاش + Stripe + محفظة + تأمين
- Docker كامل (8 خدمات)

## متطلبات التشغيل

### الموقع العام (Next.js)
```bash
cd .
bun install
bun run db:push
bunx tsx scripts/seed.ts
bun run dev
```

### نظام المستشفى (Laravel)
```bash
cd medicare-pro
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Docker (Laravel)
```bash
cd medicare-pro
docker-compose up -d
```

## التقنيات

| المكون | التقنية |
|--------|---------|
| الموقع العام | Next.js 16, React 19, TypeScript |
| UI Components | shadcn/ui, Tailwind CSS 4 |
| قاعدة البيانات | SQLite (تطوير), MySQL 8 (إنتاج) |
| API Backend | Laravel 11, PHP 8.3 |
| Authentication | Sanctum |
| الحاويات | Docker, Nginx, Soketi |

## البيانات التجريبية

- **3 مستشفيات** (الرياض + جدة)
- **12 قسم** طبي
- **10 أطباء** متخصصون
- **8 مواعيد** نموذجية

## بيانات الدخول التجريبية

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| مدير النظام | admin@medicare.sa | admin123 |
| مدير المستشفى | hospital@medicare.sa | hospital123 |

## هيكل المشروع

```
├── src/app/              # Next.js App Router
│   ├── page.tsx          # الموقع العام + لوحة الإدارة
│   ├── api/              # API Routes
│   └── globals.css       # Styles
├── prisma/               # Database Schema
├── scripts/              # Seed Script
├── medicare-pro/         # Laravel Backend
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   ├── docker/
│   └── routes/api.php
└── package.json
```
