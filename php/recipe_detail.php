<?php
session_start();
require_once "dbconn.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['email_address']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

// Check if recipe ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Recipe ID is required";
    header("Location: adminDashboard.php?tab=recipes");
    exit();
}

$recipe_id = intval($_GET['id']);

// Fetch recipe details with author information
$stmt = $conn->prepare("SELECT r.*, u.name as author_name, u.email_address as author_email 
                        FROM recipes r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Recipe not found";
    header("Location: adminDashboard.php?tab=recipes");
    exit();
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Decode JSON data
$ingredients = json_decode($recipe['ingredients'] ?? '[]', true) ?: [];
$instructions = json_decode($recipe['instructions'] ?? '[]', true) ?: [];

// Get recipe ratings
$stmt = $conn->prepare("SELECT r.*, u.name as reviewer_name 
                        FROM ratings r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.recipe_id = ? 
                        ORDER BY r.created_at DESC");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ratings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate average rating
$avg_rating = 0;
$total_ratings = count($ratings);
if ($total_ratings > 0) {
    $total_score = array_sum(array_column($ratings, 'rating'));
    $avg_rating = $total_score / $total_ratings;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Details - <?php echo htmlspecialchars($recipe['title']); ?> - Ceylon Cuisine Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/ceylon-cuisine.css">
    <style>
        .recipe-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            min-height: 100vh;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #dbb657;
            text-decoration: none;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
            margin-bottom: 20px;
            padding: 10px 15px;
            border: 2px solid #dbb657;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background-color: #dbb657;
            color: white;
        }
        
        .recipe-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .recipe-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #2c241d;
            margin-bottom: 10px;
        }
        
        .recipe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            font-family: 'Raleway', sans-serif;
            color: #666;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        
        .recipe-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .recipe-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .recipe-details h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #2c241d;
            margin-bottom: 15px;
        }
        
        .recipe-description {
            font-family: 'Raleway', sans-serif;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }
        
        .ingredients-list {
            list-style: none;
            padding: 0;
        }
        
        .ingredients-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-family: 'Raleway', sans-serif;
        }
        
        .instructions-list {
            counter-reset: step-counter;
        }
        
        .instructions-list li {
            counter-increment: step-counter;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            font-family: 'Raleway', sans-serif;
            line-height: 1.6;
            position: relative;
            padding-left: 50px;
        }
        
        .instructions-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 15px;
            background: #dbb657;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .ratings-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        
        .rating-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .avg-rating {
            font-size: 2rem;
            font-weight: bold;
            color: #dbb657;
            margin-bottom: 10px;
        }
        
        .stars {
            color: #ffc107;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .rating-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dbb657;
            margin-bottom: 15px;
        }
        
        .rating-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .reviewer-name {
            font-weight: 600;
            color: #2c241d;
        }
        
        .rating-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .rating-stars {
            color: #ffc107;
        }
        
        @media (max-width: 768px) {
            .recipe-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .recipe-title {
                font-size: 2rem;
            }
            
            .recipe-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="recipe-detail-container">
        <a href="adminDashboard.php?tab=recipes" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Recipe Management
        </a>
        
        <div class="recipe-header">
            <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
            <div class="recipe-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>By <?php echo htmlspecialchars($recipe['author_name']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('M d, Y', strtotime($recipe['created_at'])); ?></span>
                </div>
                <div class="meta-item">
                    <span class="status-badge status-<?php echo $recipe['status'] ?? 'pending'; ?>">
                        <?php echo ucfirst($recipe['status'] ?? 'pending'); ?>
                    </span>
                </div>
                <?php if ($recipe['featured'] ?? 0): ?>
                    <div class="meta-item">
                        <i class="fas fa-star" style="color: #ffc107;"></i>
                        <span>Featured</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="recipe-content">
            <div class="recipe-image-section">
                <?php if (!empty($recipe['image_url'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($recipe['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                         class="recipe-image">
                <?php else: ?>
                    <div style="background: #f0f0f0; height: 400px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <i class="fas fa-image" style="font-size: 3rem; color: #ccc;"></i>
                        <p style="margin-left: 15px; color: #999; font-family: 'Raleway', sans-serif;">No image available</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="recipe-details">
                <h3>Ingredients</h3>
                <?php if (!empty($ingredients)): ?>
                    <ul class="ingredients-list">
                        <?php foreach ($ingredients as $ingredient): ?>
                            <li><?php echo htmlspecialchars($ingredient); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>No ingredients listed</em></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="instructions-section">
            <h3>Instructions</h3>
            <?php if (!empty($instructions)): ?>
                <ol class="instructions-list">
                    <?php foreach ($instructions as $instruction): ?>
                        <li><?php echo nl2br(htmlspecialchars($instruction)); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p><em>No instructions provided</em></p>
            <?php endif; ?>
        </div>
        
        <div class="ratings-section">
            <h3>Ratings & Reviews</h3>
            
            <?php if ($total_ratings > 0): ?>
                <div class="rating-summary">
                    <div class="avg-rating"><?php echo number_format($avg_rating, 1); ?>/5</div>
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?php echo $i <= round($avg_rating) ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <div><?php echo $total_ratings; ?> review<?php echo $total_ratings != 1 ? 's' : ''; ?></div>
                </div>
                
                <?php foreach ($ratings as $rating): ?>
                    <div class="rating-item">
                        <div class="rating-header">
                            <span class="reviewer-name"><?php echo htmlspecialchars($rating['reviewer_name']); ?></span>
                            <span class="rating-date"><?php echo date('M d, Y', strtotime($rating['created_at'])); ?></span>
                        </div>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?php echo $i <= $rating['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if (!empty($rating['comment'])): ?>
                            <p style="margin-top: 10px; color: #555;"><?php echo nl2br(htmlspecialchars($rating['comment'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="rating-summary">
                    <p><em>No ratings yet</em></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
