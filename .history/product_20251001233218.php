<?php
session_start();

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// In production, fetch from database
// For now, using sample data
$all_products = [
    1 => [
        'id' => 1,
        'name' => 'Blood Pressure Monitor',
        'price' => 45.99,
        'category' => 'Diagnostics',
        'rating' => 4.5,
        'reviews' => 128,
        'stock' => 45,
        'sku' => 'BPM-001',
        'brand' => 'MediTech',
        'description' => 'A Product Intended For Therapeutic Purposes Is Called A Medical Device. It Is Used To Provide A High Level Of Protection For People\'s Health, Safety, And Functionality. Medical Devices Differ In Both Their Indication Use And Intended Use. The Quantity Of Testing Necessary To Prove The Device\'s Safety And Efficacy Grows Along With Its Related Risk. Additionally, It Raises The Patient\'s Prospective Gain',
        'features' => [
            'Automatic inflation and deflation',
            'Large, easy-to-read LCD display',
            'Irregular heartbeat detection',
            'Memory storage for 120 readings',
            'Average of last 3 readings',
            'WHO blood pressure classification indicator',
            'Universal cuff fits most arm sizes'
        ],
        'specifications' => [
            'Measurement Range' => 'Pressure: 0-299 mmHg, Pulse: 40-199 beats/min',
            'Accuracy' => '±3 mmHg (pressure), ±5% (pulse)',
            'Power' => '4 AA batteries or AC adapter',
            'Cuff Size' => '22-42 cm',
            'Weight' => '350g',
            'Warranty' => '2 years'
        ],
        'dimensions' => '18 X 9 X 18 Cm',
        'colors' => ['Gray', 'Blue', 'Yellow', 'Cyan'],
        'material' => 'Steel',
        'finished_type' => 'Polished',
        'delivery_days' => 3
    ],
    6 => [
        'id' => 6,
        'name' => 'Hospital Bed',
        'price' => 350.00,
        'category' => 'Furniture',
        'rating' => 4.9,
        'reviews' => 23,
        'stock' => 10,
        'sku' => 'HBD-006',
        'brand' => 'MedEquip',
        'description' => 'A Product Intended For Therapeutic Purposes Is Called A Medical Device. It Is Used To Provide A High Level Of Protection For People\'s Health, Safety, And Functionality. Medical Devices Differ In Both Their Indication Use And Intended Use. The Quantity Of Testing Necessary To Prove The Device\'s Safety And Efficacy Grows Along With Its Related Risk. Additionally, It Raises The Patient\'s Prospective Gain',
        'features' => [
            'Electric height adjustment',
            'Backrest and leg rest positioning',
            'Side rails included for safety',
            'Heavy-duty casters with locks',
            'Easy-to-clean surface',
            'Weight capacity up to 200kg',
            'Remote control included'
        ],
        'specifications' => [
            'Dimensions' => '210 x 95 x 50-80 cm (adjustable height)',
            'Frame Material' => 'Steel powder coated',
            'Mattress Platform' => 'Perforated steel mesh',
            'Casters' => '5 inch with central locking',
            'Weight Capacity' => '200 kg',
            'Warranty' => '3 years'
        ],
        'dimensions' => '18 X 9 X 18 Cm',
        'colors' => ['Gray', 'Blue', 'Yellow', 'Cyan'],
        'material' => 'Steel',
        'finished_type' => 'Polished',
        'delivery_days' => 3
    ],
];

$product = isset($all_products[$product_id]) ? $all_products[$product_id] : $all_products[1];

