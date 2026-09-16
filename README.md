# Foydalanuvchi loyihasi

User Management System — built with **Symfony 7.4**, **API Platform 4**, and **SQLite** (zero-config database).

## Requirements

- **PHP 8.2+** (with `pdo_sqlite`, `openssl`, `json`, `mbstring`)
- **Node.js 18+** and **npm** (for the one-command start)
- **Composer** (PHP dependency manager)

## Quick Start

```bash
git clone https://github.com/Shuxrat-dat/Foydalanuvchi-loyihasi.git
cd Foydalanuvchi-loyihasi

# Install PHP dependencies
composer install

# Install Node dependencies (none required, but registers npm scripts)
npm install

# Start the server — auto-initialises DB, migrations, and demo data
npm run dev
```

Open your browser: **http://127.0.0.1:8000**

## Environment

The project ships with `.env` pre-configured for SQLite — no extra setup needed.

For custom overrides (e.g. switching to PostgreSQL):

```bash
cp .env.example .env.local
# edit .env.local
```

## Demo Accounts

| Role    | Email                   | Password    |
|---------|-------------------------|-------------|
| Admin   | admin@example.com       | admin123    |
| Manager | manager@example.com     | manager123  |
| User    | user@example.com        | user123     |

## Available Scripts

| Command        | Description                                     |
|----------------|-------------------------------------------------|
| `npm run dev`  | Setup + start the development server            |
| `npm run build`| Warm up the Symfony cache                       |
| `npm run start`| Start the server (same as `npm run dev`)        |

## Key URLs

| URL                | Description                          |
|--------------------|--------------------------------------|
| `http://localhost:8000/`     | Web dashboard (User Management) |
| `http://localhost:8000/login`| Sign in page                    |
| `http://localhost:8000/api`  | Swagger UI / API Platform docs  |
| `http://localhost:8000/api/users` | REST API — users endpoint  |
| `http://localhost:8000/api/login_check` | JWT token endpoint   |

## Tech Stack

- **Symfony 7.4** — PHP framework
- **API Platform 4** — REST API + Swagger UI
- **LexikJWTAuthenticationBundle** — JWT authentication
- **Doctrine ORM + SQLite** — Database (zero-config)
- **Tailwind CSS CDN** — UI styling (no build step)
