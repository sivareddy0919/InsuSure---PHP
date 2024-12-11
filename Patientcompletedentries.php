<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

$username = isset($_GET['username']) ? $_GET['username'] : '';

$response = array();
if ($username != '') {
    // Prepare statement
    $sql = "SELECT * FROM glucoseentry WHERE username = ? AND status = 'completed'";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param('s', $username);
        
        // Execute statement
        $stmt->execute();
        
        // Get result
        $result = $stmt->get_result();
        
        if ($result) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            // Check if data exists
            if (count($data) > 0) {
                $response['status'] = 'success';
                $response['data'] = $data;
            } else {
                $response['status'] = 'success'; // Still indicate success
                $response['data'] = []; // Return empty data array
                $response['message'] = 'No data exists for this username';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Query execution failed: ' . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Statement preparation failed: ' . $conn->error;
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid username';
}

$conn->close();
echo json_encode($response);
?>
