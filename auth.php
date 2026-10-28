
<?php
// Start the session if it has not started yet.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect users who are not logged in.
if (!isset($_SESSION['user_id'])) {
    header('Location: /kope_shop/login.php');
    exit;
}
?>
