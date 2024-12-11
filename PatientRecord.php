<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
include 'Phpconnection.php';

$username = isset($_GET['username']) ? $_GET['username'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$response = array();
if ($username != '' && $start_date != '' && $end_date != '') {
    // Format start date and end date correctly
    $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
    $end_date = date('Y-m-d 23:59:59', strtotime($end_date));

    // Log the parameters
    error_log("Username: $username");
    error_log("Start Date: $start_date");
    error_log("End Date: $end_date");

    $sql = "SELECT * FROM glucoseentry WHERE username = ? AND status = 'completed' AND datetime BETWEEN ? AND ? ORDER BY datetime ASC";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param('sss', $username, $start_date, $end_date);
        
        // Execute statement
        $stmt->execute();
        
        // Get result
        $result = $stmt->get_result();
        
        if ($result) {
            if ($result->num_rows > 0) {
                $data = array();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $response['status'] = 'success';
                $response['data'] = $data;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'No completed entries found for this username within the specified date range';
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
    $response['message'] = 'Invalid input parameters';
}

$conn->close();
echo json_encode($response);
?>