<?php
// Include session and database connection files
include "session.php";
include "db.php";

// Handle lead status update if POST request is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lead_id']) && isset($_POST['status'])) {
    $lead_id = $_POST['lead_id'];
    $status = $_POST['status'];

    // Prepare and execute update statement
    $stmt = $conn->prepare("UPDATE leads SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $lead_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: lead.php");
    exit();
}

// Prepare SQL query to fetch leads, including sales rep name for Admin
if ($_SESSION['role'] == "Admin") {
    $query = "
        SELECT leads.*, 
                customers.name AS customer_name, 
                users.name AS sales_rep_name 
        FROM leads 
        LEFT JOIN customers ON leads.customer_id = customers.id 
        LEFT JOIN users ON leads.assigned_to = users.id
    ";
} else {
    $query = "
        SELECT leads.*, 
                customers.name AS customer_name 
        FROM leads 
        LEFT JOIN customers ON leads.customer_id = customers.id 
        WHERE leads.assigned_to = " . $_SESSION['user_id'];
}

// Execute query and store result
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page metadata and resources -->
    <title>Leads - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS and icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">ABB Robotics</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Common navigation links -->
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="customer.php"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a class="nav-link active" href="lead.php"><i class="bi bi-lightbulb"></i> Leads</a></li>
                <li class="nav-item"><a class="nav-link" href="interaction.php"><i class="bi bi-chat-left-text"></i> Interactions</a></li>
                
                <!-- Admin-only links -->
                <?php if ($_SESSION['role'] == 'Admin') { ?>
                    <li class="nav-item"><a class="nav-link" href="assign_customer.php"><i class="bi bi-person-plus"></i> Assign Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-person-gear"></i> Manage Users</a></li>
                <?php } ?>

                <!-- Logout button -->
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white px-3" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main page content -->
<div class="container mt-5">
    <h2 class="text-center">Leads</h2>

    <!-- Leads table -->
    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>Customer</th>
                <th>Status</th>
                <!-- Only show "Assigned To" for Admin users -->
                <?php if ($_SESSION['role'] == "Admin") echo "<th>Assigned To</th>"; ?>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through leads and display them -->
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                <td>
                    <!-- Status update form -->
                    <form method="POST" class="d-flex align-items-center">
                        <input type="hidden" name="lead_id" value="<?= $row['id']; ?>">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="New" <?= $row['status'] == 'New' ? 'selected' : '' ?>>New</option>
                            <option value="Contacted" <?= $row['status'] == 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="In Progress" <?= $row['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Closed" <?= $row['status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </form>
                </td>
                <!-- Display sales rep name for Admin -->
                <?php if ($_SESSION['role'] == "Admin") { ?>
                    <td><?= $row['sales_rep_name'] ?? 'Unassigned'; ?></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
