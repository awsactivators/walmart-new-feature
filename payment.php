<?php
  include('auth.php'); 
  include('connection.php');

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

  $first_name = $first_name ?: "User";
  $postal_code = $postal_code ?: "L4Y 1N6";
  $street_address = $street_address ?: "1234 Address st";
  $province = $province ?: "Toronto, ON";
  $full_address = "$street_address, $province, $postal_code";

  $cart = $_SESSION['cart'] ?? [];
  $clearance_cart = $_SESSION['clearance_cart'] ?? [];

  function calculateCartTotals($items) {
    $totals = ['subtotal' => 0, 'points' => 0];
    foreach ($items as $item) {
      $price = isset($item['price']) ? floatval($item['price']) : 0;
      $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
      $points = isset($item['points']) ? intval($item['points']) : 0;

      $totals['subtotal'] += $price * $quantity;
      $totals['points'] += $points * $quantity;
    }
    return $totals;
  }

  $totals_cart = calculateCartTotals($cart);
  $totals_clearance = calculateCartTotals($clearance_cart);

  $full_subtotal = $totals_cart['subtotal'] + $totals_clearance['subtotal'];
  $points_to_receive = $totals_cart['points'] + $totals_clearance['points'];

  $stmt = $connect->prepare("SELECT point FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($user_points);
  $stmt->fetch();
  $stmt->close();

  $redeemable_dollars = min($user_points / 1000, $full_subtotal);
  $redeemed_points = floor($redeemable_dollars * 1000);

  $subtotal = $full_subtotal - $redeemable_dollars;
  $tax = round($subtotal * 0.13, 2);
  $total = round($subtotal + $tax, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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

    <section class="item-details">
      <div class="item-header">
        <h4>Item Details</h4>
        <a href="#">View All</a>
      </div>
      <div class="item-images">
        <?php
          $all_cart_items = array_merge(
            array_map(fn($item) => ['type' => 'regular', 'data' => $item], $cart),
            array_map(fn($item) => ['type' => 'clearance', 'data' => $item], $clearance_cart)
          );

          $displayed = 0;
          if (!empty($all_cart_items)) {
            foreach ($all_cart_items as $itemWrapper) {
              if ($displayed >= 4) break;
              $item = $itemWrapper['data'];
              $image = $item['image'] ?? null;
              if ($image) {
                echo '<div class="item-img"><img src="' . htmlspecialchars($image) . '" alt="Product Image"></div>';
                $displayed++;
              }
            }
          } else {
            echo '<p>No items in cart</p>';
          }
        ?>
      </div>
    </section>

    <section class="payment-method">
      <h4>Payment Method</h4>
      <label>
        <input type="radio" name="payment" value="card" checked /> Credit / Debit Card
      </label>
      <label>
        <input type="radio" name="payment" value="paypal" /> Paypal
      </label>
      <label>
        <input type="radio" name="payment" value="apple_pay" /> Pay with Apple Pay
      </label>
    </section>


    <section class="redeem-points">
      <label>
        <input type="checkbox" id="redeem-check" />
        Redeem Points (<?php echo $user_points; ?> Points)
      </label>
    </section>

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
        <span id="redeemed-val">$<?php echo number_format($redeemable_dollars, 2); ?></span>
      </div>
      <hr />
      <div class="row total">
        <strong>Total</strong>
        <strong id="final-total">$<?php echo number_format($total, 2); ?></strong>
      </div>
    </section>

    <form action="confirm_order.php" method="POST">
      <input type="hidden" name="redeemed_points" id="redeemed-points" value="<?php echo $redeemed_points; ?>" />
      <input type="hidden" name="points_to_receive" value="<?php echo $points_to_receive; ?>" />
      <input type="hidden" name="total" id="hidden-total" value="<?php echo number_format($total, 2); ?>" />
      <button type="submit" class="confirm-btn">Confirm Order</button>
      <a href="./cart.php" class="back-to-cart">Back to Cart</a>
    </form>
  </main>

  <script>
    const subtotalOriginal = parseFloat(<?php echo $full_subtotal; ?>);
    const userPoints = parseInt(<?php echo $user_points; ?>);

    const redeemCheckbox = document.getElementById('redeem-check');
    const subtotalField = document.getElementById('subtotal');
    const taxField = document.getElementById('tax');
    const redeemedVal = document.getElementById('redeemed-val');
    const finalTotal = document.getElementById('final-total');
    const hiddenTotal = document.getElementById('hidden-total');
    const redeemedPointsField = document.getElementById('redeemed-points');

    function updateTotals() {
      let redeemed = 0;
      if (redeemCheckbox.checked) {
        redeemed = Math.min(userPoints / 1000, subtotalOriginal);
      }

      const newSubtotal = subtotalOriginal - redeemed;
      const tax = +(newSubtotal * 0.13).toFixed(2);
      const total = +(newSubtotal + tax).toFixed(2);

      subtotalField.textContent = `$${newSubtotal.toFixed(2)}`;
      taxField.textContent = `$${tax.toFixed(2)}`;
      redeemedVal.textContent = `$${redeemed.toFixed(2)}`;
      finalTotal.textContent = `$${total.toFixed(2)}`;
      hiddenTotal.value = total.toFixed(2);
      redeemedPointsField.value = Math.floor(redeemed * 1000);
    }

    redeemCheckbox.addEventListener('change', updateTotals);
    document.addEventListener('DOMContentLoaded', updateTotals);
  </script>
</body>
</html>
