<?php
if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'address.php') {
    // Allow address.php to continue for new signups
    header("Location: index.php");
    exit();
}
