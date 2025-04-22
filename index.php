<?php
session_start();
// Check if user is logged in
require_once 'includes/db.php';

// Fetch recipes
$stmt = $pdo->query("SELECT recipes.rid, recipes.name, recipes.type, recipes.description, users.username 
                     FROM recipes 
                     JOIN users ON recipes.uid = users.uid 
                     ORDER BY recipes.rid DESC");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Virtual Kitchen</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Welcome to Virtual Kitchen</h1>
    <?php if (isset($_SESSION['uid'])): ?>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> |
    <p><a href="add_recipe.php">Add New Recipe</a></p>

       <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="register.php">Register</a> | <a href="login.php">Login</a></p>
<?php endif; ?>

    <h2>All Recipes</h2>

    <?php if (count($recipes) > 0): ?>
        <ul>
            <?php foreach ($recipes as $recipe): ?>
                <li>
                    <strong><?= htmlspecialchars($recipe['name']) ?></strong> (<?= $recipe['type'] ?>)<br>
                    <?= htmlspecialchars($recipe['description']) ?><br>
                    Posted by: <?= htmlspecialchars($recipe['username']) ?><br>
                    <a href="recipe.php?rid=<?= $recipe['rid'] ?>">View Recipe</a>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recipes found.</p>
    <?php endif; ?>
</body>
</html>
