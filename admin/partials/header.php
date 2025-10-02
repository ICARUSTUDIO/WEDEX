<?php
// This file is included at the top of all admin pages.
// It checks for a valid admin session and includes the common HTML head and navigation.
// The config.php file, which is required before this file, handles starting the session.

// If the user is not logged in (the session variable is not set), redirect to the login page.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold text-teal-600">WEDEX Admin</a>
                <nav class="flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-600 hover:text-teal-600 font-medium">Dashboard</a>
                    <a href="../index.php" target="_blank" class="text-gray-600 hover:text-teal-600 font-medium">View Site</a>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout.php" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-900 transition">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <main>

