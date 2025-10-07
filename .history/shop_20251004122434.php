<?php
require_once 'config.php';

// Get filter parameters
$category_filter = $_GET['category'] ?? '';
$sort_by = $_GET['sort'] ?? 'featured';
$search_query = trim($_GET['search'] ?? '');
$price_range = $_GET['price'] ?? '';
$min_rating = (float)($_GET['rating'] ?? 0);

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Build the base query
$sql = "SELECT * FROM products WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM products WHERE 1=1";
$params = [];

// Apply filters
if ($category_filter) {
    $sql .= " AND category = :category";
    $count_sql .= " AND category = :category";
    $params[':category'] = $category_filter;
}
if ($search_query) {
    $sql .= " AND name LIKE :search";
    $count_sql .= " AND name LIKE :search";
    $params[':search'] = "%$search_query%";
}
if ($price_range) {
    switch($price_range) {
        case 'under50': $sql .= " AND price < 50"; $count_sql .= " AND price < 50"; break;
        case '50-100': $sql .= " AND price BETWEEN 50 AND 100"; $count_sql .= " AND price BETWEEN 50 AND 100"; break;
        case '100-500': $sql .= " AND price BETWEEN 100 AND 500"; $count_sql .= " AND price BETWEEN 100 AND 500"; break;
        case 'over500': $sql .= " AND price > 500"; $count_sql .= " AND price > 500"; break;
    }
}
if ($min_rating > 0) {
    $sql .= " AND rating >= :rating";
    $count_sql .= " AND rating >= :rating";
    $params[':rating'] = $min_rating;
}

// Get total product count for pagination
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

// Apply sorting
$order_clause = match ($sort_by) {
    'price_low' => 'ORDER BY price ASC',
    'price_high' => 'ORDER BY price DESC',
    'rating' => 'ORDER BY rating DESC',
    'name' => 'ORDER BY name ASC',
    default => 'ORDER BY created_at DESC',
};
$sql .= " $order_clause LIMIT :limit OFFSET :offset";

// Add pagination params
$params[':limit'] = $per_page;
$params[':offset'] = $offset;

// Fetch products
$stmt = $pdo->prepare($sql);
// Bind parameters correctly, especially for LIMIT and OFFSET
$stmt->bindValue(':limit', (int) $params[':limit'], PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $params[':offset'], PDO::PARAM_INT);
if (isset($params[':category'])) $stmt->bindValue(':category', $params[':category']);
if (isset($params[':search'])) $stmt->bindValue(':search', $params[':search']);
if (isset($params[':rating'])) $stmt->bindValue(':rating', $params[':rating']);

$stmt->execute();
$paginated_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all distinct categories for the filter sidebar
$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Check wishlist status for displayed products
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
    <title>Shop - WEDEX Healthcare</title>
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
                                    <input type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>" <?php echo strtolower($category_filter) === strtolower($cat) ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    <?php echo htmlspecialchars($cat); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="font-semibold text-gray-700 mb-3">Price Range</h4>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="price" value="" <?php echo !$price_range ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">All Prices</label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="price" value="under50" <?php echo $price_range === 'under50' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">Under ₦50</label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="price" value="50-100" <?php echo $price_range === '50-100' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">₦50 - ₦100</label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="price" value="100-500" <?php echo $price_range === '100-500' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">₦100 - ₦500</label>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="price" value="over500" <?php echo $price_range === 'over500' ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">Over ₦500</label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-3">Rating</h4>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer"><input type="radio" name="rating" value="" <?php echo !$min_rating ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">All Ratings</label>
                                <?php for ($i = 5; $i >= 3; $i--): ?>
                                <label class="flex items-center text-sm text-gray-600 hover:text-teal-600 cursor-pointer">
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo $min_rating == $i ? 'checked' : ''; ?> onchange="this.form.submit()" class="w-4 h-4 text-teal-600 mr-2">
                                    <div class="flex text-yellow-400 ml-1">
                                        <?php for ($j = 0; $j < $i; $j++): ?><svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg><?php endfor; ?>
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
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-semibold"><?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_products); ?></span> of <span class="font-semibold"><?php echo $total_products; ?></span> products
                        </p>
                        <div class="flex items-center space-x-4">
                            <label class="text-sm text-gray-600">Sort by:</label>
                            <form method="GET" action="shop.php" class="inline">
                                <?php foreach ($_GET as $key => $value) { if ($key != 'sort') { echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">'; } } ?>
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

                <?php if (count($paginated_products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($paginated_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <div class="relative">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-64 object-cover">
                            </a>
                            <?php if ($product['stock'] > 0 && $product['stock'] < 20): ?>
                            <span class="absolute top-4 left-4 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Only <?php echo $product['stock']; ?> left</span>
                            <?php endif; ?>
                            <button onclick="addToWishlist(<?php echo $product['id']; ?>, this)" class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition">
                                <?php $is_in_wishlist = in_array($product['id'], $wishlist_ids); ?>
                                <svg class="w-5 h-5 <?php echo $is_in_wishlist ? 'text-red-500 fill-current' : 'text-gray-600'; ?>" fill="<?php echo $is_in_wishlist ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-teal-600 font-medium mb-1"><?php echo htmlspecialchars($product['category']); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block h-12"><?php echo htmlspecialchars($product['name']); ?></a>
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?><svg class="w-4 h-4 <?php echo $i <= $product['rating'] ? 'fill-current' : 'text-gray-300'; ?>" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg><?php endfor; ?>
                                </div>
                                <span class="text-xs text-gray-500 ml-2"><?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?>)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-gray-800">₦<?php echo number_format($product['price'], 2); ?></p>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters or search query</p>
                    <a href="shop.php" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">View All Products</a>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php
                            $query_params = $_GET;
                            unset($query_params['page']);
                            $base_url = '?' . http_build_query($query_params);
                        ?>
                        <a href="<?php echo $base_url . '&page=' . max(1, $page - 1); ?>" class="<?php echo $page <= 1 ? 'hidden' : ''; ?> px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Previous</a>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="<?php echo $base_url . '&page=' . $i; ?>" class="px-4 py-2 rounded-lg <?php echo $i == $page ? 'bg-teal-600 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <a href="<?php echo $base_url . '&page=' . min($total_pages, $page + 1); ?>" class="<?php echo $page >= $total_pages ? 'hidden' : ''; ?> px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Next</a>
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
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId + '&quantity=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
                location.reload(); // Reload to update header cart count
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

