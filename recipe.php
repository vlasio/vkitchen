<?php
session_start();

require_once 'includes/db.php';

if (!isset($_GET['rid'])) {
    die('Recipe ID is missing.');
}

$rid = $_GET['rid'];

$stmt = $pdo->prepare("SELECT recipes.*, users.username 
                       FROM recipes 
                       JOIN users ON recipes.uid = users.uid 
                       WHERE rid = ?");
$stmt->execute([$rid]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    die('Recipe not found.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($recipe['name']) ?> - Virtual Kitchen</title>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>
    <h1><?= htmlspecialchars($recipe['name']) ?></h1>
    <p><strong>Type:</strong> <?= htmlspecialchars($recipe['type']) ?></p>
    <p><strong>Cooking Time:</strong> <?= htmlspecialchars($recipe['cookingtime']) ?> minutes</p>
    <p><strong>Posted by:</strong> <?= htmlspecialchars($recipe['username']) ?></p>

    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

    <h2>Ingredients</h2>
    <p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>

    <h2>Instructions</h2>
    <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>

    <?php if (!empty($recipe['image'])): ?>
        <img src="<?= htmlspecialchars($recipe['image']) ?>" alt="Recipe Image" width="300">
    <?php endif; ?>

    <p><a href="index.php">Back to all recipes</a></p>
    <?php if (isset($_SESSION['uid']) && $_SESSION['uid'] == $recipe['uid']): ?>
    <p><a href="edit_recipe.php?rid=<?= $recipe['rid'] ?>">Edit this recipe</a></p>
<?php endif; ?>

</body>
</html>
