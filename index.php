<?php
// Start session and redirect if user is already logged in
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>ABB Robotics Contact Management System - Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">ABB Robotics</a>
            <div>
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="register.php" class="btn btn-light">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container text-center mt-5">
        <h1 class="display-4">Welcome to ABB Robotics</h1>
        <p class="lead text-muted">Manage your customers, leads, and interactions efficiently.</p>
        <a href="login.php" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Get Started
        </a>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
