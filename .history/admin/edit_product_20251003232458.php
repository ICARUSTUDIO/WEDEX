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

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('Location: dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['description'])) {
        $error = 'Please fill in all required fields: Name, Price, and Description.';
    } else {
        $image_name = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            $source_file = $_FILES["image"]["tmp_name"];
            
            // Check if API key is set and library is loaded
            if ($tinify_loaded && defined('TINIFY_API_KEY') && TINIFY_API_KEY != 'YOUR_API_KEY_HERE') {
                 try {
                    \Tinify\Tinify::setKey(TINIFY_API_KEY);
                    \Tinify\Tinify::validate();
                    $source = \Tinify\Tinify::fromFile($source_file);
                    $source->toFile($target_file);
                    $_SESSION['compression_status'] = "Success: Image was compressed successfully!";
                } catch (\Tinify\Exception $e) {
                    $_SESSION['compression_status'] = "Error: TinyPNG API failed with message: " . $e->getMessage();
                    if (!move_uploaded_file($source_file, $target_file)) {
                        $error = "Sorry, there was an error uploading your file after a compression attempt failed.";
                        $image_name = $product['image']; // Revert to old image
                    }
                }
            } else {
                $_SESSION['compression_status'] = "Skipped: Image uploaded without compression (API key not set or library missing).";
                if (!move_uploaded_file($source_file, $target_file)) {
                    $error = "Sorry, there was an error uploading your new file.";
                    $image_name = $product['image']; // Revert to old image
                }
            }
        }
        
        if (empty($error)) {
            try {
                $sql = "UPDATE products SET name = :name, description = :description, price = :price, category = :category, stock = :stock, image = :image, sku = :sku, brand = :brand, features = :features, specifications = :specifications, dimensions = :dimensions, colors = :colors, material = :material, finished_type = :finished_type, delivery_days = :delivery_days, is_featured = :is_featured WHERE id = :id";
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
                    ':is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                    ':id' => $id
                ]);

                $_SESSION['success_message'] = 'Product updated successfully!';
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
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Product</h1>

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
        <form action="edit_product.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (₦) *</label>
                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                <textarea name="description" id="description" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($product['category']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                 <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Image</label>
                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" class="mt-2 h-24 w-24 object-cover rounded-md">
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Change Product Image</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>
            
            <div class="border-t pt-6 space-y-6">
                 <h3 class="text-lg font-medium text-gray-900">Additional Details</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" name="brand" id="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label for="material" class="block text-sm font-medium text-gray-700">Material</label>
                        <input type="text" name="material" id="material" value="<?php echo htmlspecialchars($product['material']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="features" class="block text-sm font-medium text-gray-700">Features (JSON format)</label>
                    <textarea name="features" id="features" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($product['features']); ?></textarea>
                </div>
                 <div>
                    <label for="specifications" class="block text-sm font-medium text-gray-700">Specifications (JSON format)</label>
                    <textarea name="specifications" id="specifications" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($product['specifications']); ?></textarea>
                </div>
                 <div>
                    <label for="colors" class="block text-sm font-medium text-gray-700">Colors (Comma-separated)</label>
                    <input type="text" name="colors" id="colors" value="<?php echo htmlspecialchars($product['colors']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="dimensions" class="block text-sm font-medium text-gray-700">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" value="<?php echo htmlspecialchars($product['dimensions']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="finished_type" class="block text-sm font-medium text-gray-700">Finished Type</label>
                        <input type="text" name="finished_type" id="finished_type" value="<?php echo htmlspecialchars($product['finished_type']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="delivery_days" class="block text-sm font-medium text-gray-700">Delivery Days</label>
                        <input type="number" name="delivery_days" id="delivery_days" value="<?php echo htmlspecialchars($product['delivery_days']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                 <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_featured" name="is_featured" type="checkbox" <?php echo ($product['is_featured'] ? 'checked' : ''); ?> class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                        <p class="text-gray-500">Check this to display the product on the homepage.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="dashboard.php" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">Update Product</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>