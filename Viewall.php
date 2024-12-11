<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


// Include the database connection file
include 'Phpconnection.php';

// Check if a username search parameter is provided
if (isset($_GET['username'])) {
    $searchUsername = $_GET['username'];

    // SQL query to search for patients based on username
    $sql = "SELECT `pname`, `mob`, `mail`, `gender`, `age`,  `username`, `pass`, `cpass`, `image_path` FROM `patientlogin` WHERE `username` LIKE ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $searchTerm = "%".$searchUsername."%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        if ($result->num_rows > 0) {
            // Fetch all rows and store in $data array
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            $data = ["message" => "0 results"];
        }
        
        $stmt->close();
    } else {
        $data = ["error" => "Error preparing statement: " . $conn->error];
    }
} else {
    // SQL query to fetch all patients
    $sql = "SELECT `pname`, `mob`, `mail`, `gender`, `age`, `username`, `pass`, `cpass`, `image_path` FROM `patientlogin`";
    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        if ($result->num_rows > 0) {
            // Fetch all rows and store in $data array
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            $data = ["message" => "0 results"];
        }
    } else {
        $data = ["error" => "Error executing query: " . $conn->error];
    }
}

$conn->close();

// Set Content-Type header to application/json
header('Content-Type: application/json');
echo json_encode($data);
?>