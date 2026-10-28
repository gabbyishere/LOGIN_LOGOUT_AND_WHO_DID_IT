
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

// Check whether this category still has menu items
$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM menu_items WHERE category_id = :id"
);
$stmt->execute([':id' => $id]);
$itemCount = (int) $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category | Kope Shop</title>
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
        <h2>Delete Category</h2>

        <p>Category Name: <strong><?= e($category['category_name']) ?></strong></p>
        <p>Description: <?= e($category['description'] ?? '') ?></p>

        <?php if ($itemCount > 0): ?>

            <p class="notice warning">
                This category has <?= $itemCount ?> menu item(s) assigned to it.
                You must reassign or remove those menu items before deleting
                this category.
            </p>

            <a href="viewprojects.php" class="button">
                View Menu Items
            </a>

        <?php else: ?>

            <p>Are you sure you want to delete this category?</p>

            <form action="core/handleForms.php" method="POST">
                <input type="hidden" name="action" value="delete_category">
                <input
                    type="hidden"
                    name="category_id"
                    value="<?= e($category['category_id']) ?>"
                >

                <button type="submit" class="button delete-button">
                    Yes, Delete Category
                </button>

                <a href="index.php" class="button">Cancel</a>
            </form>

        <?php endif; ?>
    </section>
</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
