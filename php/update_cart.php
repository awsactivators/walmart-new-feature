<?php
session_start();
header('Content-Type: application/json');

$product_id = $_POST['product_id'] ?? null;
$cart_type = $_POST['cart_type'] ?? null;
$action = $_POST['action'] ?? null;

$response = ['success' => false];

if ($product_id && $cart_type && isset($_SESSION[$cart_type][$product_id])) {
    $item = $_SESSION[$cart_type][$product_id];
    $current_qty = $item['quantity'] ?? 1;

    if ($action === 'increment') {
        $current_qty += 1;
    } elseif ($action === 'decrement') {
        $current_qty -= 1;
    }

    if ($current_qty < 1) {
        unset($_SESSION[$cart_type][$product_id]);
        $response['removed'] = true;
    } else {
        $_SESSION[$cart_type][$product_id]['quantity'] = $current_qty;
        $response['quantity'] = $current_qty;
        $response['removed'] = false;
    }

    $response['success'] = true;
}

// ----- CALCULATE TOTALS -----
$total_items = 0;
$subtotal = 0;
$tax_rate = 0.13;
$points_earned = 0;

foreach (['cart', 'clearance_cart'] as $type) {
    if (!empty($_SESSION[$type])) {
        foreach ($_SESSION[$type] as $item) {
            $qty = $item['quantity'] ?? 1;
            $price = $item['product_price'] ?? $item['price'] ?? 0;
            $total_items += $qty;
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

$response['item_count'] = $total_items;
$response['subtotal'] = number_format($subtotal, 2);
$response['tax'] = number_format($tax, 2);
$response['total'] = number_format($total, 2);
$response['points_earned'] = $points_earned;

echo json_encode($response);
?>