// Related products
$related_products = [
    ['id' => 2, 'name' => 'Digital Thermometer', 'price' => 12.99, 'rating' => 4.8],
    ['id' => 3, 'name' => 'Pulse Oximeter', 'price' => 29.99, 'rating' => 4.6],
    ['id' => 7, 'name' => 'Nebulizer Machine', 'price' => 79.99, 'rating' => 4.5],
    ['id' => 8, 'name' => 'First Aid Kit', 'price' => 34.99, 'rating' => 4.6],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Hospice Medical Supplies</title>
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
                <a href="shop.php" class="text-gray-500 hover:text-teal-600">Shop</a>
                <span class="text-gray-400">/</span>
                <a href="shop.php?category=<?php echo urlencode($product['category']); ?>" class="text-gray-500 hover:text-teal-600"><?php echo $product['category']; ?></a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium"><?php echo $product['name']; ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                
                <!-- Product Images -->
                <div>
                    <div class="bg-gray-200 rounded-lg mb-4 h-96 flex items-center justify-center relative">
                        <span class="text-gray-400 text-lg">Main Product Image</span>
                        <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-gray-700">
                            WPS Office
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="bg-gray-200 rounded-lg h-24 flex items-center justify-center cursor-pointer border-2 border-blue-600">
                            <span class="text-gray-400 text-xs">Thumb 1</span>
                        </div>
                        <div class="bg-gray-200 rounded-lg h-24 flex items-center justify-center cursor-pointer hover:border-2 hover:border-blue-600">
                            <span class="text-gray-400 text-xs">Thumb 2</span>
                        </div>
                        <div class="bg-gray-200 rounded-lg h-24 flex items-center justify-center cursor-pointer hover:border-2 hover:border-blue-600">
                            <span class="text-gray-400 text-xs">Thumb 3</span>
                        </div>
                        <div class="bg-gray-200 rounded-lg h-24 flex items-center justify-center cursor-pointer hover:border-2 hover:border-blue-600">
                            <span class="text-gray-400 text-xs">Thumb 4</span>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <div class="mb-4">
                        <h1 class="text-3xl font-bold text-blue-900 mb-2"><?php echo $product['name']; ?></h1>
                        <p class="text-4xl font-bold text-blue-900 mb-3">₦<?php echo number_format($product['price'], 0); ?></p>
                        
                        <p class="text-sm text-gray-700 mb-4"><?php echo substr($product['description'], 0, 150); ?>...</p>
                        
                        <!-- Rating -->
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <svg class="w-5 h-5 <?php echo $i < floor($product['rating']) ? 'fill-current' : 'fill-gray-300'; ?>" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm text-gray-600 ml-2">(<?php echo $product['reviews']; ?>) reviews</span>
                        </div>
                    </div>

                    <!-- Product Specifications -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                            <span class="font-semibold text-gray-700 w-32">Finished Type</span>
                            <span class="bg-blue-900 text-white px-4 py-1 rounded text-sm"><?php echo $product['finished_type']; ?></span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-32">Dimensions:</span>
                            <span class="text-gray-700"><?php echo $product['dimensions']; ?></span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-32">Color</span>
                            <div class="flex space-x-2">
                                <?php 
                                $color_map = [
                                    'Gray' => 'bg-gray-400',
                                    'Blue' => 'bg-blue-500',
                                    'Yellow' => 'bg-yellow-400',
                                    'Cyan' => 'bg-cyan-400'
                                ];
                                foreach ($product['colors'] as $color): 
                                ?>
                                <button class="w-8 h-8 rounded-full <?php echo $color_map[$color]; ?> border-2 border-gray-300 hover:border-blue-900"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-32">Availability</span>
                            <span class="text-gray-700"><?php echo $product['stock']; ?> In stock</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-32">Material</span>
                            <div class="flex space-x-2">
                                <button class="bg-blue-900 text-white px-4 py-1 rounded text-sm">Steel</button>
                                <button class="border border-gray-300 text-gray-700 px-4 py-1 rounded text-sm hover:border-blue-900">Aluminum Alloy</button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4 mb-6">
                        <button onclick="addToWishlist(<?php echo $product['id']; ?>)" class="flex items-center space-x-2 text-gray-700 hover:text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-sm">Add To Wishlist</span>
                        </button>
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            <span class="text-sm">Share</span>
                        </button>
                    </div>

                    <!-- Add to Cart and Buy Now -->
                    <div class="flex space-x-4">
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="flex-1 bg-blue-900 text-white py-4 rounded-lg font-semibold hover:bg-blue-800 transition flex items-center justify-center space-x-2">
                            <span>Add To Cart</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                        <button class="flex-1 bg-gray-800 text-white py-4 rounded-lg font-semibold hover:bg-gray-900 transition">
                            Buy it now
                        </button>
                    </div>

                    <!-- Delivery Info -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-semibold">Delivery : <?php echo $product['delivery_days']; ?> Working Days</span>
                        </div>
                        <div class="text-sm text-gray-700 mt-2">
                            <span class="font-semibold">Free Shipping & Returns:</span> On Orders Above ₦200,000
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="bg-white rounded-lg shadow-lg mt-8 overflow-hidden">
            <div class="border-b">
                <div class="flex">
                    <button onclick="showTab('description')" id="tab-description" class="px-8 py-4 font-semibold text-blue-900 border-b-2 border-blue-900">
                        Description
                    </button>
                    <button onclick="showTab('shipping')" id="tab-shipping" class="px-8 py-4 font-semibold text-gray-600 hover:text-blue-900">
                        Shipping Information
                    </button>
                    <button onclick="showTab('reviews')" id="tab-reviews" class="px-8 py-4 font-semibold text-gray-600 hover:text-blue-900">
                        Reviews
                    </button>
                </div>
            </div>

            <div class="p-8">
                <!-- Description Tab -->
                <div id="content-description" class="tab-content">
                    <p class="text-gray-700 leading-relaxed"><?php echo $product['description']; ?></p>
                </div>

                <!-- Shipping Tab -->
                <div id="content-shipping" class="tab-content hidden">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Shipping Information</h3>
                    <p class="text-gray-700 mb-4">We offer fast and reliable shipping across Nigeria.</p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <li>Standard Shipping: 3-5 business days</li>
                        <li>Express Shipping: 1-2 business days</li>
                        <li>Free shipping on orders above ₦200,000</li>
                        <li>Same-day delivery available in Lagos</li>
                    </ul>
                </div>

                <!-- Reviews Tab -->
                <div id="content-reviews" class="tab-content hidden">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Customer Reviews (<?php echo $product['reviews']; ?>)</h3>
                    <div class="space-y-4">
                        <div class="border-b pb-4">
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="ml-2 font-semibold text-gray-800">John Doe</span>
                            </div>
                            <p class="text-gray-700">Excellent product! Highly recommended.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Explore More Products -->
        <div class="mt-16">
            <div class="flex items-center justify-center mb-8">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mx-3">Explore More Products</h2>
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <div class="relative">
                        <div class="bg-gray-200 h-64 flex items-center justify-center">
                            <span class="text-gray-400">Product Image</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-1">Microscope</p>
                        <a href="product.php?id=<?php echo $related['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-blue-900 block">
                            <?php echo $related['name']; ?>
                        </a>
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xl font-bold text-gray-800">₦<?php echo number_format($related['price'], 0); ?></p>
                            <button onclick="addToCart(<?php echo $related['id']; ?>)" class="bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition text-sm font-medium">
                                Add To Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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

        function addToWishlist(productId) {
            alert('Wishlist feature coming soon!');
        }

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('text-blue-900', 'border-b-2', 'border-blue-900');
                tab.classList.add('text-gray-600');
            });
            
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('text-gray-600');
            activeTab.classList.add('text-blue-900', 'border-b-2', 'border-blue-900');
        }
    </script>

</body>
</html>