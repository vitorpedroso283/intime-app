## 📘 inTime – Technical Challenge

Welcome! This repository represents the delivery of a technical challenge.

---

### 💡 About the challenge

The goal was to develop a modern and robust time tracking API, applying best practices in architecture, security, and clean code organization.

---

### ✨ Key highlights:

* 📚 **Fully documented API** via Postman (collection included);
* 🧱 **Solid backend**, with real-world project structure and ready to evolve;
* 🛠️ **Clean code**, automated tests and clear separation of responsibilities;
* ☕ Yes... a few liters of coffee were consumed to make it polished.

---

> *"Clean code always looks like it was written by someone who cares."*
> — **Robert C. Martin (Uncle Bob)**

---

### 🗃️ Personal note:

> This project was built with dedication and attention to detail, with a handcrafted touch that every technical assessment deserves.
> The README was written with the same care as the code: structured sections, direct explanations and an accessible language so that any developer or reviewer can understand the choices and strategies clearly.
> Even without a frontend, the API was designed as a strong foundation for any future expansion — with or without a "clock in" button.

## 🌟 Challenge Objective

The challenge consists of building a time tracking application that allows:

* Employees to clock in/out;
* Admins to manage users and view punch records;
* Authentication and authorization using roles (admin and employee).

### 🛠️ Main technologies

* **PHP 8.4**
* **Laravel 12**
* **Laravel Sanctum** – token-based authentication
* **Eloquent ORM** – database abstraction
* **PestPHP** – test framework

## 🐬 MySQL with Docker (optional)

To run the app quickly, a Docker Compose setup is included for MySQL.

```bash
docker-compose up -d
```

## 🚀 Running the App

```bash
git clone https://github.com/vitorpedroso283/intime-app.git
cd intime-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## ✅ Admin credentials

* **Email:** [admin@intime.test](mailto:admin@intime.test)
* **Password:** t0atr\@sado

## 📊 API Overview

All routes are protected using Laravel Sanctum. The access token is generated through the `/login` endpoint.

### 🔐 Auth routes

* `POST /login`
* `POST /logout`
* `PATCH /me/password`

### 👤 Admin (User management)

* CRUD: `GET /users`, `POST`, `PUT`, `DELETE`
* `PATCH /users/{id}/password`

All admin routes require ability: `MANAGE_EMPLOYEES`

### ⏱️ Clock punches

* `POST /clock-in` (employee)
* `POST /manual`, `PUT`, `DELETE` (admin)
* `GET /report` (filtered punch report)

### 📌 Address lookup

* `GET /zipcode/{cep}` – ViaCEP integration with caching

## 🧑‍💼 Roles & Permissions

* `admin`: full access
* `employee`: clock in/out and change password

Enums centralize permissions and provide helper methods like `->abilities()` and `->label()`.

## 📝 Architecture

* **Service Layer**: business logic separation
* **Form Requests**: validations
* **API Resources**: consistent responses
* **Enums** for roles and permissions
* **Middleware** to control access
* **Traits** and **Rules** (CPF and CEP validators)
* **Raw SQL** used for punch report as requested

## 📊 Logs & Observability

* Structured logs using Laravel daily log channel
* IP, route, and user info included
* Passwords and sensitive info are excluded

## 📈 Tests

* End-to-end tests for all key features using **PestPHP**
* Traits used for generating valid CPF data

```bash
./vendor/bin/pest
```

## 📃 Database structure

### `users`

* Holds both admins and employees
* Includes address fields and soft deletes
* Indexed on `position`, `role`, `name`

### `punches`

* Tracks punch records with `type`, `punched_at`, `created_by`
* `created_by` helps identify manual entries
* Indexes on `punched_at`, `created_by`, and `type`

## 📚 Extras

* Postman collection and environment are included
* Readme and code comments are mostly in Portuguese for local understanding
* Commit messages in English following conventional commits

## 🚧 Development strategy

* Used TDD approach wherever possible
* Feature-first testing
* Clean commit history

## 👍 Final notes

This project was a joy to build. Focused on clarity, clean design and thoughtful technical decisions. Even though no frontend was included, the API is complete and easily extensible.

If you have any questions or feedback, feel free to reach out!
