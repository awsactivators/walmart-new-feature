<?php
include('auth.php'); 
include('connection.php');

$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $connect->prepare("
  SELECT a.first_name 
  FROM addresses a 
  WHERE a.user_id = ?
  LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

$first_name = $first_name ?: "User";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile | Walmart</title>
  <link rel="stylesheet" href="./css/profile.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
</head>
<body>

  <header class="profile-header">
    <h1>Hi <?php echo htmlspecialchars($first_name); ?></h1>
    <p>Thanks for being a walmart customer</p>
    <i class="fi fi-br-check"></i>
  </header>

  <main class="profile-main">
    <section class="profile-section card">
      <a href="#">
        <div class="icon-title">
          <i class="fa-regular fa-calendar"></i>
          <div>
            <h3>Purchase History</h3>
            <p>Track your order status, start a return, or view purchases history and receipts</p>
          </div>
        </div>
        <i class="fa-solid fa-chevron-right"></i>
      </a>
    </section>

    <section class="profile-section card">
      <a href="#">
        <div class="icon-title">
          <i class="fa-regular fa-wallet"></i>
          <div>
            <h3>Wallet</h3>
            <p>Manage your payment methods and learn about reward card, payment tools, and more.</p>
          </div>
        </div>
        <i class="fa-solid fa-chevron-right"></i>
      </a>
    </section>

    <section class="profile-section card">
      <h4>Manage Account</h4>
      <a href="#">
        <div class="icon-title">
          <i class="fa-regular fa-user"></i>
          <p>Personal Info</p>
        </div>
        <i class="fa-solid fa-chevron-right"></i>
      </a>
      <a href="#">
        <div class="icon-title">
          <i class="fa-solid fa-location-dot"></i>
          <p>Addresses</p>
        </div>
        <i class="fa-solid fa-chevron-right"></i>
      </a>
    </section>

    <section class="profile-section  sign-out">
      <a href="logout.php">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
      </a>
    </section>
  </main>

  <footer class="bottom-nav">
    <div>
      <a href="./index.php"><i class="fa-solid fa-house"></i></a>
      <p>Home</p>
    </div>
    <div>
      <a href="./index.php"><i class="fa-solid fa-shapes"></i></a>
      <p>Categories</p>
    </div>
    <div>
      <a href="./points.php"><i class="fa-solid fa-piggy-bank"></i></a>
      <p>My Points</p>
    </div>
    <div>
      <a href="./cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
      <p>Cart</p>
    </div>
    <div>
      <a href="./profile.php"><i class="fa-solid fa-user"></i></a>
      <p>Profile</p>
    </div>
  </footer>

</body>
</html>
