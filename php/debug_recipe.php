<?php
session_start();
require_once "dbconn.php";
require_once "recipe_helpers.php";

// Check if user is logged in
if (!isset($_SESSION['email_address'])) {
    die("User not logged in. Please <a href='signin.php'>sign in</a> to continue.");
}

// Check if recipe ID is provided
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$debug_mode = isset($_GET['debug']) && $_GET['debug'] === '1';
$comprehensive = isset($_GET['comprehensive']) && $_GET['comprehensive'] === '1';

// Get user ID from session
$email = $_SESSION['email_address'];
$sql = "SELECT id FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found in database");
}

$user = $result->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

// Check if admin search mode is active (search by recipe ID across all users)
$admin_search = isset($_GET['admin_search']) && $_GET['admin_search'] === '1' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Fetch all user recipes for the selector
if ($admin_search) {
    // Admin can see all recipes
    $sql = "SELECT id, title, status, user_id FROM recipes ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    // Regular users see only their recipes
    $sql = "SELECT id, title, status FROM recipes WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

// Get specific recipe data if ID is provided
$recipe = null;
$original_recipe = null;
$standardized_recipe = null;

if ($recipe_id) {
    if ($admin_search) {
        // Admin can view any recipe
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
    } else {
        // Regular users can only view their own recipes
        $sql = "SELECT * FROM recipes WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $recipe_id, $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $original_recipe = $result->fetch_assoc();
        $standardized_recipe = standardize_recipe_data($original_recipe);
    } else {
        echo "<p class='error'>Recipe not found or not owned by this user.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Data Debugger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .recipe-selector {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        select, button, input[type="checkbox"] {
            padding: 8px 12px;
            margin-right: 10px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .checkbox-label {
            display: inline-flex;
            align-items: center;
            margin-right: 15px;
        }
        .checkbox-label input {
            margin-right: 5px;
        }
        .card {
            padding: 20px;
            margin: 15px 0;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .debug-section {
            background: #f8f8f8;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin: 10px 0;
            overflow-x: auto;
        }
        pre {
            background: #f1f1f1;
            padding: 10px;
            overflow-x: auto;
            border-radius: 4px;
        }
        .json-view {
            font-family: monospace;
            white-space: pre;
            background: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        .warning {
            color: #e67e22;
            font-weight: bold;
        }
        .nav-links {
            margin: 20px 0;
        }
        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #3498db;
        }
        .ingredient-list, .instruction-list {
            list-style-type: none;
            padding-left: 0;
        }
        .ingredient-list li, .instruction-list li {
            padding: 8px;
            margin-bottom: 5px;
            background: #f9f9f9;
            border-left: 3px solid #3498db;
        }
        .instruction-list li {
            border-left-color: #e67e22;
        }
        .fixed-data {
            background: #e8f8f5;
            border-left: 4px solid #2ecc71;
            padding: 15px;
            margin: 15px 0;
        }
        .tabs {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            border-color: #ddd;
            background: white;
            font-weight: bold;
        }
        .tab-content {
            display: none;
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
            background: white;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recipe Data Debugger</h1>
        
        <div class="nav-links">
            <a href="profile.php">Back to Profile</a>
            <a href="recipes.php">All Recipes</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="?admin_search=1">Admin Search Mode</a>
            <?php endif; ?>
        </div>
        
        <div class="recipe-selector">
            <h2>Select a Recipe to Debug</h2>            <form action="debug_recipe.php" method="GET">
                <select name="id" id="recipe-select">
                    <option value="">-- Select a Recipe --</option>
                    <?php foreach ($recipes as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($recipe_id == $r['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['title'] ?? 'Unknown Title') ?> 
                            (<?= $r['status'] ?? 'Unknown Status' ?>)
                            <?= isset($r['user_id']) && $admin_search ? ' - User ID: ' . $r['user_id'] : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <div style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="checkbox-label">
                        <input type="checkbox" id="debug-mode" name="debug" value="1" <?= $debug_mode ? 'checked' : '' ?>>
                        <span>Show Raw Debug Data</span>
                    </span>
                    
                    <span class="checkbox-label">
                        <input type="checkbox" id="comprehensive" name="comprehensive" value="1" <?= $comprehensive ? 'checked' : '' ?>>
                        <span>Comprehensive Analysis</span>
                    </span>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <span class="checkbox-label">
                        <input type="checkbox" id="admin-search" name="admin_search" value="1" <?= $admin_search ? 'checked' : '' ?>>
                        <span>Admin Search Mode</span>
                    </span>
                    <?php endif; ?>
                </div>
                
                <button type="submit">Load Recipe</button>
            </form>
        </div>
        
        <?php if ($original_recipe): ?>
            <div class="card">
                <h2><?= htmlspecialchars($original_recipe['title'] ?? 'Unknown Title') ?></h2>
                
                <div class="tabs">
                    <div class="tab active" data-tab="preview">Preview</div>
                    <div class="tab" data-tab="standardized">Standardized Data</div>
                    <div class="tab" data-tab="raw">Raw Data</div>
                    <div class="tab" data-tab="modal-test">Modal Test</div>
                </div>
                
                <!-- Preview Tab -->
                <div class="tab-content active" id="tab-preview">
                    <div class="preview-section">
                        <h3>Ingredients Preview</h3>
                        <ul class="ingredient-list">
                            <?php 
                            $ingredients = json_decode($standardized_recipe['ingredients'], true);
                            if (empty($ingredients)): 
                            ?>
                                <li class="error">No ingredients found</li>
                            <?php else: ?>
                                <?php foreach ($ingredients as $ingredient): ?>
                                    <li><?= htmlspecialchars($ingredient) ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        
                        <h3>Instructions Preview</h3>
                        <ol class="instruction-list">
                            <?php 
                            $instructions = json_decode($standardized_recipe['instructions'], true);
                            if (empty($instructions)): 
                            ?>
                                <li class="error">No instructions found</li>
                            <?php else: ?>
                                <?php foreach ($instructions as $instruction): ?>
                                    <li><?= htmlspecialchars($instruction) ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
                
                <!-- Standardized Data Tab -->
                <div class="tab-content" id="tab-standardized">
                    <?php if ($comprehensive): ?>
                        <?= debug_recipe_data_comprehensive($original_recipe) ?>
                    <?php else: ?>
                        <h3>Standardized Recipe Data</h3>
                        <?= debug_recipe_data($standardized_recipe) ?>
                    <?php endif; ?>
                </div>
                
                <!-- Raw Data Tab -->
                <div class="tab-content" id="tab-raw">
                    <h3>Original Recipe Data</h3>
                    <?= debug_recipe_data($original_recipe) ?>
                    
                    <div class="debug-section">
                        <h4>Raw Ingredients Data</h4>
                        <pre><?= htmlspecialchars($original_recipe['ingredients'] ?? 'NULL') ?></pre>
                        
                        <h4>Raw Instructions Data</h4>
                        <pre><?= htmlspecialchars($original_recipe['instructions'] ?? 'NULL') ?></pre>
                    </div>
                </div>
                
                <!-- Modal Test Tab -->
                <div class="tab-content" id="tab-modal-test">
                    <h3>Test in JavaScript Modal</h3>
                    <p>This simulates how the recipe will appear in the viewRecipe modal on the profile page.</p>
                    <button onclick="testInModal()">Run Modal Simulation</button>
                    <div id="modal-result" style="margin-top: 15px;"></div>
                </div>
                
                <div class="fixed-data">
                    <h3>How to Use This Tool</h3>
                    <p>This debugging tool shows how your recipe data is being processed:</p>
                    <ol>
                        <li>Select any recipe from the dropdown to see how its data is handled</li>
                        <li>The "Comprehensive Analysis" option provides a detailed breakdown of how the data is processed</li>
                        <li>Use the "Modal Test" tab to simulate exactly how the recipe will display in the JavaScript modal</li>
                    </ol>
                    <p>If you see your recipe content correctly displayed here but not in the recipe modal, there might be a JavaScript issue.</p>
                </div>
            </div>
        <?php elseif ($recipe_id): ?>
            <div class="card">
                <p class="error">Recipe not found or not owned by this user.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <p>Please select a recipe to debug.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Tab functionality
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = 'tab-' + this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    function testInModal() {
        const result = document.getElementById('modal-result');
        result.innerHTML = '<div class="debug-section"><p>Processing...</p></div>';
        
        <?php if ($standardized_recipe): ?>
        // Use the standardized recipe data
        const recipeId = <?= json_encode($standardized_recipe['id'] ?? '') ?>;
        const title = <?= json_encode($standardized_recipe['title'] ?? '') ?>;
        const description = <?= json_encode($standardized_recipe['description'] ?? '') ?>;
        const imageUrl = <?= json_encode($standardized_recipe['image_url'] ?? '') ?>;
        const ingredients = <?= $standardized_recipe['ingredients'] ?? '[]' ?>;
        const instructions = <?= $standardized_recipe['instructions'] ?? '[]' ?>;
          // Simulate the viewRecipe function from profile.js
        let ingredientsArray = [];
        let instructionsArray = [];
        
        // Process ingredients - exactly like in the viewRecipe function
        if (typeof ingredients === "string") {
            try {
                // Try to parse as JSON first
                const parsed = JSON.parse(ingredients);
                ingredientsArray = Array.isArray(parsed) ? parsed : [parsed];
            } catch (e) {
                // If JSON parsing fails, try comma split
                if (ingredients.includes("\n")) {
                    ingredientsArray = ingredients.split("\n").map(item => item.trim()).filter(item => item);
                } else if (ingredients.includes("|")) {
                    ingredientsArray = ingredients.split("|").map(item => item.trim()).filter(item => item);
                } else if (ingredients.includes(";")) {
                    ingredientsArray = ingredients.split(";").map(item => item.trim()).filter(item => item);
                } else {
                    ingredientsArray = ingredients.split(",").map(item => item.trim()).filter(item => item);
                }
            }
        } else if (Array.isArray(ingredients)) {
            ingredientsArray = ingredients;
        } else if (ingredients) {
            ingredientsArray = [ingredients.toString()];
        }
        
        // Process instructions - exactly like in the viewRecipe function
        if (typeof instructions === "string") {
            try {
                // Try to parse as JSON first
                const parsed = JSON.parse(instructions);
                instructionsArray = Array.isArray(parsed) ? parsed : [parsed];
            } catch (e) {
                // If JSON parsing fails, try paragraph split or comma split
                if (instructions.includes("\n")) {
                    instructionsArray = instructions.split("\n").map(item => item.trim()).filter(item => item);
                } else if (instructions.includes("|")) {
                    instructionsArray = instructions.split("|").map(item => item.trim()).filter(item => item);
                } else if (instructions.includes(";")) {
                    instructionsArray = instructions.split(";").map(item => item.trim()).filter(item => item);
                } else {
                    instructionsArray = instructions.split(",").map(item => item.trim()).filter(item => item);
                }
            }
        } else if (Array.isArray(instructions)) {
            instructionsArray = instructions;
        } else if (instructions) {
            instructionsArray = [instructions.toString()];
        }
        
        // Generate the modal result HTML
        let html = `
            <div class="debug-section">
                <h3>JavaScript Modal Display Simulation</h3>
                <p>This is exactly how the recipe would appear in the profile page modal:</p>
                
                <div class="modal-simulation" style="border: 1px solid #ddd; padding: 20px; border-radius: 5px; background: white;">
                    <h2>${title}</h2>
                    ${description ? `<p>${description}</p>` : ''}
                    ${imageUrl ? `<div style="margin: 10px 0;"><img src="${imageUrl}" alt="${title}" style="max-width: 100%; max-height: 300px;"></div>` : ''}
                    
                    <h3>Ingredients</h3>
                    ${ingredientsArray.length > 0 ? 
                        `<ul>${ingredientsArray.map(item => `<li>${item}</li>`).join('')}</ul>` : 
                        `<p class="error">No ingredients found</p>`}
                    
                    <h3>Instructions</h3>
                    ${instructionsArray.length > 0 ? 
                        `<ol>${instructionsArray.map(item => `<li>${item}</li>`).join('')}</ol>` : 
                        `<p class="error">No instructions found</p>`}
                </div>
                
                <div style="margin-top: 20px;">
                    <h4>JavaScript Data Processing Steps</h4>
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace;">
                        <p><strong>Original ingredients value:</strong> ${typeof ingredients === 'string' ? `"${ingredients}"` : JSON.stringify(ingredients)}</p>
                        <p><strong>Ingredients type:</strong> ${typeof ingredients}</p>
                        <p><strong>After JavaScript processing:</strong> ${JSON.stringify(ingredientsArray)}</p>
                        <p><strong>Final ingredients count:</strong> ${ingredientsArray.length}</p>
                        
                        <p><strong>Original instructions value:</strong> ${typeof instructions === 'string' ? `"${instructions}"` : JSON.stringify(instructions)}</p>
                        <p><strong>Instructions type:</strong> ${typeof instructions}</p>
                        <p><strong>After JavaScript processing:</strong> ${JSON.stringify(instructionsArray)}</p>
                        <p><strong>Final instructions count:</strong> ${instructionsArray.length}</p>
                    </div>
                </div>
            </div>
        `;
          result.innerHTML = html;
        
        <?php if (!$standardized_recipe): ?>
        result.innerHTML = '<div class="debug-section"><p class="error">No recipe data available.</p></div>';
        <?php endif; ?>
    }
    </script>
</body>
</html>try {
            // Process ingredients
            if (Array.isArray(ingredients)) {
                ingredientsArray = ingredients;
            } else if (typeof ingredients === 'string') {
                try {
                    const parsed = JSON.parse(ingredients);
                    ingredientsArray = Array.isArray(parsed) ? parsed : [String(parsed)];
                } catch (e) {
                    if (ingredients.includes(',')) {
                        ingredientsArray = ingredients.split(',').map(item => item.trim());
                    } else if (ingredients.includes('\n')) {
                        ingredientsArray = ingredients.split('\n').map(item => item.trim());
                    } else {
                        ingredientsArray = [ingredients];
                    }
                }
            } else if (ingredients) {
                ingredientsArray = [String(ingredients)];
            }
            
            // Process instructions
            if (Array.isArray(instructions)) {
                instructionsArray = instructions;
            } else if (typeof instructions === 'string') {
                try {
                    const parsed = JSON.parse(instructions);
                    instructionsArray = Array.isArray(parsed) ? parsed : [String(parsed)];
                } catch (e) {
                    if (instructions.includes(',')) {
                        instructionsArray = instructions.split(',').map(item => item.trim());
                    } else if (instructions.includes('\n')) {
                        instructionsArray = instructions.split('\n').map(item => item.trim());
                    } else {
                        instructionsArray = [instructions];
                    }
                }
            } else if (instructions) {
                instructionsArray = [String(instructions)];
            }
            
            // Filter out empty items
            ingredientsArray = ingredientsArray.filter(item => item && String(item).trim() !== '');
            instructionsArray = instructionsArray.filter(item => item && String(item).trim() !== '');
            
            // Display results
            let html = `
                <h4>JavaScript Modal Simulation</h4>
                <div class="card">
                    <h3>${title}</h3>
                    <p><strong>Ingredients:</strong></p>
                    <ul>
            `;
            
            if (ingredientsArray.length === 0) {
                html += '<li class="error">No ingredients available</li>';
            } else {
                ingredientsArray.forEach(ingredient => {
                    html += `<li>${ingredient}</li>`;
                });
            }
            
            html += `
                    </ul>
                    <p><strong>Instructions:</strong></p>
                    <ol>
            `;
            
            if (instructionsArray.length === 0) {
                html += '<li class="error">No instructions available</li>';
            } else {
                instructionsArray.forEach(instruction => {
                    html += `<li>${instruction}</li>`;
                });
            }
            
            html += `
                    </ol>
                </div>
                <div class="debug-section">
                    <h4>Raw Data Used:</h4>
                    <p><strong>Ingredients:</strong> <pre>${JSON.stringify(ingredients, null, 2)}</pre></p>
                    <p><strong>Instructions:</strong> <pre>${JSON.stringify(instructions, null, 2)}</pre></p>
                    <p><strong>Processed Ingredients Array:</strong> <pre>${JSON.stringify(ingredientsArray, null, 2)}</pre></p>
                    <p><strong>Processed Instructions Array:</strong> <pre>${JSON.stringify(instructionsArray, null, 2)}</pre></p>
                </div>
            `;
            
            result.innerHTML = html;
        } catch (error) {
            result.innerHTML = `<div class="debug-section error">
                <h4>Error in JavaScript Processing</h4>
                <p>${error.message}</p>
                <p>Stack: ${error.stack}</p>
            </div>`;
        }
        <?php else: ?>
        result.innerHTML = '<div class="debug-section error"><p>No recipe data available</p></div>';
        <?php endif; ?>
    }
    </script>
</body>
</html>
