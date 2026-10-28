
<?php
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Kope Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <h1>Kope Shop ☕</h1>
    <p>A little coffee, a little comfort, a lot of love.</p>
</header>

<main class="page-container">
    <section class="card">
        <h2>Create Your Account 💜</h2>
        <p>Join Kope Shop to access the system.</p>

        <?php if ($status === 'duplicate'): ?>
            <p class="notice warning">
                That email address is already registered.
            </p>
        <?php elseif ($status === 'invalid'): ?>
            <p class="notice warning">
                Please enter a valid name and email, and use a password
                with at least 8 characters.
            </p>
        <?php endif; ?>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="action" value="register">

            <label for="full_name">Full Name</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                maxlength="100"
                placeholder="Enter your full name"
                required
            >

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                placeholder="you@example.com"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                placeholder="At least 8 characters"
                required
            >

            <button type="submit" class="button">
                Create Account
            </button>
        </form>

        <p>Already have an account?
            <a href="login.php">Log in here</a>.
        </p>
    </section>
</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
