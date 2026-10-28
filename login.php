
<?php
session_start();

require_once __DIR__ . '/core/dbConfig.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        $sql = "SELECT user_id, full_name, email, password_hash
                FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify(
            $password,
            $user['password_hash']
        )) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];

            header('Location: index.php');
            exit;
        }

        $error = 'Incorrect email or password.';
    } else {
        $error = 'Please enter your email and password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | Kope Shop</title>

    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <h1>Kope Shop ☕</h1>
    <p>A little coffee, a little comfort, a lot of love.</p>
</header>

<main class="page-container">
    <section class="card">
        <h2>Welcome Back 💜</h2>
        <p>Log in to access the Kope Shop system.</p>

        <?php if (isset($_GET['status'])
                  && $_GET['status'] === 'registered'): ?>
            <p class="notice success">
                Account created successfully! Please log in.
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="notice warning">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="you@example.com"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit" class="button">
                Log In
            </button>
        </form>

        <p>
            Don't have an account?
            <a href="register.php">Create one here.</a>
        </p>
    </section>
</main>

<footer class="site-footer">
    <p>Made with love and coffee at Kope Shop. 💜</p>
</footer>

</body>
</html>
