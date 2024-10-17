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
$location = $_POST['location'];
$gps = $_POST['gps']; // Add this line

// Remove picture-related code
$sql = "UPDATE `users` SET 
        `carname`=?, 
        `vin`=?, 
        `plate_number`=?, 
        `car_model`=?, 
        `car_color`=?, 
        `company_name`=?,
        `location`=?,
        `gps`=?
        WHERE id=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssssssssi", $carname, $vin, $plate_number, $car_model, $car_color, $company_name, $location, $gps, $id);

if($stmt->execute()) {
    $sql = "SELECT location, gps FROM users WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $data = [
        'status' => 'true',
        'location' => $row['location'],
        'gps' => $row['gps'],
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
