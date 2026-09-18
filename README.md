# Foydalanuvchi loyihasi

Foydalanuvchilarni boshqarish tizimi — **Symfony 7.4**, **API Platform 4** va **SQLite** (konfiguratsiyasiz maʼlumotlar bazasi) bilan qurilgan.

## Taʼrif

Bu loyiha foydalanuvchilarni roʻyxatdan oʻtkazish, tahrirlash, oʻchirish, qidirish va filtrlash uchun toʻliq veb interfeys va REST API taqdim etadi. Loyiha tayyor demo maʼlumotlar bilan keladi va bir buyruq bilan ishga tushiriladi.

## Texnologiyalar

```
Frontend:   Twig shablonlar + Tailwind CSS CDN + FontAwesome 6
Backend:    PHP 8.2+ / Symfony 7.4
API:        API Platform 4 (REST + Swagger UI)
Database:   SQLite (standart, konfiguratsiyasiz) / PostgreSQL / MySQL
ORM:        Doctrine ORM 3.6 + Doctrine Migrations
Auth:       Session (web) + JWT (LexikJWTAuthenticationBundle) (API uchun)
Auth UI:    Login / Logout (Symfony Security)
Ishga tushirish: Node.js 18+ skriptlar orqali (yoki toʻgʻridan-toʻgʻri PHP server)
```

## Talablar

- **PHP 8.2+** — kengaytmalar bilan: `pdo_sqlite`, `openssl`, `json`, `mbstring`, `xml`
- **Composer** — PHP bogʻliqliklarni boshqarish
- **Node.js 18+** va **npm** — (ixtiyoriy, ammo setup va dev skriptlari uchun tavsiya etiladi)

## Tez ishga tushirish

Loyihani ishga tushirishning eng oddiy usuli:

```bash
git clone https://github.com/Shuxrat-dat/Foydalanuvchi-loyihasi.git
cd Foydalanuvchi-loyihasi

# 1. PHP bogʻliqliklarni oʻrnating
composer install

# 2. Node.js bogʻliqliklarni oʻrnating (faqat npm skriptlari uchun, hech qanday paket yuklanmaydi)
npm install

# 3. Ishlab chiqish serverini ishga tushiring
#    Avtomatik ravishda: JWT kalitlari yaratiladi, sxema yangilanadi, demo maʼlumotlar yuklanadi
npm run dev
```

Brauzeringizda oching: **http://127.0.0.1:8000**

Node.js siz ham ishlatishingiz mumkin (faqat PHP bilan):

```bash
composer install
composer run setup
composer run devserver
```

## Demo kirish maʼlumotlari

| Rol          | Email                   | Parol       |
|--------------|-------------------------|-------------|
| Admin        | admin@example.com       | admin123    |
| Menejjer     | manager@example.com     | manager123  |
| Foydalanuvchi| user@example.com        | user123     |

## Mavjud skriptlar

### npm skriptlari

| Buyruq                  | Tavsif                                                       |
|-------------------------|--------------------------------------------------------------|
| `npm run install:all`   | composer install + npm install (bitta joyda)                 |
| `npm run setup`         | JWT kalitlari, DB sxemasi va demo maʼlumotlarni tayyorlash   |
| `npm run dev`           | Setup + ishlab chiqish serverini ishga tushirish             |
| `npm run start`         | `npm run dev` bilan bir xil                                  |
| `npm run build`         | Symfony keshlashni ishga tushirish (cache:warmup)            |
| `npm run db:migrate`   | Doctrine sxemasini yangilash (`doctrine:schema:update`)      |
| `npm run db:seed`      | Demo maʼlumotlarni yuklash (`app:seed-demo-data`)            |

### composer skriptlari

| Buyruq                    | Tavsif                                                    |
|---------------------------|-----------------------------------------------------------|
| `composer run setup`      | Setup.js ishga tushirish                                  |
| `composer run dev`        | Start.js ishga tushirish                                  |
| `composer run devserver`  | PHP ichki serverini toʻgʻridan-toʻgʻri ishga tushirish    |
| `composer run db:migrate`| Doctrine sxemasini yangilash                              |
| `composer run db:seed`   | Demo maʼlumotlarni yuklash                                |

## Maʼlumotlar bazasi

Standart konfiguratsiya **SQLite** dan foydalanadi — qoʻshimcha sozlash kerak emas, DB fayli avtomatik ravishda `var/data.db` ga yaratiladi.

