<?php
session_start();
require_once "dbconn.php";
require_once "recipe_helpers.php"; // Include helper functions

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if recipe ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Recipe ID is required']);
    exit;
}

$recipe_id = intval($_GET['id']);

// Get user ID from session
$sql = "SELECT id FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email_address']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

// Get recipe data
$sql = "SELECT * FROM recipes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Recipe not found or not owned by this user']);
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Use our helper function to standardize the recipe data
$recipe = standardize_recipe_data($recipe);

// Return recipe data
echo json_encode([
    'success' => true,
    'recipe' => $recipe
]);
