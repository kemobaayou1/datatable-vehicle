<?php 
include('connection.php');

$employeeNumber = $_POST['employeeNumber'];
$username = $_POST['username'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$city = $_POST['city'];
$job = $_POST['job'];
$secjob = $_POST['secjob'];
$id = $_POST['id'];
$currentPicturePath = $_POST['currentPicturePath'];

// Handle picture upload
$picturePath = $currentPicturePath; // Default to current picture path
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
    $uploadDir = 'uploads/';
    $fileExtension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
    $fileName = $employeeNumber . '_' . time() . '.' . $fileExtension; // Add timestamp to ensure uniqueness
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
        $picturePath = $targetFile;
        // Delete the old picture if it exists and is different from the default
        if ($currentPicturePath != '' && $currentPicturePath != 'path/to/default/image.jpg' && file_exists($currentPicturePath)) {
            unlink($currentPicturePath);
        }
    } else {
        echo json_encode(['status' => 'false', 'message' => 'Failed to upload new picture: ' . error_get_last()['message']]);
        exit;
    }
}

$sql = "UPDATE `users` SET 
        `employeenumber`='$employeeNumber', 
        `username`='$username', 
        `email`='$email', 
        `mobile`='$mobile', 
        `city`='$city', 
        `job`='$job', 
        `secjob`='$secjob',
        `picture_path`='$picturePath'
        WHERE id='$id'";

$query = mysqli_query($con, $sql);

if($query === true) {
    $sql = "SELECT status, type_of_work, picture_path FROM users WHERE id='$id'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $timestamp = time();
    $data = array(
        'status' => 'true',
        'status_value' => $row['status'],
        'type_of_work' => $row['type_of_work'],
        'picture_path' => $row['picture_path'] . '?t=' . $timestamp,
        'id' => $id
    );
    echo json_encode($data);
} else {
    $data = array(
        'status' => 'false',
        'message' => 'MySQL Error: ' . mysqli_error($con)
    );
    echo json_encode($data);
} 

?>