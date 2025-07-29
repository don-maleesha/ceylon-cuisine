<?php
session_start();

// Add error handling for includes
try {
    require_once "dbconn.php";
    require_once "recipe_helpers.php"; // Include our helper functions
} catch (Exception $e) {
    error_log("Include error: " . $e->getMessage());
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Server configuration error']);
        exit();
    } else {
        die("Server configuration error");
    }
}

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    error_log("Database connection error: " . ($conn->connect_error ?? 'Connection not established'));
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit();
    } else {
        die("Database connection error");
    }
}

// Check if the user is logged in
if (!isset($_SESSION['email_address'])) {
    error_log("User not logged in - Session data: " . print_r($_SESSION, true));
    // Better AJAX detection for login check
    $isAjaxRequest = (
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
        (isset($_SERVER['HTTP_ACCEPT']) && 
         strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    );
    
    if ($isAjaxRequest) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User not logged in. Please log in again.']);
        exit();
    } else {
        die("User not logged in.");
    }
}

// Fetch user information from the session
$email = $_SESSION['email_address'];
$profile_picture = '../images/user-profile-icon-front-side-with-white-background.jpg';
$uploadMessage = ''; // Initialize upload message

// Check for profile picture upload message in session
if (isset($_SESSION['upload_message'])) {
    $uploadMessage = $_SESSION['upload_message'];
    unset($_SESSION['upload_message']);
}

// Check for favorite/unfavorite action
if (isset($_GET['action']) && ($_GET['action'] === 'favorite' || $_GET['action'] === 'unfavorite') && isset($_GET['recipe_id'])) {
    $recipe_id = (int)$_GET['recipe_id'];
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        // Fetch user ID from email if not in session
        $sql = "SELECT id FROM users WHERE email_address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['email_address']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_id = $result->fetch_assoc()['id'];
        $_SESSION['user_id'] = $user_id; // Store for future use
    }
    
    if ($_GET['action'] === 'favorite') {
        // Add to favorites
        $checkSql = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $user_id, $recipe_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            $insertSql = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ii", $user_id, $recipe_id);
            $insertStmt->execute();
            $_SESSION['favorite_message'] = "Recipe added to favorites!";
        } else {
            $_SESSION['favorite_message'] = "Recipe already in favorites.";
        }
    } else {
        // Remove from favorites
        $deleteSql = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $user_id, $recipe_id);
        $deleteStmt->execute();
        $_SESSION['favorite_message'] = "Recipe removed from favorites.";
    }
    
    // Redirect back to profile page
    header("Location: profile.php");
    exit();
}

// Check for favorite message in session
$favorite_message = '';
if (isset($_SESSION['favorite_message'])) {
    $favorite_message = $_SESSION['favorite_message'];
    unset($_SESSION['favorite_message']);
}

// Check for recipe update messages stored in session
$update_message = '';
$update_status = '';
if (isset($_SESSION['update_message'])) {
    $update_message = $_SESSION['update_message'];
    $update_status = $_SESSION['update_status'] ?? 'success';
    // Clear the session variables
    unset($_SESSION['update_message']);
    unset($_SESSION['update_status']);
}

