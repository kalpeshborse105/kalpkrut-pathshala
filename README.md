# Kalpkrut Pathshala — Coaching Institute Management ERP
### By Kalpkrut Infotech | Enterprise Multi-Branch Coaching & Learning Management System
#### Built with Laravel 11 · PHP 8.4 · MySQL 8 · Tailwind CSS · Alpine.js · Chart.js

---

## About Kalpkrut Pathshala

**Kalpkrut Pathshala** is a full-featured ERP system built by **Kalpkrut Infotech** for coaching institutes, 
tuition centers, test-prep academies (IIT-JEE / NEET / GPSC / SSC), IT training centers, and vocational academies.

This product is owned and operated by **Kalpkrut Infotech Pvt Ltd**, Ahmedabad, Gujarat, India.

---

## Workflow

`
Lead Inquiry → Follow-up → Counselling → Admission → Batch → Installment Dues
→ Fee Collection → Attendance → LMS & Assignments → Exams → Report Cards → QR Certificates
`

---

## Default Login Credentials (Demo Seed Data)

| Role                | Email                          | Password     |
|---------------------|--------------------------------|--------------|
| Super Administrator | admin@coaching.com             | password123  |
| Branch Manager      | manager.central@pathshala.test | password123  |
| Counsellor          | counsellor1@pathshala.test     | password123  |
| Accountant          | accountant@pathshala.test      | password123  |
| Faculty             | faculty1@pathshala.test        | password123  |
| Student             | student@pathshala.test         | password123  |

> Change all passwords after first login in production!

---

## Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 8.4 · Laravel 11              |
| Database   | MySQL 8                           |
| Frontend   | Tailwind CSS · Alpine.js · Vite   |
| Charts     | Chart.js                          |
| Auth       | Laravel built-in + RBAC           |

---

## Local Development Setup (Windows / XAMPP)

### Prerequisites
- PHP 8.2+ (8.4 recommended)
- Composer (latest)
- Node.js 18+ & NPM
- MySQL 8.0+
- Git

### Step 1 — Clone the repository
`ash
git clone https://github.com/YOUR_USERNAME/kalpkrut-pathshala.git
cd kalpkrut-pathshala
`

### Step 2 — Install PHP dependencies
`ash
composer install
`

### Step 3 — Environment setup
`ash
cp .env.example .env
php artisan key:generate
`

### Step 4 — Configure database in .env
`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kalpkrut_pathshala
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
`

### Step 5 — Create database & migrate
`ash
# Create database in MySQL first:
mysql -u root -p -e "CREATE DATABASE kalpkrut_pathshala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations and seed demo data:
php artisan migrate --seed
`

### Step 6 — Storage & Frontend
`ash
php artisan storage:link
npm install
npm run build
`

### Step 7 — Start local server
`ash
php artisan serve
`
Visit: http://localhost:8000

---

## Hostinger Deployment Guide (Git Auto-Deploy)

Live site: https://pathshala.kalpkrutinfotech.com

### One-time Hostinger setup
1. PHP Configuration → remove `proc_open` and `symlink` from `disableFunctions`
2. hPanel → **Git** → connect `https://github.com/kalpeshborse105/kalpkrut-pathshala.git`
3. Branch: `master`
4. Keep **Install Composer dependencies** enabled
5. Website files must live at domain root (Laravel root), with `public_html` → `public`
6. Production `.env` is restored automatically from `~/pathshala.production.env` by `scripts/hostinger-bootstrap.php` after every `composer install`

### Every push (auto)
Push to `master` (or click **Deploy** in hPanel Git). Hostinger will:
1. Clone the public repo
2. Run `composer install`
3. Run `scripts/hostinger-bootstrap.php` (migrate, storage link, caches, public_html symlink)

### Manual fallback (SSH)
```bash
ssh -p 65002 u796881501@YOUR_SSH_IP
cd ~/domains/pathshala.kalpkrutinfotech.com
bash scripts/hostinger-post-deploy.sh
```

### Build assets
Build Vite assets locally before push (`npm run build`). `public/build` is committed so Hostinger does not need Node.

---

## Brand & Customization

- **Brand:** Kalpkrut Infotech
- **Product:** Kalpkrut Pathshala ERP
- **Color:** Indigo/Brand (#4f46e5)
- **Logo:** /public/images/logo.png (replace with your logo)
- **Institute Name:** Configurable via Admin → Settings

---

## Android / Mobile App

Native Android project is in `android-app/` (same live ERP URL).

```bat
cd android-app
gradlew.bat assembleDebug
gradlew.bat assembleRelease
gradlew.bat bundleRelease
```

Install/test the APK first, then upload the AAB to Google Play Console.

---

## License & Ownership

**Kalpkrut Infotech Pvt Ltd** — All Rights Reserved.
This product is proprietary software. Do not redistribute without written permission from Kalpkrut Infotech.

© 2026 Kalpkrut Infotech Pvt Ltd, Ahmedabad, Gujarat, India.
