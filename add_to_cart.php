<?php
session_start();
ob_start(); // Start output buffering to avoid unexpected output

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Ensure JSON response format
    ob_clean(); // Clean any unwanted output before sending JSON
    
    $productName = $_POST['product_name'] ?? '';
    $productPrice = $_POST['product_price'] ?? '';
    $source = $_POST['source'] ?? '';

    if (!$productName || !$productPrice || !$source) {
        echo json_encode(["error" => "Invalid product data."]);
        exit;
    }

    // Determine the correct session key
    $cartKey = ($source === 'index') ? 'index_cart' : 'clearance_cart';

    // Initialize cart session if not set
    if (!isset($_SESSION[$cartKey])) {
        $_SESSION[$cartKey] = [];
    }

    // Add product to session cart
    if (isset($_SESSION[$cartKey][$productName])) {
        $_SESSION[$cartKey][$productName]['quantity'] += 1;
    } else {
        $_SESSION[$cartKey][$productName] = [
            'price' => $productPrice,
            'quantity' => 1
        ];
    }

    $response = [
        "message" => "Added $productName to cart.",
        "quantity" => $_SESSION[$cartKey][$productName]['quantity'],
        "cart" => $_SESSION[$cartKey]
    ];
    
    echo json_encode($response);
    exit;
} else {
    echo json_encode(["error" => "Invalid request."]);
    exit;
}
