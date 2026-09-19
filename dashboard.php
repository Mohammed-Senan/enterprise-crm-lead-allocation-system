<?php
include "session.php";

// Fetch user role for conditional rendering
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">ABB Robotics</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                Logged in as: <strong><?= htmlspecialchars($user_role); ?></strong>
            </span>
            <a href="edit_profile.php" class="btn btn-light me-2">My Account</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="container mt-5">
    <h2 class="text-center">Welcome to Your Dashboard</h2>
    <p class="text-center text-muted">Manage customers, leads, and interactions efficiently.</p>

    <div class="row mt-4">
        <?php if ($user_role === "Admin"): ?>
            <div class="col-md-4">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-person-check display-4 text-primary"></i>
                        <h5 class="card-title mt-3">Manage Users</h5>
                        <p class="card-text text-muted">Add, update, and delete users.</p>
                        <a href="users.php" class="btn btn-primary w-100">Go</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-person-plus display-4 text-primary"></i>
                        <h5 class="card-title mt-3">Assign Customers</h5>
                        <p class="card-text text-muted">Assign customers to Sales Representatives.</p>
                        <a href="assign_customer.php" class="btn btn-primary w-100">Go</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-people display-4 text-primary"></i>
                    <h5 class="card-title mt-3">Manage Customers</h5>
                    <p class="card-text text-muted">View, add, and update customer records.</p>
                    <a href="customer.php" class="btn btn-primary w-100">Go</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-lightbulb display-4 text-warning"></i>
                    <h5 class="card-title mt-3">Manage Leads</h5>
                    <p class="card-text text-muted">Track and manage potential customers.</p>
                    <a href="lead.php" class="btn btn-warning w-100 text-white">Go</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-chat-left-text display-4 text-success"></i>
                    <h5 class="card-title mt-3">Manage Interactions</h5>
                    <p class="card-text text-muted">View and log meetings, calls, and emails.</p>
                    <a href="interaction.php" class="btn btn-success w-100">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
