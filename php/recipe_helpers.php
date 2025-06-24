<?php
/**
 * Helper functions for handling recipe data
 */

/**
 * Standardizes the ingredients and instructions data for display
 * Ensures the data is properly formatted JSON, handling various input formats
 * 
 * @param array $recipe The recipe data array to process
 * @return array The processed recipe data
 */
function standardize_recipe_data($recipe) {
    // Handle ingredients
    if (!isset($recipe['ingredients']) || $recipe['ingredients'] === null) {
        $recipe['ingredients'] = json_encode([]);
    } else {
        // Check if it's already valid JSON
        $decoded = json_decode($recipe['ingredients'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Filter out empty items and re-encode
            $decoded = array_filter($decoded, function($item) { 
                return trim($item) !== ''; 
            });
            $recipe['ingredients'] = json_encode(array_values($decoded));
        } else {
            // Not valid JSON, try to convert it
            $ingredients = [];
            
            // If it's a string, try to parse it
            if (is_string($recipe['ingredients'])) {
                // Check if it's escaped JSON
                $doubleDecoded = json_decode(stripslashes($recipe['ingredients']), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($doubleDecoded)) {
                    $ingredients = $doubleDecoded;
                }
                // Try as pipe-separated list
                elseif (strpos($recipe['ingredients'], '|') !== false) {
                    $ingredients = array_map('trim', explode('|', $recipe['ingredients']));
                }
                // Try as semicolon-separated list
                elseif (strpos($recipe['ingredients'], ';') !== false) {
                    $ingredients = array_map('trim', explode(';', $recipe['ingredients']));
                }
                // Try as comma-separated list
                elseif (strpos($recipe['ingredients'], ',') !== false) {
                    $ingredients = array_map('trim', explode(',', $recipe['ingredients']));
                } 
                // Try as newline-separated list
                elseif (strpos($recipe['ingredients'], "\n") !== false) {
                    $ingredients = array_map('trim', explode("\n", $recipe['ingredients']));
                }
                // Otherwise, treat as a single item
                else {
                    $ingredients = [$recipe['ingredients']];
                }
            } elseif (is_array($recipe['ingredients'])) {
                // If it's already an array, use it directly
                $ingredients = $recipe['ingredients'];
            } else {
                // Not a string or array, convert to string and use as single item
                $ingredients = [(string)$recipe['ingredients']];
            }
            
            // Filter out empty items
            $ingredients = array_filter($ingredients, function($item) { 
                return trim($item) !== ''; 
            });
            
            // Re-encode as proper JSON
            $recipe['ingredients'] = json_encode(array_values($ingredients));
        }
    }
    
    // Handle instructions
    if (!isset($recipe['instructions']) || $recipe['instructions'] === null) {
        $recipe['instructions'] = json_encode([]);
    } else {
        // Check if it's already valid JSON
        $decoded = json_decode($recipe['instructions'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Filter out empty items and re-encode
            $decoded = array_filter($decoded, function($item) { 
                return trim($item) !== ''; 
            });
            $recipe['instructions'] = json_encode(array_values($decoded));
        } else {
            // Not valid JSON, try to convert it
            $instructions = [];
            
            // If it's a string, try to parse it
            if (is_string($recipe['instructions'])) {
                // Check if it's escaped JSON
                $doubleDecoded = json_decode(stripslashes($recipe['instructions']), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($doubleDecoded)) {
                    $instructions = $doubleDecoded;
                }
                // Try as pipe-separated list
                elseif (strpos($recipe['instructions'], '|') !== false) {
                    $instructions = array_map('trim', explode('|', $recipe['instructions']));
                }
                // Try as semicolon-separated list
                elseif (strpos($recipe['instructions'], ';') !== false) {
                    $instructions = array_map('trim', explode(';', $recipe['instructions']));
                }
                // Try as comma-separated list
                elseif (strpos($recipe['instructions'], ',') !== false) {
                    $instructions = array_map('trim', explode(',', $recipe['instructions']));
                } 
                // Try as newline-separated list
                elseif (strpos($recipe['instructions'], "\n") !== false) {
                    $instructions = array_map('trim', explode("\n", $recipe['instructions']));
                }
                // Otherwise, treat as a single item
                else {
                    $instructions = [$recipe['instructions']];
                }
            } elseif (is_array($recipe['instructions'])) {
                // If it's already an array, use it directly
                $instructions = $recipe['instructions'];
            } else {
                // Not a string or array, convert to string and use as single item
                $instructions = [(string)$recipe['instructions']];
            }
            
            // Filter out empty items
            $instructions = array_filter($instructions, function($item) { 
                return trim($item) !== ''; 
            });
            
            // Re-encode as proper JSON
            $recipe['instructions'] = json_encode(array_values($instructions));
        }
    }
    
    return $recipe;
}

/**
 * Debug function to help diagnose recipe data issues
 * 
 * @param array $recipe The recipe data array to debug
 * @return string HTML formatted debug information
 */
function debug_recipe_data($recipe) {
    $debug = '<div style="border: 2px solid red; padding: 10px; margin: 10px; background: #ffe6e6;">';
    $debug .= '<h3>Recipe Data Debug</h3>';
    
    // Recipe ID and title
    $debug .= '<p><strong>ID:</strong> ' . htmlspecialchars($recipe['id'] ?? 'Not set') . '</p>';
    $debug .= '<p><strong>Title:</strong> ' . htmlspecialchars($recipe['title'] ?? 'Not set') . '</p>';
    
    // Ingredients information
    $debug .= '<h4>Ingredients Data:</h4>';
    $debug .= '<p><strong>Raw Value:</strong> <pre>' . htmlspecialchars(var_export($recipe['ingredients'] ?? 'Not set', true)) . '</pre></p>';
    
    // Try to decode ingredients
    $debug .= '<p><strong>JSON Decode Result:</strong> ';
    $decoded = json_decode($recipe['ingredients'] ?? '[]', true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $debug .= 'Valid JSON ✓';
        $debug .= '<br><strong>Decoded Value:</strong> <pre>' . htmlspecialchars(var_export($decoded, true)) . '</pre>';
    } else {
        $debug .= 'Invalid JSON ✗ - Error: ' . json_last_error_msg();
    }
    $debug .= '</p>';
    
    // Instructions information
    $debug .= '<h4>Instructions Data:</h4>';
    $debug .= '<p><strong>Raw Value:</strong> <pre>' . htmlspecialchars(var_export($recipe['instructions'] ?? 'Not set', true)) . '</pre></p>';
    
    // Try to decode instructions
    $debug .= '<p><strong>JSON Decode Result:</strong> ';
    $decoded = json_decode($recipe['instructions'] ?? '[]', true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $debug .= 'Valid JSON ✓';
        $debug .= '<br><strong>Decoded Value:</strong> <pre>' . htmlspecialchars(var_export($decoded, true)) . '</pre>';
    } else {
        $debug .= 'Invalid JSON ✗ - Error: ' . json_last_error_msg();
    }
    $debug .= '</p>';
    
    $debug .= '</div>';
    
    return $debug;
}

/**
 * Comprehensive debug function to analyze recipe data and display detailed diagnostics
 * 
 * @param array $recipe The recipe array to debug
 * @param bool $verbose Whether to show verbose output with step-by-step analysis
 * @return string HTML output with debug information
 */
function debug_recipe_data_comprehensive($recipe, $verbose = true) {
    $output = '<div style="font-family: monospace; background: #f5f5f5; padding: 20px; border: 1px solid #ddd; margin: 20px 0;">';
    $output .= '<h2>Recipe Debug: ' . htmlspecialchars($recipe['recipe_name'] ?? 'Unknown Recipe') . '</h2>';
    
    // Basic info
    $output .= '<h3>Basic Recipe Info</h3>';
    $output .= '<ul>';
    $output .= '<li>Recipe ID: ' . htmlspecialchars($recipe['recipe_id'] ?? 'N/A') . '</li>';
    $output .= '<li>User ID: ' . htmlspecialchars($recipe['user_id'] ?? 'N/A') . '</li>';
    $output .= '<li>Category: ' . htmlspecialchars($recipe['category'] ?? 'N/A') . '</li>';
    $output .= '</ul>';
    
    // Original data analysis
    $output .= '<h3>Original Data Analysis</h3>';
    
    // Ingredients analysis
    $output .= '<h4>Ingredients</h4>';
    if (!isset($recipe['ingredients']) || $recipe['ingredients'] === null) {
        $output .= '<p style="color: red;">WARNING: Ingredients field is missing or null</p>';
    } else {
        $output .= '<p>Raw Value: <code>' . htmlspecialchars($recipe['ingredients']) . '</code></p>';
        $output .= '<p>Data Type: <code>' . gettype($recipe['ingredients']) . '</code></p>';
        
        if (is_string($recipe['ingredients'])) {
            $output .= '<p>String Length: ' . strlen($recipe['ingredients']) . '</p>';
            $output .= '<p>Contains comma: ' . (strpos($recipe['ingredients'], ',') !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains newline: ' . (strpos($recipe['ingredients'], "\n") !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains pipe: ' . (strpos($recipe['ingredients'], '|') !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains semicolon: ' . (strpos($recipe['ingredients'], ';') !== false ? 'Yes' : 'No') . '</p>';
            
            // Try JSON decode
            $decoded = json_decode($recipe['ingredients'], true);
            $json_error = json_last_error();
            $output .= '<p>JSON Decode Result: ' . ($json_error === JSON_ERROR_NONE ? 'Valid JSON' : 'Invalid JSON: ' . json_last_error_msg()) . '</p>';
            
            if ($json_error === JSON_ERROR_NONE) {
                $output .= '<p>Decoded JSON Type: ' . gettype($decoded) . '</p>';
                if (is_array($decoded)) {
                    $output .= '<p>Array Count: ' . count($decoded) . '</p>';
                    $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($decoded, true)) . '</pre></p>';
                }
            }
            
            // Try double decode (for escaped JSON)
            $doubleDecoded = json_decode(stripslashes($recipe['ingredients']), true);
            $double_json_error = json_last_error();
            if ($double_json_error === JSON_ERROR_NONE && $json_error !== JSON_ERROR_NONE) {
                $output .= '<p style="color: orange;">WARNING: This appears to be escaped JSON</p>';
                $output .= '<p>Double-Decoded JSON Type: ' . gettype($doubleDecoded) . '</p>';
                if (is_array($doubleDecoded)) {
                    $output .= '<p>Array Count: ' . count($doubleDecoded) . '</p>';
                    $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($doubleDecoded, true)) . '</pre></p>';
                }
            }
        } elseif (is_array($recipe['ingredients'])) {
            $output .= '<p>Array Count: ' . count($recipe['ingredients']) . '</p>';
            $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($recipe['ingredients'], true)) . '</pre></p>';
        }
    }
    
    // Instructions analysis
    $output .= '<h4>Instructions</h4>';
    if (!isset($recipe['instructions']) || $recipe['instructions'] === null) {
        $output .= '<p style="color: red;">WARNING: Instructions field is missing or null</p>';
    } else {
        $output .= '<p>Raw Value: <code>' . htmlspecialchars($recipe['instructions']) . '</code></p>';
        $output .= '<p>Data Type: <code>' . gettype($recipe['instructions']) . '</code></p>';
        
        if (is_string($recipe['instructions'])) {
            $output .= '<p>String Length: ' . strlen($recipe['instructions']) . '</p>';
            $output .= '<p>Contains comma: ' . (strpos($recipe['instructions'], ',') !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains newline: ' . (strpos($recipe['instructions'], "\n") !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains pipe: ' . (strpos($recipe['instructions'], '|') !== false ? 'Yes' : 'No') . '</p>';
            $output .= '<p>Contains semicolon: ' . (strpos($recipe['instructions'], ';') !== false ? 'Yes' : 'No') . '</p>';
            
            // Try JSON decode
            $decoded = json_decode($recipe['instructions'], true);
            $json_error = json_last_error();
            $output .= '<p>JSON Decode Result: ' . ($json_error === JSON_ERROR_NONE ? 'Valid JSON' : 'Invalid JSON: ' . json_last_error_msg()) . '</p>';
            
            if ($json_error === JSON_ERROR_NONE) {
                $output .= '<p>Decoded JSON Type: ' . gettype($decoded) . '</p>';
                if (is_array($decoded)) {
                    $output .= '<p>Array Count: ' . count($decoded) . '</p>';
                    $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($decoded, true)) . '</pre></p>';
                }
            }
            
            // Try double decode (for escaped JSON)
            $doubleDecoded = json_decode(stripslashes($recipe['instructions']), true);
            $double_json_error = json_last_error();
            if ($double_json_error === JSON_ERROR_NONE && $json_error !== JSON_ERROR_NONE) {
                $output .= '<p style="color: orange;">WARNING: This appears to be escaped JSON</p>';
                $output .= '<p>Double-Decoded JSON Type: ' . gettype($doubleDecoded) . '</p>';
                if (is_array($doubleDecoded)) {
                    $output .= '<p>Array Count: ' . count($doubleDecoded) . '</p>';
                    $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($doubleDecoded, true)) . '</pre></p>';
                }
            }
        } elseif (is_array($recipe['instructions'])) {
            $output .= '<p>Array Count: ' . count($recipe['instructions']) . '</p>';
            $output .= '<p>Array Items: <pre>' . htmlspecialchars(print_r($recipe['instructions'], true)) . '</pre></p>';
        }
    }
    
    // Standardization simulation
    $output .= '<h3>Standardization Process</h3>';
    $standardized = standardize_recipe_data($recipe);
    
    // Compare original vs standardized
    $output .= '<h4>Standardized Ingredients</h4>';
    $output .= '<p>Raw Value: <code>' . htmlspecialchars($standardized['ingredients']) . '</code></p>';
    
    $ingredients_decoded = json_decode($standardized['ingredients'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($ingredients_decoded)) {
        $output .= '<p>Successfully standardized to JSON array with ' . count($ingredients_decoded) . ' items</p>';
        $output .= '<ul>';
        foreach ($ingredients_decoded as $item) {
            $output .= '<li>' . htmlspecialchars($item) . '</li>';
        }
        $output .= '</ul>';
    } else {
        $output .= '<p style="color: red;">ERROR: Standardization failed for ingredients</p>';
    }
    
    $output .= '<h4>Standardized Instructions</h4>';
    $output .= '<p>Raw Value: <code>' . htmlspecialchars($standardized['instructions']) . '</code></p>';
    
    $instructions_decoded = json_decode($standardized['instructions'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($instructions_decoded)) {
        $output .= '<p>Successfully standardized to JSON array with ' . count($instructions_decoded) . ' items</p>';
        $output .= '<ol>';
        foreach ($instructions_decoded as $item) {
            $output .= '<li>' . htmlspecialchars($item) . '</li>';
        }
        $output .= '</ol>';
    } else {
        $output .= '<p style="color: red;">ERROR: Standardization failed for instructions</p>';
    }
    
    // JavaScript simulation
    $output .= '<h3>JavaScript Modal Display Simulation</h3>';
    $output .= '<p>This simulates how the data would be processed in the JavaScript viewRecipe function:</p>';
    
    // Ingredients
    $output .= '<h4>Ingredients Display</h4>';
    $output .= '<pre class="js-simulation">
// JavaScript code simulation:
let ingredients = ' . $standardized['ingredients'] . ';

// Parsing logic from viewRecipe function
if (typeof ingredients === "string") {
    try {
        ingredients = JSON.parse(ingredients);
    } catch (e) {
        // If JSON parsing fails, try comma split
        ingredients = ingredients.split(",").map(item => item.trim()).filter(item => item);
    }
}

// Display each ingredient
if (Array.isArray(ingredients)) {
    // Correctly parsed as array
    // Would display ' . count($ingredients_decoded) . ' items
} else {
    // Failed to parse, would display raw
    // Would display as raw text
}
</pre>';
    
    // Instructions
    $output .= '<h4>Instructions Display</h4>';
    $output .= '<pre class="js-simulation">
// JavaScript code simulation:
let instructions = ' . $standardized['instructions'] . ';

// Parsing logic from viewRecipe function
if (typeof instructions === "string") {
    try {
        instructions = JSON.parse(instructions);
    } catch (e) {
        // If JSON parsing fails, try paragraph or comma split
        if (instructions.includes("\n")) {
            instructions = instructions.split("\n").map(item => item.trim()).filter(item => item);
        } else {
            instructions = instructions.split(",").map(item => item.trim()).filter(item => item);
        }
    }
}

// Display each instruction
if (Array.isArray(instructions)) {
    // Correctly parsed as array
    // Would display ' . count($instructions_decoded) . ' items
} else {
    // Failed to parse, would display raw
    // Would display as raw text
}
</pre>';
    
    $output .= '</div>';
    return $output;
}
