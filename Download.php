<?php
// Include the database connection file
require_once('Phpconnection.php');

// Set the headers for JSON response
header('Content-Type: application/json');

// Use a SQL query to fetch all rows from the 'patientlogin' table
$query = "SELECT * FROM patientlogin";
$result = $conn->query($query);

// Check if there are results
if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Collect each row as an associative array
    }

    // Send the data as a JSON response
    echo json_encode($data);
} else {
    // Return an empty JSON array if no data is found
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>
