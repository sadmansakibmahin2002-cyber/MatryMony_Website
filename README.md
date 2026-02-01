# Matrimony Management System (PHP & MySQL)

A **web-based Matrimony Management System** developed using **Core PHP and MySQL**. This project allows users to create matrimonial profiles and search for suitable matches, while administrators manage, verify, and control user profiles through an admin panel.

This repository is **public** and intended to be easily understood by students, developers, and evaluators.

---

## 📖 About the Project

The Matrimony Management System is designed to digitize the traditional matrimonial process. Users can register, create profiles, and search for matches based on basic criteria. Administrators ensure authenticity by reviewing and approving profiles.

The project is suitable for:

* Final Year Project
* Mini Project
* Academic Demonstration
* Learning PHP & MySQL CRUD operations

---

## ✨ Key Features

### User Module

* User registration and login
* Create and update personal details
* Add address and profile information
* Search profiles (Bride / Groom)
* View approved profiles only
* Secure logout

### Admin Module

* Admin authentication (login/logout)
* View all registered users
* Approve or reject user profiles
* Block and unblock users
* Manage overall system data

---

## 🛠️ Technology Used

* **Frontend:** HTML, CSS, Bootstrap
* **Backend:** PHP (Core PHP)
* **Database:** MySQL
* **Server:** Apache (XAMPP / WAMP)

---

## 📁 Project Folder Structure

```
matrymony/
│── admin/                 # Admin panel files
│── includes/              # Database connection & common files
│── assets/                # CSS, JavaScript, Images
│── index.php              # Home page
│── register.php           # User registration
│── login.php              # User login
│── search.php             # Search profiles
│── address.php            # Address details
│── logout.php             # Logout
│── README.md
```

---

## ⚙️ How to Run the Project Locally

### Step 1: Download or Clone the Repository

```bash
git clone https://github.com/sadmansakibmahin2002-cyber/MatryMony_Website
```

### Step 2: Setup Server

* Install **XAMPP** or **WAMP**
* Start **Apache** and **MySQL** services

### Step 3: Database Setup

* Open **phpMyAdmin**
* Create a database named:

  ```
  matrymony
  ```
* Import the provided SQL file (if available)
* Configure database connection in:

  ```
  includes/db_connect.php
  ```

### Step 4: Run the Application

* Move the project folder to:

  ```
  htdocs/
  ```
* Open browser and visit:

  ```
  http://localhost/matrymony/
  ```

---

## 🔐 Admin Panel Access

* Admin URL:

  ```
  http://localhost/matrymony/admin/
  ```
* Admin can manage users and approve profiles

---

## ⏱️ Project Duration

* Development Time: **3.5 – 4 Months**
* Suitable for academic project submission

---

## ⚠️ Limitations

* Manual profile verification
* Internet connection required
* No real-time chat system
* No mobile application

---

## 🚀 Future Enhancements

* Advanced matchmaking algorithm
* User chat & messaging system
* Mobile application
* Payment gateway integration
* Profile recommendation system

---

## 👨‍💻 Author

Developed as an academic project for learning and demonstration purposes.

---

## 📄 License

This project is open for **educational use only**.

---

⭐ If this repository helped you, consider giving it a **star** on GitHub!