// Check for recipe submission messages stored in session
$recipe_message = '';
$recipe_status = '';
if (isset($_SESSION['recipe_message'])) {
    $recipe_message = $_SESSION['recipe_message'];
    $recipe_status = $_SESSION['recipe_status'] ?? 'success';
    // Clear the session variables
    unset($_SESSION['recipe_message']);
    unset($_SESSION['recipe_status']);
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['submit-picture']) || isset($_FILES['profile_picture']))) {
    // Check if this is an AJAX request expecting JSON
    $isAjaxRequest = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    
    // Start output buffering for AJAX requests to catch any unwanted output
    if ($isAjaxRequest) {
        ob_start();
    }
    
    // Debug: Log the upload attempt
    error_log("Profile picture upload attempt for user: " . $email);
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("Is AJAX request: " . ($isAjaxRequest ? 'YES' : 'NO'));
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        error_log("File details - Name: $fileName, Size: $fileSize, Type: $fileType, Extension: $fileExtension");

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif');
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            error_log("File extension not allowed: $fileExtension");
            if ($isAjaxRequest) {
                ob_clean(); // Clear any previous output
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG, and JFIF files are allowed.']);
                exit();
            } else {
                $_SESSION['upload_message'] = 'Only JPG, JPEG, PNG, and JFIF files are allowed.';
                header("Location: profile.php");
                exit();
            }
        } elseif ($fileSize > $maxFileSize) {
            error_log("File size too large: $fileSize bytes");
            if ($isAjaxRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB limit.']);
                exit();
            } else {
                $_SESSION['upload_message'] = 'File size exceeds 2MB limit.';
                header("Location: profile.php");
                exit();
            }
        } else {
            $uploadFileDir = '../uploads/';
            
            // Check if uploads directory exists and is writable
            if (!is_dir($uploadFileDir)) {
                error_log("Uploads directory does not exist, attempting to create: $uploadFileDir");
                if (!mkdir($uploadFileDir, 0755, true)) {
                    error_log("Failed to create uploads directory");
                    if ($isAjaxRequest) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Unable to create uploads directory.']);
                        exit();
                    } else {
                        $_SESSION['upload_message'] = 'Unable to create uploads directory.';
                        header("Location: profile.php");
                        exit();
                    }
                }
            }
            
            if (!is_writable($uploadFileDir)) {
                error_log("Uploads directory is not writable: $uploadFileDir");
                if ($isAjaxRequest) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Uploads directory is not writable.']);
                    exit();
                } else {
                    $_SESSION['upload_message'] = 'Uploads directory is not writable.';
                    header("Location: profile.php");
                    exit();
                }
            }
            
            // Create a unique filename to prevent overwriting
            $uniqueFileName = uniqid() . '_' . $fileName;
            $destPath = $uploadFileDir . $uniqueFileName;
            
            error_log("Attempting to move file from $fileTmpPath to $destPath");

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                error_log("File moved successfully to: $destPath");
                $sql = "UPDATE users SET profile_picture = ? WHERE email_address = ?";
                $updateStmt = $conn->prepare($sql);
                $relativePath = 'uploads/' . $uniqueFileName; // Store relative path without ../
                $updateStmt->bind_param("ss", $relativePath, $email);
                
                if ($updateStmt->execute()) {
                    error_log("Database updated successfully for user: $email");
                    $profile_picture = '../' . $relativePath; // Update the local variable with full path for display
                    
                    if ($isAjaxRequest) {
                        ob_clean(); // Clear any previous output
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Profile picture uploaded successfully!',
                            'profile_picture' => '../' . $relativePath // Include ../ for frontend display
                        ]);
                        exit();
                    } else {
                        $_SESSION['upload_message'] = 'Profile picture uploaded successfully!';
                        header("Location: profile.php");
                        exit();
                    }
                } else {
                    error_log("Database update failed: " . $updateStmt->error);
                    if ($isAjaxRequest) {
                        ob_clean(); // Clear any previous output
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Database error: ' . $updateStmt->error
                        ]);
                        exit();
                    } else {
                        $_SESSION['upload_message'] = 'Database error: ' . $updateStmt->error;
                        header("Location: profile.php");
                        exit();
                    }
                }
                $updateStmt->close();
            } else {
                error_log("Failed to move uploaded file from $fileTmpPath to $destPath");
                if ($isAjaxRequest) {
                    ob_clean(); // Clear any previous output
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error uploading file. Please check file permissions.'
                    ]);
                    exit();
                } else {
                    $_SESSION['upload_message'] = 'Error uploading file. Please check file permissions and try again.';
                    header("Location: profile.php");
                    exit();
                }
            }
        }
    } else {
        $error_code = $_FILES['profile_picture']['error'] ?? 'unknown';
        error_log("File upload error code: $error_code");
        
        $error_message = '';
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = 'The file is too large.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = 'The file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error_message = 'Missing temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error_message = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $error_message = 'File upload stopped by extension.';
                break;
            default:
                $error_message = 'An unknown error occurred during upload (Error code: ' . $error_code . ').';
        }
        
        if ($isAjaxRequest) {
            ob_clean(); // Clear any previous output
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_message]);
            exit();
        } else {
            $_SESSION['upload_message'] = $error_message;
            header("Location: profile.php");
            exit();
        }
    }
    
    // If we reach here and it's an AJAX request, something went wrong
    if ($isAjaxRequest) {
        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unexpected error during upload']);
        exit();
    }
}

// Fetch the updated profile picture from the database
$sql = "SELECT profile_picture FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profile_picture_db);
    $stmt->fetch();
    $stmt->close();

    if (!empty($profile_picture_db)) {
        // Check if the path already starts with ../ or if it's a relative path
        if (strpos($profile_picture_db, '../') === 0) {
            $profile_picture = $profile_picture_db;
        } else {
            // Add ../ prefix for display
            $profile_picture = '../' . $profile_picture_db;
        }
    }
}

