<?php
require_once '../config.php';
require_once 'partials/header.php'; // Includes session check and header HTML

// Fetch all products from the database
try {
    $stmt = $pdo->query("SELECT id, name, category, price, stock, image FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not retrieve products: " . $e->getMessage());
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Product Management</h1>
        <a href="add_product.php" class="bg-teal-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">
            + Add New Product
        </a>
    </div>

    <?php 
    // Display the main success message for adding/editing products
    if (isset($_SESSION['success_message'])): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg" role="alert">
        <p><?php echo $_SESSION['success_message']; ?></p>
    </div>
    <?php unset($_SESSION['success_message']); endif; ?>

    <?php 
    // Display the new compression status message
    if (isset($_SESSION['compression_status'])): 
        $status_message = $_SESSION['compression_status'];
        $status_type = strtolower(explode(':', $status_message)[0]);
        $alert_class = 'bg-blue-100 border-blue-500 text-blue-700'; // Default for info
        if ($status_type === 'success') {
            $alert_class = 'bg-green-100 border-green-500 text-green-700';
        } elseif ($status_type === 'error') {
            $alert_class = 'bg-red-100 border-red-500 text-red-700';
        } elseif ($status_type === 'skipped') {
             $alert_class = 'bg-yellow-100 border-yellow-500 text-yellow-700';
        }
    ?>
    <div class="<?php echo $alert_class; ?> border-l-4 p-4 mb-4 rounded-lg" role="alert">
        <p><strong>Image Compression Status:</strong> <?php echo htmlspecialchars(substr($status_message, strpos($status_message, ':') + 2)); ?></p>
    </div>
    <?php unset($_SESSION['compression_status']); endif; ?>


    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text