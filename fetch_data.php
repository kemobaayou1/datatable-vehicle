<?php
include('connection.php');

// Define columns to be used for ordering and searching
$columns = array(
    0 => 'id',
    1 => 'carname',
    2 => 'vin',
    3 => 'plate_number',
    4 => 'car_model',
    5 => 'car_color',
    6 => 'company_name',
    7 => 'location',
    8 => 'gps'
);

// Initialize variables for server-side pagination
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Build base SQL query
$sql = "SELECT * FROM users WHERE 1=1";

// Add individual column filtering
$individualFilters = [];
foreach ($columns as $i => $column) {
    if (isset($_POST['columns'][$i]['search']['value']) && $_POST['columns'][$i]['search']['value'] !== '') {
        $individualFilters[] = $column . " LIKE '%" . mysqli_real_escape_string($con, $_POST['columns'][$i]['search']['value']) . "%'";
    }
}

if (!empty($individualFilters)) {
    $sql .= " AND " . implode(" AND ", $individualFilters);
}

// Add global search condition
if (!empty($search)) {
    $sql .= " AND (";
    $searchConditions = array();
    foreach ($columns as $column) {
        $searchConditions[] = $column . " LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
    }
    $sql .= implode(" OR ", $searchConditions) . ")";
}

// Get total number of records (without filtering)
$totalRecords = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users"));

// Get number of filtered records
$filteredRecordsQuery = mysqli_query($con, $sql);
$filteredRecords = mysqli_num_rows($filteredRecordsQuery);

// Add ordering
$sql .= " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;

// Add pagination
$sql .= " LIMIT $start, $length";

// Fetch filtered data
$query = mysqli_query($con, $sql);
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $sub_array = array();
    foreach ($columns as $column) {
        $value = $row[$column];
        if ($column === 'gps') {
            $gpsClass = $value === 'يوجد' ? 'gps-available' : 'gps-unavailable';
            $value = '<span class="' . $gpsClass . '">' . $value . '</span>';
        }
        $sub_array[] = $value;
    }
    $sub_array[] = '<a href="javascript:void(0);" data-id="' . $row['id'] . '" class="btn btn-info btn-sm editbtn">تعديل</a> ' .
                   '<a href="#!" data-id="' . $row['id'] . '" class="btn btn-danger btn-sm deleteBtn">حذف</a>';
    $data[] = $sub_array;
}

// Prepare the response
$response = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

echo json_encode($response);
?>
