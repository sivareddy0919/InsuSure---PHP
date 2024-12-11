<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


// Include the database connection file
include 'Phpconnection.php';

// Fetch entries based on the provided username and status 'pending'
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $sql = "SELECT id, datetime, sugar_concentration, note, unit, username, session, insulinintake, status 
            FROM glucoseentry
            WHERE status = 'pending' AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

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
} else {
    $response = array(
        "status" => "error",
        "message" => "Username not provided"
    );
}

$conn->close();

echo json_encode($response);
?>