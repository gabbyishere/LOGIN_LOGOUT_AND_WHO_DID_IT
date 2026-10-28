
<?php

// ============================================
// KOPE SHOP - DATABASE MODELS
// Database operations using PDO
// ============================================


// ============================================
// PARENT ENTITY: CATEGORIES
// ============================================

// Get all categories

function getAllCategories($pdo)
{
    $sql = "SELECT
                c.*,
                creator.full_name AS added_by_name,
                editor.full_name AS updated_by_name
            FROM categories c
            LEFT JOIN users creator
                ON c.added_by = creator.user_id
            LEFT JOIN users editor
                ON c.updated_by = editor.user_id
            ORDER BY c.category_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}



// Get one category by ID
function getCategoryById($pdo, $category_id)
{
    $sql = "SELECT * FROM categories
            WHERE category_id = :category_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category_id' => $category_id
    ]);

    return $stmt->fetch();
}


// Insert a category

function addCategory($pdo, $category_name, $description, $added_by)
{
    $sql = "INSERT INTO categories
            (category_name, description, added_by)
            VALUES (:category_name, :description, :added_by)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':category_name' => $category_name,
        ':description' => $description,
        ':added_by' => $added_by
    ]);
}



// Update a category

function updateCategory(
    $pdo, $category_id, $category_name, $description, $updated_by
) {
    $sql = "UPDATE categories
            SET category_name = :category_name,
                description = :description,
                updated_by = :updated_by
            WHERE category_id = :category_id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':category_name' => $category_name,
        ':description' => $description,
        ':updated_by' => $updated_by,
        ':category_id' => $category_id
    ]);
}



// Delete a category
function deleteCategory($pdo, $category_id)
{
    $sql = "DELETE FROM categories
            WHERE category_id = :category_id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':category_id' => $category_id
    ]);
}


// ============================================
// CHILD ENTITY: MENU ITEMS
// ============================================

// Get all menu items with their category names

function getAllMenuItems($pdo)
{
    $sql = "SELECT
                m.*,
                c.category_name,
                creator.full_name AS added_by_name,
                editor.full_name AS updated_by_name
            FROM menu_items m
            INNER JOIN categories c
                ON m.category_id = c.category_id
            LEFT JOIN users creator
                ON m.added_by = creator.user_id
            LEFT JOIN users editor
                ON m.updated_by = editor.user_id
            ORDER BY m.menu_item_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}



// Get one menu item by ID
function getMenuItemById($pdo, $menu_item_id)
{
    $sql = "SELECT * FROM menu_items
            WHERE menu_item_id = :menu_item_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':menu_item_id' => $menu_item_id
    ]);

    return $stmt->fetch();
}


// Insert a menu item

function addMenuItem(
    $pdo, $category_id, $item_name, $description,
    $price, $is_available, $added_by
) {
    $sql = "INSERT INTO menu_items
            (category_id, item_name, description, price,
             is_available, added_by)
            VALUES
            (:category_id, :item_name, :description, :price,
             :is_available, :added_by)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':category_id' => $category_id,
        ':item_name' => $item_name,
        ':description' => $description,
        ':price' => $price,
        ':is_available' => $is_available,
        ':added_by' => $added_by
    ]);
}



// Update a menu item

function updateMenuItem(
    $pdo, $menu_item_id, $category_id, $item_name,
    $description, $price, $is_available, $updated_by
) {
    $sql = "UPDATE menu_items
            SET category_id = :category_id,
                item_name = :item_name,
                description = :description,
                price = :price,
                is_available = :is_available,
                updated_by = :updated_by
            WHERE menu_item_id = :menu_item_id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':category_id' => $category_id,
        ':item_name' => $item_name,
        ':description' => $description,
        ':price' => $price,
        ':is_available' => $is_available,
        ':updated_by' => $updated_by,
        ':menu_item_id' => $menu_item_id
    ]);
}



// Delete a menu item
function deleteMenuItem($pdo, $menu_item_id)
{
    $sql = "DELETE FROM menu_items
            WHERE menu_item_id = :menu_item_id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':menu_item_id' => $menu_item_id
    ]);
}

?>

<?php
// ============================================
// USER ENTITY
// ============================================

// Register a new user with a securely hashed password
function registerUser($pdo, $full_name, $email, $password)
{
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password_hash)
            VALUES (:full_name, :email, :password_hash)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);
}


// Find a user by email for login
function getUserByEmail($pdo, $email)
{
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// ============================================
// AUDIT LOGS
// ============================================

// Record a user action
function addAuditLog($pdo, $user_id, $action, $details = null)
{
    $sql = "INSERT INTO audit_logs (user_id, action, details)
            VALUES (:user_id, :action, :details)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':user_id' => $user_id,
        ':action' => $action,
        ':details' => $details
    ]);
}

/**
 * Record a user's action in the audit_logs table.
 */
function logAudit($pdo, $user_id, $action, $details = '')
{
    $sql = "INSERT INTO audit_logs
            (user_id, action, details, created_at)
            VALUES
            (:user_id, :action, :details, CURRENT_TIMESTAMP)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':user_id' => $user_id,
        ':action' => $action,
        ':details' => $details
    ]);
}

?>
