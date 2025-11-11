# 🏡 Estate Management Software

**Estate Management Software** is a full-featured web platform designed to streamline and digitize real estate administration.  
It empowers estate managers, property owners, and residents to manage operations such as property tracking, payments, maintenance, announcements, and user communication — all in one place.

---

## 🚀 Features

### 🧭 Admin Dashboard
- Intuitive and responsive interface built with **Blade (Laravel)** and **Tailwind CSS**
- Quick insights into estate activities, residents, and transactions
- Role-based access for Admins, Managers, and Residents

### 🏘️ Property Management
- Add, edit, and delete property records
- Track property types (apartments, duplexes, plots, etc.)
- Manage ownership and tenancy records

### 👥 Residents & Users
- Create and manage user accounts with specific roles and permissions
- View resident profiles, payment history, and property details
- Automated onboarding for new tenants

### 💰 Payments & Transactions
- Record rent payments and other charges
- Generate invoices and receipts
- Track payment status and arrears

### 🔧 Maintenance Requests
- Submit, approve, and track repair or maintenance reports
- Assign workers or contractors
- Log completion reports and costs

### 📢 Announcements & Notifications
- Admins can broadcast messages to residents
- Instant alerts for upcoming maintenance, dues, or meetings

### 📅 Events & Scheduling
- Schedule estate meetings or community events
- Sync reminders with user dashboards

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|-------------|
| **Frontend** | Laravel Blade / TailwindCSS |
| **Backend** | Laravel 12 (PHP 8+) |
| **Database** | MySQL or PostgreSQL |
| **Authentication** | Laravel Breeze / Jetstream |
| **Version Control** | Git & GitHub |
| **Deployment** |  |

---

## ⚙️ Installation Guide

### 🔩 Requirements
- PHP >= 8.2  
- Composer  
- MySQL or PostgreSQL  
- Node.js & NPM  
- Laravel 12+

---

### 🪜 Setup Instructions

```bash
# Clone the repository


# Navigate to the project
cd estate-management-software

# Install backend dependencies
composer install

# Copy and edit environment variables
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=estate_db
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Install frontend dependencies
npm install && npm run dev

# Start local development server
php artisan serve
