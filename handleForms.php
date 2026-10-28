
<?php
// Start the session if it has not started yet.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow registration and login before authentication.
// Other actions require the user to be logged in.
$publicActions = ['register', 'login'];
$requestedAction = $_POST['action'] ?? '';

if (
    !isset($_SESSION['user_id']) &&
    !in_array($requestedAction, $publicActions, true)
) {
    header('Location: ../login.php');
    exit;
}

// ============================================
// KOPE SHOP - FORM HANDLER
// Processes category and menu item forms
// ============================================

require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/models.php';


// Only process submitted POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}


// Get the requested action
$action = $_POST['action'] ?? '';

/* ============================================
   REGISTRATION ACTION
============================================ */

if ($action === 'register') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $full_name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        header("Location: ../register.php?status=invalid");
        exit;
    }

    try {
        registerUser($pdo, $full_name, $email, $password);

        $user_id = (int) $pdo->lastInsertId();

        addAuditLog(
            $pdo,
            $user_id,
            'REGISTER',
            'New account registered'
        );

        header("Location: ../login.php?status=registered");
        exit;

    } catch (PDOException $e) {

        // MySQL error 1062 means the email already exists.
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
            header("Location: ../register.php?status=duplicate");
            exit;
        }

        throw $e;
    }
}


/* ============================================
   LOGIN ACTION
============================================ */

if ($action === 'login') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUserByEmail($pdo, $email);

    if ($user && password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];

        addAuditLog(
            $pdo,
            (int) $user['user_id'],
            'LOGIN',
            'Successful login'
        );

        header("Location: ../index.php");
        exit;
    }

    // Log a failed login without associating it with a user.
    addAuditLog(
        $pdo,
        null,
        'FAILED_LOGIN',
        'Login failed for email: ' . $email
    );

    header("Location: ../login.php?status=invalid");
    exit;
}


/* ============================================
   LOGOUT ACTION
============================================ */

if ($action === 'logout') {

    if (isset($_SESSION['user_id'])) {
        addAuditLog(
            $pdo,
            (int) $_SESSION['user_id'],
            'LOGOUT',
            'User logged out'
        );
    }

    $_SESSION = [];

    session_destroy();

    header("Location: ../login.php?status=logout");
    exit;
}


// ============================================
// CATEGORY ACTIONS
// ============================================


 
if ($action === 'add_category') {

    $category_name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($category_name !== '') {
        try {
            addCategory(
    $pdo,
    $category_name,
    $description,
    $_SESSION['user_id']
);
            logAudit(
    $pdo,
    $_SESSION['user_id'],
    'INSERT',
    "Added category: " . $category_name
);

            header("Location: ../index.php?status=added");
            exit;

        } catch (PDOException $e) {

            // MySQL error 1062 means duplicate entry
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                header("Location: ../index.php?status=duplicate");
                exit;
            }

            throw $e;
        }
    }

    header("Location: ../index.php?status=empty");
    exit;
}




// Update a category
if ($action === 'update_category') {

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $category_name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($category_id && $category_name !== '') {
        updateCategory(
    $pdo,
    $category_id,
    $category_name,
    $description,
    $_SESSION['user_id']
);
        logAudit(
    $pdo,
    $_SESSION['user_id'],
    'UPDATE',
    "Updated category ID: " . $category_id
);
    }

    header("Location: ../index.php");
    exit;
}



if ($action === 'delete_category') {

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    if ($category_id) {
        try {
            deleteCategory($pdo, $category_id);
            logAudit(
    $pdo,
    $_SESSION['user_id'],
    'DELETE',
    "Deleted category ID: " . $category_id
);

            header("Location: ../index.php?status=deleted");
            exit;

        } catch (PDOException $e) {
            // MySQL error 1451 means a related record prevents deletion
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451) {
                header("Location: ../index.php?status=category_in_use");
                exit;
            }

            throw $e;
        }
    }

    header("Location: ../index.php");
    exit;
}



// ============================================
// MENU ITEM ACTIONS
// ============================================

// Add a menu item
if ($action === 'add_menu_item') {

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $item_name = trim($_POST['item_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $is_available = (int) ($_POST['is_available'] ?? 0);

    if (
        $category_id &&
        $item_name !== '' &&
        $price !== '' &&
        is_numeric($price) &&
        (float) $price >= 0
    ) {
        addMenuItem(
    $pdo,
    $category_id,
    $item_name,
    $description,
    $price,
    $is_available,
    $_SESSION['user_id']
);
        logAudit(
    $pdo,
    $_SESSION['user_id'],
    'INSERT',
    "Added menu item: " . $item_name
);
    }

    header("Location: ../viewprojects.php?status=added");
    exit;
}


// Update a menu item
if ($action === 'update_menu_item') {

    $menu_item_id = filter_input(
        INPUT_POST,
        'menu_item_id',
        FILTER_VALIDATE_INT
    );

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $item_name = trim($_POST['item_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $is_available = (int) ($_POST['is_available'] ?? 0);

    if (
        $menu_item_id &&
        $category_id &&
        $item_name !== '' &&
        $price !== '' &&
        is_numeric($price) &&
        (float) $price >= 0
    ) {
        updateMenuItem(
    $pdo,
    $menu_item_id,
    $category_id,
    $item_name,
    $description,
    $price,
    $is_available,
    $_SESSION['user_id']
);
        logAudit(
    $pdo,
    $_SESSION['user_id'],
    'UPDATE',
    "Updated menu item ID: " . $menu_item_id
);
    }

    header("Location: ../viewprojects.php?status=updated");
    exit;
}


// Delete a menu item
if ($action === 'delete_menu_item') {

    $menu_item_id = filter_input(
        INPUT_POST,
        'menu_item_id',
        FILTER_VALIDATE_INT
    );

    if ($menu_item_id) {
        deleteMenuItem($pdo, $menu_item_id);
        logAudit(
    $pdo,
    $_SESSION['user_id'],
    'DELETE',
    "Deleted menu item ID: " . $menu_item_id
);
    }

    header("Location: ../viewprojects.php?status=deleted");
    exit;
}


// Return to the home page if the action is unknown
header("Location: ../index.php");
exit;

?>
