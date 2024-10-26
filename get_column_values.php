<?php
include('connection.php');

// Map column index to column name
$columnMap = [
    0 => 'id',
    1 => 'carname',
    2 => 'vin',
    3 => 'plate_number',
    4 => 'car_model',
    5 => 'car_color',
    6 => 'company_name',
    7 => 'location'
];

$columnIndex = isset($_POST['column']) ? (int)$_POST['column'] : 0;
$columnName = isset($columnMap[$columnIndex]) ? $columnMap[$columnIndex] : null;

if ($columnName) {
    // Get distinct values for the column
    $sql = "SELECT DISTINCT $columnName FROM users WHERE $columnName IS NOT NULL AND $columnName != '' ORDER BY $columnName";
    $result = $con->query($sql);
    
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row[$columnName];
    }
    
    header('Content-Type: application/json');
    echo json_encode(['values' => $values]);
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid column']);
}

$con->close();
?>