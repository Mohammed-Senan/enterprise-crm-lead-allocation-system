<?php
include "session.php";
include "db.php";

// Only Admins can delete users
if ($_SESSION['role'] != "Admin") {
    header("Location: dashboard.php");
    exit();
}

// Check if id is provided
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = intval($_GET['id']);

// Prevent admin from deleting themselves
if ($_SESSION['user_id'] == $id) {
    header("Location: users.php?error=cannot_delete_self");
    exit();
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: users.php?deleted=1");
} else {
    header("Location: users.php?error=deletion_failed");
}
$stmt->close();
?>
