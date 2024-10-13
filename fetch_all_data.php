<?php
include('connection.php');

// Fetch all data from the users table, including type_of_work
$sql = "SELECT id, employeenumber, username, email, mobile, city, status, job, secjob, type_of_work FROM users";
$result = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Log the data
error_log(print_r($data, true));

// Output JSON response
$output = array(
    'data' => $data
);

echo json_encode($output);
?>