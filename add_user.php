<?php 
include('connection.php');

// Function to get the next available ID
function getNextAvailableId($con) {
    $result = mysqli_query($con, "SELECT MAX(id) as max_id FROM users");
    $row = mysqli_fetch_assoc($result);
    return $row['max_id'] + 1;
}

$carname = $_POST['carname'];
$vin = $_POST['vin'];
$plate_number = $_POST['plate_number'];
$car_model = $_POST['car_model'];
$car_color = $_POST['car_color'];
$company_name = $_POST['company_name'];
$location = $_POST['location'];
$picturePath = $_POST['picture_path'];

// Get the next available ID
$nextId = getNextAvailableId($con);

$sql = "INSERT INTO `users` (`carname`, `vin`, `plate_number`, `car_model`, `car_color`, `company_name`, `location`, `picture_path`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssssssss", $carname, $vin, $plate_number, $car_model, $car_color, $company_name, $location, $picturePath);

if($stmt->execute()) {
    $data = array(
        'status' => 'true',
        'id' => $con->insert_id
    );
    echo json_encode($data);
} else {
    $data = array(
        'status' => 'false',
        'message' => 'Error: ' . $stmt->error
    );
    echo json_encode($data);
}

$stmt->close();
$con->close();
?>
