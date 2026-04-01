Here’s a clean and professional `README.md` you can use for your GitHub repo:

---

# Custom Auth System (Laravel 12)

This project is a custom authentication system built with Laravel 12. It supports role-based login for Admin and User with pre-seeded credentials.

---

## 🚀 Getting Started

Follow the steps below to set up the project locally.

### 1. Clone the Repository

```bash
git clone https://github.com/M-Farhan-Shaukat/Custom_auth.git
cd Custom_auth
```

---

### 2. Install Dependencies

Make sure you have:

* PHP >= 8.2
* Composer installed

Then run:

```bash
composer install
```

---

### 3. Environment Setup

Copy the example environment file:

```bash
cp .env .env
```

Update your database credentials in the `.env` file.

---

### 4. Generate Application Key

```bash
php artisan key:generate
```

---

### 5. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

---

### 6. Start the Server

```bash
php artisan serve
```

Application will run at:

```
http://127.0.0.1:8000
```

---

## 🔐 Login Credentials

### 👨‍💼 Admin Role

* URL: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)
* Email: `admin@gmail.com`
* Password: `Temp123!`

---

### 👤 User Role

* URL: [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
* Email: `user@gmail.com`
* Password: `Temp123!`

---

## 📌 Features

* Role-based authentication (Admin & User)
* Laravel 12 structure
* Database seeding for demo users
* Clean and simple auth flow

---

## ⚠️ Notes

* Make sure your database is created before running migrations
* Update `.env` properly to avoid connection issues

---

## 📄 License

This project is open-source and available for learning and development purposes.
