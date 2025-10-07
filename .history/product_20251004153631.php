<?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: shop.php");
    exit;
}

// Fetch the main product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // If no product found, redirect or show a 404 page
    http_response_code(404);
    // A simple not found message. You can create a fancier 404.php page and include it here.
    echo "<h1>404 Not Found</h1><p>The product you are looking for does not exist.</p><a href='shop.php'>Go back to shop</a>";
    exit;
}

// Fetch related products from the same category, excluding the current one
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->execute([$product['category'], $product_id]);
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if this product is in the user's wishlist
$is_in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wishlist_stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wishlist_stmt->execute([$_SESSION['user_id'], $product_id]);
    $is_in_wishlist = $wishlist_stmt->fetchColumn() > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - WEDEX Healthcare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <script src="js/notifications.js"></script>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <a href="shop.php" class="text-gray-500 hover:text-teal-600">Shop</a>
                <span class="text-gray-400">/</span>
                <a href="shop.php?category=<?php echo urlencode($product['category']); ?>" class="text-gray-500 hover:text-teal-600"><?php echo htmlspecialchars($product['category']); ?></a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                
                <!-- Product Image -->
                <div>
                    <div class="bg-gray-100 rounded-lg mb-4 h-96 flex items-center justify-center relative">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full max-w-full object-contain">
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl font-bold text-blue-900 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-4xl font-bold text-blue-900 mb-3">₦<?php echo number_format($product['price'], 2); ?></p>
                    
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <?php for ($i = 1; $i <= 5; $i++): ?><svg class="w-5 h-5 <?php echo $i <= $product['rating'] ? 'fill-current' : 'text-gray-300'; ?>" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg><?php endfor; ?>
                        </div>
                        <span class="text-sm text-gray-600 ml-2">(<?php echo $product['reviews']; ?> reviews)</span>
                    </div>

                    <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center"><span class="font-semibold text-gray-700 w-32">Brand</span><span class="text-gray-700"><?php echo htmlspecialchars($product['brand']); ?></span></div>
                        <div class="flex items-center"><span class="font-semibold text-gray-700 w-32">SKU</span><span class="text-gray-700"><?php echo htmlspecialchars($product['sku']); ?></span></div>
                        <div class="flex items-center"><span class="font-semibold text-gray-700 w-32">Availability</span>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="text-green-600 font-semibold"><?php echo $product['stock']; ?> In stock</span>
                            <?php else: ?>
                                <span class="text-red-600 font-semibold">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center border rounded-lg">
                            <button onclick="updateQty(-1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100">-</button>
                            <input id="quantity-input" type="text" value="1" class="w-12 h-10 text-center border-0 focus:ring-0">
                            <button onclick="updateQty(1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100">+</button>
                        </div>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="flex-1 bg-blue-900 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition flex items-center justify-center space-x-2">
                            <span>Add To Cart</span>
                        </button>
                        <button onclick="addToWishlist(<?php echo $product['id']; ?>, this)" class="border p-3 rounded-lg hover:bg-gray-50">
                            <svg class="w-6 h-6 <?php echo $is_in_wishlist ? 'text-red-500 fill-current' : 'text-gray-600'; ?>" fill="<?php echo $is_in_wishlist ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Explore More Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <a href="product.php?id=<?php echo $related['id']; ?>" class="block">
                        <img src="uploads/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" class="w-full h-64 object-cover">
                    </a>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-1"><?php echo htmlspecialchars($related['category']); ?></p>
                        <a href="product.php?id=<?php echo $related['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-blue-900 block h-12"><?php echo htmlspecialchars($related['name']); ?></a>
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xl font-bold text-gray-800">₦<?php echo number_format($related['price'], 2); ?></p>
                            <button onclick="addToCart(<?php echo $related['id']; ?>, 1)" class="bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition text-sm font-medium">Add To Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
        const quantityInput = document.getElementById('quantity-input');
        function updateQty(change) {
            let currentQty = parseInt(quantityInput.value);
            if (currentQty + change > 0) {
                quantityInput.value = currentQty + change;
            }
        }

        function addToCart(productId, quantity = null) {
            // If quantity isn't passed (like from related products), use the input value.
            const qty = quantity || parseInt(quantityInput.value);
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=${qty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product(s) added to cart!');
                    location.reload(); // Simple reload to update header count
                } else {
                     alert(data.message);
                }
            });
        }

        function addToWishlist(productId, buttonElement) {
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const svg = buttonElement.querySelector('svg');
            if (data.action === 'added') {
                // Item was added to wishlist - fill the heart red
                svg.classList.add('text-red-500', 'fill-current');
                svg.classList.remove('text-gray-600');
            } else if (data.action === 'removed') {
                // Item was removed from wishlist - unfill the heart
                svg.classList.remove('text-red-500', 'fill-current');
                svg.classList.add('text-gray-600');
            }
            alert(data.message);
        } else {
            alert(data.message);
            if (data.message.toLowerCase().includes('logged in')) {
                window.location.href = 'login.php';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
    </script>

</body>
</html>

