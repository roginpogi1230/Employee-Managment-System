<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Dito natin nilagyan ng 'root' dahil kadalasan ito ang default sa Uniform Server
define('DB_NAME', 'expenses_db');

try {
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
} catch (mysqli_sql_exception $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}
?>