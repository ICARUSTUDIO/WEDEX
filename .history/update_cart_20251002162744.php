<?php
session_start();
header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $change = isset($_POST['change']) ? intval($_POST['change']) : 0;

    if ($product_id > 0) {
        // Find and update the product quantity
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $change;
                
                // Remove if quantity is 0 or less
                if ($item['quantity'] <= 0) {
                    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($cart_item) use ($product_id) {
                        return $cart_item['product_id'] != $product_id;
                    });
                    // Reindex array
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart_count' => count($_SESSION['cart'])
                ]);
                exit;
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'Product not found in cart'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>