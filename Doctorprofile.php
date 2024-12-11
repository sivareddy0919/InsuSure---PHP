<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // SQL query to fetch doctorlogin details
    $sql = "SELECT `doctorname`, `contactNumber`, `email`, `qualification`, `imagepath` FROM `doctorlogin`";
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result === false) {
        die(json_encode(array("status" => "error", "message" => $conn->error)));
    }

    // Check if there are results
    if ($result->num_rows > 0) {
        // Fetch data and store it in an array
        $doctorlogins = array();
        while ($row = $result->fetch_assoc()) {
            $doctorlogins[] = $row;
        }

        // Output data in JSON format
        echo json_encode(array("status" => "success", "data" => $doctorlogins));
    } else {
        echo json_encode(array("status" => "error", "message" => "No doctor records found."));
    }
} else {
    // Handle other HTTP methods (e.g., POST, OPTIONS)
    echo json_encode(array("status" => "error", "message" => "Invalid request method. Only GET is allowed."));
}

// Close the connection
$conn->close();
?>
