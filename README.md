Here’s a polished README for your PHP site within the Vinyl Store project, keeping it cohesive with your existing content:

---

# 🎵 Vinyl Store Web Application – Admin Panel

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge\&logo=bootstrap\&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge\&logo=mariadb\&logoColor=white)

This repository contains the **PHP-based administration panel** for the Vinyl Store Web Application.
It allows store administrators to manage users, products, and orders efficiently.

---

## 🧩 Overview

The Admin Panel complements the main Java + Tomcat website by providing **backend management capabilities**:

### 🛠️ Admin Features

* Manage users: activate, modify, or delete accounts
* Add, edit, and remove vinyl records from the catalog
* View and manage customer orders
* Dashboard with statistics and quick actions

The panel is built with **PHP and Bootstrap** and connects to the same **MariaDB database** used by the main website.

---

## ⚙️ Tech Stack

| Layer           | Technologies                         |
| --------------- | ------------------------------------ |
| **Frontend**    | Bootstrap 5, HTML5, CSS3, JavaScript |
| **Backend**     | PHP                                  |
| **Database**    | MariaDB                              |
| **IDE / Tools** | Visual Studio Code                   |

---

## 🚀 Installation & Setup

### 1. Prerequisites

Before running the Admin Panel, ensure you have:

* 🐘 **PHP 8+**
* 🐬 **MariaDB Server**
* 💻 **Visual Studio Code** or any PHP editor
* 🌐 A local server environment (e.g., XAMPP, WAMP, MAMP, or Docker)

### 2. Database Configuration

1. Make sure the **MariaDB database** used by the main site is running.
2. Update the connection details in `baseDatos.php` (or equivalent file) with your database credentials.

```php
<?php
$host = 'localhost';
$db = 'vinylstore_db';
$user = 'root';
$pass = '';
?>
```

### 3. Running the Admin Panel

Run the PHP server from VS Code:
Right-click the main PHP file → PHP Server: Serve Project
Then visit http://localhost:3000

## 🎨 Features

* ✅ User management (CRUD operations)
* ✅ Product catalog management (CRUD operations)
* ✅ Order overview and management
* ✅ Responsive layout with Bootstrap
* ✅ Simple and intuitive dashboard

---

## 🖼️ Screenshots

### 🖥️ Admin Dashboard

![Admin Dashboard Screenshot](/demofiles/admin_dashboard.png)

### 💿 Product Management

![Product Management Screenshot](/demofiles/admin_products.png)

### 👤 User Management

![User Management Screenshot](/demofiles/admin_users.png)

---

## 🧠 Learnings

This admin panel demonstrates:

* PHP backend development and MVC structure concepts
* Database connectivity with **MariaDB**
* Integration with an existing e-commerce system
* Bootstrap-powered responsive design for administrative interfaces

---

## 📚 References

* [PHP Official Documentation](https://www.php.net/docs.php)
* [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/getting-started/introduction/)
* [MariaDB Foundation](https://mariadb.org/)

