<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include 'Phpconnection.php';

// Check if the connection is established
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Ensure that the request method is GET and the username parameter is set and not empty
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['username']) && !empty($_GET['username'])) {
        $username = $_GET['username'];

        // Debugging: Log the received username
        error_log("Received username: " . $username);

        // Prepare the SQL query
        $query = $conn->prepare("SELECT `pname`, `mob`, `mail`, `gender`, `age`, `username`, `pass`, `cpass`, `image_path` FROM `patientlogin` WHERE `username` = ?");
        
        if ($query) {
            $query->bind_param("s", $username);
            if ($query->execute()) {
                $result = $query->get_result();
                if ($result->num_rows > 0) {
                    $userDetails = $result->fetch_assoc();
                    echo json_encode(['status' => 'success', 'data' => $userDetails]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'User not found']);
                }
                $result->free();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Query execution failed: ' . $query->error]);
            }
            $query->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input: Username parameter missing or empty']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the connection if open
if ($conn) {
    $conn->close();
}
?>
