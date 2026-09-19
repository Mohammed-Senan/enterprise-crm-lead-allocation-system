<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$user_role = $_SESSION['role'];
?>
