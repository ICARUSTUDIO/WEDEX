<?php
session_start();

// Sample products database
$all_products = [
    ['id' => 1, 'name' => 'Blood Pressure Monitor', 'price' => 45.99, 'category' => 'Diagnostics', 'rating' => 4.5, 'reviews' => 128, 'stock' => 45],
    ['id' => 2, 'name' => 'Digital Thermometer', 'price' => 12.99, 'category' => 'Diagnostics', 'rating' => 4.8, 'reviews' => 245, 'stock' => 120],
    ['id' => 3, 'name' => 'Pulse Oximeter', 'price' => 29.99, 'category' => 'Diagnostics', 'rating' => 4.6, 'reviews' => 189, 'stock' => 78],
    ['id' => 4, 'name' => 'Wheelchair Standard', 'price' => 299.99, 'category' => 'Mobility', 'rating' => 4.7, 'reviews' => 95, 'stock' => 12],
    ['id' => 5, 'name' => 'Walking Cane Adjustable', 'price' => 19.99, 'category' => 'Mobility', 'rating' => 4.4, 'reviews' => 156, 'stock' => 89],
    ['id' => 6, 'name' => 'Hospital Bed Electric', 'price' => 899.99, 'category' => 'Furniture', 'rating' => 4.9, 'reviews' => 67, 'stock' => 8],
    ['id' => 7, 'name' => 'Nebulizer Machine', 'price' => 79.99, 'category' => 'Respiratory', 'rating' => 4.5, 'reviews' => 134, 'stock' => 34],
    ['id' => 8, 'name' => 'First Aid Kit Complete', 'price' => 34.99, 'category' => 'Emergency', 'rating' => 4.6, 'reviews' => 312, 'stock' => 156],
    ['id' => 9, 'name' => 'Oxygen Concentrator', 'price' => 549.99, 'category' => 'Respiratory', 'rating' => 4.8, 'reviews' => 89, 'stock' => 15],
    ['id' => 10, 'name' => 'Patient Monitor', 'price' => 1299.99, 'category' => 'Diagnostics', 'rating' => 4.7, 'reviews' => 45, 'stock' => 6],
    ['id' => 11, 'name' => 'Crutches Aluminum', 'price' => 39.99, 'category' => 'Mobility', 'rating' => 4.5, 'reviews' => 178, 'stock' => 67],
    ['id' => 12, 'name' => 'Medical Examination Bed', 'price' => 449.99, 'category' => 'Furniture', 'rating' => 4.6, 'reviews' => 52, 'stock' => 11],
];

// Filter and sort logic
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$filtered_products = $all_products;

// Apply category filter
if ($category_filter) {
    $filtered_products = array_filter($filtered_products, function($product) use ($category_filter) {
        return strtolower($product['category']) === strtolower($category_filter);
    });
}

// Apply search filter
if ($search_query) {
    $filtered_products = array_filter($filtered_products, function($product) use ($search_query) {
        return stripos($product['name'], $search_query) !== false;
    });
}

// Apply sorting
switch ($sort_by) {
    case 'price_low':
        usort($filtered_products, function($a, $b) {
            return $a['price'] - $b['price'];
        });
        break;
    case 'price_high':
        usort($filtered_products, function($a, $b) {
            return $b['price'] - $a['price'];
        });
        break;
    case 'rating':
        usort($filtered_products, function($a, $b) {
            return $b['rating'] - $a['rating'];
        });
        break;
}

