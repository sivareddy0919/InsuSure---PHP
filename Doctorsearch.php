<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection and base image URL
include 'Phpconnection.php';

// Initialize response array
$response = array();

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if 'username' query parameter exists
    if (isset($_GET['username'])) {
        $searchText = $_GET['username'];

        // Query to search for patients based on username and select all columns
        $sql = "SELECT * FROM patientlogin WHERE `username` LIKE ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $response['status'] = "error";
            $response['message'] = "Database error: " . $conn->error;
        } else {
            $searchText = '%' . $searchText . '%';
            $stmt->bind_param('s', $searchText);

            if (!$stmt->execute()) {
                $response['status'] = "error";
                $response['message'] = "Execution error: " . $stmt->error;
            } else {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $patients = $result->fetch_all(MYSQLI_ASSOC);

                    // Append the base image URL to the image path
                    foreach ($patients as &$patient) {
                        if (!empty($patient['image_path'])) {
                            $patient['image_path'] = $base_image_url . '/' . $patient['image_path'];
                        }
                    }

                    $response['status'] = "success";
                    $response['message'] = "Search successful";
                    $response['patients'] = $patients;
                } else {
                    $response['status'] = "error";
                    $response['message'] = "No patients found";
                }
            }

            $stmt->close();
        }
    } else {
        $response['status'] = "error";
        $response['message'] = "No username parameter provided";
    }
}

// Close the database connection
$conn->close();

// Output the response as JSON
echo json_encode($response);
?>
