<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include the database connection file
include 'Phpconnection.php';

// Check if the request method is POST or GET and handle accordingly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON data sent from the client
    $data = json_decode(file_get_contents('php://input'), true);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve data from the URL parameters for GET request
    $data = $_GET;
}

// Check if all required parameters are set
if (isset($data['username']) && isset($data['datetime']) && isset($data['sugar_concentration']) && isset($data['note']) && isset($data['unit']) && isset($data['session']) && isset($data['insulinintake'])) {
    $username = $data['username'];
    $datetime = $data['datetime'];
    $sugar_concentration = $data['sugar_concentration'];
    $note = $data['note'];
    $unit = $data['unit'];
    $session = $data['session'];
    $insulinintake = $data['insulinintake'];
    $status = 'completed';  // Set the status to 'completed'

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE glucoseentry SET sugar_concentration = ?, note = ?, unit = ?, session = ?, insulinintake = ?, status = ? WHERE username = ? AND datetime = ?");
    if ($stmt === false) {
        die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . htmlspecialchars($conn->error)]));
    }
    
    $bind = $stmt->bind_param("ssssssss", $sugar_concentration, $note, $unit, $session, $insulinintake, $status, $username, $datetime);
    if ($bind === false) {
        die(json_encode(['status' => 'error', 'message' => 'Bind failed: ' . htmlspecialchars($stmt->error)]));
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Check if any row was updated
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Insulin intake updated successfully and status changed to completed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No record found to update']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update insulin intake: ' . htmlspecialchars($stmt->error)]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
}
?>
