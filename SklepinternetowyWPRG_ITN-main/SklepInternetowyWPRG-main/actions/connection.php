<?php

$servername = "localhost";
$username = "root";  // replace with your username
$password = "";  // replace with your password
$database = "php_project";  // replace with your database name
$charset = "utf8";

// Create connection
try {
    $conn1 = new mysqli($servername, $username, $password, $database);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>