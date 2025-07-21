<?php
session_start();
require_once "dbconn.php";

header('Content-Type: application/json');

if (!isset($_SESSION['email_address'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if (!isset($_GET['recipe_id'])) {
    echo json_encode(['error' => 'Recipe ID not provided']);
    exit();
}

$recipe_id = (int)$_GET['recipe_id'];

$email = $_SESSION['email_address'];
$sql = "SELECT id FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found']);
    exit();
}

$user_id = $result->fetch_assoc()['id'];
$stmt->close();

// Ensure favorites table exists
$check_table_sql = "SHOW TABLES LIKE 'favorites'";
$check_table_result = $conn->query($check_table_sql);

if ($check_table_result->num_rows == 0) {
    $create_table_sql = "CREATE TABLE `favorites` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `recipe_id` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_recipe_unique` (`user_id`,`recipe_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if (!$conn->query($create_table_sql)) {
        echo json_encode(['error' => 'Could not create favorites table']);
        exit();
    }
}

$check_favorite_sql = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
$check_stmt = $conn->prepare($check_favorite_sql);
$check_stmt->bind_param("ii", $user_id, $recipe_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$is_favorite = $check_result->num_rows > 0;
$check_stmt->close();

echo json_encode(['is_favorite' => $is_favorite]);
?>
