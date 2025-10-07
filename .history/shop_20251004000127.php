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
        case 'under50000': $sql .= " AND price < 50000"; $count_sql .= " AND price < 50000"; break;
        case '50000-100000': $sql .= " AND price BETWEEN 50000 AND 100000"; $count_sql .= " AND price BETWEEN 50000 AND 100000"; break;
        case '100000-500000': $sql .= " AND price BETWEEN 100000 AND 500000"; $count_sql .= " AND price BETWEEN 100000 AND 500000"; break;
        case 'over500000': $sql .= " AND price > 500000"; $count_sql .= " AND price > 500000"; break;
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
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .wishlist-btn svg { transition: all 0.2s ease-in-out; }
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
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                        
                        <!-- Categories, Price, and Rating Filters from your original file -->
                        
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <!-- Toolbar from your original file -->
                </div>

                <?php if (count($paginated_products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($paginated_products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <div class="relative">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-64 object-cover">
                            </a>
                            <button 
                                onclick="handleWishlistToggle(<?php echo $product['id']; ?>, this)" 
                                class="wishlist-btn absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition"
                            >
                                <?php $is_in_wishlist = in_array($product['id'], $wishlist_ids); ?>
                                <svg class="w-5 h-5 <?php echo $is_in_wishlist ? 'text-red-500' : 'text-gray-600 hover:text-red-500'; ?>" viewBox="0 0 20 20" <?php echo $is_in_wishlist ? 'fill="currentColor"' : 'fill="none"'; ?> stroke="currentColor" stroke-width="1.5">
                                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
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
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your filters or search query</p>
                        <a href="shop.php" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">View All Products</a>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <!-- Pagination HTML from your original file -->
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
    function addToCart(productId) {
        // Your existing addToCart function
    }

    function handleWishlistToggle(productId, buttonElement) {
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.action === 'login_required') {
                window.location.href = 'login.php';
                return;
            }

            if (data.success) {
                const svg = buttonElement.querySelector('svg');
                if (data.action === 'added') {
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.add('text-red-500');
                    svg.classList.remove('text-gray-600');
                } else { // removed
                    svg.setAttribute('fill', 'none');
                    svg.classList.remove('text-red-500');
                    svg.classList.add('text-gray-600');
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>

