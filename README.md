# Foydalanuvchi-loyihasi

Веб-приложение на **PHP и Symfony**.

Проект использует стандартную структуру Symfony, Composer для управления зависимостями, миграции базы данных и Docker для локального окружения.

## Стек

* PHP
* Symfony
* Composer
* Doctrine Migrations
* Twig
* Docker / Docker Compose

## Структура проекта

```text
├── bin/          # Консольные команды Symfony
├── config/       # Конфигурация приложения
├── migrations/   # Миграции базы данных
├── public/       # Публичная часть приложения
├── src/          # Основной код приложения
├── templates/    # Twig-шаблоны
├── compose.yaml  # Docker-конфигурация
└── composer.json # PHP-зависимости
```

## Установка

Установить зависимости:

```bash
composer install
```

Запустить Docker:

```bash
docker compose up -d
```

Применить миграции:

```bash
php bin/console doctrine:migrations:migrate
```

После этого приложение можно запустить через локальное окружение PHP/Symfony.

## Полезные команды

```bash
php bin/console
php bin/console cache:clear
php bin/console doctrine:migrations:migrate
```

## О проекте

Проект создан в рамках практики **backend-разработки на PHP/Symfony** с использованием структурированной архитектуры, работы с базой данных, миграций и Docker.

## Лицензия

Проект создан в учебных и портфолио-целях.
# Foydalanuvchi-loyihasi

Веб-приложение на **PHP и Symfony**.

Проект использует стандартную структуру Symfony, Composer для управления зависимостями, миграции базы данных и Docker для локального окружения.

## Стек

* PHP
* Symfony
* Composer
* Doctrine Migrations
* Twig
* Docker / Docker Compose

## Структура проекта

```text
├── bin/          # Консольные команды Symfony
├── config/       # Конфигурация приложения
├── migrations/   # Миграции базы данных
├── public/       # Публичная часть приложения
├── src/          # Основной код приложения
├── templates/    # Twig-шаблоны
├── compose.yaml  # Docker-конфигурация
└── composer.json # PHP-зависимости
```

## Установка

Установить зависимости:

```bash
composer install
```

Запустить Docker:

```bash
docker compose up -d
```

Применить миграции:

```bash
php bin/console doctrine:migrations:migrate
```

После этого приложение можно запустить через локальное окружение PHP/Symfony.

## Полезные команды

```bash
php bin/console
php bin/console cache:clear
php bin/console doctrine:migrations:migrate
```

## О проекте

Проект создан в рамках практики **backend-разработки на PHP/Symfony** с использованием структурированной архитектуры, работы с базой данных, миграций и Docker.

## Лицензия

Проект создан в учебных и портфолио-целях.
