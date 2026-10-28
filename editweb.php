
<?php
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$menu_item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$menu_item_id) {
    header('Location: viewprojects.php');
    exit;
}

$item = getMenuItemById($pdo, $menu_item_id);
$categories = getAllCategories($pdo);

if (!$item) {
    header('Location: viewprojects.php?status=not_found');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item | Kope Shop</title>
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
        <h2>Edit Menu Item</h2>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="update_menu_item">

            <input
                type="hidden"
                name="menu_item_id"
                value="<?= e($item['menu_item_id']) ?>"
            >

            <label for="item_name">Menu Item Name</label>
            <input
                type="text"
                id="item_name"
                name="item_name"
                maxlength="100"
                value="<?= e($item['item_name']) ?>"
                required
            >

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= e($category['category_id']) ?>"
                        <?= (int) $category['category_id'] ===
                            (int) $item['category_id'] ? 'selected' : '' ?>
                    >
                        <?= e($category['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                maxlength="255"
                rows="3"
            ><?= e($item['description'] ?? '') ?></textarea>

            <label for="price">Price (₱)</label>
            <input
                type="number"
                id="price"
                name="price"
                min="0.01"
                step="0.01"
                value="<?= e($item['price']) ?>"
                required
            >

            <label for="is_available">Availability</label>
            <select id="is_available" name="is_available">
                <option
                    value="1"
                    <?= (int) $item['is_available'] === 1 ? 'selected' : '' ?>
                >
                    Available
                </option>
                <option
                    value="0"
                    <?= (int) $item['is_available'] === 0 ? 'selected' : '' ?>
                >
                    Unavailable
                </option>
            </select>

            <button type="submit" class="button">
                Save Changes
            </button>

            <a href="viewprojects.php" class="nav-button">
                Cancel
            </a>
        </form>
    </section>
</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