// Handle recipe submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit-recipe'])) {
    error_log("=== RECIPE SUBMISSION STARTED ===");
    error_log("=== RECIPE SUBMISSION DEBUG ===");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("Session user: " . ($_SESSION['email_address'] ?? 'Not logged in'));
    error_log("Current working directory: " . getcwd());
    error_log("Script filename: " . $_SERVER['SCRIPT_FILENAME']);

    $email = $_SESSION['email_address'];
    $sql = "SELECT id FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Failed to prepare user query: " . $conn->error);
        $_SESSION['recipe_message'] = "Database error. Please try again.";
        $_SESSION['recipe_status'] = "error";
        header("Location: profile.php");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log("User not found in database for email: $email");
        die("User not found in database");
    }
    $user_id = $result->fetch_assoc()['id'];
    $stmt->close();
    
    error_log("User ID found: $user_id");

    // Sanitize inputs
    $name = htmlspecialchars($_POST['title'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');

    error_log("Sanitized inputs - Name: '$name', Description length: " . strlen($description));

    // Process ingredients and instructions from textareas
    // Handle both array and string inputs for consistency
    $ingredients_input = $_POST['ingredients'] ?? '';
    $instructions_input = $_POST['instructions'] ?? '';
    
    error_log("Raw ingredients input: " . print_r($ingredients_input, true));
    error_log("Raw instructions input: " . print_r($instructions_input, true));
    
    // If it's an array, take the first element
    if (is_array($ingredients_input)) {
        $ingredients_input = $ingredients_input[0] ?? '';
    }
    if (is_array($instructions_input)) {
        $instructions_input = $instructions_input[0] ?? '';
    }
    
    error_log("Processed ingredients input: '$ingredients_input'");
    error_log("Processed instructions input: '$instructions_input'");
    
    $ingredients = array_filter(array_map('trim', explode("\n", $ingredients_input)));
    $instructions = array_filter(array_map('trim', explode("\n", $instructions_input)));

    error_log("Final ingredients array: " . print_r($ingredients, true));
    error_log("Final instructions array: " . print_r($instructions, true));

    // Validate
    $errors = [];
    if (empty($name) || strlen(trim($name)) < 3) {
        $errors[] = "Recipe title must be at least 3 characters long.";
        error_log("Validation error: Empty or short title - '$name'");
    }
    if (empty($description) || strlen(trim($description)) < 20) {
        $errors[] = "Description must be at least 20 characters long.";
        error_log("Validation error: Empty or short description - length: " . strlen($description));
    }
    if (empty($ingredients)) {
        $errors[] = "Ingredients cannot be empty. Please add at least one ingredient.";
        error_log("Validation error: Empty ingredients array");
    }
    if (empty($instructions)) {
        $errors[] = "Instructions cannot be empty. Please add at least one instruction.";
        error_log("Validation error: Empty instructions array");
    }
    
    error_log("Validation completed. Errors found: " . count($errors));
    
    // Handle image upload
    $temporary_name = '';
    $folder = '';
    $unique_file_name = '';
    
    if (empty($errors)) {
        if (empty($_FILES['image_url']['name'])) {
            $errors[] = "Please select an image.";
            error_log("Validation error: No image file selected");
        } else {
            $file_name = $_FILES['image_url']['name'];
            $temporary_name = $_FILES['image_url']['tmp_name'];
            $unique_file_name = uniqid() . '_' . $file_name;
            $folder = '../uploads/' . $unique_file_name;

            error_log("Image upload attempt - File: $file_name, Temp: $temporary_name, Target: $folder");

            // Validate image
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'jfif'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and JFIF are allowed.";
                error_log("Validation error: Invalid file extension - $file_extension");
            } elseif ($_FILES['image_url']['size'] > 5 * 1024 * 1024) {
                $errors[] = "Image size exceeds 5MB limit.";
                error_log("Validation error: File too large - " . $_FILES['image_url']['size'] . " bytes");
            }
        }
    }

    // Insert recipe into the database
    if (empty($errors)) {
        error_log("Starting database insertion process...");
        
        // Ensure uploads directory exists and is writable
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            error_log("Uploads directory does not exist, creating: $upload_dir");
            if (!mkdir($upload_dir, 0755, true)) {
                error_log("Failed to create uploads directory");
                $_SESSION['recipe_message'] = "Server configuration error. Please contact administrator.";
                $_SESSION['recipe_status'] = "error";
                header("Location: profile.php");
                exit();
            }
        }
        
        if (!is_writable($upload_dir)) {
            error_log("Uploads directory is not writable: $upload_dir");
            $_SESSION['recipe_message'] = "Server configuration error. Please contact administrator.";
            $_SESSION['recipe_status'] = "error";
            header("Location: profile.php");
            exit();
        }
        
        error_log("Attempting to upload file from '$temporary_name' to '$folder'");
        
        if (move_uploaded_file($temporary_name, $folder)) {
            error_log("File uploaded successfully to: $folder");
            
            $ingredients_json = json_encode($ingredients);
            $instructions_json = json_encode($instructions);
            
            error_log("JSON encoding - Ingredients: $ingredients_json");
            error_log("JSON encoding - Instructions: $instructions_json");
            
            $sql = "INSERT INTO recipes (user_id, title, description, image_url, ingredients, instructions, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Database prepare failed: " . $conn->error);
                $_SESSION['recipe_message'] = "Database error occurred. Please try again.";
                $_SESSION['recipe_status'] = "error";
                header("Location: profile.php");
                exit();
            }
            
            $stmt->bind_param("isssss", $user_id, $name, $description, $unique_file_name, $ingredients_json, $instructions_json);
            
            error_log("Attempting to execute insert query with data:");
            error_log("User ID: $user_id, Name: $name, Description: $description, Image: $unique_file_name");
            
            if ($stmt->execute()) {
                $recipe_id = $stmt->insert_id;
                error_log("Recipe inserted successfully with ID: $recipe_id");
                $stmt->close();
                
                // Set success message in session
                $_SESSION['recipe_message'] = "Recipe submitted successfully! It will be visible after admin approval.";
                $_SESSION['recipe_status'] = "success";
                error_log("Recipe submission successful - redirecting to profile.php");
                
                // Redirect to prevent form resubmission
                header("Location: profile.php");
                exit();
            } else {
                error_log("Database insert failed: " . $stmt->error);
                $stmt->close();
                $_SESSION['recipe_message'] = "Error inserting recipe: " . $stmt->error;
                $_SESSION['recipe_status'] = "error";
                header("Location: profile.php");
                exit();
            }
        } else {
            error_log("File upload failed - could not move from '$temporary_name' to '$folder'");
            $_SESSION['recipe_message'] = "Error uploading image. Please check file permissions.";
            $_SESSION['recipe_status'] = "error";
            header("Location: profile.php");
            exit();
        }
    } else {
        error_log("Validation errors found: " . implode(", ", $errors));
        $_SESSION['recipe_message'] = implode("<br>", $errors);
        $_SESSION['recipe_status'] = "error";
        header("Location: profile.php");
        exit();
    }
}

// Fetch user ID from the session
$sql = "SELECT id FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email_address']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found in database");
}
$user = $result->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

// Display user recipes
$sql = "SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$recipes = array();
$pending_recipes = array();
$rejected_recipes = array();

while ($row = $result->fetch_assoc()) {
    // Standardize the recipe data
    $row = standardize_recipe_data($row);
    
    if ($row['status'] === 'approved') {
        $recipes[] = $row;
    } elseif ($row['status'] === 'pending') {
        $pending_recipes[] = $row;
    }
}

// Check if favorites table exists and create it if not
$check_table_sql = "SHOW TABLES LIKE 'favorites'";
$check_table_result = $conn->query($check_table_sql);

if ($check_table_result->num_rows == 0) {
    // Table doesn't exist, create it
    $create_table_sql = "CREATE TABLE `favorites` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `recipe_id` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_recipe_unique` (`user_id`,`recipe_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if (!$conn->query($create_table_sql)) {
        error_log("Error creating favorites table: " . $conn->error);
    }
}

