<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include 'Phpconnection.php';

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for 'id' parameter in the query string
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Prepare SQL query to filter by status 'completed'
    if ($id !== null) {
        // If 'id' is provided, filter results by 'id' and status 'completed'
        $sql = "SELECT `id`, `datetime`, `sugar_concentration`, `note`, `unit`, `username`, `session`, `insulinintake`, `status` FROM `glucoseentry` WHERE `id` = ? AND `status` = 'completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // If 'id' is not provided, get all records with status 'completed'
        $sql = "SELECT `id`, `datetime`, `sugar_concentration`, `note`, `unit`, `username`, `session`, `insulinintake`, `status` FROM `glucoseentry` WHERE `status` = 'completed'";
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
    
    // Close the connection
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
