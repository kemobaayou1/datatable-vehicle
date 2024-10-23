<?php 
session_start(); // Add this at the top
include('connection.php');

// Get the car details before deletion for the log message
$user_id = $_POST['id'];
$getCarSql = "SELECT carname, vin, plate_number FROM users WHERE id = ?";
$stmt = $con->prepare($getCarSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$carData = $result->fetch_assoc();
$carname = $carData['carname'];
$vin = $carData['vin'];
$plate_number = $carData['plate_number'];

// Function to update IDs after deletion
function updateIdsAfterDeletion($con, $deletedId) {
    $sql = "UPDATE users SET id = id - 1 WHERE id > $deletedId";
    mysqli_query($con, $sql);
}

$sql = "DELETE FROM users WHERE id=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$delQuery = $stmt->execute();

if($delQuery == true) {
    // Log the delete event with more details
    $eventSql = "INSERT INTO event_logs (event_type, message, user_id) VALUES (?, ?, ?)";
    $eventStmt = $con->prepare($eventSql);
    $eventType = 'delete';
    $message = "Car deleted: '$carname' (VIN: $vin, Plate: $plate_number)";
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $eventStmt->bind_param("ssi", $eventType, $message, $userId);
    $eventStmt->execute();

    // Update IDs after successful deletion
    updateIdsAfterDeletion($con, $user_id);
    
    $data = array(
        'status' => 'success',
    );
    echo json_encode($data);
} else {
    $data = array(
        'status' => 'failed',
    );
    echo json_encode($data);
} 

$stmt->close();
$con->close();
?>
