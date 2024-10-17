<?php
include('connection.php');

// Fetch all data from the users table
$sql = "SELECT id, carname, vin, plate_number, car_model, car_color, company_name, location FROM users";
$result = mysqli_query($con, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Output JSON response
$output = array(
    'data' => $data
);

echo json_encode($output);
?>
