<?php
// Start session and connect to database
include "session.php";
include "db.php";

// Redirect if no customer ID is provided
if (!isset($_GET['id'])) {
    header("Location: customer.php");
    exit();
}

$id = $_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and assign submitted form data
    $name = $_POST['name'];
    $company = $_POST['company'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE customers SET name = ?, company = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $company, $email, $phone, $address, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect to customer list with update success message
    header("Location: customer.php?updated=1");
    exit();
}

// Fetch existing customer data for the form
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Customer - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom form validation styles -->
    <style>
        .invalid-feedback {
            display: none;
        }
        input:invalid, textarea:invalid {
            border-color: #dc3545;
        }
        input:invalid ~ .invalid-feedback,
        textarea:invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body class="bg-light">

<!-- Main container -->
<div class="container mt-5">
    <h2 class="text-center">Edit Customer</h2>

    <!-- Customer edit form -->
    <form method="POST" class="w-50 mx-auto p-4 border bg-white rounded shadow-sm needs-validation" novalidate>
        
        <!-- Name input with validation -->
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']); ?>" 
                required pattern="^[A-Za-z\s]+$"
                oninvalid="this.setCustomValidity('Please enter a valid name using letters only.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Please enter a valid name using letters only.</div>
        </div>

        <!-- Company input -->
        <div class="mb-3">
            <label class="form-label">Company</label>
            <input type="text" name="company" class="form-control" value="<?= htmlspecialchars($customer['company']); ?>">
        </div>

        <!-- Email input with validation -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']); ?>" 
                required
                oninvalid="this.setCustomValidity('Please enter a valid email address.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>

        <!-- Phone input with validation -->
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']); ?>" 
                pattern="^[0-9+\-\s\(\)]*$"
                oninvalid="this.setCustomValidity('Phone can only contain numbers, +, -, and brackets.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Phone can only contain numbers, +, -, and brackets.</div>
        </div>

        <!-- Address textarea -->
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($customer['address']); ?></textarea>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary w-100">Update Customer</button>
    </form>
</div>

<!-- JavaScript for Bootstrap and client-side validation -->
<script>
// Bootstrap form validation
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
