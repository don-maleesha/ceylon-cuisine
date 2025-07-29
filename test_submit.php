<?php
// Simple test file to debug recipe submission
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

echo "<h2>Recipe Submission Test</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>FILES Data Received:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<h3>Session Data:</h3>";
    session_start();
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    // Check if submit-recipe is set
    if (isset($_POST['submit-recipe'])) {
        echo "<p style='color: green;'>✓ submit-recipe parameter found!</p>";
    } else {
        echo "<p style='color: red;'>✗ submit-recipe parameter NOT found!</p>";
    }
    
    // Check required fields
    $required_fields = ['title', 'description', 'ingredients', 'instructions'];
    foreach ($required_fields as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            echo "<p style='color: green;'>✓ $field: " . htmlspecialchars($_POST[$field]) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ $field: missing or empty</p>";
        }
    }
    
} else {
    echo "<p>No POST data received. Please submit the form from the profile page.</p>";
}

echo "<p><a href='php/profile.php'>Back to Profile</a></p>";
?>
