<?php
include "session.php";
include "db.php";

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Handle new interaction submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['interaction_type'])) {
    $customer_id = $_POST['customer_id'];
    $interaction_type = $_POST['interaction_type'];
    $interaction_date = $_POST['interaction_date'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO interactions (customer_id, user_id, interaction_type, interaction_date, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $customer_id, $user_id, $interaction_type, $interaction_date, $notes);
    $stmt->execute();
    $stmt->close();

    header("Location: interaction.php?success=1");
    exit();
}

// Fetch customers for the dropdown
$customers = ($user_role == "Admin")
    ? $conn->query("SELECT id, name FROM customers")
    : $conn->query("SELECT id, name FROM customers WHERE assigned_to = $user_id");

// Build search query
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_sql = "";
if ($search) {
    $search = $conn->real_escape_string($search);
    $search_sql = "AND (
        customers.name LIKE '%$search%' OR 
        interaction_type LIKE '%$search%' OR 
        notes LIKE '%$search%'
    )";
}

// Fetch interactions
$interaction_sql = ($user_role == "Admin")
    ? "SELECT interactions.*, customers.name AS customer_name, users.name AS rep_name 
        FROM interactions 
        LEFT JOIN customers ON interactions.customer_id = customers.id 
        LEFT JOIN users ON interactions.user_id = users.id 
        WHERE 1 $search_sql
        ORDER BY interaction_date DESC"
    : "SELECT interactions.*, customers.name AS customer_name 
        FROM interactions 
        LEFT JOIN customers ON interactions.customer_id = customers.id 
        WHERE customers.assigned_to = $user_id $search_sql
        ORDER BY interaction_date DESC";

$interactions = $conn->query($interaction_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Interactions - ABB Robotics</title>
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="customer.php"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="lead.php"><i class="bi bi-lightbulb"></i> Leads</a></li>
                <li class="nav-item"><a class="nav-link active" href="interaction.php"><i class="bi bi-chat-left-text"></i> Interactions</a></li>
                <?php if ($user_role == 'Admin') { ?>
                    <li class="nav-item"><a class="nav-link" href="assign_customer.php"><i class="bi bi-person-plus"></i> Assign Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-person-gear"></i> Manage Users</a></li>
                <?php } ?>
                <li class="nav-item"><a class="nav-link btn btn-danger text-white px-3" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container mt-5">
    <h2 class="text-center">Record Interaction</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">Interaction recorded successfully!</div>
    <?php endif; ?>

    <!-- Interaction Form -->
    <form method="POST" class="card shadow-sm p-4 bg-white mb-5 w-100 mx-auto" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Select Customer</label>
            <select name="customer_id" class="form-select" required>
                <?php while ($cust = $customers->fetch_assoc()) { ?>
                    <option value="<?= $cust['id']; ?>"><?= $cust['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Interaction Type</label>
            <select name="interaction_type" class="form-select" required>
                <option value="Call">Call</option>
                <option value="Meeting">Meeting</option>
                <option value="Email">Email</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Interaction Date</label>
            <input type="date" name="interaction_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="4" placeholder="Write notes here..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Save Interaction</button>
    </form>

    <!-- Search Form -->
    <form method="GET" class="mb-4 d-flex justify-content-center">
        <input type="text" name="search" class="form-control w-50 me-2" placeholder="Search interactions..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
    </form>

    <!-- Interaction Table -->
    <h3 class="text-center mb-3">Past Interactions</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Notes</th>
                <?php if ($user_role == "Admin") echo "<th>Recorded By</th>"; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($interactions->num_rows > 0): ?>
                <?php while ($row = $interactions->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['interaction_date']); ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['interaction_type']); ?></td>
                    <td><?= htmlspecialchars($row['notes']); ?></td>
                    <?php if ($user_role == "Admin") { ?>
                        <td><?= htmlspecialchars($row['rep_name'] ?? 'Unknown'); ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            <?php else: ?>
                <tr><td colspan="<?= $user_role == "Admin" ? 5 : 4 ?>" class="text-center">No interactions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
