<?php
// 1. Include the configuration file to connect to the database.
require_once 'config.php';
// 2. Set the content type to JSON, as this script will only communicate with JavaScript.
header('Content-Type: application/json');

// 3. Get the user data sent from the Firebase JavaScript in the login page.
$data = json_decode(file_get_contents("php://input"));

// 4. Basic validation to ensure we received the necessary data from the frontend.
if (!$data || !isset($data->email) || !isset($data->uid)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received from Firebase.']);
    exit;
}

// 5. Sanitize the data received.
$email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
$firebase_uid = htmlspecialchars($data->uid);
$displayName = htmlspecialchars($data->displayName ?? 'New User');

// Split the user's full name into first name and last name.
$name_parts = explode(' ', $displayName, 2);
$first_name = $name_parts[0];
$last_name = $name_parts[1] ?? ''; // The last name might not exist.

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

try {
    // 6. Check if a user with this email already exists in your database.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 7. USER EXISTS: Log them in by creating a PHP session.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];

        // If their firebase_uid is not set (maybe they signed up with email first), update it now.
        if (empty($user['firebase_uid'])) {
            $update_stmt = $pdo->prepare("UPDATE users SET firebase_uid = ? WHERE id = ?");
            $update_stmt->execute([$firebase_uid, $user['id']]);
        }
        
        echo json_encode(['success' => true, 'action' => 'logged_in']);
    } else {
        // 8. USER DOES NOT EXIST: Create a new account for them in your database.
        $insert_stmt = $pdo->prepare(
            "INSERT INTO users (first_name, last_name, email, firebase_uid, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        
        if ($insert_stmt->execute([$first_name, $last_name, $email, $firebase_uid])) {
            // Get the ID of the new user we just created.
            $new_user_id = $pdo->lastInsertId();
            
            // Log the new user in immediately by creating a PHP session.
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_name'] = $first_name;
            echo json_encode(['success' => true, 'action' => 'registered']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create a new user account.']);
        }
    }
} catch (PDOException $e) {
    // In a real production environment, you would log this error to a file instead of displaying it.
    error_log("Firebase Auth Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}
?>

