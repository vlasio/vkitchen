<?php
require_once 'includes/db.php';

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['query']) || isset($_GET['type']))) {
    $query = $_GET['query'] ?? '';
    $type = $_GET['type'] ?? '';

    $sql = "SELECT recipes.*, users.username 
            FROM recipes 
            JOIN users ON recipes.uid = users.uid 
            WHERE 1=1";
    $params = [];

    if (!empty($query)) {
        $sql .= " AND recipes.name LIKE ?";
        $params[] = '%' . $query . '%';
    }

    if (!empty($type)) {
        $sql .= " AND recipes.type = ?";
        $params[] = $type;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Recipes - Virtual Kitchen</title>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>
    <h1>Search Recipes</h1>
    <form method="GET" action="search.php">
        <label>Search by name: <input type="text" name="query" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"></label>
        <label>Filter by type:
            <select name="type">
                <option value="">-- Any --</option>
                <?php
                $types = ['French', 'Italian', 'Chinese', 'Indian', 'Mexican', 'others'];
                foreach ($types as $t):
                    $selected = (isset($_GET['type']) && $_GET['type'] === $t) ? 'selected' : '';
                    echo "<option value=\"$t\" $selected>$t</option>";
                endforeach;
                ?>
            </select>
        </label>
        <button type="submit">Search</button>
    </form>

    <h2>Results</h2>
    <?php if (count($results) > 0): ?>
        <ul>
            <?php foreach ($results as $recipe): ?>
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
        <p>No matching recipes found.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to home</a></p>
</body>
</html>
