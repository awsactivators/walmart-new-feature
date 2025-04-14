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

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $image = $_POST['image'];
    $price = $_POST['price'];
    $price_per_unit = $_POST['price_per_unit'];
    $points = $_POST['points'] ?? 0;

    if (!isset($_SESSION['clearance_cart'])) {
        $_SESSION['clearance_cart'] = [];
    }

    if (isset($_SESSION['clearance_cart'][$product_id])) {
        $_SESSION['clearance_cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['clearance_cart'][$product_id] = [
            'name' => $name,
            'image' => $image,
            'price' => $price,
            'price_per_unit' => $price_per_unit,
            'points' => $points,
            'quantity' => 1
        ];
    }
    

    // If it's an AJAX call, return JSON and exit
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit;
    }
  }
  // Dynamic cart calculations
  $item_count = 0;
  $subtotal = 0;
  $tax_rate = 0.13;
  $points_earned = 0;
  
  foreach (['cart', 'clearance_cart'] as $type) {
    if (!empty($_SESSION[$type])) {
      foreach ($_SESSION[$type] as $item) {
        $qty = $item['quantity'] ?? 1;
        $price = $item['product_price'] ?? $item['price'] ?? 0;
        $item_count += $qty;
        $subtotal += $price * $qty;

        if ($type === 'clearance_cart') {
          $points = $item['points'] ?? 0;
          $points_earned += $points * $qty;
        }
      }
    }
  }

  $tax = $subtotal * $tax_rate;
  $total = $subtotal + $tax;

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
  <link rel="stylesheet" href="./css/cart.css">

  <title>Cart</title>
</head>
<body>
  <header>
    <div class="cart-header">
      <a href=""><img src="./assets/Back.png" alt="Back arrow"></a>
      <h1>Cart</h1>
    </div>
    <div class="location-bar">
      <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
      <i class="fa-solid fa-caret-down"></i>
    </div>
  </header>

  <main>
    <section class="cart-status">
      <p><?php echo "You have {$item_count} items in your cart"; ?></p>
    </section>

    <!-- Cart Items -->
    <section class="cart-items">
    <?php
      function renderCartItems($cartArray, $type) {
        foreach ($cartArray as $product_id => $item) {
          $name = $item['product_name'] ?? $item['name'];
          $image = $item['product_image'] ?? $item['image'];
          $price = $item['product_price'] ?? $item['price'];
          $price_per_unit = $item['price_per_unit'] ?? '11.9¢/oz';
          $quantity = $item['quantity'] ?? 1;

          echo '<div class="cart-card" id="' . $type . '-' . $product_id . '">';
          echo '<img src="' . $image . '" alt="' . $name . '">';
          echo '<div class="cart-details">';
          echo '<h3>' . $name . '</h3>';
          echo '<div class="price-quantity">';
          echo '<div class="price-text">';
          echo '<p class="subtext">' . $price_per_unit . '</p>';
          echo '<p class="price">$' . $price . '</p>';
          echo '</div>';
          if (isset($item['points'])&& $item['points'] >0) {
            echo '<div class="points-badge-clearance"><span class="dot"></span> ' . $item['points'] . '</div>';
          }
          echo '<div class="quantity">';
          echo '<button onclick="updateQuantity(\'' . $product_id . '\', \'' . $type . '\', \'decrement\')">-</button>';
          echo '<span id="qty-' . $type . '-' . $product_id . '">' . $quantity . '</span>';
          echo '<button onclick="updateQuantity(\'' . $product_id . '\', \'' . $type . '\', \'increment\')">+</button>';
          echo '</div>';
          echo '</div>';
          echo '<div class="actions">';
          echo '<a href="#">Remove</a>';
          echo '<a href="#">Save for later</a>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }
      }

      if (!empty($_SESSION['cart'])) {
        renderCartItems($_SESSION['cart'], 'cart');
      }

      if (!empty($_SESSION['clearance_cart'])) {
        renderCartItems($_SESSION['clearance_cart'], 'clearance_cart');
      }
      ?>
    </section>

    <!-- Suggestions -->
    <section class="suggestions">
      <h4>Suggested with your order</h4>
      <p>Earn more points!</p>
      <div class="products">
        <div class="product">
          <div class="points-badge"><span class="dot"></span> 250</div>
          <img src="./assets/strawberry.jpg" alt="Strawberry">
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
          <img src="./assets/pomegranate.jpg" alt="Pomegranate">
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
        <span><?php echo "$" . number_format($subtotal, 2); ?></span>
      </div>
      <div class="subtotal-tax">
        <span>Taxes</span>
        <span><?php echo "$" . number_format($tax, 2); ?></span>
      </div>
      <div class="subtotal-tax">
        <span>Points Earned:</span>
        <span><?php echo  "{$points_earned}"; ?></span>
      </div>
      <hr>
      <div class="subtotal-tax total">
        <strong>Total</strong>
        <strong><?php echo "$" . number_format($total, 2); ?></strong>
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
  <script>
    function updateQuantity(productId, cartType, action) {
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('cart_type', cartType);
      formData.append('action', action);

      fetch('update_cart.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.removed) {
          const el = document.getElementById(cartType + '-' + productId);
          if (el) el.remove();
        } else {
          document.getElementById('qty-' + cartType + '-' + productId).textContent = data.quantity;
        }
        // Update cart count
        document.querySelector('.cart-status p').textContent = `You have ${data.item_count} items in your cart`;

        // Update subtotal/tax/total
        document.querySelector('.summary .subtotal-tax:nth-of-type(1) span:last-child').textContent = `$${data.subtotal}`;
        document.querySelector('.summary .subtotal-tax:nth-of-type(2) span:last-child').textContent = `$${data.tax}`;
        document.querySelector('.summary .total strong:last-child').textContent = `$${data.total}`;
        const pointsEl = document.getElementById('points-earned');
        if (pointsEl) {
          pointsEl.textContent = `Points Earned: ${data.points_earned}`;
        }
      })
      .catch(err => console.error("Error updating cart:", err));
    }
    document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', function () {
    const productCard = this.closest('.product');
    const name = productCard.querySelector('.product-name p').textContent;
    const image = productCard.querySelector('img').getAttribute('src');
    const price = productCard.querySelector('.price').textContent.replace('$', '').trim();
    const pricePerUnit = productCard.querySelector('.price-per-unit')?.textContent || '';
    const points = parseInt(productCard.querySelector('.points-badge')?.textContent.replace(/\D/g, '') || '0');

    const formData = new FormData();
    formData.append('product_id', name.toLowerCase().replace(/\s+/g, '_'));
    formData.append('name', name);
    formData.append('image', image);
    formData.append('price', price);
    formData.append('price_per_unit', pricePerUnit);
    formData.append('points', points);

    fetch('cart.php', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest' 
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        location.reload(); // Reload to reflect cart updates
      }
    })
    .catch(err => console.error(err));
  });
});
  </script>
</body>
</html>
