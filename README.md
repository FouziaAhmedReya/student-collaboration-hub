# Student Collaboration Hub — Module 1: Student Profile & Skill Management

A fully functional Laravel + MySQL web application built for university student profile management, technical skills tracking, portfolio showcase, completed projects display, and preferred study location mapping.

---

## Technology Stack

- **Backend**: PHP 8.5+, Laravel 13, Eloquent ORM, Laravel Breeze Auth
- **Frontend**: Laravel Blade, Bootstrap 5.3, Custom CSS, Bootstrap Icons, JavaScript
- **Database**: MySQL (relational database with cascading foreign keys)
- **Asset Build**: Node.js & Vite
- **Maps**: Google Maps JavaScript API (with optional fallback preview)

---

## Required Software

Before running the application, ensure you have the following installed on your machine:

1. **PHP** >= 8.2 (Tested on PHP 8.5.8)
2. **Composer** >= 2.0
3. **Node.js** & **npm** (Installed via Homebrew/Node installer)
4. **MySQL** Server (XAMPP or Homebrew MySQL)

---

## Setup & Installation Instructions

Follow these step-by-step instructions to get the application running locally from scratch:

### Step 1: Environment File Configuration

Copy the example environment file `.env.example` to `.env`:

```bash
cp .env.example .env
```

Ensure your `.env` contains the MySQL credentials matching your local environment (XAMPP or Homebrew MySQL):

```ini
APP_NAME="Student Collaboration Hub"
APP_ENV=local
APP_KEY=base64:hlL0NPiF4XjKv0HD/61lrg8rIeR4RNuTIDcaTOHEcwc=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_collaboration_hub
DB_USERNAME=root
DB_PASSWORD=

# Optional Google Maps JavaScript API key
GOOGLE_MAPS_API_KEY=
```

### Step 2: Database Creation

Make sure MySQL server is running. Create the database in MySQL CLI or phpMyAdmin:

```sql
CREATE DATABASE student_collaboration_hub;
```

### Step 3: Run Database Migrations & Seeders

Run the Laravel migrations to create all relational database tables and seed the demo student profile:

```bash
php artisan migrate --seed
```

> **Note**: This will automatically create the default user and profile for **Rayhanul Hoque** (`rayhanul@bracu.ac.bd` / password: `password`).

### Step 4: Storage Symlink for Profile Photos

Create the symbolic link for uploaded student profile pictures:

```bash
php artisan storage:link
```

### Step 5: Install Frontend Dependencies & Compile Assets

```bash
npm install
npm run build
```

---

## Starting the Application

Start the local Laravel server by running:

```bash
php artisan serve
```

Open your browser and navigate to:

👉 **`http://localhost:8000`** (or `http://127.0.0.1:8000`)

---

## Demo Login Credentials

You can log in directly using the seeded demo account:

- **Email**: `rayhanul@bracu.ac.bd`
- **Password**: `password`

You can also register a brand new student account on the **Register** page!

---

## Google Maps API Configuration

1. Get an API key from the [Google Maps Platform Console](https://console.cloud.google.com/).
2. Enable the **Maps JavaScript API** and **Places API**.
3. Add your key to `.env`:
   ```ini
   GOOGLE_MAPS_API_KEY=your_actual_google_maps_api_key
   ```
4. If `GOOGLE_MAPS_API_KEY` is not set, the application handles it gracefully with a styled fallback location badge and map preview card.

---

## Testing Module 1 Features

Run the automated PHPUnit feature tests to verify all 25 functionality requirements:

```bash
php artisan test --filter=ModuleOneProfileTest
```

### Feature Checklist:

- [x] Student Registration
- [x] Student Login & Logout
- [x] View Student Profile ("My Profile & Skills")
- [x] Edit Personal Info (Name, Department, Semester, University, About Me, Joined Date)
- [x] Upload & Update Profile Photo (local storage)
- [x] Technical Skills Management (Add, Edit, Delete, 0-100% Proficiency slider)
- [x] Interests Tags Management (Add tag, Delete tag)
- [x] Completed Projects Management (Add, Edit, Delete project with tech stack)
- [x] Portfolio Links Management (Add, Edit, Delete link with safe tab opening)
- [x] Preferred Study Location Selection (Google Maps integration / fallback)
- [x] Dynamic Profile Completion Percentage Calculation (0 - 100%)
- [x] Authentication Route Protection (`auth` middleware)

---

## Database Architecture

- `users`: `id`, `name`, `email`, `password`, `timestamps`
- `profiles`: `id`, `user_id` (FK), `profile_photo`, `department`, `semester`, `university`, `joined_date`, `about_me`, `preferred_location_name`, `preferred_location_address`, `latitude`, `longitude`, `timestamps`
- `skills`: `id`, `profile_id` (FK), `name`, `proficiency`, `timestamps`
- `interests`: `id`, `profile_id` (FK), `name`, `timestamps`
- `projects`: `id`, `profile_id` (FK), `name`, `description`, `technologies`, `timestamps`
- `portfolio_links`: `id`, `profile_id` (FK), `platform`, `url`, `timestamps`
