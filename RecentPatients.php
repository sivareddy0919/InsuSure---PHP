<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow both GET and POST methods
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all entries with status 'pending'
    $sql = "SELECT id, datetime, sugar_concentration, note, unit, username, session, insulinintake, status FROM glucoseentry WHERE status = 'pending'";
    $result = $conn->query($sql);

    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $response = array(
        "status" => "success",
        "data" => $data
    );

    // Send the response as JSON
    echo json_encode($response);
} else {
    // Handle unsupported request methods
    $response = array(
        "status" => "error",
        "message" => "Invalid request method. Only GET is allowed."
    );
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
