<?php
require 'dbconn.php';
session_start();

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Set JSON header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to rate recipes']);
    exit;
}

// Get and validate input data
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$user_id = (int)$_SESSION['user_id'];

// Validate input
if ($rating < 1 || $rating > 5 || $recipe_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating value']);
    exit;
}

try {
    $conn->begin_transaction();

    // Insert or update rating
    $stmt = $conn->prepare("INSERT INTO ratings (user_id, recipe_id, rating) 
                           VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE rating = ?");
    $stmt->bind_param("iiii", $user_id, $recipe_id, $rating, $rating);
    $stmt->execute();

    // Calculate new average rating
    $avg_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM ratings WHERE recipe_id = ?");
    $avg_stmt->bind_param("i", $recipe_id);
    $avg_stmt->execute();
    $result = $avg_stmt->get_result()->fetch_assoc();

    // Update recipe average rating
    $update_stmt = $conn->prepare("UPDATE recipes SET average_rating = ? WHERE id = ?");
    $update_stmt->bind_param("di", $result['avg_rating'], $recipe_id);
    $update_stmt->execute();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'average_rating' => round($result['avg_rating'], 1)
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>