$categories = ['Diagnostics', 'Mobility', 'Respiratory', 'Furniture', 'Emergency'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Hospice Medical Supplies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Top Bar -->
    <div class="bg-teal-600 text-white py-2 text-sm">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span>📞 +234 800 000 0000</span>
                <span>✉️ info@hospicemedical.com</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-teal-200">Track Order</a>
                <a href="#" class="hover:text-teal-200">Help</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        H
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Hospice</h1>
                        <p class="text-xs text-gray-500">Medical Supplies</p>
                    </div>
                </div>

                <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                    <form class="w-full flex" method="GET">
                        <input 
                            type="text" 
                            name="search"
                            value="<?php echo htmlspecialchars($search_query); ?>"
                            placeholder="Search for medical supplies..." 
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                        >
                        <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-r-lg hover:bg-teal-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="account.php" class="hidden md:flex items-center space-x-2 text-gray-700 hover:text-teal-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium">Account</span>
                    </a>
                    <a href="cart.php" class="flex items-center space-x-2 text-gray-700 hover:text-teal-600 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                        <?php endif; ?>
                        <span class="text-sm font-medium hidden md:inline">Cart</span>
                    </a>
                </div>
            </div>

            <nav class="mt-4 border-t pt-4">
                <ul class="flex items-center space-x-8 text-sm font-medium">
                    <li><a href="index.php" class="text-gray-700 hover:text-teal-600">Home</a></li>
                    <li><a href="shop.php" class="text-teal-600 hover:text-teal-700">Shop All</a></li>
                    <li><a href="about.php" class="text-gray-700 hover:text-teal-600">About</a></li>
                    <li><a href="contact.php" class="text-gray-700 hover:text-teal-600">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Shop</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            
            <!-- Sidebar Filters -->
            <aside class="md:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Filters</h3>
                    
                    <!-- Categories -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Categories</h4>
                        <div class="space-y-2">
                            <a href="shop.php" class="flex items-center text-sm <?php echo !$category_filter ? 'text-teal-600 font-medium' : 'text-gray-600 hover:text-teal-600'; ?>">
                                <span class="w-4 h-4 rounded border-2 border-gray-300 mr-2 <?php echo !$category_filter ? 'bg-teal-600 border-teal-600' : ''; ?>"></span>
                                All Products
                            </a>
                            <?php foreach ($categories as $cat): ?>
                            <a href="shop.php?category=<?php echo urlencode($cat); ?>" class="flex items-center text-sm <?php echo strtolower($category_filter) === strtolower($cat) ? 'text-teal-600 font-medium' : 'text-gray-600 hover:text-teal-600'; ?>">
                                <span class="w-4 h-4 rounded border-2 border-gray-300 mr-2 <?php echo strtolower($category_filter) === strtolower($cat) ? 'bg-teal-600 border-teal-600' : ''; ?>"></span>
                                <?php echo $cat; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Price Range</h4>
                        <div class="space-y-2">
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                Under ₦20
                            </label>
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                ₦20 - ₦50
                            </label>
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                ₦50 - ₦100
                            </label>
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                ₦100 - ₦500
                            </label>
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                Over ₦500
                            </label>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Rating</h4>
                        <div class="space-y-2">
                            <?php for ($i = 5; $i >= 3; $i--): ?>
                            <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded mr-2">
                                <div class="flex text-yellow-400">
                                    <?php for ($j = 0; $j < $i; $j++): ?>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="ml-1">& up</span>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <button class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                        Apply Filters
                    </button>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Toolbar -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-semibold"><?php echo count($filtered_products); ?></span> products
                        </p>
                        <div class="flex items-center space-x-4">
                            <label class="text-sm text-gray-600">Sort by:</label>
                            <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500" onchange="window.location.href='shop.php?sort='+this.value+'<?php echo $category_filter ? '&category='.$category_filter : ''; ?>'">
                                <option value="featured" <?php echo $sort_by === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo $sort_by === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <?php if (count($filtered_products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($filtered_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <div class="relative">
                            <div class="bg-gray-200 h-64 flex items-center justify-center">
                                <span class="text-gray-400">Product Image</span>
                            </div>
                            <?php if ($product['stock'] < 20): ?>
                            <span class="absolute top-4 left-4 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                Only <?php echo $product['stock']; ?> left
                            </span>
                            <?php endif; ?>
                            <button class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-5 h-5 text-gray-600 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-teal-600 font-medium mb-1"><?php echo $product['category']; ?></p>
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
                                <span class="text-xs text-gray-500 ml-2"><?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?>)</span>
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
                <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters or search query</p>
                    <a href="shop.php" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">
                        View All Products
                    </a>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if (count($filtered_products) > 0): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50" disabled>
                            Previous
                        </button>
                        <button class="px-4 py-2 bg-teal-600 text-white rounded-lg">1</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">2</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">3</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            Next
                        </button>
                    </nav>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                            H
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hospice</h3>
                            <p class="text-xs">Medical Supplies</p>
                        </div>
                    </div>
                    <p class="text-sm">Your trusted partner for quality medical supplies and equipment.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Customer Service</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="shipping.php" class="hover:text-teal-400">Shipping Policy</a></li>
                        <li><a href="returns.php" class="hover:text-teal-400">Returns & Refunds</a></li>
                        <li><a href="privacy.php" class="hover:text-teal-400">Privacy Policy</a></li>
                        <li><a href="terms.php" class="hover:text-teal-400">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-sm">
                        <li>📞 +234 800 000 0000</li>
                        <li>✉️ info@hospicemedical.com</li>
                        <li>📍 Lagos, Nigeria</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; 2025 Hospice Medical Supplies. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function addToCart(productId) {
            // AJAX call to add to cart
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
</html>d text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="about.php" class="hover:text-teal-400">About Us</a></li>
                        <li><a href="shop.php" class="hover:text-teal-400">Shop</a></li>
                        <li><a href="contact.php" class="hover:text-teal-400">Contact</a></li>
                        <li><a href="faq.php" class="hover:text-teal-400">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibol