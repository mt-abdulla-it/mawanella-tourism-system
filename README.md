# Local Tourist Day-Visit Planner and Information System (Mawanella, Sri Lanka)

Course: ITE2953 Programming Project  
Student: Abdulla MT (Reg No: E2410182)  

---

## Overview
This web application helps tourists and day-visitors explore 10+ popular tourist destinations around Mawanella, Sri Lanka (all within a 25 km radius). Users can filter places by interest categories, search by keywords, view interactive Google Map locations, and build a custom 1-day visit itinerary.

---

## Setup Instructions (XAMPP)

1. Copy Project Folder:
   Copy the `Projact` folder into your local web server directory:
   - For Windows (XAMPP): `C:\xampp\htdocs\Projact`
   - For Linux/Ubuntu: `/var/www/html/Projact`

2. Start XAMPP Modules:
   Open XAMPP Control Panel and start both Apache and MySQL.

3. Import Database (mawanella_tourism_db):
   - Open phpMyAdmin in browser: `http://localhost/phpmyadmin`
   - Create a new database named `mawanella_tourism_db`.
   - Click Import, choose `database.sql` from project directory, and click Go.

4. Access Application:
   - Main Visitor Portal: `http://localhost/Projact/index.php`
   - Admin Login Portal: `http://localhost/Projact/admin/login.php`

---

## Admin Credentials
- Username: `admin`
- Password: `password` (standard default bcrypt hash set in `database.sql`)

---

## Technical Stack & Features
- Frontend: HTML5, Vanilla CSS3 (Flexbox/Grid), ES6 JavaScript, Google Maps API.
- Backend: PHP 7.4+ (Procedural MySQLi with Prepared Statements & CSRF Protection).
- Database: MySQL 8.0+ / MariaDB (`mawanella_tourism_db`).
