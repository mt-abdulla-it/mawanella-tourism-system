# 🌍 Local Tourist Day-Visit Planner (Mawanella, Sri Lanka)

![Project Banner](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge) ![PHP](https://img.shields.io/badge/PHP-7.4+-blue?style=for-the-badge&logo=php) ![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?style=for-the-badge&logo=mysql) ![Vanilla CSS](https://img.shields.io/badge/Vanilla_CSS-No_Frameworks-purple?style=for-the-badge&logo=css3)

**Course**: ITE2953 Programming Project  
**Student**: Abdulla MT (Reg No: E2410182)  
**Project Scope**: A strict 25km radius around Mawanella town. Purely informational (no online booking or payment gateways) as per university scope limits.

---

## 📑 Table of Contents
1. [Project Overview](#-project-overview)
2. [Stakeholder Requirements Matrix](#-stakeholder-requirements-matrix)
3. [Technical Architecture](#-technical-architecture)
4. [Database Schema Overview](#-database-schema-overview)
5. [Directory Structure](#-directory-structure)
6. [Security & UX Enhancements](#-security--ux-enhancements)
7. [Setup Instructions](#-setup-instructions)
8. [Admin Credentials](#-admin-credentials)

---

## 📖 Project Overview
This web application is a highly optimized, interactive platform designed to help tourists and day-visitors explore popular tourist destinations around Mawanella, Sri Lanka. Users can filter places by interest, search by keywords, view live weather, access interactive Google Maps, calculate estimated trip fuel & entrance fee budgets, access instant emergency contacts, and effortlessly build a printable, custom 1-day visit itinerary.

---

## 🎯 Stakeholder Requirements Matrix
The project was built adhering strictly to the required stakeholder specifications provided in the assignment brief:

| Stakeholder | Requirements Successfully Implemented |
|-------------|---------------------------------------|
| **Kirushika N** | Operating hours, entrance fees, available amenities, and an interactive day-trip itinerary planner mapping tool. |
| **Ahamed MHR** | Estimated visit duration for each place, dynamic keyword search bar, and visual UI indicators for nearby facilities. |
| **Yasvini S** | Direct Google Maps API integration for active travel routes, dedicated local cuisine tips, and safety advice sections. |
| **Perera ELN** | Categorization of all locations by interest (Nature, Wildlife, Religious, Heritage, etc.) with instant UI filtering. |

---

## 🚀 Technical Architecture
- **Frontend Core**: HTML5, Vanilla CSS3 (Custom properties, CSS Grid/Flexbox), JavaScript (ES6+). *No external CSS frameworks (like Bootstrap/Tailwind) were used, ensuring native CSS mastery.*
- **Backend Core**: PHP 7.4+ (Procedural architecture).
- **Database**: MySQL 8.0+ / MariaDB.
- **APIs Used**: 
  - **Google Maps JavaScript API**: For custom pin drops and itinerary mapping.
  - **Open-Meteo API**: For zero-key, real-time live weather forecasts.
- **Design System**: Premium Glassmorphism UI, System-synchronized Dark/Light Mode toggle, fully responsive mobile-first architecture, and Inter typography.

---

## 🗄️ Database Schema Overview
The database (`mawanella_tourism_db`) is designed using Third Normal Form (3NF) principles:
- **`admins`**: Stores secure system administrators with bcrypt `password_hash`.
- **`categories`**: Stores the core filterable place types (e.g., Nature, Historical).
- **`places`**: The core table holding metadata (GPS coordinates, descriptions, fees, times).
- **`place_images`**: A one-to-many relationship table allowing a multi-image gallery for each place, supporting a `is_primary` flag for thumbnail designation.

---

## 📁 Directory Structure
```text
mawanella-tourism-system/
│
├── admin/                  # Secure administrator portal (CRUD operations)
│   ├── add_place.php       # Form to upload new tourist locations
│   ├── dashboard.php       # Data table and statistics overview
│   ├── delete_place.php    # Delete location script
│   ├── edit_place.php      # Form to modify existing locations & galleries
│   ├── form_fields.php     # Shared form partial
│   ├── login.php           # CSRF & Bcrypt protected authentication gateway
│   ├── logout.php          # Session termination
│   └── manage_admins.php   # Admin account management
│
├── assets/                 # Static assets (CSS, JS, Images)
│   ├── css/                # Vanilla CSS modules (layout.css, details.css, etc.)
│   ├── images/places/      # Attraction image uploads
│   └── js/                 # ES6 logic (main.js, map.js)
│
├── config/                 # Environment configurations & helpers
│   ├── db.php              # Global MySQLi connection instance
│   └── helpers.php         # Security & formatting helper utilities
│
├── database/               # Database SQL script & schema setup
│   └── database.sql        # Master SQL Export for system installation
│
├── includes/               # Reusable PHP components
│   ├── header.php          # Global navigation & metadata
│   └── footer.php          # Global footer & JS imports
│
├── index.php               # Visitor Homepage (Search, Filter, Weather)
├── details.php             # Single-Place Deep Dive & Gallery
├── planner.php             # Interactive Itinerary Builder & Map
└── 404.php                 # Custom 404 Error page
```

---

## 🔒 Security & UX Enhancements
- **SQL Injection Prevention**: 100% of SQL queries utilize Prepared Statements (`mysqli_prepare`).
- **Secure Authentication**: Utilizing pure `password_hash()` and `password_verify()` with bcrypt encryption.
- **CSRF Protection**: Custom cryptographic CSRF tokens are generated and validated on all POST form submissions.
- **XSS Protection**: All user-facing database outputs are strictly sanitized using a custom `escape()` wrapper utilizing `htmlspecialchars()`.
- **Distance Range Filters & Weather/Time Badges**: Dynamic max-distance pills (`<10km`, `<20km`) and visual travel-time indicators.
- **One-Click Itinerary Copy & WhatsApp Share**: Native `navigator.clipboard` integration for sharing formatted itinerary summaries.
- **Printable PDF Export**: Custom `@media print` CSS stylesheet formatting itineraries cleanly for 1-page PDF/print output.
- **Micro-interactions**: AJAX-powered Toast notifications, native JavaScript `IntersectionObserver` for performant scroll-reveal animations, and lazy-loaded imagery.

---

## 🛠️ Setup Instructions

1. **Copy Project Folder**:
   Place the `mawanella-tourism-system` folder into your local web server directory:
   - Windows (XAMPP): `C:\xampp\htdocs\mawanella-tourism-system`
   - Linux/Ubuntu: `/var/www/html/mawanella-tourism-system`

2. **Start Server Modules**:
   Open your XAMPP Control Panel and start **Apache** and **MySQL**.

3. **Import the Database**:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named exactly `mawanella_tourism_db`.
   - Click the **Import** tab, choose the `database/database.sql` file, and click **Go**.

4. **Access the Application**:
   - **Main Visitor Portal**: `http://localhost/mawanella-tourism-system/index.php`
   - **Admin Login Portal**: `http://localhost/mawanella-tourism-system/admin/login.php`

---

## 👤 Admin Credentials
Use the following credentials to access the secure backend dashboard to add/edit/delete tourist places:
- **Username**: `admin`
- **Password**: `password`
