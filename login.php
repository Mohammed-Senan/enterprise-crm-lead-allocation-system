<?php
// Start session and include database connection
session_start();
include "db.php";

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve user input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare SQL query to fetch user by email
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    // Check if user exists and password matches
    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        // Redirect to dashboard on successful login
        header("Location: dashboard.php");
        exit();
    } else {
        // Set error message if authentication fails
        $error = "Invalid email or password.";
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page metadata -->
    <title>Login - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">ABB Robotics</a>
        <div>
            <a href="register.php" class="btn btn-light">Register</a>
        </div>
    </div>
</nav>

<!-- Main content -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <!-- Login card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-3">Login</h3>

                    <!-- Display error message if login fails -->
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <!-- Login form -->
                    <form method="POST" novalidate>
                        <!-- Email input -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required
                                    oninvalid="this.setCustomValidity('Please enter a valid email address')"
                                    oninput="this.setCustomValidity('')">
                        </div>

                        <!-- Password input -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="4"
                                    oninvalid="this.setCustomValidity('Password must be at least 4 characters')"
                                    oninput="this.setCustomValidity('')">
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <!-- Register link -->
                    <p class="mt-3 text-center">
                        Don't have an account? <a href="register.php">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
