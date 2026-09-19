<?php
include "session.php";
include "db.php";

// Restrict access to Admins only
if ($_SESSION['role'] != "Admin") {
    header("Location: dashboard.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users - ABB Robotics</title>
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
        <a class="navbar-brand" href="dashboard.php">ABB Robotics</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="customer.php"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="lead.php"><i class="bi bi-lightbulb"></i> Leads</a></li>
                <li class="nav-item"><a class="nav-link" href="interaction.php"><i class="bi bi-chat-left-text"></i> Interactions</a></li>
                <li class="nav-item"><a class="nav-link" href="assign_customer.php"><i class="bi bi-person-plus"></i> Assign Customers</a></li>
                <li class="nav-item"><a class="nav-link active" href="users.php"><i class="bi bi-person-gear"></i> Manage Users</a></li>
                <li class="nav-item"><a class="nav-link btn btn-danger text-white px-3" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="text-center">Manage Users</h2>
    
    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td>
                    <a href="delete_user.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