// Make sure we have the user_id
if (!isset($user_id)) {
    // Fetch user ID from email if not already set
    $sql = "SELECT id FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['email_address']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_row = $result->fetch_assoc();
    $user_id = $user_row['id'];
    $stmt->close();
}

// Fetch user's favorite recipes
$favorite_recipes = array();
$sql = "SELECT r.* FROM recipes r
        INNER JOIN favorites f ON r.id = f.recipe_id
        WHERE f.user_id = ? AND r.status = 'approved'
        ORDER BY f.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Standardize the recipe data
    $row = standardize_recipe_data($row);
    $favorite_recipes[] = $row;
}
$stmt->close();

// Display recipe data
$recipe_id = $_GET['id'] ?? null;
$ingredients = [];
$instructions = [];
$recipe = null;

if ($recipe_id) {
    // Get user ID if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT 
        r.title, 
        r.description, 
        r.image_url, 
        r.ingredients, 
        r.instructions, 
        COALESCE(r.average_rating, 0) AS average_rating,
        ur.rating AS user_rating
    FROM recipes r
    LEFT JOIN ratings ur ON ur.recipe_id = r.id AND ur.user_id = ?
    WHERE r.id = ?");
    
    // Bind parameters based on login status
    if ($user_id) {
        $stmt->bind_param("ii", $user_id, $recipe_id);
    } else {
        // Use dummy value for non-logged-in users
        $dummy_user_id = 0;
        $stmt->bind_param("ii", $dummy_user_id, $recipe_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    if ($recipe) {
        // Handle JSON decoding
        $ingredients = json_decode($recipe['ingredients'] ?? '[]', true);
        $instructions = json_decode($recipe['instructions'] ?? '[]', true);
        
        // Ensure rating fields exist
        $recipe['average_rating'] = $recipe['average_rating'] ?? 0;
        $recipe['user_rating'] = $recipe['user_rating'] ?? 0;
    }
}

// Update user profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $newName = htmlspecialchars(trim($_POST['name']));
    $newEmail = htmlspecialchars(trim($_POST['email']));
    
    // Validate email
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['upload_message'] = "Invalid email format";
        header("Location: profile.php");
        exit();
    } else {
        // Check if email exists (excluding current user)
        $checkEmail = $conn->prepare("SELECT email_address FROM users WHERE email_address = ? AND email_address != ?");
        $checkEmail->bind_param("ss", $newEmail, $email);
        $checkEmail->execute();
        $checkEmail->store_result();
        
        if ($checkEmail->num_rows > 0) {
            $_SESSION['upload_message'] = "Email already exists!";
            header("Location: profile.php");
            exit();
        } else {
            // Update user info
            $updateSql = "UPDATE users SET name = ?, email_address = ? WHERE email_address = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("sss", $newName, $newEmail, $email);
            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['name'] = $newName;
                $_SESSION['email_address'] = $newEmail;
                $email = $newEmail; // Update local variable for subsequent queries
                $_SESSION['upload_message'] = "Profile updated successfully!";
                header("Location: profile.php");
                exit();
            } else {
                $_SESSION['upload_message'] = "Error updating profile: " . $conn->error;
                header("Location: profile.php");
                exit();
            }
            $stmt->close();
        }
        $checkEmail->close();
    }
}

