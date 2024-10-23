<?php 
session_start(); // Add this at the top
include('connection.php');

// Prevent any output before our JSON response
ob_start();

// Get user information
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Get username from auth_users table
$username = null;
if ($userId) {
    $userStmt = $con->prepare("SELECT username FROM auth_users WHERE id = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userRow = $userResult->fetch_assoc()) {
        $username = $userRow['username'];
    }
    $userStmt->close();
}

$carname = $_POST['carname'];
$vin = $_POST['vin'];
$plate_number = $_POST['plate_number'];
$car_model = $_POST['car_model'];
$car_color = $_POST['car_color'];
$company_name = $_POST['company_name'];
$id = $_POST['id'];
$location = $_POST['location'];
$gps = $_POST['gps'];

// First, get the old values BEFORE updating
$oldValuesSql = "SELECT * FROM users WHERE id = ?";
$oldStmt = $con->prepare($oldValuesSql);
$oldStmt->bind_param("i", $id);
$oldStmt->execute();
$oldResult = $oldStmt->get_result();
$oldData = $oldResult->fetch_assoc();

// Now perform the update
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
    // Compare old and new values to create detailed message
    $changes = [];
    
    if ($oldData['carname'] !== $carname) {
        $changes[] = "Car Name changed from '{$oldData['carname']}' to '$carname'";
    }
    if ($oldData['vin'] !== $vin) {
        $changes[] = "VIN Number changed from '{$oldData['vin']}' to '$vin'";
    }
    if ($oldData['plate_number'] !== $plate_number) {
        $changes[] = "Plate Number changed from '{$oldData['plate_number']}' to '$plate_number'";
    }
    if ($oldData['car_model'] !== $car_model) {
        $changes[] = "Car Model changed from '{$oldData['car_model']}' to '$car_model'";
    }
    if ($oldData['car_color'] !== $car_color) {
        $changes[] = "Car Color changed from '{$oldData['car_color']}' to '$car_color'";
    }
    if ($oldData['company_name'] !== $company_name) {
        $changes[] = "Company Name changed from '{$oldData['company_name']}' to '$company_name'";
    }
    if ($oldData['location'] !== $location) {
        $changes[] = "Location changed from '{$oldData['location']}' to '$location'";
    }
    if ($oldData['gps'] !== $gps) {
        $changes[] = "GPS Status changed from '{$oldData['gps']}' to '$gps'";
    }

    if (empty($changes)) {
        $message = "No changes made to car (ID: $id)";
    } else {
        $message = "Updated car (ID: $id):\n" . implode("\n", $changes);
    }

    // Log the update event
    $eventSql = "INSERT INTO event_logs (event_type, message, user_id) VALUES (?, ?, ?)";
    $eventStmt = $con->prepare($eventSql);
    $eventType = 'update';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $eventStmt->bind_param("ssi", $eventType, $message, $userId);
    $eventStmt->execute();

    $data = [
        'status' => 'true',
        'location' => $location,
        'gps' => $gps,
        'id' => $id,
        'message' => $message
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
