<?php
require_once 'config.php';
header('Content-Type: application/json');

// Use an associative array for the cart: product_id => ['quantity' => X]
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($product_id > 0 && $quantity > 0) {
        // Check current stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $current_quantity_in_cart = $_SESSION['cart'][$product_id]['quantity'] ?? 0;
            $new_total_quantity = $current_quantity_in_cart + $quantity;

            if ($product['stock'] >= $new_total_quantity) {
                $_SESSION['cart'][$product_id] = ['quantity' => $new_total_quantity];
                
                // Calculate new total item count for header update
                $total_items = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $total_items += $item['quantity'];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Product added to cart',
                    'cart_count' => $total_items
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
