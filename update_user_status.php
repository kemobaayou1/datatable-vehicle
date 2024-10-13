<?php
include('connection.php');

$id = $_POST['id'];
$currentStatus = $_POST['status']; 

// Sanitize input
$id = mysqli_real_escape_string($con, $id);
$currentStatus = mysqli_real_escape_string($con, $currentStatus);

// Toggle the status
$newStatus = $currentStatus == 'active' ? 'inactive' : 'active';

// Update the status in the database
$sql = "UPDATE users SET status = '$newStatus' WHERE id = $id";
$result = mysqli_query($con, $sql);

if ($result) {
    $response = array(
        'status' => 'success',
        'newStatus' => $newStatus,
        'newStatusText' => $newStatus == 'active' ? 'نشط' : 'غير نشط'
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Error updating status.'
    );
}

echo json_encode($response);
?>