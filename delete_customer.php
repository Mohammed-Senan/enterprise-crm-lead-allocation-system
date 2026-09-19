<?php
include "session.php";
include "db.php";

if ($_SESSION['role'] != "Admin") {
    header("Location: customer.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: customer.php");
    exit();
}

$customer_id = $_GET['id'];

// 1. Delete interactions
$conn->query("DELETE FROM interactions WHERE customer_id = $customer_id");

// 2. Delete leads
$conn->query("DELETE FROM leads WHERE customer_id = $customer_id");

// 3. Delete customer
$conn->query("DELETE FROM customers WHERE id = $customer_id");

header("Location: customer.php?deleted=1");
exit();
?>
