<?php
session_start();

// Check if user is logged in (in production, check against database)
$is_logged_in = isset($_SESSION['user_id']);

// If not logged in, redirect to login page
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Sample user data (in production, fetch from database)
$user = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+234 800 000 0000',
    'address' => '123 Medical Plaza, Victoria Island',
    'city' => 'Lagos',
    'state' => 'Lagos',
    'joined' => '2024-01-15'
];

// Sample order history
$orders = [
    [
        'order_number' => 'ORD-12345',
        'date' => '2024-12-15',
        'status' => 'Delivered',
        'total' => 121.97,
        'items' => 2
    ],
    [
        'order_number' => 'ORD-12344',
        'date' => '2024-12-10',
        'status' => 'In Transit',
        'total' => 899.99,
        'items' => 1
    ],
    [
        'order_number' => 'ORD-12343',
        'date' => '2024-12-05',
        'status' => 'Processing',
        'total' => 45.99,
        'items' => 1
    ],
];

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Hospice Medical Supplies</title>
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
                <span class="text-gray-800 font-medium">My Account</span>
            </nav>
        </div>
    </div>

    <!-- Account Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- User Info -->
                    <div class="text-center pb-6 border-b mb-6">
                        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-teal-600"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
                        <p class="text-sm text-gray-500"><?php echo $user['email']; ?></p>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-2">
                        <a href="?tab=dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'dashboard' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="?tab=orders" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'orders' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="font-medium">Orders</span>
                        </a>
                        <a href="?tab=addresses" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'addresses' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Addresses</span>
                        </a>
                        <a href="?tab=profile" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'profile' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-medium">Profile</span>
                        </a>
                        <a href="?tab=wishlist" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'wishlist' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="font-medium">Wishlist</span>
                        </a>
                        <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="font-medium">Logout</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="lg:col-span-3">
                
                <?php if ($active_tab === 'dashboard'): ?>
                <!-- Dashboard -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
                    <p class="text-gray-600 mb-6">Welcome back, <?php echo $user['first_name']; ?>! Here's an overview of your account.</p>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-teal-50 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Total Orders</p>
                                    <p class="text-3xl font-bold text-teal-600"><?php echo count($orders); ?></p>
                                </div>
                                <svg class="w-12 h-12 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Active Orders</p>
                                    <p class="text-3xl font-bold text-blue-600">2</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Wishlist Items</p>
                                    <p class="text-3xl font-bold text-green-600">5</p>
                                </div>
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Order Number</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo $order['order_number']; ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo date('M d, Y', strtotime($order['date'])); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $order['status'] === 'Delivered' ? 'bg-green-100 text-green-800' : ($order['status'] === 'In Transit' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-800">₦<?php echo number_format($order['total'], 2); ?></td>
                                    <td class="px-4 py-3">
                                        <a href="?tab=orders" class="text-teal-600 hover:text-teal-700 text-sm font-medium">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php elseif ($active_tab === 'orders'): ?>
                <!-- Orders -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Order History</h2>
                    <div class="space-y-4">
                        <?php foreach ($orders as $order): ?>
                        <div class="border rounded-lg p-6 hover:shadow-md transition">
                            <div class="flex flex-wrap items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-semibold text-gray-800"><?php echo $order['order_number']; ?></h3>
                                    <p class="text-sm text-gray-600">Placed on <?php echo date('M d, Y', strtotime($order['date'])); ?></p>
                                </div>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold <?php echo $order['status'] === 'Delivered' ? 'bg-green-100 text-green-800' : ($order['status'] === 'In Transit' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between border-t pt-4">
                                <div>
                                    <p class="text-sm text-gray-600"><?php echo $order['items']; ?> item(s) • Total: <span class="font-semibold text-gray-800">₦<?php echo number_format($order['total'], 2); ?></span></p>
                                </div>
                                <div class="flex space-x-3">
                                    <button class="text-teal-600 hover:text-teal-700 text-sm font-medium">View Details</button>
                                    <button class="text-gray-600 hover:text-gray-700 text-sm font-medium">Track Order</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php elseif ($active_tab === 'addresses'): ?>
                <!-- Addresses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Saved Addresses</h2>
                        <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                            Add New Address
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border-2 border-teal-600 rounded-lg p-6 relative">
                            <span class="absolute top-4 right-4 bg-teal-600 text-white text-xs px-2 py-1 rounded">Default</span>
                            <h3 class="font-semibold text-gray-800 mb-2">Home</h3>
                            <p class="text-gray-700"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></p>
                            <p class="text-gray-700"><?php echo $user['address']; ?></p>
                            <p class="text-gray-700"><?php echo $user['city'] . ', ' . $user['state']; ?></p>
                            <p class="text-gray-700"><?php echo $user['phone']; ?></p>
                            <div class="flex space-x-4 mt-4">
                                <button class="text-teal-600 hover:text-teal-700 text-sm font-medium">Edit</button>
                                <button class="text-red-600 hover:text-red-700 text-sm font-medium">Delete</button>
                            </div>
                        </div>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex items-center justify-center hover:border-teal-600 transition cursor-pointer">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="text-gray-600 font-medium">Add New Address</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($active_tab === 'profile'): ?>
                <!-- Profile -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Information</h2>
                    <form method="POST" action="update_profile.php" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                <input 
                                    type="text" 
                                    name="first_name"
                                    value="<?php echo $user['first_name']; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                <input 
                                    type="text" 
                                    name="last_name"
                                    value="<?php echo $user['last_name']; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                name="email"
                                value="<?php echo $user['email']; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input 
                                type="tel" 
                                name="phone"
                                value="<?php echo $user['phone']; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                        </div>

                        <div class="pt-4 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                    <input 
                                        type="password" 
                                        name="current_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    >
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                        <input 
                                            type="password" 
                                            name="new_password"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                        <input 
                                            type="password" 
                                            name="confirm_password"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="bg-teal-600 text-white px-8 py-3 rounded-lg hover:bg-teal-700 transition font-semibold">
                            Save Changes
                        </button>
                    </form>
                </div>

                <?php elseif ($active_tab === 'wishlist'): ?>
                <!-- Wishlist -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition group">
                            <div class="relative">
                                <div class="bg-gray-200 h-48 flex items-center justify-center">
                                    <span class="text-gray-400">Product Image</span>
                                </div>
                                <button class="absolute top-4 right-4 bg-white p-2 rounded-full shadow-md">
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 mb-2">Blood Pressure Monitor</h3>
                                <p class="text-xl font-bold text-gray-800 mb-3">₦45.99</p>
                                <button class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

</body>
</html> 