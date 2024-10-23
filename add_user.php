<?php 
session_start(); // Add this at the top
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
$gps = $_POST['gps']; // Add this line

// Get the next available ID
$nextId = getNextAvailableId($con);

$sql = "INSERT INTO `users` (`carname`, `vin`, `plate_number`, `car_model`, `car_color`, `company_name`, `location`, `gps`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssssssss", $carname, $vin, $plate_number, $car_model, $car_color, $company_name, $location, $gps);

if($stmt->execute()) {
    // Log the event with detailed information
    $eventSql = "INSERT INTO event_logs (event_type, message, user_id) VALUES (?, ?, ?)";
    $eventStmt = $con->prepare($eventSql);
    $eventType = 'add';
    $message = "New car added (ID: {$con->insert_id}):\n" .
               "- Car Name: $carname\n" .
               "- VIN: $vin\n" .
               "- Plate Number: $plate_number\n" .
               "- Car Model: $car_model\n" .
               "- Car Color: $car_color\n" .
               "- Company Name: $company_name\n" .
               "- Location: $location\n" .
               "- GPS: $gps";
    
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $eventStmt->bind_param("ssi", $eventType, $message, $userId);
    $eventStmt->execute();
    
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
