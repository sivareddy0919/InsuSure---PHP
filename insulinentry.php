<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

// Handle PUT request
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Decode JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from the decoded JSON
    $id = isset($data['id']) ? $data['id'] : '';
    $insulinIntake = isset($data['insulinintake']) ? $data['insulinintake'] : '';

    // Validate input data
    if (empty($id) || empty($insulinIntake)) {
        $response = array(
            "status" => "error",
            "message" => "Invalid input data"
        );
        echo json_encode($response);
        exit();
    }

    // Determine the new status
    $status = !empty($insulinIntake) ? 'completed' : 'pending';

    // Prepare SQL statement
    $sql = "UPDATE glucoseentry SET insulinintake = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute statement
    $stmt->bind_param("ssi", $insulinIntake, $status, $id); // 's' for string, 'i' for integer
    $stmt->execute();

    // Check if update was successful
    if ($stmt->affected_rows > 0) {
        $response = array(
            "status" => "success",
            "message" => "Insulin intake and status updated successfully"
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Failed to update insulin intake and status"
        );
    }

    // Close statement
    $stmt->close();

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

// Close the database connection
$conn->close();
?>
