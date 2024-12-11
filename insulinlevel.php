<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


// Include the database connection file
include 'Phpconnection.php';

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

$conn->close();

echo json_encode($response);
?>