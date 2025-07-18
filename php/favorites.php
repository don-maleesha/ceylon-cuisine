<?php
include 'dbconn.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage favorites']);
    exit;
}

$user_id = $_SESSION['user_id'];
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($recipe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
    exit;
}

// Check if recipe exists
$stmt = $conn->prepare("SELECT id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Recipe not found']);
    exit;
}
$stmt->close();

if ($action === 'add') {
    // Add to favorites
    $stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_id, recipe_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Recipe added to favorites', 'action' => 'added']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Recipe already in favorites', 'action' => 'already_exists']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
    }
    $stmt->close();
    
} elseif ($action === 'remove') {
    // Remove from favorites
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Recipe removed from favorites', 'action' => 'removed']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Recipe was not in favorites', 'action' => 'not_found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
    }
    $stmt->close();
    
} elseif ($action === 'toggle') {
    // Toggle favorite status
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $isFavorite = $result->num_rows > 0;
    $stmt->close();
    
    if ($isFavorite) {
        // Remove from favorites
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Recipe removed from favorites', 'action' => 'removed', 'is_favorite' => false]);
        $stmt->close();
    } else {
        // Add to favorites
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Recipe added to favorites', 'action' => 'added', 'is_favorite' => true]);
        $stmt->close();
    }
    
} elseif ($action === 'check') {
    // Check if recipe is in favorites
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $isFavorite = $result->num_rows > 0;
    $stmt->close();
    
    echo json_encode(['success' => true, 'is_favorite' => $isFavorite]);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
