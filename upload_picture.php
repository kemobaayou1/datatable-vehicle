<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeNumber = $_POST['employeeNumber'];
    $uploadDir = 'uploads/';
    $fileExtension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
    $fileName = $employeeNumber . '.' . $fileExtension;
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'success', 'picturePath' => $targetFile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}