<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


// Include the database connection file
include 'Phpconnection.php';

// Check if the POST request contains the necessary data and file
if (isset($_POST['pname']) && isset($_POST['mob']) && isset($_POST['mail']) && isset($_POST['gender']) && isset($_POST['age']) && isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['cpass']) && isset($_FILES['image_path'])) {
    // Collecting the data from the POST request
    $pname = $_POST['pname'];
    $mob = $_POST['mob'];
    $mail = $_POST['mail'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $username = $_POST['username'];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // Validating passwords match
    if ($pass !== $cpass) {
        echo json_encode(array('status' => 'error', 'message' => 'Passwords do not match!'));
        exit;
    }

    // File upload handling
    $target_dir = "uploads/";
    $filename = time() . '_' . basename($_FILES["image_path"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["image_path"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(array('status' => 'error', 'message' => 'File is not an image.'));
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, file already exists.'));
        $uploadOk = 0;
    }

    // Check file size (5MB maximum)
    if ($_FILES["image_path"]["size"] > 5000000) {
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, your file is too large.'));
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.'));
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, your file was not uploaded.'));
    } else {
        // Attempt to upload file
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            // Preparing the SQL statement with the uploaded file path
            $stmt = $conn->prepare("INSERT INTO `patientlogin` (`pname`, `mob`, `mail`, `gender`, `age`, `username`, `pass`, `cpass`, `image_path`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Check if the statement was prepared successfully
            if ($stmt === false) {
                echo json_encode(array('status' => 'error', 'message' => 'Prepare failed: ' . $conn->error));
                exit;
            }

            $stmt->bind_param("sssssssss", $pname, $mob, $mail, $gender, $age, $username, $pass, $cpass, $target_file);

            // Executing the statement
            if ($stmt->execute()) {
                echo json_encode(array('status' => 'success', 'message' => 'New record created successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $stmt->error));
            }
            // Closing the statement
            $stmt->close();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error uploading your file.'));
        }
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Required data not provided!'));
}

// Closing the connection
if ($conn) {
    $conn->close();
}
?>
