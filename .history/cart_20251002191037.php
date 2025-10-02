<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sample cart items (in production, this would come from database)
$cart_items = [
    [
        'id' => 1,
        'name' => 'Blood Pressure Monitor',
        'price' => 45.99,
        'quantity' => 2,
        'image' => 'blood-pressure.jpg',
        'sku' => 'BPM-001'
    ],
    [
        'id' => 3,
        'name' => 'Pulse Oximeter',
        'price' => 29.99,
        'quantity' => 1,
        'image' => 'oximeter.jpg',
        'sku' => 'POX-003'
    ],
];

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 100 ? 0 : 10;
$tax = $subtotal * 0.075; // 7.5% VAT
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Hospice Medical Supplies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
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

        <?php if (count($cart_items) > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Cart Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                            <div class="col-span-6">Product</div>
                            <div class="col-span-2 text-center">Price</div>
                            <div class="col-span-2 text-center">Quantity</div>
                            <div class="col-span-2 text-right">Total</div>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    <div class="divide-y">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="p-6">
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <!-- Product Info -->
                                <div class="col-span-6 flex items-center space-x-4">
                                    <div class="bg-gray-200 w-20 h-20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-gray-400 text-xs">Image</span>
                                    </div>
                                    <div>
                                        <a href="product.php?id=<?php echo $item['id']; ?>" class="font-semibold text-gray-800 hover:text-teal-600">
                                            <?php echo $item['name']; ?>
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">SKU: <?php echo $item['sku']; ?></p>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="col-span-2 text-center">
                                    <p class="font-semibold text-gray-800">₦<?php echo number_format($item['price'], 2); ?></p>
                                </div>

                                <!-- Quantity -->
                                <div class="col-span-2 flex justify-center">
                                    <div class="flex items-center border-2 border-gray-300 rounded-lg">
                                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" class="px-3 py-1 hover:bg-gray-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1" class="w-12 text-center border-0 focus:outline-none font-semibold" readonly>
                                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" class="px-3 py-1 hover:bg-gray-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-span-2 text-right">
                                    <p class="font-bold text-gray-800">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                    <button onclick="removeItem(<?php echo $item['id']; ?>)" class="text-sm text-red-600 hover:text-red-700 mt-2">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-6">
                    <a href="shop.php" class="inline-flex items-center text-teal-600 hover:text-teal-700 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold">₦<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-semibold"><?php echo $shipping > 0 ? '₦' . number_format($shipping, 2) : 'FREE'; ?></span>
                        </div>
                        <?php if ($subtotal < 100): ?>
                        <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded">
                            Add ₦<?php echo number_format(100 - $subtotal, 2); ?> more to get FREE shipping
                        </p>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (VAT 7.5%)</span>
                            <span class="font-semibold">₦<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold text-gray-800">
                                <span>Total</span>
                                <span>₦<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Promo Code</label>
                        <div class="flex space-x-2">
                            <input 
                                type="text" 
                                placeholder="Enter code" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                            <button class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition font-medium">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="checkout.php" class="block w-full bg-teal-600 text-white text-center py-4 rounded-lg font-semibold hover:bg-teal-700 transition mb-3">
                        Proceed to Checkout
                    </a>
                    <button class="w-full border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Update Cart
                    </button>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span>Secure Checkout</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Safe Payment</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                <span>Fast Delivery</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-32 h-32 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet</p>
            <a href="shop.php" class="inline-block bg-teal-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-teal-700 transition">
                Start Shopping
            </a>
        </div>
        <?php endif; ?>

        <!-- You May Also Like -->
        <?php if (count($cart_items) > 0): ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $suggested_products = [
                    ['id' => 8, 'name' => 'First Aid Kit Complete', 'price' => 34.99, 'rating' => 4.6],
                    ['id' => 5, 'name' => 'Walking Cane Adjustable', 'price' => 19.99, 'rating' => 4.4],
                    ['id' => 7, 'name' => 'Nebulizer Machine', 'price' => 79.99, 'rating' => 4.5],
                    ['id' => 2, 'name' => 'Digital Thermometer', 'price' => 12.99, 'rating' => 4.8],
                ];
                foreach ($suggested_products as $product):
                ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <div class="relative">
                        <div class="bg-gray-200 h-48 flex items-center justify-center">
                            <span class="text-gray-400">Product Image</span>
                        </div>
                        <button class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-5 h-5 text-gray-600 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block">
                            <?php echo $product['name']; ?>
                        </a>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <svg class="w-4 h-4 <?php echo $i < floor($product['rating']) ? 'fill-current' : 'fill-gray-300'; ?>" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <span class="text-xs text-gray-500 ml-2"><?php echo $product['rating']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xl font-bold text-gray-800">₦<?php echo number_format($product['price'], 2); ?></p>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
        function updateQuantity(productId, change) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&change=' + change
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function addToCart(productId) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                }
            });
        }
    </script>

</body>
</html>