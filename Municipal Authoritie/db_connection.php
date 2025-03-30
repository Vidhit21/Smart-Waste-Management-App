<?php
// Database connection settings (matching your provided SQL schema)
$host       = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname     = "smart_waste_management"; // This is the database created by your SQL query

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>