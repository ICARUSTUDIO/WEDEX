<?php
require_once 'config.php';

// --- Wishlist Logic ---
// Fetch the user's wishlist if they are logged in.
$user_wishlist = [];
if (isset($_SESSION['user_id'])) {
    $wishlist_stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wishlist_stmt->execute([$_SESSION['user_id']]);
    // Fetch all product_ids and store them in a simple array for easy checking.
    $user_wishlist = $wishlist_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// --- Filtering and Sorting Logic ---
$base_query = " FROM products WHERE stock > 0";
$params = [];

// Apply category filter
if (!empty($_GET['category'])) {
    $base_query .= " AND category = ?";
    $params[] = $_GET['category'];
}

// Apply search filter
if (!empty($_GET['search'])) {
    $base_query .= " AND name LIKE ?";
    $params[] = '%' . $_GET['search'] . '%';
}

// Apply price range filter
if (!empty($_GET['price'])) {
    switch ($_GET['price']) {
        case 'under50000': $base_query .= " AND price < 50000"; break;
        case '50000-100000': $base_query .= " AND price BETWEEN 50000 AND 100000"; break;
        case '100000-500000': $base_query .= " AND price BETWEEN 100000 AND 500000"; break;
        case 'over500000': $base_query .= " AND price >= 500000"; break;
    }
}

// --- Pagination ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$count_query = "SELECT COUNT(*) " . $base_query;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);


// --- Sorting ---
$sort_order = " ORDER BY created_at DESC"; // Default sort
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low': $sort_order = " ORDER BY price ASC"; break;
        case 'price_high': $sort_order = " ORDER BY price DESC"; break;
        case 'name': $sort_order = " ORDER BY name ASC"; break;
    }
}

// --- Final Query ---
$products_query = "SELECT * " . $base_query . $sort_order . " LIMIT " . $per_page . " OFFSET " . $offset;
$products_stmt = $pdo->prepare($products_query);
$products_stmt->execute($params);
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all distinct categories for the filter sidebar
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

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
                         <!-- Categories -->
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="font-semibold text-gray-700 mb-3">Categories</h4>
                            <div class="space-y-2">
                                <a href="shop.php" class="flex items-center text-sm <?php echo empty($_GET['category']) ? 'text-teal-600 font-bold' : 'text-gray-600 hover:text-teal-600'; ?>">All Products</a>
                                <?php foreach ($categories as $cat): ?>
                                <a href="?category=<?php echo urlencode($cat); ?>" class="flex items-center text-sm <?php echo (!empty($_GET['category']) && $_GET['category'] === $cat) ? 'text-teal-600 font-bold' : 'text-gray-600 hover:text-teal-600'; ?>"><?php echo htmlspecialchars($cat); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Toolbar -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
                    <!-- Sorting Dropdown can be added here -->
                </div>

                <!-- Products -->
                <?php if (count($products) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): 
                        // Check if the current product is in the user's wishlist
                        $is_wishlisted = in_array($product['id'], $user_wishlist);
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <div class="relative">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-64 object-cover">
                            </a>
                            <button 
                                onclick="handleWishlistToggle(<?php echo $product['id']; ?>, this)"
                                class="wishlist-btn absolute top-4 right-4 bg-white p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition"
                                data-product-id="<?php echo $product['id']; ?>"
                            >
                                <svg class="w-5 h-5 <?php echo $is_wishlisted ? 'text-red-500' : 'text-gray-600 hover:text-red-500'; ?>" viewBox="0 0 20 20" <?php echo $is_wishlisted ? 'fill="currentColor"' : 'fill="none"'; ?> stroke="currentColor" stroke-width="1.5">
                                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-teal-600 font-medium mb-1"><?php echo htmlspecialchars($product['category']); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block h-12">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
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
                    <div class="text-center py-16">
                        <h2 class="text-2xl font-bold text-gray-800">No Products Found</h2>
                        <p class="text-gray-600 mt-2">Sorry, we couldn't find any products matching your criteria. Try clearing the filters.</p>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                 <div class="mt-8 flex justify-center">
                    <!-- Pagination links will go here -->
                </div>
            </main>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
    function handleWishlistToggle(productId, buttonElement) {
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            // If the user is not logged in, the backend will send this action.
            if (data.action === 'login_required') {
                // Redirect them to the login page.
                window.location.href = 'login.php';
                return;
            }

            if (data.success) {
                const svg = buttonElement.querySelector('svg');
                
                if (data.action === 'added') {
                    // Item was added: fill the heart red.
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.add('text-red-500');
                    svg.classList.remove('text-gray-600');
                } else { // action === 'removed'
                    // Item was removed: unfill the heart and make it gray.
                    svg.setAttribute('fill', 'none');
                    svg.classList.remove('text-red-500');
                    svg.classList.add('text-gray-600');
                }
            } else {
                // Show an error message if something went wrong.
                alert(data.message);
            }
        });
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
                // Optionally, update cart count in header here without reloading
            } else {
                alert(data.message);
            }
        });
    }
    </script>

</body>
</html>

