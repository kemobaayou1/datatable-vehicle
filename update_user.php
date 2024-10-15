<?php 
include('connection.php');

// Prevent any output before our JSON response
ob_start();

$carname = $_POST['carname'];
$vin = $_POST['vin'];
$plate_number = $_POST['plate_number'];
$car_model = $_POST['car_model'];
$car_color = $_POST['car_color'];
$company_name = $_POST['company_name'];
$id = $_POST['id'];
$currentPicturePath = $_POST['currentPicturePath'];
$location = $_POST['location'];

// Handle picture upload
$picturePath = $currentPicturePath; // Default to current picture path
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
    $uploadDir = 'uploads/';
    $fileExtension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
        $picturePath = $targetFile;
        // Delete the old picture if it exists and is different from the default
        if ($currentPicturePath != '' && $currentPicturePath != 'path/to/default/image.jpg' && file_exists($currentPicturePath)) {
            unlink($currentPicturePath);
        }
    } else {
        $data = ['status' => 'false', 'message' => 'Failed to upload new picture: ' . error_get_last()['message']];
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

$sql = "UPDATE `users` SET 
        `carname`=?, 
        `vin`=?, 
        `plate_number`=?, 
        `car_model`=?, 
        `car_color`=?, 
        `company_name`=?,
        `location`=?,
        `picture_path`=?
        WHERE id=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssssssssi", $carname, $vin, $plate_number, $car_model, $car_color, $company_name, $location, $picturePath, $id);

if($stmt->execute()) {
    $sql = "SELECT picture_path, location FROM users WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $timestamp = time();
    $data = [
        'status' => 'true',
        'location' => $row['location'],
        'picture_path' => $row['picture_path'] . '?t=' . $timestamp,
        'id' => $id
    ];
} else {
    $data = [
        'status' => 'false',
        'message' => 'MySQL Error: ' . $stmt->error
    ];
}

// Clear the output buffer and send the JSON response
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$con->close();
?>
