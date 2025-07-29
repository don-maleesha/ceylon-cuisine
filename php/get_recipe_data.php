<?php
session_start();
require_once "dbconn.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['email_address']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Check if recipe ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
    exit();
}

$recipeId = intval($_GET['id']);

try {
    // Fetch recipe data
    $stmt = $conn->prepare("SELECT id, title, description, image_url, ingredients, instructions FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        exit();
    }
    
    $recipe = $result->fetch_assoc();
    $stmt->close();
    
    // Return recipe data as JSON
    echo json_encode([
        'success' => true,
        'recipe' => $recipe
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching recipe data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?>
