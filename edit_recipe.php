<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['rid'])) {
    die("Recipe ID missing.");
}

$rid = (int)$_GET['rid'];

// Check if the logged-in user owns this recipe
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE rid = ? AND uid = ?");
$stmt->execute([$rid, $_SESSION['uid']]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    die("Recipe not found or you don't have permission to edit it.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $cookingtime = (int)($_POST['cookingtime'] ?? 0);
    $image = trim($_POST['image'] ?? '');

    if (!$name || !$type || !$description || !$ingredients || !$instructions || !$cookingtime) {
        $errors[] = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("UPDATE recipes 
                               SET name = ?, description = ?, type = ?, cookingtime = ?, 
                                   ingredients = ?, instructions = ?, image = ? 
                               WHERE rid = ? AND uid = ?");
        $stmt->execute([$name, $description, $type, $cookingtime, $ingredients, $instructions, $image, $rid, $_SESSION['uid']]);
        header("Location: recipe.php?rid=" . $rid);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe - Virtual Kitchen</title>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>
    <h1>Edit Your Recipe</h1>

    <?php foreach ($errors as $e): ?>
        <p style="color: red;"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($recipe['name']) ?>" required></label><br>

        <label>Type:
            <select name="type" required>
                <?php
                $types = ['French', 'Italian', 'Chinese', 'Indian', 'Mexican', 'others'];
                foreach ($types as $t) {
                    $selected = ($recipe['type'] === $t) ? 'selected' : '';
                    echo "<option value=\"$t\" $selected>$t</option>";
                }
                ?>
            </select>
        </label><br>

        <label>Cooking Time (mins): <input type="number" name="cookingtime" value="<?= htmlspecialchars($recipe['cookingtime']) ?>" required></label><br>

        <label>Description:<br>
            <textarea name="description" required rows="3" cols="40"><?= htmlspecialchars($recipe['description']) ?></textarea>
        </label><br>

        <label>Ingredients:<br>
            <textarea name="ingredients" required rows="3" cols="40"><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
        </label><br>

        <label>Instructions:<br>
            <textarea name="instructions" required rows="3" cols="40"><?= htmlspecialchars($recipe['instructions']) ?></textarea>
        </label><br>

        <label>Image URL (optional): <input type="text" name="image" value="<?= htmlspecialchars($recipe['image']) ?>"></label><br>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="recipe.php?rid=<?= $rid ?>">Back to Recipe</a></p>
</body>
</html>
