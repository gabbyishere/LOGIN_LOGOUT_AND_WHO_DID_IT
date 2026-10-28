
<?php
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';

// Get all categories from the database
$categories = getAllCategories($pdo);

// Escape output to help prevent HTML injection
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kope Shop | Categories</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <h1>Kope Shop ☕</h1>
    <p>A little coffee, a little comfort, a lot of love.</p>

    
<div class="header-actions">
    <nav class="navigation">
        <a href="index.php" class="nav-button active">Categories</a>
        <a href="viewprojects.php" class="nav-button">Menu Items</a>
    </nav>

    <a href="logout.php" class="nav-button logout-button">
        ↪ Logout
    </a>
</div>

</header>

<main class="page-container">
    
<?php
$status = $_GET['status'] ?? '';

if ($status === 'added') {
    echo '<p class="notice success">Category added successfully! ☕</p>';
} elseif ($status === 'duplicate') {
    echo '<p class="notice warning">That category already exists. Try a different name!</p>';
} elseif ($status === 'empty') {
    echo '<p class="notice warning">Please enter a category name.</p>';
} elseif ($status === 'updated') {
    echo '<p class="notice success">Category updated successfully!</p>';
} elseif ($status === 'deleted') {
    echo '<p class="notice success">Category deleted successfully!</p>';
} elseif ($status === 'category_in_use') {
    echo '<p class="notice warning">This category still has menu items. Reassign or remove those items first.</p>';
}
?>


    <!-- Add Category Form -->
    <section class="card">
        <h2>Add a Category</h2>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="add_category">

            <label for="category_name">Category Name</label>
            <input
                type="text"
                id="category_name"
                name="category_name"
                placeholder="e.g. Coffee"
                maxlength="100"
                required
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                placeholder="Describe this category"
                maxlength="255"
                rows="4"
            ></textarea>

            <button type="submit" class="button">
                + Add Category
            </button>
        </form>
    </section>

    <!-- Display Categories -->
    <section class="card">
        <h2>Our Categories</h2>

        <?php if (count($categories) > 0): ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                        
                    </thead>


                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <?= e($category['category_id']) ?>
                                </td>

                                <td>
                                    <?= e($category['category_name']) ?>
                                </td>

                                <td>
                                    <?= e($category['description'] ?? '') ?>
                                </td>

                                <td>
                                    <?= e($category['added_by_name'] ?? 'Not recorded') ?>
                                </td>

                                <td>
                                    <?= e($category['updated_by_name'] ?? 'Not updated yet') ?>
                                </td>

                                <td>
                                    <?= !empty($category['last_updated'])
                                        ? e($category['last_updated'])
                                        : 'Not recorded' ?>
                                </td>

                                <td class="actions">
                                    <a
                                        class="button small-button"
                                        href="editproject.php?id=<?= e($category['category_id']) ?>"
                                    >Edit</a>

                                    <a
                                        class="button small-button delete-button"
                                        href="deleteproject.php?id=<?= e($category['category_id']) ?>"
                                    >Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <p class="empty-message">
                No categories yet. Add your first category above!
            </p>

        <?php endif; ?>
    </section>

</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
