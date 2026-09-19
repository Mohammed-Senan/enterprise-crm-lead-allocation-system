# 🏢 Enterprise CRM & Lead Allocation System

A secure, database-driven Customer Relationship Management (CRM) platform engineered with PHP and MySQL. The system streamlines customer lifecycles, automates sales lead dispatching, and records client interactions through role-based access control (RBAC).

---

## ⚡ Core Architecture & Features

* **Role-Based Access Control (RBAC):** Granular permission isolation across Admin, Manager, and Sales Representative tiers with secure session tracking (`session.php`, `users.php`).
* **Lead Pipeline & Dynamic Allocation:** Automatic creation of sales pipeline entries upon customer onboarding, allowing real-time assignment and reassignment to sales reps (`assign_customer.php`, `lead.php`).
* **Omnichannel Interaction Logging:** Comprehensive logging of customer calls, meetings, follow-ups, and notes linked directly to customer profiles and responsible account managers (`interaction.php`).
* **Central Analytics Dashboard:** Real-time visibility into customer distribution, conversion pipeline statuses, and team activity metrics (`dashboard.php`).
* **Secure Database Layer:** Parameterized SQL queries via PHP Data Objects / Prepared Statements (`mysqli`) to prevent SQL injection vulnerabilities.

---

## 📁 Repository Structure

```text
├── add_customer.php       # Customer registration & initial lead generation pipeline
├── assign_customer.php    # Lead & customer dispatch engine for account reps
├── customer.php           # Customer profile management & directory views
├── dashboard.php          # Real-time metrics, pipeline analytics & activity logs
├── db.php                 # Relational MySQL database connector
├── delete_customer.php    # Customer deletion with cascade management
├── delete_user.php        # User lifecycle & staff administration
├── edit_customer.php      # Customer record mutations & updates
├── edit_profile.php       # Account preference & credential update handler
├── index.php              # Application entry point & route redirector
├── interaction.php        # Client meeting & communication interaction logger
├── lead.php               # Sales pipeline stages & conversion tracker
├── login.php              # Session authentication & credential verification
├── logout.php             # Session destruction & secure logout handler
├── register.php           # User account provisioning & role assignment
├── schema.sql             # Relational MySQL schema DDL & relationship definitions
├── script.js              # Client-side dynamic interaction scripts
├── session.php            # RBAC session validator & route guard
├── styles.css             # Enterprise dashboard styling & layout engine
└── users.php              # Team member administration interface
```

---

## 🛠️ Tech Stack

* **Backend:** PHP 8+ / Native Prepared Statements (`mysqli`)
* **Database:** MySQL 8.0+ / MariaDB (InnoDB with Foreign Key Cascades)
* **Frontend:** Vanilla JavaScript (ES6+), HTML5, Custom Responsive CSS3
* **Environment:** Apache / Nginx / XAMPP / WAMP / Local PHP Server

---

## 🚀 Quick Start & Deployment

### 1. Clone the Repository
```bash
git clone https://github.com/Mohammed-Senan/enterprise-crm-lead-allocation-system.git
cd enterprise-crm-lead-allocation-system
```

### 2. Database Provisioning
Import the database schema using MySQL CLI or phpMyAdmin:
```bash
mysql -u root -p < schema.sql
```

### 3. Database Credentials Setup
Update `db.php` if your local MySQL credentials differ from default:
```php
$servername = "localhost";
$username   = "root";
$password   = ""; // update your password here
$database   = "crm_system";
```

### 4. Run the Application
Run via PHP's built-in web server:
```bash
php -S localhost:8000
```
Then open `http://localhost:8000/login.php` in your web browser.