// Handle recipe update - Check for traditional form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-recipe'])) {  
    error_log("Recipe update request detected via traditional form submission");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Add early error detection
    try {
        // Ensure we have user_id - always fetch fresh from database to be safe
        $sql = "SELECT id FROM users WHERE email_address = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $_SESSION['email_address']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $_SESSION['user_id'] = $user_id; // Store in session for future use
        } else {
            $stmt->close();
            throw new Exception("User not found. Please log in again.");
        } 
        $stmt->close();
        
        // Continue with recipe processing
        $recipe_id = (int)($_POST['recipe_id'] ?? 0);
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        
        error_log("Recipe ID: $recipe_id, User ID: $user_id");
        
        // Process ingredients and instructions
        $ingredients_input = $_POST['ingredients'] ?? '';
        $instructions_input = $_POST['instructions'] ?? '';
        
        // Handle both array and string inputs for consistency
        if (is_array($ingredients_input)) {
            $ingredients_input = $ingredients_input[0] ?? '';
        }
        if (is_array($instructions_input)) {
            $instructions_input = $instructions_input[0] ?? '';
        }
        
        $ingredients = array_filter(array_map('trim', explode("\n", $ingredients_input)));
        $instructions = array_filter(array_map('trim', explode("\n", $instructions_input)));
        
        // Validation
        $update_errors = [];
        if ($recipe_id <= 0) $update_errors[] = "Invalid recipe ID.";
        if (empty($title)) $update_errors[] = "Recipe title cannot be empty.";
        if (empty($description)) $update_errors[] = "Description cannot be empty.";
        if (empty($ingredients)) $update_errors[] = "Ingredients cannot be empty.";
        if (empty($instructions)) $update_errors[] = "Instructions cannot be empty.";
        
        error_log("Validation check - Recipe ID: $recipe_id, Title length: " . strlen($title) . ", Description length: " . strlen($description) . ", Ingredients count: " . count($ingredients) . ", Instructions count: " . count($instructions));
        
        // Query current recipe for image (only for this user)
        $current_recipe_query = $conn->prepare("SELECT image_url FROM recipes WHERE id = ? AND user_id = ?");
        $current_recipe_query->bind_param("ii", $recipe_id, $user_id);
        $current_recipe_query->execute();
        $current_recipe_result = $current_recipe_query->get_result();
        
        if ($current_recipe_result->num_rows === 0) {
            $update_errors[] = "Recipe not found or you don't have permission to edit this recipe.";
            $current_recipe = null;
        } else {
            $current_recipe = $current_recipe_result->fetch_assoc();
        }
        $current_recipe_query->close();
        
    } catch (Exception $e) {
        error_log("Recipe update error: " . $e->getMessage());
        $_SESSION['update_message'] = $e->getMessage();
        $_SESSION['update_status'] = "error";
        header("Location: profile.php");
        exit();
    }
    
    // If there are validation errors, return them early
    if (!empty($update_errors)) {
        $_SESSION['update_message'] = implode("<br>", $update_errors);
        $_SESSION['update_status'] = "error";
        header("Location: profile.php");
        exit();
    }
    
    // Continue with the rest of the processing only if no exception occurred and no validation errors
    // Handle image update
    $image_url = $current_recipe['image_url'] ?? ''; // Keep existing image by default
    $new_image_uploaded = false;
    
    // Only process image upload if we have a new image
    if (!empty($_FILES['new_image']['name']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK && empty($update_errors)) {
        $file_name = $_FILES['new_image']['name'];
        $temporary_name = $_FILES['new_image']['tmp_name'];
        $unique_file_name = uniqid() . '_' . $file_name;
        $folder = '../uploads/' . $unique_file_name;
        
        // Validate and move file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'jfif'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $update_errors[] = "Invalid image format. Only JPG, JPEG, and PNG are allowed.";
        } elseif ($_FILES['new_image']['size'] > 5 * 1024 * 1024) {
            $update_errors[] = "Image size exceeds 5MB limit.";
        } else {
            if (move_uploaded_file($temporary_name, $folder)) {
                $image_url = $unique_file_name;
                $new_image_uploaded = true;
            } else {
                $update_errors[] = "Error uploading new image.";
                $_SESSION['update_message'] = "Error uploading new image.";
                $_SESSION['update_status'] = "error";
                header("Location: profile.php");
                exit();
            }
        }
    }
    
    // Update database
    $ingredients_json = json_encode($ingredients);
    $instructions_json = json_encode($instructions);
    
    // Check if JSON encoding was successful
    if ($ingredients_json === false) {
        $error_msg = "Error processing ingredients data.";
        error_log("JSON encode failed for ingredients: " . json_last_error_msg());
        $_SESSION['update_message'] = $error_msg;
        $_SESSION['update_status'] = "error";
        header("Location: profile.php");
        exit();
    }
    
    if ($instructions_json === false) {
        $error_msg = "Error processing instructions data.";
        error_log("JSON encode failed for instructions: " . json_last_error_msg());
        $_SESSION['update_message'] = $error_msg;
        $_SESSION['update_status'] = "error";
        header("Location: profile.php");
        exit();
    }
    
    error_log("About to update recipe with: Title=$title, RecipeID=$recipe_id, UserID=$user_id");
    error_log("Image URL: $image_url");
    error_log("New image uploaded: " . ($new_image_uploaded ? 'Yes' : 'No'));
    error_log("Ingredients JSON: $ingredients_json");
    error_log("Instructions JSON: $instructions_json");

    // Build SQL query conditionally based on whether image was uploaded
    $sql = "UPDATE recipes SET 
            title = ?, 
            description = ?, 
            " . ($new_image_uploaded ? "image_url = ?, " : "") . "
            ingredients = ?, 
            instructions = ?,
            status = 'pending' 
            WHERE id = ? AND user_id = ?";

    // Build parameter types and values conditionally
    $types = "ss" . ($new_image_uploaded ? "s" : "") . "ssii";
    $params = [
        $title, 
        $description
    ];

    if ($new_image_uploaded) {
        $params[] = $image_url;
    }

    $params = array_merge($params, [
        $ingredients_json, 
        $instructions_json, 
        $recipe_id, 
        $user_id
    ]);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $error_msg = "Database prepare error: " . $conn->error;
        error_log($error_msg);
        $_SESSION['update_message'] = $error_msg;
        $_SESSION['update_status'] = "error";
        header("Location: profile.php");
        exit();
    }

    $stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // Check if any rows were actually updated
    if ($stmt->affected_rows > 0) {
        $update_message = "Recipe updated successfully! It will be visible after admin approval.";
        $update_status = "success";
        $stmt->close();
        
        $_SESSION['update_message'] = $update_message;
        $_SESSION['update_status'] = $update_status;
        header("Location: profile.php");
        exit();
    } else {
        $update_message = "No changes were made to the recipe.";
        $update_status = "info";
        $stmt->close();
        
        $_SESSION['update_message'] = $update_message;
        $_SESSION['update_status'] = $update_status;
        header("Location: profile.php");
        exit();
    }
} else {
    $update_message = "Error updating recipe: " . $stmt->error;
    $update_status = "error";
    $stmt->close();
    
    $_SESSION['update_message'] = $update_message;
    $_SESSION['update_status'] = $update_status;
    header("Location: profile.php");
    exit();
}
} // Close the recipe update section
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Cuisine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <img src="../images/Ceylon.png" alt="Logo">
            <span class="company-name josefin-sans">Ceylon Cuisine</span>
        </div>
        <nav>
            <ul>
                <li><a href="homePage.php" class="raleway">Home</a></li>
                <li><a href="aboutus.php" class="raleway">About</a></li>
                <li><a href="contacts.php" class="raleway">Contact</a></li>
                <li><a href="recipes.php" class="raleway">Recipes</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <?php if(isset($_SESSION["email_address"])): ?>
                <div class="dropdown">
                    <a href="#" class="custom-icon">
                        <div class="user-info">
                            <i class="fas fa-user-circle" aria-hidden="true"></i>
                            <span class="username raleway"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </div>
                        <i id="customIcon" class="fas fa-chevron-down" aria-hidden="true"></i>
                    </a>
                    <ul id="dropdownMenu" class="dropdown-menu">
                        <li><a class="dropdown-item raleway" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item raleway" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="signin.php" class="sign-in raleway">Sign in</a>
                <a href="signup.php" class="sign-up raleway">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<div class="container">
    <div class="breadcrumb raleway">
        <a href="#">Home</a>&gt;<a href="#">Profile</a>
    </div>
    <!-- Message container for profile picture upload messages -->
    <div id="profile-message-container" class="message-container" 
         data-message="<?= htmlspecialchars($uploadMessage) ?>" 
         data-status="<?= empty($uploadMessage) ? '' : (strpos(strtolower($uploadMessage), 'success') !== false ? 'success' : 'error') ?>">
    </div>
    
    <div class="profile-info">
        <div class="profile-picture">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <form action="profile.php" method="POST" enctype="multipart/form-data" id="profile-picture-form">
                <label for="profile-picture-upload" class="change-picture-button">
                    <i class="fas fa-camera"></i>
                    <span class="raleway">Change Picture</span>
                </label>
                <input type="file" id="profile-picture-upload" name="profile_picture" accept="image/jpeg,image/png,image/jpg,image/jfif" class="upload-input">
                <button type="submit" class="upload-button raleway" name="submit-picture" id="profile-picture-submit" disabled>Select Image</button>
                <small class="form-text text-muted">Max file size: 2MB. Accepted formats: JPG, JPEG, PNG, JFIF</small>
            </form>
        </div>
        <div class="profile-divider"></div>
        <div class="profile-details">
            <h2 class="playfair-display"><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
            <p class="raleway"><?php echo htmlspecialchars($_SESSION['email_address']); ?></p>
            <button onclick="openProfileModal()" class="raleway profile-update-btn"><i class="fas fa-edit"></i></button>        </div>
    </div>
