<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");


// Include the database connection file
include 'Phpconnection.php';

// Check if username parameter is set
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT `datetime`, `sugar_concentration`, `unit`, `username`, `session`, `insulinintake` FROM `glucoseentry` WHERE `username` = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $data = array();

    // Fetch data
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Debugging: Print the fetched data
    echo json_encode(array("status" => "success", "data" => $data));

    // Close statement
    $stmt->close();
} else {
    echo json_encode(array("error" => "Username not provided"));
}

// Close connection
$conn->close();
?>