### Migrations va sxema yangilash

Doctrine Migrations oʻrnatilgan, ammo oddiy ishlab chiqish uchun `doctrine:schema:update` dan foydalaniladi:

```bash
npm run db:migrate
# yoki
php bin/console doctrine:schema:update --force
```

### Demo maʼlumotlarni yuklash

```bash
npm run db:seed
# yoki
php bin/console app:seed-demo-data
```

Mavjud foydalanuvchilarni oʻchirib, qayta yuklash uchun `--reset` flagidan foydalaning:

```bash
php bin/console app:seed-demo-data --reset
```

PostgreSQL yoki MySQL dan foydalanish uchun `.env.example` dan nusxa oling va `DATABASE_URL` ni oʻzgartiring:

```bash
cp .env.example .env.local
# .env.local da DATABASE_URL ni tahrirlang
```

## Asosiy URL lar

| URL                                    | Tavsif                              |
|----------------------------------------|-------------------------------------|
| `http://localhost:8000/`              | Veb paneli (Foydalanuvchilar)       |
| `http://localhost:8000/login`         | Kirish sahifasi                     |
| `http://localhost:8000/api`           | Swagger UI / API Platform hujjatlari|
| `http://localhost:8000/api/users`     | REST API — foydalanuvchilar endpointi |
| `http://localhost:8000/api/login_check` | JWT token olish endpointi        |

## Atrof-muhit oʻzgaruvchilari

`.env` faylida quyidagi oʻzgaruvchilar mavjud:

| Oʻzgaruvchi        | Majburiy | Tavsif                                                                 |
|--------------------|----------|------------------------------------------------------------------------|
| `APP_ENV`          | **Ha**   | `dev` (ishlab chiqish), `prod` (ishlatishga chiqarish)                 |
| `APP_SECRET`       | **Ha**   | Symfony xavfsizlik kaliti (istalmagan xalqaro kirish uchun ishlatiladi)|
| `APP_SHARE_DIR`    | Yoʻq     | Ulashilgan fayllar katalogi (standart: `var/share`)                   |
| `DEFAULT_URI`      | Yoʻq     | CLI buyruqlari uchun URL generatsiyalash (standart: `http://localhost`)|
| `DATABASE_URL`     | **Ha**   | Doctrine ulanish URLi (standart: SQLite)                               |
| `CORS_ALLOW_ORIGIN`| Yoʻq     | API uchun CORS ruxsat etilgan domenlar regex                           |
| `JWT_SECRET_KEY`   | **Ha**   | JWT maxfiy kalit fayl yoʻli (`config/jwt/private.pem`)                |
| `JWT_PUBLIC_KEY`   | **Ha**   | JWT ochiq kalit fayl yoʻli (`config/jwt/public.pem`)                  |
| `JWT_PASSPHRASE`   | Yoʻq     | JWT maxfiy kalit parol-frazasi (setup.js avtomatik yaratadi)          |

## Ishlatishga chiqarish (Production)

Keshni tayyorlash va ilovani production rejimiga oʻtkazish:

```bash
export APP_ENV=prod
export APP_SECRET="your-production-secret"
composer install --no-dev --optimize-autoloader
npm run build
```

Apache/Nginx uchun hujjatlar qatori `public/` katalogiga ishora qilishi kerak.

## Muammolarni bartaraf etish

### PHP topilmadi
Buyruq qatorida `php -v` ishga tushadiganligini tekshiring. PHP ikki nusxadur — `PATH` ga qoʻshilganiga ishonch hosil qiling.

### Composer topilmadi
Composer oʻrnatilganligini va `composer --version` ishlaganini tekshiring.

### Node.js topilmadi
`npm run dev` dan foydalanish uchun Node.js 18+ kerak. Node.js siz `composer run devserver` dan foydalaning (avval `composer run setup` qiling).

### Port 8000 band
Boshqa portda ishga tushiring:
```bash
PORT=8080 npm run dev
# yoki
php -S 127.0.0.1:8080 -t public
```

### JWT kalitlari yaratilmadi
`config/jwt/` katalogi mavjudligini va yozish huquqiga ega ekanligini tekshiring. Yoki qoʻlda yarating:
```bash
npm run setup
```

### Doctrine sxemasi yangilanmadi
`var/` katalogi yozish mumkinligini va `DATABASE_URL` toʻgʻri ekanligini tekshiring.
