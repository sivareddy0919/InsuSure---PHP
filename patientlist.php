<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, OPTIONS");
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

// Handle GET request
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for 'id' or 'username' parameter in the query string
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $username = isset($_GET['username']) ? $_GET['username'] : null;

    // Prepare SQL query
    if ($id !== null) {
        // If 'id' is provided, filter results by 'id'
        $sql = "SELECT `id`, `datetime`, `sugar_concentration`, `note`, `unit`, `username`, `session`, `insulinintake`, `status` FROM `glucoseentry` WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    } elseif ($username !== null) {
        // If 'username' is provided, filter results by 'username'
        $sql = "SELECT `id`, `datetime`, `sugar_concentration`, `note`, `unit`, `username`, `session`, `insulinintake`, `status` FROM `glucoseentry` WHERE `username` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // If neither 'id' nor 'username' is provided, get all records
        $sql = "SELECT `id`, `datetime`, `sugar_concentration`, `note`, `unit`, `username`, `session`, `insulinintake`, `status` FROM `glucoseentry`";
        $result = $conn->query($sql);
    }

    // Check if there are results
    if ($result->num_rows > 0) {
        // Fetch all results into an associative array
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // Output the results as JSON
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No records found']);
    }

    // Close the statement if prepared statement was used
    if (isset($stmt)) {
        $stmt->close();
    }

// Handle other HTTP methods or invalid requests
} else {
    $response = array(
        "status" => "error",
        "message" => "Invalid request method"
    );
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
