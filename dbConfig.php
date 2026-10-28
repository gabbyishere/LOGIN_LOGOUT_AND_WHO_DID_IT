
<?php

// Kope Shop database configuration
$host = "localhost";
$dbname = "kope_shop_db";
$username = "root";
$password = "";

// Connect to MySQL using PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Enable PDO error reporting
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Fetch database results as associative arrays
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    // Show a friendly error if the connection fails
    exit("Database connection failed. Please check your settings.");
}

?>
