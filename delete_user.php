<?php 
include('connection.php');

// Function to update IDs after deletion
function updateIdsAfterDeletion($con, $deletedId) {
    $sql = "UPDATE users SET id = id - 1 WHERE id > $deletedId";
    mysqli_query($con, $sql);
}

$user_id = $_POST['id'];
$sql = "DELETE FROM users WHERE id='$user_id'";
$delQuery = mysqli_query($con, $sql);

if($delQuery == true) {
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

?>