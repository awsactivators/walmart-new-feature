<?php
  include('auth.php'); 
  include('connection.php');

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }
  if (!isset($_SESSION['clearance_cart'])) {
    $_SESSION['clearance_cart'] = [];
  }

  $user_id = $_SESSION['user_id'] ?? 1;

  // Fetch user info
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

  // Load products
  $productsData = json_decode(file_get_contents('../data/products.json'), true);

  // Merge regular and clearance cart
  $cart = array_merge($_SESSION['cart'], $_SESSION['clearance_cart']);

  $subtotal = 0;
  $points_to_receive = 0;

  foreach ($cart as $item) {
      $product = array_filter($productsData, fn($p) => $p['id'] == $item['id']);
      $product = array_values($product)[0] ?? null;

      if ($product) {
        $subtotal += $product['price'] * $item['quantity'];
        $points_to_receive += $product['points'] * $item['quantity'];
      }
  }

  $tax = round($subtotal * 0.13, 2);

  // Get user points
  $stmt = $connect->prepare("SELECT point FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($user_points);
  $stmt->fetch();
  $stmt->close();

  $redeemable_dollars = min($user_points / 1000, $subtotal); 
  $redeemed_points = floor($redeemable_dollars * 1000); 
  $total = ($subtotal - $redeemable_dollars) + $tax;
?>

<!-- The rest of the HTML remains the same -->




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./css/payment.css" />
  <title>Payment</title>
</head>
<body>
  <header>
    <div class="payment-header">
      <a href=""><img src="./assets/Back.png" alt="Back arrow"></a>
      <h1>Payment</h1>
    </div>
    <div class="location-bar">
      <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
      <i class="fa-solid fa-caret-down"></i>
    </div>
  </header>

  <main>
    <!-- Delivery Address Options -->
    <section class="delivery-address">
      <label class="address-option">
        <div class="address-text">
          <h4>Delivering to <strong><span><?php echo htmlspecialchars($first_name); ?></span></strong></h4>
          <p><?php echo htmlspecialchars($full_address); ?></p>
        </div>
        <input type="radio" name="address" value="default" checked />
      </label>

      <label class="address-option">
        <div class="address-text">
          <h4>Use another address</h4>
          <input type="text" class="alt-address-input" placeholder="93-1234 Address st, Toronto ON, LY4 1N6" disabled />
        </div>
        <input type="radio" name="address" value="custom" />
      </label>
    </section>


    <!-- Item Details -->
    <section class="item-details">
      <div class="item-header">
        <h4>Item Details</h4>
        <a href="#">View All</a>
      </div>
      <div class="item-images">
        <div class="item-img"></div>
        <div class="item-img"></div>
        <div class="item-img"></div>
      </div>
    </section>

    <!-- Payment Method -->
    <section class="payment-method">
      <h4>Payment Method</h4>
      <label>
        <input type="radio" name="payment" />
        Credit / Debit Card
      </label>
      <label>
        <input type="radio" name="payment" />
        Paypal
      </label>
      <label>
        <input type="radio" name="payment" />
        Pay with Apple Pay
      </label>
    </section>

    <!-- Redeem Points -->
    <section class="redeem-points">
      <label>
        <input type="checkbox" id="redeem-check" />
        Redeem Points (<?php echo $user_points; ?> Points)
      </label>
    </section>

    <!-- Summary -->
    <section class="payment-summary">
      <div class="row">
        <span>Points to receive</span>
        <span><?php echo $points_to_receive; ?></span>
      </div>
      <hr />
      <div class="row">
        <span>Subtotal</span>
        <span id="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
      </div>
      <div class="row">
        <span>Taxes</span>
        <span id="tax">$<?php echo number_format($tax, 2); ?></span>
      </div>
      <div class="row">
        <span>Redeemed Points</span>
        <span id="redeemed-val">$0.00</span>
      </div>
      <hr />
      <div class="row total">
        <strong>Total</strong>
        <strong id="final-total">$<?php echo number_format($total, 2); ?></strong>
      </div>
    </section>

    <!-- Submit Form -->
    <form action="confirm_order.php" method="POST">
      <input type="hidden" name="redeemed_points" id="redeemed-points" value="0" />
      <input type="hidden" name="points_to_receive" value="<?php echo $points_to_receive; ?>" />
      <input type="hidden" name="total" id="hidden-total" value="<?php echo $total; ?>" />
      <button type="submit" class="confirm-btn">Confirm Order</button>
      <a href="./cart.php" class="back-to-cart">Back to Cart</a>
    </form>

  </main>

    <script>
    window.orderData = {
      subtotal: <?php echo $subtotal; ?>,
      tax: <?php echo $tax; ?>,
      userPoints: <?php echo $user_points; ?>
    };
  </script>
  <script src="./js/payment.js"></script>


</body>
</html>
