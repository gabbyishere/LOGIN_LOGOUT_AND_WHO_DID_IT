
<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$category = getCategoryById($pdo, $id);

if (!$category) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | Kope Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <h1>Kope Shop ☕</h1>
    <p>A little coffee, a little comfort, a lot of love.</p>
    <nav class="navigation">
        <a href="index.php" class="nav-button">Categories</a>
        <a href="viewprojects.php" class="nav-button">Menu Items</a>
    </nav>
</header>

<main class="page-container">
    <section class="card">
        <h2>Edit Category</h2>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="update_category">

            <input
                type="hidden"
                name="category_id"
                value="<?= e($category['category_id']) ?>"
            >

            <label for="category_name">Category Name</label>
            <input
                type="text"
                id="category_name"
                name="category_name"
                maxlength="100"
                value="<?= e($category['category_name']) ?>"
                required
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                maxlength="255"
                rows="4"
            ><?= e($category['description'] ?? '') ?></textarea>

            <button type="submit" class="button">Save Changes</button>
            <a href="index.php" class="button">Cancel</a>
        </form>
    </section>
</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
