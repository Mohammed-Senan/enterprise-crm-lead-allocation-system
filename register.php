<?php
// Include database connection
include "db.php";

// Initialize message variables
$errorMessage = "";
$successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate password format (min 8 chars, at least one upper, lower, and number)
    if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/", $password)) {
        // Do nothing; error message handled by frontend
    }
    // Validate name format (letters and spaces only)
    elseif (!preg_match("/^[A-Za-z\s]+$/", $name)) {
        // Do nothing; error message handled by frontend
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMessage = "This email is already registered. Please use another.";
        } else {
            // Hash password and insert user into database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $successMessage = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $errorMessage = "Error: " . $conn->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta and Title -->
    <title>Register - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Validation Style -->
    <style>
        .invalid-feedback {
            display: none;
        }
        input:invalid, select:invalid {
            border-color: rgb(0, 0, 0);
        }
        input:invalid ~ .invalid-feedback,
        select:invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body class="bg-light">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">ABB Robotics</a>
        <div>
            <a href="login.php" class="btn btn-light">Login</a>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <!-- Registration Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-3">Register</h3>

                    <!-- Display Feedback Messages -->
                    <?php if (!empty($successMessage)) : ?>
                        <div class="alert alert-success text-center"><?= $successMessage ?></div>
                    <?php elseif (!empty($errorMessage)) : ?>
                        <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- Full Name Field -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required
                                    pattern="^[A-Za-z\s]+$"
                                    oninvalid="this.setCustomValidity('Please enter a valid name using letters only.')"
                                    oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback">Please enter a valid name using letters only.</div>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required
                                    oninvalid="this.setCustomValidity('Enter a valid email address')"
                                    oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback">Enter a valid email address.</div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required
                                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}"
                                    oninvalid="this.setCustomValidity('Password must be at least 8 characters with uppercase, lowercase, and number')"
                                    oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback">Password must be at least 8 characters with uppercase, lowercase, and number.</div>
                        </div>

                        <!-- Role Dropdown -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Sales">Sales Representative</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <!-- Login Link -->
                    <p class="mt-3 text-center">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Validation Script -->
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
