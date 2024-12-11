<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


// Include the database connection file
include 'Phpconnection.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from the decoded JSON
    $date = isset($data['date']) ? $data['date'] : '';
    $time = isset($data['time']) ? $data['time'] : '';
    $sugarConcentration = isset($data['sugarConcentration']) ? $data['sugarConcentration'] : '';
    $note = isset($data['note']) ? $data['note'] : '';
    $unit = isset($data['unit']) ? $data['unit'] : ''; // Unit can be 'mmol/L' or 'mg/dL'
    $username = isset($data['username']) ? $data['username'] : ''; // Get the username from the request
    $session = isset($data['session']) ? $data['session'] : ''; // Get the session from the request
    $insulinintake = isset($data['insulinintake']) ? $data['insulinintake'] : null;
    $status = isset($data['status']) ? $data['status'] : '';



    // Combine date and time into a single datetime string
    $datetime = $date . ' ' . $time;

    // If insulinintake is not provided, set status to 'pending'
    if ($insulinintake === null) {
        $status = 'pending';
    }


    // Prepare SQL statement
    $sql = "INSERT INTO glucoseentry (datetime, sugar_concentration, note, unit, username, session, insulinintake, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if prepare statement was successful
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    // Handle null insulinintake properly
    if ($insulinintake === null) {
        $stmt->bind_param("sdssssss", $datetime, $sugarConcentration, $note, $unit, $username, $session, $insulinintake, $status);
    } else {
        $stmt->bind_param("sdssssss", $datetime, $sugarConcentration, $note, $unit, $username, $session, $insulinintake, $status);
    }

    // Execute the statement
    if ($stmt->execute()) {
        $response = array(
            "status" => "success",
            "message" => "Submitted successfully"
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Failed to insert data: " . htmlspecialchars($stmt->error)
        );
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return JSON response
    echo json_encode($response);
} else {
    // Handle other HTTP methods or invalid requests
    $response = array(
        "status" => "error",
        "message" => "Invalid request method"
    );
    echo json_encode($response);
}
?>