<?php
// Start session and DB connection
include "session.php";
include "db.php";

// Redirect non-admin users
if ($_SESSION['role'] != "Admin") {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $sales_rep_id = $_POST['sales_rep_id'];

    // 1. Assign customer to sales rep
    $stmt = $conn->prepare("UPDATE customers SET assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ii", $sales_rep_id, $customer_id);

    if ($stmt->execute()) {
        $stmt->close();

        // 2. Also assign related leads
        $stmt_lead = $conn->prepare("UPDATE leads SET assigned_to = ? WHERE customer_id = ?");
        $stmt_lead->bind_param("ii", $sales_rep_id, $customer_id);
        $stmt_lead->execute();
        $stmt_lead->close();

        // Redirect on success
        header("Location: assign_customer.php?success=1");
        exit();
    } else {
        $error = "Error: " . $conn->error;
        $stmt->close();
    }
}

// Get customer list with assigned sales rep
$customers = $conn->query("
    SELECT customers.id, customers.name, users.name AS sales_rep_name, customers.assigned_to 
    FROM customers 
    LEFT JOIN users ON customers.assigned_to = users.id
");

// Get list of all sales representatives
$sales_reps_all = $conn->query("SELECT id, name FROM users WHERE role = 'Sales'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Customers - ABB Robotics</title>
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
        <a class="navbar-brand" href="dashboard.php">ABB CRM</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="customer.php"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="lead.php"><i class="bi bi-lightbulb"></i> Leads</a></li>
                <li class="nav-item"><a class="nav-link" href="interaction.php"><i class="bi bi-chat-left-text"></i> Interactions</a></li>
                <li class="nav-item"><a class="nav-link active" href="assign_customer.php"><i class="bi bi-person-plus"></i> Assign Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-person-gear"></i> Manage Users</a></li>
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
    <h2 class="text-center">Assign Customers to Sales Representatives</h2>

    <!-- Display success or error message -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            Customer and associated leads reassigned successfully!
        </div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error; ?></div>
    <?php endif; ?>

    <!-- Assignment form -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <!-- Select Customer -->
                        <div class="mb-3">
                            <label class="form-label">Select Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <?php while ($row = $customers->fetch_assoc()) { ?>
                                    <option value="<?= $row['id']; ?>">
                                        <?= $row['name']; ?> 
                                        (<?= $row['sales_rep_name'] ? "Assigned to: " . $row['sales_rep_name'] : "Unassigned"; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Select Sales Rep -->
                        <div class="mb-3">
                            <label class="form-label">Assign to Sales Representative</label>
                            <select name="sales_rep_id" class="form-select" required>
                                <?php while ($rep = $sales_reps_all->fetch_assoc()) { ?>
                                    <option value="<?= $rep['id']; ?>"><?= $rep['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary w-100">Assign Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
