<?php
require_once 'config.php';

// Fetch 4 featured products (e.g., latest additions)
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // On failure, use an empty array to prevent page crash
    $featured_products = [];
    // Optionally log the error: error_log($e->getMessage());
}

// Check wishlist status for featured products
$wishlist_ids = [];
if (isset($_SESSION['user_id'])) {
    $wishlist_stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wishlist_stmt->execute([$_SESSION['user_id']]);
    $wishlist_ids = $wishlist_stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEDEX Healthcare Services - Quality Medical Equipment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-teal-50 to-blue-50 py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <h2 class="text-5xl font-bold text-gray-800 mb-4">Quality Medical Supplies for Your Care</h2>
                    <p class="text-xl text-gray-600 mb-6">Shop from our wide range of certified medical equipment and supplies delivered to your doorstep.</p>
                    <div class="flex space-x-4">
                        <a href="shop.php" class="bg-teal-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-teal-700 transition">Shop Now</a>
                        <a href="about.php" class="border-2 border-teal-600 text-teal-600 px-8 py-4 rounded-lg font-semibold hover:bg-teal-50 transition">Learn More</a>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="bg-gray-200 rounded-2xl h-96 flex items-center justify-center">
                         <img class="" style="height: 150%;" src="images/doctor_homepage.png" alt="Doctor with medical equipment">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
                <a href="shop.php" class="text-teal-600 hover:text-teal-700 font-medium flex items-center">
                    View All
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($featured_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <div class="relative">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-64 object-cover">
                        </a>
                        <button onclick="addToWishlist(<?php echo $product['id']; ?>, this)" class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition">
                             <?php $is_in_wishlist = in_array($product['id'], $wishlist_ids); ?>
                            <svg class="w-5 h-5 text-gray-600 hover:text-red-500 <?php echo $is_in_wishlist ? 'text-red-500 fill-current' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-teal-600 font-medium mb-1"><?php echo htmlspecialchars($product['category']); ?></p>
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
    </section>
    
    <!-- (Your other homepage sections can go here) -->

    <?php require 'Static/footer.php'; ?>
    <script>
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
                location.reload(); // Simple reload for header update
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
                    svg.classList.add('text-red-500', 'fill-current');
                } else {
                    svg.classList.remove('text-red-500', 'fill-current');
                }
                alert(data.message);
            } else {
                alert(data.message);
                if (data.message.toLowerCase().includes('logged in')) {
                    window.location.href = 'login.php';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>

