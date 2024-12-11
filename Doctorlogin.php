<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

// Initialize response array
$response = array();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    $username = isset($_GET['username']) ? $_GET['username'] : null;
    $password = isset($_GET['password']) ? $_GET['password'] : null;
}

// Check if username and password are provided
if ($username && $password) {
    // Query to check login credentials using prepared statements
    $sql = "SELECT `doctor_id`, `username`, `doctorname`, `contactNumber`, `email`, `qualification`, `imagepath` 
            FROM doctorlogin 
            WHERE `username` = ? AND `password` = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['status'] = "error";
        $response['message'] = "Database error: " . $conn->error;
    } else {
        // Bind parameters (username and password)
        $stmt->bind_param('ss', $username, $password);

        // Execute the prepared statement
        if (!$stmt->execute()) {
            $response['status'] = "error";
            $response['message'] = "Execution error: " . $stmt->error;
        } else {
            // Get the result
            $result = $stmt->get_result()->fetch_assoc();

            // Check if login credentials are valid
            if ($result) {
                // Return user details including the doctor_id
                $response['status'] = "success";
                $response['message'] = "Login successful!";
                $response['data'] = array(
                    'doctor_id' => $result['doctor_id'],
                    'username' => $result['username'],
                    'doctorname' => $result['doctorname'],
                    'contactNumber' => $result['contactNumber'],
                    'email' => $result['email'],
                    'qualification' => $result['qualification'],
                    'imagepath' => $result['imagepath']
                );
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
