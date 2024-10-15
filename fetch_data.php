<?php
include('connection.php');

// Define columns to be used for ordering and searching
$columns = array(
    0 => 'id',
    1 => 'picture_path',
    2 => 'carname',
    3 => 'vin',
    4 => 'plate_number',
    5 => 'car_model',
    6 => 'car_color',
    7 => 'company_name',
    8 => 'location'
);

// Initialize variables for server-side pagination
$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
$start = isset($_POST['start']) ? $_POST['start'] : 0;
$length = isset($_POST['length']) ? $_POST['length'] : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumn = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Build base SQL query
$sql = "SELECT * FROM users WHERE 1=1";

// Add search conditions
$where = '';
if (!empty($search)) {
    $where .= " AND (";
    $searchConditions = array();
    foreach ($columns as $key => $column) {
        $searchConditions[] = "$column LIKE '%$search%'";
    }
    $where .= implode(" OR ", $searchConditions);
    $where .= ")";
}

// Add ordering conditions
if ($orderColumn >= 0 && $orderColumn < count($columns)) {
    $sql .= " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
} else {
    $sql .= " ORDER BY id DESC";
}

// Get total number of records (without pagination)
$totalRecords = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users"));

// Get filtered number of records (with search conditions)
$filteredRecordsQuery = mysqli_query($con, $sql);
$filteredRecords = mysqli_num_rows($filteredRecordsQuery);

// Add pagination conditions
$sql .= " LIMIT $start, $length";

// Fetch filtered data (using the SQL with pagination)
$query = mysqli_query($con, $sql);
$count_rows = mysqli_num_rows($query);

// Prepare data for DataTable
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $sub_array = array();
    $sub_array[] = $row['id'];
     
    // Add picture column
    $picturePath = $row['picture_path'] ? $row['picture_path'] : 'path/to/default/image.jpg';
    $sub_array[] = $picturePath; // Just store the path, not the HTML
    
    $sub_array[] = $row['carname'];
    $sub_array[] = $row['vin'];
    $sub_array[] = $row['plate_number'];
    $sub_array[] = $row['car_model'];
    $sub_array[] = $row['car_color'];
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['location'];

    // Add the Edit/Delete/Work Report buttons with Arabic text
    $sub_array[] = '<a href="javascript:void(0);" data-id="' . $row['id'] . '" class="btn btn-info btn-sm editbtn">تعديل</a> ' .
                   '<a href="#!" data-id="' . $row['id'] . '" class="btn btn-danger btn-sm deleteBtn">حذف</a> ' .
                   '<a href="javascript:void(0);" data-id="' . $row['id'] . '" class="btn btn-primary btn-sm workReportBtn">تقرير عمل</a>';

    $data[] = $sub_array;
}

// Output JSON response for DataTable
$output = array(
    'draw' => intval($draw),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
);

echo json_encode($output);
?>
