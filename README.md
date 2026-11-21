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

## Hostinger Deployment Guide

### Pre-requisites on Hostinger Business Plan
- PHP 8.2+ enabled (set via hPanel)
- MySQL database created in hPanel
- Node.js available (or build assets locally)

### Step 1 — Connect GitHub to Hostinger
1. In hPanel → **Git** section → Add Repository
2. Enter your GitHub repo URL
3. Set branch: main
4. Set web root: public_html/kalpkrut-pathshala/public

### Step 2 — SSH into Hostinger
`ash
ssh u123456789@your-hostinger-server.com
cd ~/public_html/kalpkrut-pathshala
`

### Step 3 — Install dependencies on server
`ash
composer install --optimize-autoloader --no-dev
npm install && npm run build
`

### Step 4 — Environment setup on server
`ash
cp .env.example .env
# Edit .env with Hostinger DB credentials
nano .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
`

### Step 5 — Set document root
In hPanel → Domains → Point domain to: public_html/kalpkrut-pathshala/public

---

## Brand & Customization

- **Brand:** Kalpkrut Infotech
- **Product:** Kalpkrut Pathshala ERP
- **Color:** Indigo/Brand (#4f46e5)
- **Logo:** /public/images/logo.png (replace with your logo)
- **Institute Name:** Configurable via Admin → Settings

---

## Android / Mobile App

The web ERP is fully responsive. For native Android APK:
1. Use WebView wrapper or PWA approach
2. Or use Capacitor.js to wrap the web app as APK
3. See /mobile-app/ folder for Android project (coming soon)

---

## License & Ownership

**Kalpkrut Infotech Pvt Ltd** — All Rights Reserved.
This product is proprietary software. Do not redistribute without written permission from Kalpkrut Infotech.

© 2026 Kalpkrut Infotech Pvt Ltd, Ahmedabad, Gujarat, India.
