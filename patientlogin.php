<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

// Initialize response array
$response = array();

// Check if the request method is POST or GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data as a string
    $json_data = file_get_contents("php://input");

    // Decode the JSON data into an associative array
    $request_data = json_decode($json_data, true);

    // Check if 'username' and 'password' keys exist in $request_data
    if (isset($request_data['username']) && isset($request_data['password'])) {
        // Get the username and password from the decoded JSON data
        $username = $request_data['username'];
        $password = $request_data['password'];
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    if (isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
    }
}

// Check if username and password are set
if (isset($username) && isset($password)) {
    // Query to check login credentials using prepared statements
    $sql = "SELECT * FROM patientlogin WHERE `username` = ? AND `pass` = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $response['status'] = "error";
        $response['message'] = "Database error: " . $conn->error;
    } else {
        // Bind parameters
        $stmt->bind_param('ss', $username, $password);

        // Execute the prepared statement
        if (!$stmt->execute()) {
            $response['status'] = "error";
            $response['message'] = "Execution error: " . $stmt->error;
        } else {
            // Get the result set
            $result = $stmt->get_result()->fetch_assoc();

            // Check if login credentials are valid
            if ($result) {
                $response['status'] = "success";
                $response['message'] = "Login successful!";
                
                // Fetch additional patient details including image_path
                $patient_details_sql = "SELECT `pname`, `mob`, `mail`, `gender`, `age`, `image_path` FROM patientlogin WHERE `username` = ?";
                $patient_stmt = $conn->prepare($patient_details_sql);
                $patient_stmt->bind_param('s', $username);
                $patient_stmt->execute();
                $patient_result = $patient_stmt->get_result()->fetch_assoc();
                
                // Include patient details in the response
                $response['patient_details'] = $patient_result;

                // Close the patient details statement
                $patient_stmt->close();
            } else {
                $response['status'] = "error";
                $response['message'] = "Invalid username or password";
            }
        }

        // Close the prepared statement
        $stmt->close();
    }
} else {
    // Handle the case where 'username' or 'password' is missing
    $response['status'] = "error";
    $response['message'] = "Invalid request data";
}

// Close the database connection
$conn->close();

// Respond with JSON
echo json_encode($response);
?>
