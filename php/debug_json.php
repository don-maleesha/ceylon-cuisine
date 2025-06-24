<?php
// Debugging script to help diagnose JSON parsing issues
session_start();

header('Content-Type: application/json');

// Simulate a recipe with ingredients and instructions
$recipe = [
    'id' => 1,
    'title' => 'Test Recipe',
    'description' => 'Test Description',
    'image_url' => 'test.jpg',
    'ingredients' => json_encode(['Ingredient 1', 'Ingredient 2', 'Ingredient 3']),
    'instructions' => json_encode(['Step 1', 'Step 2', 'Step 3'])
];

// Echo what we have in PHP
echo json_encode([
    'original_ingredients' => $recipe['ingredients'],
    'original_instructions' => $recipe['instructions'],
    'decoded_ingredients' => json_decode($recipe['ingredients']),
    'decoded_instructions' => json_decode($recipe['instructions']),
    'json_encode_decode_ingredients' => json_encode(json_decode($recipe['ingredients'])),
    'json_encode_decode_instructions' => json_encode(json_decode($recipe['instructions'])),
    'double_encoded' => json_encode([
        'ingredients' => $recipe['ingredients'],
        'instructions' => $recipe['instructions']
    ])
]);
