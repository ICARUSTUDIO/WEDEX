<?php
require_once 'config.php';

$cart_items_details = [];
$subtotal = 0;

// Check if cart exists and is not empty
if (!empty($_SESSION['cart'])) {
    // Get all product IDs from the cart
    $product_ids = array_keys($_SESSION['cart']);
    
    // Create placeholders for the IN clause (e.g., ?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // Fetch product details from the database for items in the cart
    try {
        $stmt = $pdo->prepare("SELECT id, name, price, image, stock FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products_in_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create a lookup array for products
        $product_map = [];
        foreach ($products_in_db as $product) {
            $product_map[$product['id']] = $product;
        }

        // Build the detailed cart items array and calculate subtotal
        foreach ($_SESSION['cart'] as $product_id => $item) {
            if (isset($product_map[$product_id])) {
                $product = $product_map[$product_id];
                $quantity = $item['quantity'];
                
                // Ensure quantity does not exceed stock
                if ($quantity > $product['stock']) {
                    $quantity = $product['stock'];
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Update session
                }

                if ($quantity > 0) {
                     $cart_items_details[] = [
                        'id' => $product_id,
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity,
                        'stock' => $product['stock']
                    ];
                    $subtotal += $product['price'] * $quantity;
                } else {
                    // Remove item if stock is 0 or quantity becomes 0
                    unset($_SESSION['cart'][$product_id]);
                }
            } else {
                // Product not found in DB, remove from cart
                unset($_SESSION['cart'][$product_id]);
            }
        }
    } catch (PDOException $e) {
        // Handle database error
        die("Could not retrieve cart items.");
    }
}

// Calculate totals
$shipping = $subtotal > 2000 ? 0 : 500; // Free shipping over ₦2000
$tax_rate = 0.075; // 7.5% VAT
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - WEDEX Healthcare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

        <?php if (!empty($cart_items_details)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md">
                <div class="divide-y">
                    <?php foreach ($cart_items_details as $item): ?>
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-20 h-20 object-cover rounded-lg">
                            <div>
                                <a href="product.php?id=<?php echo $item['id']; ?>" class="font-semibold text-gray-800 hover:text-teal-600"><?php echo htmlspecialchars($item['name']); ?></a>
                                <p class="text-sm text-red-600 hover:text-red-700 mt-2 cursor-pointer" onclick="removeItem(<?php echo $item['id']; ?>)">Remove</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center border rounded-lg">
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                                <input type="text" value="<?php echo $item['quantity']; ?>" class="w-12 text-center border-0 focus:ring-0" readonly>
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                            </div>
                            <p class="font-bold text-gray-800 w-24 text-right">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold">₦<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="flex justify-between text-gray-600"><span>Shipping</span><span class="font-semibold"><?php echo $shipping > 0 ? '₦' . number_format($shipping, 2) : 'FREE'; ?></span></div>
                        <div class="flex justify-between text-gray-600"><span>Tax (VAT 7.5%)</span><span class="font-semibold">₦<?php echo number_format($tax, 2); ?></span></div>
                        <div class="border-t pt-4"><div class="flex justify-between text-lg font-bold text-gray-800"><span>Total</span><span>₦<?php echo number_format($total, 2); ?></span></div></div>
                    </div>
                    <a href="checkout.php" class="block w-full bg-teal-600 text-white text-center py-4 rounded-lg font-semibold hover:bg-teal-700 transition">Proceed to Checkout</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything yet.</p>
            <a href="shop.php" class="inline-block bg-teal-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
    function updateQuantity(productId, change) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&change=${change}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload the page to see changes
            } else {
                alert(data.message);
            }
        });
    }

    function removeItem(productId) {
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    </script>
</body>
</html>
