<?php
require_once 'config.php';

// If already logged in, redirect to account page
if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit;
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                header('Location: account.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
            // error_log($e->getMessage());
        }
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'An account with this email already exists.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($insert_stmt->execute([$first_name, $last_name, $email, $hashed_password])) {
                    $success = 'Account created successfully! You can now log in.';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'A database error occurred.';
            // error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register - WEDEX Healthcare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="container mx-auto px-4 py-16">
        <div class="max-w-5xl mx-auto">
            
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Login Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Login to Your Account</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="your.email@example.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Enter your password">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center"><input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded"><span class="ml-2 text-sm text-gray-600">Remember me</span></label>
                            <a href="#" class="text-sm text-teal-600 hover:text-teal-700">Forgot Password?</a>
                        </div>
                        <button type="submit" name="login" class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Login</button>
                    </form>
                </div>

                <!-- Registration Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h2>
                    <form method="POST" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                <input type="text" id="first_name" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div>
                            <label for="reg_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="reg_email" name="reg_email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="your.email@example.com">
                        </div>
                        <div>
                            <label for="reg_password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" id="reg_password" name="reg_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Re-enter password">
                        </div>
                        <button type="submit" name="register" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>
