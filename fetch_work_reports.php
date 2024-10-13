<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include('connection.php');

$employeeNumber = isset($_POST['employeeNumber']) ? intval($_POST['employeeNumber']) : 0;

if ($employeeNumber <= 0) {
    echo json_encode(['error' => 'Invalid employee number', 'data' => []]);
    exit;
}

// Fetch work reports
$sql = "SELECT * FROM work_reports WHERE employeenumber = ? ORDER BY date DESC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $employeeNumber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(['error' => 'Database query failed', 'data' => []]);
    exit;
}

// Prepare data for DataTable
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $row['date'],
        $row['location'],
        $row['description'],
        $row['percentage_done']
    ];
}

// Output JSON response for DataTable
$output = array(
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => count($data),
    "recordsFiltered" => count($data),
    "data" => $data
);

echo json_encode($output);
?>