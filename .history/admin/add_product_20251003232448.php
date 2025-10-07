<?php
require_once '../config.php';
require_once 'partials/header.php';

// Define the path to the Tinify library
$tinify_path = $_SERVER['DOCUMENT_ROOT'] . '/WEDEX/lib/tinypng/Tinify.php';
$tinify_loaded = false;

if (file_exists($tinify_path)) {
    require_once $tinify_path;
    $tinify_loaded = class_exists('\Tinify\Tinify');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['description'])) {
        $error = 'Please fill in all required fields: Name, Price, and Description.';
    } else {
        $image_name = 'default.jpg'; // Default image
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            $source_file = $_FILES["image"]["tmp_name"];

            // Check if API key is set and library is loaded before trying to compress
            if ($tinify_loaded && defined('TINIFY_API_KEY') && TINIFY_API_KEY != 'YOUR_API_KEY_HERE') {
                try {
                    // Initialize Tinify with your API key
                    \Tinify\Tinify::setKey(TINIFY_API_KEY);
                    \Tinify\Tinify::validate();
                    
                    // Compress the image from the temporary uploaded file
                    $source = \Tinify\Tinify::fromFile($source_file);
                    $source->toFile($target_file);
                    $_SESSION['compression_status'] = "Success: Image was compressed successfully!";
                    
                } catch(\Tinify\Exception $e) {
                    // If compression fails, log the error and fall back to a simple move.
                    $_SESSION['compression_status'] = "Error: TinyPNG API failed with message: " . $e->getMessage();
                    if (!move_uploaded_file($source_file, $target_file)) {
                         $error = "Sorry, there was an error uploading your file after a compression attempt failed.";
                    }
                }
            } else {
                // Fallback for when API key is not set or library is missing
                 $_SESSION['compression_status'] = "Skipped: Image uploaded without compression (API key not set or library missing).";
                if (!move_uploaded_file($source_file, $target_file)) {
                    $error = "Sorry, there was an error uploading your file.";
                }
            }
        }
        
        if (empty($error)) {
            try {
                $sql = "INSERT INTO products (name, description, price, category, stock, image, sku, brand, features, specifications, dimensions, colors, material, finished_type, delivery_days, is_featured) 
                        VALUES (:name, :description, :price, :category, :stock, :image, :sku, :brand, :features, :specifications, :dimensions, :colors, :material, :finished_type, :delivery_days, :is_featured)";
                
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':name' => $_POST['name'],
                    ':description' => $_POST['description'],
                    ':price' => $_POST['price'],
                    ':category' => $_POST['category'],
                    ':stock' => $_POST['stock'],
                    ':image' => $image_name,
                    ':sku' => $_POST['sku'] ?? null,
                    ':brand' => $_POST['brand'] ?? null,
                    ':features' => $_POST['features'] ?? null,
                    ':specifications' => $_POST['specifications'] ?? null,
                    ':dimensions' => $_POST['dimensions'] ?? null,
                    ':colors' => $_POST['colors'] ?? null,
                    ':material' => $_POST['material'] ?? null,
                    ':finished_type' => $_POST['finished_type'] ?? null,
                    ':delivery_days' => $_POST['delivery_days'] ?? 3,
                    ':is_featured' => isset($_POST['is_featured']) ? 1 : 0
                ]);
                
                $_SESSION['success_message'] = 'Product added successfully!';
                header('Location: dashboard.php');
                exit;

            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Product</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    <?php if (!$tinify_loaded): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4" role="alert">
            <p><strong>Warning:</strong> The TinyPNG library was not found. Images will be uploaded without compression. Please ensure <code>/WEDEX/lib/tinypng/Tinify.php</code> exists.</p>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg p-8">
        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (₦) *</label>
                    <input type="number" name="price" id="price" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                <textarea name="description" id="description" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock" id="stock" value="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                 <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>
            
            <div class="border-t pt-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Additional Details</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" name="brand" id="brand" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label for="material" class="block text-sm font-medium text-gray-700">Material</label>
                        <input type="text" name="material" id="material" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="features" class="block text-sm font-medium text-gray-700">Features (JSON format)</label>
                    <textarea name="features" id="features" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder='["Feature 1", "Feature 2"]'></textarea>
                </div>
                <div>
                    <label for="specifications" class="block text-sm font-medium text-gray-700">Specifications (JSON format)</label>
                    <textarea name="specifications" id="specifications" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder='{"Key 1": "Value 1", "Key 2": "Value 2"}'></textarea>
                </div>
                 <div>
                    <label for="colors" class="block text-sm font-medium text-gray-700">Colors (Comma-separated)</label>
                    <input type="text" name="colors" id="colors" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Gray,Blue,Yellow">
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="dimensions" class="block text-sm font-medium text-gray-700">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label for="finished_type" class="block text-sm font-medium text-gray-700">Finished Type</label>
                        <input type="text" name="finished_type" id="finished_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label for="delivery_days" class="block text-sm font-medium text-gray-700">Delivery Days</label>
                        <input type="number" name="delivery_days" id="delivery_days" value="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                 <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_featured" name="is_featured" type="checkbox" class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                        <p class="text-gray-500">Check this to display the product on the homepage.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="dashboard.php" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>