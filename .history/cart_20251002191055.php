<?php
require_once 'config.php';

$cart_items = [];
$subtotal = 0;
$product_ids_in_cart = [];

// Check if the cart session exists and is not empty
if (!empty($_SESSION['cart'])) {
    // Get all product IDs from the cart
    $product_ids = array_keys($_SESSION['cart']);
    
    // Prepare a placeholder string for the IN clause (e.g., ?,?,?)
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Fetch all products from the database that are in the cart
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    $products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a final cart items array with full details and calculate subtotal
    foreach ($products_in_cart as $product) {
        $product_id = $product['id'];
        $quantity = $_SESSION['cart'][$product_id]['quantity'];
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'sku' => $product['sku'],
            'stock' => $product['stock'],
            'quantity' => $quantity,
        ];
        $subtotal += $product['price'] * $quantity;
        $product_ids_in_cart[] = $product_id;
    }
}

// Calculate totals
$shipping = $subtotal > 200000 ? 0 : 5000; // Free shipping on orders over ₦200,000
$tax_rate = 0.075; // 7.5% VAT
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;


// Fetch 4 random products for "You May Also Like", excluding items already in the cart
$suggested_products = [];
if (!empty($product_ids_in_cart)) {
    $placeholders_suggest = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
    $suggest_sql = "SELECT * FROM products WHERE id NOT IN ($placeholders_suggest) ORDER BY RAND() LIMIT 4";
    $suggest_stmt = $pdo->prepare($suggest_sql);
    $suggest_stmt->execute($product_ids_in_cart);
    $suggested_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If cart is empty, just get any 4 random products
    $suggest_stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
    $suggested_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Shopping Cart</span>
            </nav>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

        <?php if (!empty($cart_items)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="divide-y">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="p-6 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                            <div class="flex-1 text-center md:text-left">
                                <a href="product.php?id=<?php echo $item['id']; ?>" class="font-semibold text-gray-800 hover:text-teal-600"><?php echo htmlspecialchars($item['name']); ?></a>
                                <p class="text-sm text-gray-500 mt-1">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                            </div>
                            <div class="flex items-center border rounded-lg">
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" class="px-3 py-1 hover:bg-gray-100 transition">-</button>
                                <input type="number" value="<?php echo $item['quantity']; ?>" class="w-12 text-center border-0 focus:outline-none font-semibold" readonly>
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" class="px-3 py-1 hover:bg-gray-100 transition">+</button>
                            </div>
                            <div class="w-24 text-center">
                                <p class="font-bold text-gray-800">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                            <button onclick="removeItem(<?php echo $item['id']; ?>)" class="text-red-600 hover:text-red-700">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
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
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet.</p>
            <a href="shop.php" class="inline-block bg-teal-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Start Shopping</a>
        </div>
        <?php endif; ?>

        <!-- You May Also Like -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($suggested_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                     <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block h-12"><?php echo htmlspecialchars($product['name']); ?></a>
                        <div class="flex items-center justify-between">
                            <p class="text-xl font-bold text-gray-800">₦<?php echo number_format($product['price'], 2); ?></p>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
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
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }

        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
        
        function addToCart(productId) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>

