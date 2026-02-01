<?php
// Start the session at the very beginning of the header file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Define a variable to check if a user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PerfectMatch - Find Your Life Partner</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    /* Custom Color Palette */
    :root {
        --primary-red: #dc3545;
        --dark-bg: #222;
    }
    
    /* Global Styles */
    body {
        font-family: Arial, sans-serif;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                  url('https://images.pexels.com/photos/1024984/pexels-photo-1024984.jpeg') center/cover no-repeat;
      height: 85vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: white;
      text-shadow: 0 2px 6px rgba(0,0,0,0.6);
    }
    .hero h1 { font-size: 3rem; font-weight: 700; }
    .hero p { font-size: 1.3rem; }
    .search-box { margin-top: 30px; }
    .search-box {
        background: rgba(255, 255, 255, 0.95) !important;
        border-radius: 12px !important;
    }

    /* Sections */
    .section-title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
      color: var(--primary-red);
    }
    .feature-box {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .feature-box:hover {
      transform: translateY(-5px);
    }
    
    /* Footer Styling */
    footer {
      background: var(--dark-bg);
      color: #bbb;
      padding: 40px 0 20px 0;
    }
    footer a {
        color: #adb5bd;
        text-decoration: none;
    }
    footer a:hover {
        color: var(--primary-red);
    }

    /* Scroll to Top Button Styling */
    #scrollToTopBtn {
        display: none;
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99;
        border: none;
        outline: none;
        background-color: rgba(220, 53, 69, 0.8);
        color: white;
        cursor: pointer;
        padding: 12px 18px;
        border-radius: 50%;
        font-size: 1.5rem;
        line-height: 1;
        transition: background-color 0.3s, opacity 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    #scrollToTopBtn:hover {
        background-color: var(--primary-red);
    }
  </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">❤️ PerfectMatch</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
        
        <?php if ($is_logged_in): ?>
            <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="dashboard.php">My Profile</a></li>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white ms-2" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white ms-2" href="register.php">Sign Up</a></li>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white ms-2" href="log_in.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main>