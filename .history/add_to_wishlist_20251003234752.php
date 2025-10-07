<?php
require_once 'config.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your wishlist.', 'action' => 'login_required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

try {
    // Check if the item is already in the wishlist
    $check_stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->execute([$user_id, $product_id]);
    $existing_item = $check_stmt->fetch();

    if ($existing_item) {
        // If it exists, remove it
        $delete_stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete_stmt->execute([$user_id, $product_id]);
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist.']);
    } else {
        // If it does not exist, add it
        $insert_stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert_stmt->execute([$user_id, $product_id]);
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist.']);
    }

} catch (PDOException $e) {
    // Log the error for debugging, but don't show it to the user
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again.']);
}
?>

