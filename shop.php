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

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';
$min_rating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 9;

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
        return stripos($product['name'], $search_query) !== false || 
               stripos($product['category'], $search_query) !== false;
    });
}

// Apply price range filter
if ($price_range) {
    $filtered_products = array_filter($filtered_products, function($product) use ($price_range) {
        switch($price_range) {
            case 'under20':
                return $product['price'] < 20;
            case '20-50':
                return $product['price'] >= 20 && $product['price'] < 50;
            case '50-100':
                return $product['price'] >= 50 && $product['price'] < 100;
            case '100-500':
                return $product['price'] >= 100 && $product['price'] < 500;
            case 'over500':
                return $product['price'] >= 500;
            default:
                return true;
        }
    });
}

// Apply rating filter
if ($min_rating > 0) {
    $filtered_products = array_filter($filtered_products, function($product) use ($min_rating) {
        return $product['rating'] >= $min_rating;
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
    case 'name':
        usort($filtered_products, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        break;
}

// Calculate pagination
$total_products = count($filtered_products);
$total_pages = ceil($total_products / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_products = array_slice($filtered_products, $offset, $per_page);

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
    
    <?php require 'Static/header.php'; ?>

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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Filters</h3>
                        <a href="shop.php" class="text-sm text-teal-600 hover:text-teal-700">Clear All</a>
                    </div>
                    
                    <form method="GET" action="shop.php" id="filterForm">
                        <!-- Keep existing filters -->
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                        
                        <!-- Categories -->
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="font-semibold text-gray-700 mb-3">Categories</h4>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm cursor-pointer hover:text-teal-600 <?php echo !$category_filter ? 'text-teal-600 font-medium' : 'text-gray-600'; ?>">
                                    <input type="radio" name="category" value="" <?php echo !$category_filter ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    All Products
                                </label>
                                <?php foreach ($categories as $cat): ?>
                                <label class="flex items-center text-sm cursor-pointer hover:text-teal-600 <?php echo strtolower($category_filter) === strtolower($cat) ? 'text-teal-600 font-medium' : 'text-gray-600'; ?>">
                                    <input type="radio" name="category" value="<?php echo $cat; ?>" <?php echo strtolower($category_filter) === strtolower($cat) ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    <?php echo $cat; ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="font-semibold text-gray-700 mb-3">Price Range</h4>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="" <?php echo !$price_range ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    All Prices
                                </label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="under20" <?php echo $price_range === 'under20' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    Under ₦20
                                </label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="20-50" <?php echo $price_range === '20-50' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    ₦20 - ₦50
                                </label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="50-100" <?php echo $price_range === '50-100' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    ₦50 - ₦100
                                </label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="100-500" <?php echo $price_range === '100-500' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    ₦100 - ₦500
                                </label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="price" value="over500" <?php echo $price_range === 'over500' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    Over ₦500
                                </label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Rating</h4>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="rating" value="" <?php echo !$min_rating ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    All Ratings
                                </label>
                                <?php for ($i = 5; $i >= 3; $i--): ?>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo $min_rating == $i ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    <div class="flex text-yellow-400 ml-1">
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
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Toolbar -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-semibold"><?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_products); ?></span> of <span class="font-semibold"><?php echo $total_products; ?></span> products
                        </p>
                        <div class="flex items-center space-x-4">
                            <label class="text-sm text-gray-600">Sort by:</label>
                            <form method="GET" action="shop.php" class="inline">
                                <!-- Preserve filters -->
                                <?php if ($category_filter): ?>
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                                <?php endif; ?>
                                <?php if ($search_query): ?>
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                <?php endif; ?>
                                <?php if ($price_range): ?>
                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($price_range); ?>">
                                <?php endif; ?>
                                <?php if ($min_rating): ?>
                                <input type="hidden" name="rating" value="<?php echo htmlspecialchars($min_rating); ?>">
                                <?php endif; ?>
                                
                                <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="featured" <?php echo $sort_by === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                    <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="rating" <?php echo $sort_by === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                    <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <?php if (count($paginated_products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($paginated_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <div class="relative">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <div class="bg-gray-200 h-64 flex items-center justify-center cursor-pointer">
                                    <span class="text-gray-400">Product Image</span>
                                </div>
                            </a>
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
                <?php if ($total_pages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?><?php echo $price_range ? '&price=' . urlencode($price_range) : ''; ?><?php echo $min_rating ? '&rating=' . $min_rating : ''; ?><?php echo $sort_by ? '&sort=' . $sort_by : ''; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            Previous
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Previous</span>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                            <span class="px-4 py-2 bg-teal-600 text-white rounded-lg"><?php echo $i; ?></span>
                            <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?><?php echo $price_range ? '&price=' . urlencode($price_range) : ''; ?><?php echo $min_rating ? '&rating=' . $min_rating : ''; ?><?php echo $sort_by ? '&sort=' . $sort_by : ''; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                                <?php echo $i; ?>
                            </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?><?php echo $price_range ? '&price=' . urlencode($price_range) : ''; ?><?php echo $min_rating ? '&rating=' . $min_rating : ''; ?><?php echo $sort_by ? '&sort=' . $sort_by : ''; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            Next
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
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