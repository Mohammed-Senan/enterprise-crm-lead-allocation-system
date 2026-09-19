<?php
// Start session and connect to database
include "session.php";
include "db.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Build base SQL query
$base_sql = "SELECT * FROM customers WHERE 1";

// Filter for non-admin users
if ($role != "Admin") {
    $base_sql .= " AND assigned_to = $user_id";
}

// Apply search filter
if (!empty($search)) {
    $escaped = $conn->real_escape_string($search);
    $base_sql .= " AND (
        name LIKE '%$escaped%' OR
        company LIKE '%$escaped%' OR
        email LIKE '%$escaped%' OR
        phone LIKE '%$escaped%' OR
        address LIKE '%$escaped%'
    )";
}

$base_sql .= " ORDER BY name ASC";
$result = $conn->query($base_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link active" href="customer.php"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="lead.php"><i class="bi bi-lightbulb"></i> Leads</a></li>
                <li class="nav-item"><a class="nav-link" href="interaction.php"><i class="bi bi-chat-left-text"></i> Interactions</a></li>
                <?php if ($role == 'Admin') { ?>
                    <li class="nav-item"><a class="nav-link" href="assign_customer.php"><i class="bi bi-person-plus"></i> Assign Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-person-gear"></i> Manage Users</a></li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white px-3" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Customers</h2>

    <!-- Add button for Admin -->
    <?php if ($role == 'Admin'): ?>
        <div class="d-flex justify-content-end mb-3">
            <a href="add_customer.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New Customer
            </a>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="GET" class="mb-4 d-flex justify-content-center">
        <input type="text" name="search" class="form-control w-50 me-2" placeholder="Search customers..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
        </button>
    </form>

    <!-- Success Messages -->
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success text-center">Customer and related records deleted.</div>
    <?php elseif (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">Customer added successfully.</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success text-center">Customer updated successfully.</div>
    <?php endif; ?>

    <!-- Customer Table -->
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['company']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                    <td><?= htmlspecialchars($row['address']); ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm mb-1">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <?php if ($role == 'Admin') { ?>
                        <a href="delete_customer.php?id=<?= $row['id']; ?>" 
                            onclick="return confirm('Are you sure you want to delete this customer and all their leads/interactions?');"
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
