<?php
// Include session and DB connection
include "session.php";
include "db.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $company = $_POST['company'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Insert new customer into database
    $stmt = $conn->prepare("INSERT INTO customers (name, company, email, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $company, $email, $phone, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    // Create a new lead for the customer with default "New" status
    $stmt_lead = $conn->prepare("INSERT INTO leads (customer_id, status) VALUES (?, 'New')");
    $stmt_lead->bind_param("i", $customer_id);
    $stmt_lead->execute();
    $stmt_lead->close();

    // Redirect after successful insertion
    header("Location: customer.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Customer - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Custom validation styling */
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
<div class="container mt-5">
    <h2 class="text-center">Add New Customer</h2>
    
    <!-- Customer form -->
    <form method="POST" class="w-50 mx-auto p-4 border bg-white rounded shadow-sm needs-validation" novalidate>
        <!-- Name input -->
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control"
                pattern="^[A-Za-z\s]+$"
                oninvalid="this.setCustomValidity('Please enter a valid name using letters only.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Please enter a valid name using letters only.</div>
        </div>

        <!-- Company input -->
        <div class="mb-3">
            <label class="form-label">Company</label>
            <input type="text" name="company" class="form-control">
        </div>

        <!-- Email input -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                oninvalid="this.setCustomValidity('Please enter a valid email address.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>

        <!-- Phone input -->
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
                pattern="^[0-9+\-\s\(\)]*$"
                oninvalid="this.setCustomValidity('Phone can only contain numbers, +, -, and brackets.')"
                oninput="this.setCustomValidity('')">
            <div class="invalid-feedback">Phone can only contain numbers, +, -, and brackets.</div>
        </div>

        <!-- Address input -->
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-success w-100">Add Customer</button>
    </form>
</div>

<!-- Form validation script -->
<script>
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

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