</div>
<div class="tabs">
    <button class="active raleway" onclick="showSection('newRecipe')">Add New Recipe</button>
    <button class="raleway" onclick="showSection('myRecipe')">
        My Recipes
    </button>
    <button class="raleway" onclick="showSection('myFavourits')">My Favorites</button>
    <button class="raleway" onclick="showSection('stats')">Stats</button>
</div>
<div class="content">    <section id="newRecipe" class="content-section active">
        <div class="container">
            <h2 class="playfair-display">Share Your Delicious Recipe with the World!</h2>
            <p class="raleway">We are excited to see what you have created in your kitchen. Please fill out the form below to share your recipe with our community.</p>
            
            <div class="approval-notice">
                <i class="fas fa-info-circle"></i>
                <p class="raleway">All recipes require admin approval before being published on the site. Your recipe will be reviewed shortly.</p>
            </div>
            
            <!-- Message container for recipe submission messages -->
            <div id="recipe-message-container" 
                 data-message="<?= htmlspecialchars($recipe_message ?? '') ?>" 
                 data-status="<?= htmlspecialchars($recipe_status ?? '') ?>">
            </div>
            
            <form action="profile.php" method="POST" enctype="multipart/form-data" id="recipe-form">
                <!-- Hidden field to ensure submit-recipe parameter is always sent -->
                <input type="hidden" name="submit-recipe" value="1">
                <div class="form-group">
                    <label for="name" class="raleway">Recipe Name</label>
                    <input type="text" id="name" name="title" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
                    <small class="form-text text-muted">Enter a descriptive name for your recipe (min. 3 characters)</small>
                </div>
                <div class="form-group">
                    <label for="description" class="raleway">Description</label>
                    <textarea id="description" name="description" class="form-control" required><?= htmlspecialchars($description ?? '') ?></textarea>
                    <small class="form-text text-muted">Describe your recipe in detail (min. 20 characters)</small>
                </div>                <div class="form-group">
                    <label for="recipe-ingredients" class="raleway">Ingredients</label>
                    <div id="ingredients-container">
                        <div class="ingredient-input">
                        <textarea id="recipe-ingredients" name="ingredients[]" class="form-control"
                            placeholder="Enter one ingredient per line:
1 cup flour
2 eggs
1/2 cup sugar"
                            rows="4" required><?= !empty($ingredients) ? htmlspecialchars(implode("\n", $ingredients)) : '' ?></textarea>
                        </div>
                    </div>
                    <small class="form-text text-muted">List each ingredient on a separate line</small>
                </div>
                <div class="form-group">
                    <label for="recipe-instructions" class="raleway">Instructions</label>
                    <div id="instructions-container">
                        <div class="instruction-input">
                        <textarea id="recipe-instructions" name="instructions[]" class="form-control" 
                            placeholder="Enter one step per line:
Step 1: Mix ingredients
Step 2: Bake at 350°F
Step 3: Let cool before serving" 
                            rows="4" required><?= !empty($instructions) ? htmlspecialchars(implode("\n", $instructions)) : '' ?></textarea>
                        </div>
                    </div>
                    <small class="form-text text-muted">List each step on a separate line</small>
                </div>                <div class="form-group upload-image">
                    <label for="image" class="raleway">Upload Image</label>
                    <input type="file" id="image" name="image_url" accept="image/*" class="form-control" required>
                    <small class="form-text text-muted">Upload an image of your dish (JPG, JPEG, PNG only, max 5MB)</small>
                    <div class="image-preview-container">
                        <img src="#" alt="Image Preview" class="image-preview" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>
                </div>
                <div class="form-group button-row">
                    <button type="submit" name="submit-recipe" class="raleway submit-btn" 
                            style="pointer-events: auto !important; cursor: pointer !important; z-index: 1000 !important;">
                        <i class="fas fa-paper-plane"></i> Submit Recipe
                    </button>
                </div>
            </form>
        </div>
    </section>
    <section id="myRecipe" class="content-section">
        <div class="card-container">
            <h2 class="section-title playfair-display">Approved Recipes</h2>
            <div class="recipe-list">
                <?php if (empty($recipes)): ?>
                    <p class="raleway no-recipes">You don't have any approved recipes yet. Your submitted recipes will appear here after admin approval.</p>
                <?php else: ?>
                    <?php foreach ($recipes as $myrecipe) : ?>
                        <div class="card">
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($myrecipe['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($myrecipe['title']) ?>">
                            </div>
                            <div class="title">
                                <h2 class="playfair-display"><?= htmlspecialchars($myrecipe['title']) ?></h2>
                            </div>
                            <div class="description">
                                <p class="merriweather-regular"><?= htmlspecialchars($myrecipe['description']) ?></p>
                            </div>
                            <div class="rating-section">
                                <div class="average-rating merriweather-regular">
                                    <!-- Star display for average rating -->
                                    <div class="stars">
                                        <?php
                                        $average = (float)($myrecipe['average_rating'] ?? 0);
                                        $fullStars = floor($average);
                                        $hasHalfStar = ($average - $fullStars) >= 0.5;
                                        
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $fullStars) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span><?= number_format($average, 1) ?></span>
                                </div>                            </div>                            <div class="action-buttons">                                <button class="view-button" onclick='viewRecipe(
                                    <?= json_encode($myrecipe['id']) ?>, 
                                    <?= json_encode($myrecipe['title']) ?>, 
                                    <?= json_encode($myrecipe['description']) ?>, 
                                    <?= json_encode($myrecipe['image_url']) ?>, 
                                    <?= json_encode($myrecipe['ingredients']) ?>, 
                                    <?= json_encode($myrecipe['instructions']) ?>
                                )'>View Recipe</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>            </div>            <!-- Pending Recipes Section -->
            <h2 class="section-title playfair-display">Awaiting Admin Approval</h2>
            <div class="recipe-list">
                <?php if (empty($pending_recipes)): ?>
                    <p class="raleway no-recipes">You don't have any recipes pending approval.</p>
                <?php else: ?>
                    <?php foreach ($pending_recipes as $recipe): ?><div class="card pending">
                            <div class="pending-badge">Pending</div>
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($recipe['image_url'] ?? 'default.jpg') ?>" 
                                    alt="<?= htmlspecialchars($recipe['title'] ?? 'Recipe image') ?>">
                            </div>
                            <div class="title">
                                <h2 class="playfair-display"><?= htmlspecialchars($recipe['title'] ?? 'Untitled Recipe') ?></h2>
                            </div>
                            <div class="description">
                                <p class="merriweather-regular"><?= htmlspecialchars($recipe['description'] ?? 'No description available') ?></p>                            </div>                            <div class="action-buttons">                                <button class="view-button" onclick='viewRecipe(
                                    <?= json_encode($recipe['id']) ?>, 
                                    <?= json_encode($recipe['title']) ?>, 
                                    <?= json_encode($recipe['description']) ?>, 
                                    <?= json_encode($recipe['image_url']) ?>, 
                                    <?= json_encode($recipe['ingredients']) ?>, 
                                    <?= json_encode($recipe['instructions']) ?>
                                )'>View Recipe</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section id="myFavourits" class="content-section">
        <div class="card-container">
            <h2 class="section-title playfair-display">My Favorite Recipes</h2>
            <div id="favorite-message" class="message-container" 
                 data-message="<?= htmlspecialchars($favorite_message ?? '') ?>"></div>
            <div class="recipe-list">
                <?php if (empty($favorite_recipes)): ?>
                    <p class="raleway no-recipes">You haven't added any favorites yet. Browse recipes and click the heart icon to add them to your favorites.</p>
                <?php else: ?>
                    <?php foreach ($favorite_recipes as $fav_recipe) : ?>
                        <div class="card">
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($fav_recipe['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($fav_recipe['title']) ?>">
                            </div>
                            <div class="title">
                                <h2 class="playfair-display"><?= htmlspecialchars($fav_recipe['title']) ?></h2>
                            </div>
                            <div class="description">
                                <p class="merriweather-regular"><?= htmlspecialchars($fav_recipe['description']) ?></p>
                            </div>
                            <div class="rating-section">
                                <div class="average-rating merriweather-regular">
                                    <!-- Star display for average rating -->
                                    <div class="stars">
                                        <?php
                                        $average = (float)($fav_recipe['average_rating'] ?? 0);
                                        $fullStars = floor($average);
                                        $hasHalfStar = ($average - $fullStars) >= 0.5;
                                        
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $fullStars) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span><?= number_format($average, 1) ?></span>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <button class="view-button" onclick='viewRecipe(
                                    <?= json_encode(value: $fav_recipe['id']) ?>, 
                                    <?= json_encode($fav_recipe['title']) ?>, 
                                    <?= json_encode($fav_recipe['description']) ?>, 
                                    <?= json_encode($fav_recipe['image_url']) ?>, 
                                    <?= json_encode($fav_recipe['ingredients']) ?>, 
                                    <?= json_encode($fav_recipe['instructions']) ?>
                                )'>View Recipe</button>
                                <a href="profile.php?action=unfavorite&recipe_id=<?= $fav_recipe['id'] ?>" class="remove-favorite-btn">
                                    <i class="fas fa-heart-broken"></i> Remove
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- model -->
<!-- Single modal template (place this outside any loops) -->
<div id="recipeModal" class="modal">
    <div class="modal-content">        <span class="close" onclick="closeModal()">&times;</span>        <div class="update">
            <h2 class="playfair-display" id="modalRecipeTitle"></h2>
            <div class="modal-buttons">
                <button onclick="openUpdatePanel()" class="edit-button"><i class="fas fa-edit"></i></button>
            </div>
        </div>
        <img id="modalRecipeImage" src="" alt="" class="recipe-image">
        <p id="modalRecipeDescription"></p>

        <div class="columns">
            <div class="ingredients-column">
                <h3>🍴 Ingredients</h3>
                <ul class="ingredient-list" id="modalRecipeIngredients">
                </ul>
            </div>
            <div class="instructions-column">
                <h3>📝 Instructions</h3>
                <ol class="instruction-list" id="modalRecipeInstructions">
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="panel-overlay" id="panelOverlay">
<div id="updatePanel" class="update-panel">
    <span class="close-btn" onclick="closeUpdatePanel()">&times;</span>
    <h2 class="playfair-display">Update Recipe</h2>
    
    <!-- Message container for update form -->
    <div id="update-message-container" 
         data-message="<?= htmlspecialchars($update_message ?? '') ?>" 
         data-status="<?= htmlspecialchars($update_status ?? '') ?>">
    </div>
    
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update-recipe" value="1">
        <input type="hidden" name="recipe_id" id="update_recipe_id">
        
        <div class="form-group">
            <label for="update_title" class="raleway">Title</label>
            <input type="text" name="title" id="update_title" class="form-control" required
                   minlength="3" maxlength="100">
            <small class="form-text text-muted">Title should be between 3-100 characters</small>
        </div>

        <div class="form-group">
            <label for="update_description" class="raleway">Description</label>
            <textarea name="description" id="update_description" class="form-control" required
                      minlength="20"></textarea>
            <small class="form-text text-muted">Describe your recipe in detail (min. 20 characters)</small>
        </div>
        
        <div class="form-group">
            <label for="update_ingredients" class="raleway">Ingredients</label>
            <textarea name="ingredients" id="update_ingredients" class="form-control" required
                      placeholder="Enter one ingredient per line:
1 cup flour
2 eggs
1/2 cup sugar" rows="4"></textarea>
            <small class="form-text text-muted">List each ingredient on a separate line</small>
        </div>
        
        <div class="form-group">
            <label for="update_instructions" class="raleway">Instructions</label>
            <textarea name="instructions" id="update_instructions" class="form-control" required
                      placeholder="Enter one step per line:
Step 1: Mix ingredients
Step 2: Bake at 350°F
Step 3: Let cool before serving" rows="4"></textarea>
            <small class="form-text text-muted">List each step on a separate line</small>
        </div>
        
        <div class="form-group">
            <label class="raleway">Current Image</label>
            <div class="current-image-preview">
                <img id="current_recipe_image" src="" alt="Current Recipe" style="max-width: 200px;">
            </div>
        </div>
        
        <div class="form-group upload-image">
            <label for="new_image" class="raleway">Upload New Image (optional)</label>
            <input type="file" id="new_image" name="new_image" accept="image/*" class="form-control">
            <small class="form-text text-muted">Upload a new image of your dish (JPG, JPEG, PNG only, max 5MB)</small>
            <div class="image-preview-container">
                <img src="#" alt="New Preview" class="new-image-preview" style="display: none; max-width: 200px; margin-top: 10px;">
            </div>
        </div>
        
        <div class="form-group button-row">
            <button type="submit" name="update-recipe" class="raleway submit-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <button type="button" class="raleway cancel-btn" onclick="closeUpdatePanel()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </form>
</div>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProfileModal()">&times;</span>
        <h2 class="playfair-display">Update Profile</h2>
        
        <form action="profile.php" method="POST">
            <div class="form-group">
                <label class="raleway">Name</label>
                <input type="text" name="name" 
                    value="<?php echo htmlspecialchars($_SESSION['name']); ?>" 
                    class="form-control" required>
            </div>
            <div class="form-group">
                <label class="raleway">Email</label>
                <input type="email" name="email" 
                    value="<?php echo htmlspecialchars($_SESSION['email_address']); ?>" 
                    class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update_profile" class="raleway">Save Changes</button>
                <button type="button" class="raleway" onclick="closeProfileModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="logo">
            <img src="../images/Ceylon.png" alt="Logo">
        </div>
        <div class="links">
            <h3 class="josefin-sans">Recipes</h3>
            <ul>
                <li><a href="#" class="raleway">Explore Recipes</a></li>
                <li><a href="#" class="raleway">Submit Your Recipe</a></li>
                <li><a href="#" class="raleway">Top Rated Dishes</a></li>
            </ul>
        </div>
        <div class="resources">
            <h3 class="josefin-sans">Kitchen Tips</h3>
            <ul>
                <li><a href="#" class="raleway">Cooking Techniques</a></li>
                <li><a href="#" class="raleway">Spice Guide</a></li>
                <li><a href="#" class="raleway">Food Pairing Tips</a></li>
            </ul>
        </div>
        <div class="company">
            <h3 class="josefin-sans">About Ceylon Cuisine</h3>
            <ul>
                <li><a href="#" class="raleway">Our Story</a></li>
                <li><a href="#" class="raleway">Contact Us</a></li>
                <li><a href="#" class="raleway">Privacy Policy</a></li>
                <li><a href="#" class="raleway">Terms of Conditions</a></li>
            </ul>
        </div>
        <div class="social">
            <h3 class="josefin-sans">Follow us</h3>
            <ul>
                <li><a href="#" class="raleway"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#" class="raleway"><i class="fab fa-facebook"></i></a></li>
                <li><a href="#" class="raleway"><i class="fab fa-instagram"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2024 Ceylon Cuisine. All rights reserved.</p>
    </div>
</footer>

<script src="../js/profile.js"></script>
<script src="../js/ceylon-cuisine.js"></script>
<script src="../js/form-protection.js"></script>
</body>
</html>