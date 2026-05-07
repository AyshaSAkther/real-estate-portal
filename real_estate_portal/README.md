# Real Estate Agency Portal

A web-based Real Estate Agency Portal built with PHP and MySQL.
Developed for CIS344 — Spring 2026.

---

## Setup Instructions

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- A local server like XAMPP or WAMP

### Step 1 — Import the Database
1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Click **Import**
3. Select the file `real_estate_portal.sql`
4. Click **Go**

### Step 2 — Configure the Connection
Open `db.php` and update your credentials:
```php
$host     = 'localhost';
$dbname   = 'real_estate_portal_db';
$username = 'root';
$password = ''; // your MySQL password
```

### Step 3 — Run the Project
1. Copy the project folder into your XAMPP `htdocs` directory
   - Example: `C:/xampp/htdocs/real_estate_portal/`
2. Start Apache and MySQL in XAMPP Control Panel
3. Open your browser and go to:
   `http://localhost/real_estate_portal/index.php`

---

## Project Structure

```
real_estate_portal/
│
├── real_estate_portal.sql   ← Database script
├── db.php                   ← Database connection
├── index.php                ← Homepage
├── login.php                ← Login page
├── register.php             ← Registration page
├── properties.php           ← Browse all listings
├── add_property.php         ← Agent: add new listing
├── property_details.php     ← View one property, inquiry, favorite
├── dashboard.php            ← User dashboard (role-based)
├── logout.php               ← End session
└── README.md
```

---

## Test Accounts (after importing SQL)

Register new users via the Register page. Sample data is already seeded in the database.

---

## Features

- Secure login with `password_hash()` and `password_verify()`
- Session-based authentication
- Role-based access: Agent / Buyer / Renter
- Agents can add and manage property listings
- Buyers and renters can browse, submit inquiries, and save favorites
- MySQL view (`PropertyListingView`) for combined listing display
- Stored procedures: `AddOrUpdateUser`, `ProcessTransaction`
- Trigger: `AfterTransactionInsert` auto-updates property status
