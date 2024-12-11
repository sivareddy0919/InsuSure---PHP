<?php
// Database connection details
$servername = "localhost"; // Your database host
$username = "root"; // Your database username
$password = ""; // Your database password
$database = "diabetesassist"; // Your database name

// Base URL for images
$base_image_url = 'http://192.168.175.121/diabetesAssist';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
