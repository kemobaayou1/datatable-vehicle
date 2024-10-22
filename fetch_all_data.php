<?php
include('connection.php');

$isFiltered = isset($_POST['filtered']) ? $_POST['filtered'] : false;
$search = isset($_POST['search']) ? $_POST['search'] : '';
$headerFilters = isset($_POST['headerFilters']) ? $_POST['headerFilters'] : [];

$sql = "SELECT * FROM users";

if ($isFiltered) {
    $where_conditions = [];
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($con, $search);
        $where_conditions[] = "(carname LIKE '%$search%' OR vin LIKE '%$search%' OR plate_number LIKE '%$search%' OR car_model LIKE '%$search%' OR car_color LIKE '%$search%' OR company_name LIKE '%$search%' OR location LIKE '%$search%')";
    }

    foreach ($headerFilters as $index => $filter_value) {
        if ($filter_value !== '') {
            $column_name = getColumnName($index);
            $filter_value = mysqli_real_escape_string($con, $filter_value);
            $where_conditions[] = "$column_name = '$filter_value'";
        }
    }

    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(' AND ', $where_conditions);
    }
}

$result = mysqli_query($con, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);

function getColumnName($index) {
    $columns = ['id', 'carname', 'vin', 'plate_number', 'car_model', 'car_color', 'company_name', 'location', 'gps'];
    return $columns[$index] ?? '';
}
?>
