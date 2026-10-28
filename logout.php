
<?php
// Start the session if it has not started yet.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remove all session variables.
$_SESSION = [];

// Destroy the current session.
session_destroy();

// Redirect the user to the login page.
header('Location: login.php');
exit;
?>
