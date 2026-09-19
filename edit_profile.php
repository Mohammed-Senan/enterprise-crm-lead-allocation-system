<?php
include "session.php";
include "db.php";

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    // Basic validations
    if (!preg_match("/^[a-zA-Z\s]+$/", $new_name)) {
        $error = "Name can only contain letters and spaces.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        if (!empty($new_password)) {
            // Update with new password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $new_name, $new_email, $hashed, $user_id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
        }
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            $name = $new_name;
            $email = $new_email;
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile - ABB Robotics</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">ABB Robotics</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Edit Your Profile</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm bg-white mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name); ?>" class="form-control" required pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back to Dashboard</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
