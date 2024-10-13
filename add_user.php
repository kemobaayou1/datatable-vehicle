<?php 
include('connection.php');

// Function to get the next available ID
function getNextAvailableId($con) {
    $result = mysqli_query($con, "SELECT MAX(id) as max_id FROM users");
    $row = mysqli_fetch_assoc($result);
    return $row['max_id'] + 1;
}

$employeeNumber = $_POST['employeeNumber']; // Add employee number
$picturePath = $_POST['picturePath']; 
$username = $_POST['username'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$city = $_POST['city'];
$job = $_POST['job']; // Add job
$secjob = $_POST['secjob'];  // Add this line
$typeOfWork = $_POST['typeOfWork']; // New field
// Add this line to handle the picture path

// Check if employee number already exists
$checkSql = "SELECT * FROM users WHERE employeenumber = '$employeeNumber'";
$checkResult = mysqli_query($con, $checkSql);

if (mysqli_num_rows($checkResult) > 0) {
    // Employee number already exists
    $data = array(
        'status' => 'false',
        'message' => ' الرقم الوظيفي مستخدم من قبل موظف اخر'
    );
    echo json_encode($data);
    exit;
}

// Get the next available ID
$nextId = getNextAvailableId($con);

$sql = "INSERT INTO `users` (`id`, `employeenumber`, `username`, `email`, `mobile`, `city`, `job`, `secjob`, `type_of_work`, `picture_path`) 
        VALUES ($nextId, '$employeeNumber', '$username', '$email', '$mobile', '$city', '$job', '$secjob', '$typeOfWork', '$picturePath')";

$query = mysqli_query($con, $sql);

$lastId = mysqli_insert_id($con); // Get the ID of the newly inserted row

if($query === true) {
    $data = array(
        'status' => 'true',
        'lastId' => $lastId // Send the last ID back to the front-end
    );

    echo json_encode($data);
} else {
    $data = array(
        'status' => 'false',
        'message' => 'Error adding user'
    );

    echo json_encode($data);
} 

?>