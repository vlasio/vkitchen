<?php
session_start();
require_once 'includes/db.php';

// Redirects to login page
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
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

    // Validates fields
    if (!$name || !$type || !$description || !$ingredients || !$instructions || !$cookingtime) {
        $errors[] = "Fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO recipes (name, description, type, cookingtime, ingredients, instructions, image, uid)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $type, $cookingtime, $ingredients, $instructions, $image, $_SESSION['uid']]);
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/style.css">

    <title>Add Recipe - Virtual Kitchen</title>
</head>
<body>
    <h1>Add New Recipe</h1>

    <?php foreach ($errors as $e): ?>
        <p style="color: red;"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Type:
            <select name="type" required>
                <option value="">-- Select Type --</option>
                <?php
                $types = ['French', 'Italian', 'Chinese', 'Indian', 'Mexican', 'others'];
                foreach ($types as $t) {
                    echo "<option value=\"$t\">$t</option>";
                }
                ?>
            </select>
        </label><br>
        <label>Cooking Time (mins): <input type="number" name="cookingtime" required></label><br>
        <label>Description:<br>
            <textarea name="description" required rows="3" cols="40"></textarea>
        </label><br>
        <label>Ingredients:<br>
            <textarea name="ingredients" required rows="3" cols="40"></textarea>
        </label><br>
        <label>Instructions:<br>
            <textarea name="instructions" required rows="3" cols="40"></textarea>
        </label><br>
        <label>Image URL (optional): <input type="text" name="image"></label><br>
        <button type="submit">Submit Recipe</button>
    </form>

    <p><a href="index.php">Back to home</a></p>
</body>
</html>
