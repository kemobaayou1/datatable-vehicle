<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include('connection.php');

$employeeNumber = isset($_POST['employeeNumber']) ? intval($_POST['employeeNumber']) : 0;
$date = isset($_POST['date']) ? mysqli_real_escape_string($con, $_POST['date']) : '';
$location = isset($_POST['location']) ? mysqli_real_escape_string($con, $_POST['location']) : '';
$description = isset($_POST['description']) ? mysqli_real_escape_string($con, $_POST['description']) : '';
$percentageDone = isset($_POST['percentage_done']) ? intval($_POST['percentage_done']) : 0;

if ($employeeNumber <= 0 || empty($date) || empty($location) || empty($description) || $percentageDone < 0 || $percentageDone > 100) {
    echo json_encode(array("status" => "false", "error" => "Invalid input data"));
    exit;
}

$sql = "INSERT INTO `work_reports` (`employeenumber`, `date`, `location`, `description`, `percentage_done`) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($con, $sql);
if ($stmt === false) {
    echo json_encode(array("status" => "false", "error" => "Failed to prepare statement"));
    exit;
}

mysqli_stmt_bind_param($stmt, "isssi", $employeeNumber, $date, $location, $description, $percentageDone);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(array("status" => "true", "message" => "تم اضافة التقرير بنجاح"));
} else {
    echo json_encode(array("status" => "false", "error" => mysqli_error($con)));
}

mysqli_stmt_close($stmt);
?>