
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
    <title>Delete Menu Item | Kope Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <h1>Kope Shop ☕</h1>
    <p>A little coffee, a little comfort, a lot of love.</p>
</header>

<main class="page-container">
    <section class="card">
        <h2>Remove Menu Item?</h2>

        <p>
            Are you sure you want to delete
            <strong><?= e($item['item_name']) ?></strong>?
        </p>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="delete_menu_item">

            <input
                type="hidden"
                name="menu_item_id"
                value="<?= e($item['menu_item_id']) ?>"
            >

            <button type="submit" class="button">
                Yes, Delete
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
