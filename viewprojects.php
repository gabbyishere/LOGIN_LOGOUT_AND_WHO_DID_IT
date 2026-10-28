
<?php
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/core/dbConfig.php';
require_once __DIR__ . '/core/models.php';

// Get all categories and menu items from the database
$categories = getAllCategories($pdo);
$menuItems = getAllMenuItems($pdo);

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$status = $_GET['status'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Menu Items | Kope Shop</title>

    <link rel="stylesheet" href="styles.css">
<style>
/* Kope Shop custom delete confirmation */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(48, 32, 42, 0.65);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.show {
    display: flex;
}

.modal-box {
    background: #ffffff;
    color: #684735;
    width: 100%;
    max-width: 430px;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.modal-box h2 {
    color: #62446f;
    margin-top: 0;
}

.modal-box p {
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 24px;
}

.modal-actions button {
    border: none;
    border-radius: 10px;
    padding: 12px 20px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.cancel-button {
    background: #f5eadb;
    color: #684735;
}

.confirm-delete-button {
    background: #684735;
    color: #ffffff;
}

.cancel-button:hover {
    background: #ead9c5;
}

.confirm-delete-button:hover {
    background: #503526;
}

.action-buttons .delete-action {
    border: none;
    cursor: pointer;
    font-family: inherit;
    font-size: inherit;
}
</style>
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

    <?php if ($status === 'added'): ?>
        <p class="notice success">Menu item added successfully! ☕</p>
    <?php elseif ($status === 'updated'): ?>
        <p class="notice success">Menu item updated successfully!</p>
    <?php elseif ($status === 'deleted'): ?>
        <p class="notice success">Menu item deleted successfully!</p>
    <?php elseif ($status === 'duplicate'): ?>
        <p class="notice warning">This menu item already exists.</p>
    <?php endif; ?>

    <section class="card">
        <h2>Add a Menu Item</h2>

        <form action="core/handleForms.php" method="POST">

            <input type="hidden" name="action" value="add_menu_item">

            <label for="item_name">Menu Item Name</label>
            <input
                type="text"
                id="item_name"
                name="item_name"
                maxlength="100"
                placeholder="e.g. Iced Spanish Latte"
                required
            >

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Choose a category</option>

                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['category_id']) ?>">
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
                placeholder="Describe this drink..."
            ></textarea>

            <label for="price">Price (₱)</label>
            <input
                type="number"
                id="price"
                name="price"
                min="0.01"
                step="0.01"
                placeholder="120.00"
                required
            >

            <label for="is_available">Availability</label>
            <select id="is_available" name="is_available">
                <option value="1">Available</option>
                <option value="0">Unavailable</option>
            </select>

            <button type="submit" class="button">
                + Add Menu Item
            </button>

        </form>
    </section>

    <section class="card">
        <h2>Our Menu Items ☕</h2>

        <?php if (empty($menuItems)): ?>

            <p>No menu items yet. Add your first drink above!</p>

        <?php else: ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Category</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Availability</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $displayNumber = 1; ?>
                        <?php foreach ($menuItems as $item): ?>
                            <tr>
                                <td><?= $displayNumber++ ?></td>

                                <td>
                                    <?php
                                    $categoryName = 'Unknown category';

                                    foreach ($categories as $category) {
                                        if (
                                            $category['category_id']
                                            == $item['category_id']
                                        ) {
                                            $categoryName = $category['category_name'];
                                            break;
                                        }
                                    }

                                    echo e($categoryName);
                                    ?>
                                </td>

                                <td><?= e($item['item_name']) ?></td>

                                <td><?= e($item['description'] ?? '') ?></td>

                                <td>₱<?= number_format((float) $item['price'], 2) ?></td>

                                <td>
                                    <?php if ((int) $item['is_available'] === 1): ?>
                                        <span class="success-text">Available</span>
                                    <?php else: ?>
                                        <span class="warning-text">Unavailable</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= e($item['added_by_name'] ?? 'Not recorded') ?>
                                </td>

                                <td>
                                    <?= e($item['updated_by_name'] ?? 'Not updated yet') ?>
                                </td>

                                <td>
                                    <?= !empty($item['last_updated'])
                                        ? e($item['last_updated'])
                                        : 'Not recorded' ?>
                                </td>

                               
                                <td class="action-buttons">
                                    <a
                                        href="editweb.php?id=<?= e($item['menu_item_id']) ?>"
                                        class="action-button edit-action"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        class="action-button delete-action"
                                        data-id="<?= e($item['menu_item_id']) ?>"
                                        data-name="<?= e($item['item_name']) ?>"
                                        onclick="openDeleteModal(this)"
                                    >
                                        Delete
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
<!-- Custom Delete Confirmation Modal -->
<div
    class="modal-overlay"
    id="deleteModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="deleteModalTitle"
>
    <div class="modal-box">
        <h2 id="deleteModalTitle">Remove Menu Item? ☕</h2>

        <p>
            Are you sure you want to remove
            <strong id="deleteItemName"></strong>
            from the Kope Shop menu?
        </p>

        <p>This action cannot be undone.</p>

        <form action="core/handleForms.php" method="POST">
            <input
                type="hidden"
                name="action"
                value="delete_menu_item"
            >

            <input
                type="hidden"
                name="menu_item_id"
                id="deleteItemId"
                value=""
            >

            <div class="modal-actions">
                <button
                    type="button"
                    class="cancel-button"
                    onclick="closeDeleteModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="confirm-delete-button"
                >
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(button) {
    const modal = document.getElementById('deleteModal');

    document.getElementById('deleteItemId').value =
        button.dataset.id;

    document.getElementById('deleteItemName').textContent =
        button.dataset.name;

    modal.classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal')
        .classList.remove('show');
}

document.getElementById('deleteModal').addEventListener(
    'click',
    function (event) {
        if (event.target === this) {
            closeDeleteModal();
        }
    }
);

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
</html>
