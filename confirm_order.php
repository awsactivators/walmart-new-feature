<?php
include('auth.php'); 
include('connection.php');

$user_id = $_SESSION['user_id'] ?? 1;

$redeemed_points = $_POST['redeemed_points'];
$points_to_receive = $_POST['points_to_receive'];

// Update user points
// 1. Subtract redeemed points
// 2. Add earned points
$stmt = $connect->prepare("UPDATE users SET point = point - ? + ? WHERE user_id = ?");
$stmt->bind_param("iii", $redeemed_points, $points_to_receive, $user_id);
$stmt->execute();
$stmt->close();

// Clear the cart before redirecting
unset($_SESSION['cart']);
unset($_SESSION['clearance_cart']);

// Set session flag for success page access
$_SESSION['purchase_success'] = true;
$_SESSION['earned_points'] = $points_to_receive;

// Redirect to success
header("Location: success.php");

// Redirect to success
// header("Location: success.php?earned=" . $points_to_receive);
exit();
