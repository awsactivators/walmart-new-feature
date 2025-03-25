<?php
  include('auth.php'); 
  include('connection.php'); 

  $user_id = $_SESSION['user_id'] ?? 1;

  $stmt = $connect->prepare("
    SELECT u.email, a.first_name, a.postal_code, a.street_address, a.province
    FROM users u
    LEFT JOIN addresses a ON u.user_id = a.user_id
    WHERE u.user_id = ?
    LIMIT 1
  ");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($email, $first_name, $postal_code, $street_address, $province);
  $stmt->fetch();
  $stmt->close();

  // fallback values
  $first_name = $first_name ?: "User";
  $postal_code = $postal_code ?: "L4Y 1N6";
  $street_address = $street_address ?: "1234 Address st";
  $province = $province ?: "Toronto, ON";
  $full_address = "$street_address, $province, $postal_code";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../css/cart.css">

  <title>Cart</title>
</head>
<body>
  <header>
    <div class="cart-header">
      <a href=""><img src="../assets/Back.png" alt="Back arrow"></a>
      <h1>Cart</h1>
    </div>
    <div class="location-bar">
      <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
      <i class="fa-solid fa-caret-down"></i>
    </div>
  </header>

  <main>
    <section class="cart-status">
      <p>You have 2 items in your cart</p>
    </section>

    <!-- Cart Items -->
    <section class="cart-items">
      <div class="cart-card">
        <img src="../assets/kiwi.jpg" alt="Kiwi">
        <div class="cart-details">
          <h3>Kiwis</h3>
          <div class="price-quantity">
            <div class="price-text">
              <p class="subtext">11.9 ¢/oz</p>
              <p class="price">$2.39</p>
            </div>
            <div class="quantity">
              <button>-</button>
              <span>1</span>
              <button>+</button>
            </div>
          </div>
          <div class="actions">
            <a href="#">Remove</a>
            <a href="#">Save for later</a>
          </div>
        </div>
      </div>

      <div class="cart-card">
        <img src="../assets/orange.jpg" alt="Orange">
        <div class="cart-details">
          <h3>Oranges</h3>
          <div class="price-quantity">
            <div class="price-text">
              <p class="subtext">11.9 ¢/oz</p>
              <p class="price">$2.39</p>
            </div>
            <div class="quantity">
              <button>-</button>
              <span>1</span>
              <button>+</button>
            </div>
          </div>
          <div class="actions">
            <a href="#">Remove</a>
            <a href="#">Save for later</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Suggestions -->
    <section class="suggestions">
      <h4>Suggested with your order</h4>
      <p>Earn more points!</p>
      <div class="products">
        <div class="product">
          <div class="points-badge"><span class="dot"></span> 250</div>
          <img src="../assets/strawberry.jpg" alt="Strawberry">
          <div class="product-name">
            <p>Strawberry</p>
          </div>
          <div class="price-container">
            <div class="price-col">
              <p class="price">$2.39</p>
              <p class="price-per-unit">11.9¢/oz</p>
            </div>
            <div>
              <button class="add-to-cart">+</button>
            </div>
          </div>
        </div>
        <div class="product">
          <div class="points-badge"><span class="dot"></span> 250</div>
          <img src="../assets/pomegranate.jpg" alt="Pomegranate">
          <div class="product-name">
            <p>Pomegranate</p>
          </div>
          <div class="price-container">
            <div class="price-col">
              <p class="price">$3.19</p>
              <p class="price-per-unit">11.9¢/oz</p>
            </div>
            <div>
              <button class="add-to-cart">+</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cart Summary -->
    <section class="summary">
      <div class="subtotal-tax">
        <span>Subtotal</span>
        <span>$499.60</span>
      </div>
      <div class="subtotal-tax">
        <span>Taxes</span>
        <span>$41.40</span>
      </div>
      <hr>
      <div class="subtotal-tax total">
        <strong>Total</strong>
        <strong>$541.00</strong>
      </div>
      <a href="payment.php"><button class="pay-btn">Proceed to Payment</button></a>
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
