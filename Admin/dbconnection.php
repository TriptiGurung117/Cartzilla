<?php
$host = "localhost";       // Change if your DB server is different
$dbUser = "root";          // Your DB username
$dbPassword = "";          // Your DB password (often empty for localhost)
$dbName = "cartzilla_db";  // Your database name

// Create connection
$conn = new mysqli($host, $dbUser, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>