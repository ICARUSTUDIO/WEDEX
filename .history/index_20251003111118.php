<?php
require_once 'config.php';

// Fetch 4 featured products (e.g., latest additions)
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 4");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_products = [];
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

    <!-- Top Bar -->
    <div class="bg-teal-600 text-white py-2 text-sm">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>+234 800 000 0000</span>
                </span>
                <span class="hidden md:flex items-center space-x-1">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>wedexhealthcareservices@gmail.com</span>
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden md:flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Mon - Fri: 8am - 5pm</span>
                </span>
                <a href="contact.php" class="hover:text-teal-200">Support</a>
            </div>
        </div>
    </div>
    
    <?php require 'Static/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-teal-50 to-blue-50 py-20">
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
                    <div class="rounded-2xl h-[32rem] flex items-center justify-center">
                         <img class="h-full object-contain" src="images/doctor_homepage.png" alt="Doctor with medical equipment">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="flex items-start space-x-4 p-4">
                    <div class="bg-teal-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Fast Delivery</h3>
                        <p class="text-sm text-gray-600">Quick shipping across Nigeria</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 p-4">
                    <div class="bg-teal-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Certified Products</h3>
                        <p class="text-sm text-gray-600">100% authentic & certified</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 p-4">
                    <div class="bg-teal-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Secure Payment</h3>
                        <p class="text-sm text-gray-600">Safe & secure transactions</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 p-4">
                    <div class="bg-teal-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">24/7 Support</h3>
                        <p class="text-sm text-gray-600">Always here to help you</p>
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
                    View All<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
                            <svg class="w-5 h-5 text-gray-600 hover:text-red-500 <?php echo $is_in_wishlist ? 'text-red-500 fill-current' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
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

    <!-- Categories Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Shop by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <a href="shop.php?category=Diagnostics" class="group">
                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition"><span class="text-3xl">🩺</span></div>
                        <h3 class="font-semibold text-gray-800">Diagnostics</h3>
                    </div>
                </a>
                <a href="shop.php?category=Mobility" class="group">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition"><span class="text-3xl">♿</span></div>
                        <h3 class="font-semibold text-gray-800">Mobility Aids</h3>
                    </div>
                </a>
                <a href="shop.php?category=Respiratory" class="group">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition"><span class="text-3xl">🫁</span></div>
                        <h3 class="font-semibold text-gray-800">Respiratory</h3>
                    </div>
                </a>
                <a href="shop.php?category=Furniture" class="group">
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition"><span class="text-3xl">🛏️</span></div>
                        <h3 class="font-semibold text-gray-800">Furniture</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-teal-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Subscribe to Our Newsletter</h2>
            <p class="text-teal-100 mb-8">Get updates on new products and exclusive offers</p>
            <form class="max-w-md mx-auto flex" method="POST" action="subscribe.php">
                <input type="email" name="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-l-lg focus:outline-none" required>
                <button type="submit" class="bg-gray-800 text-white px-8 py-3 rounded-r-lg hover:bg-gray-900 transition font-medium">Subscribe</button>
            </form>
        </div>
    </section>

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
                location.reload();
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

