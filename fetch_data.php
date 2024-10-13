<?php
include('connection.php');

// Define columns to be used for ordering and searching
$columns = array(
    0 => 'id',
    1 => 'employeenumber',  // Add employee number column
    2 => 'username',
    3 => 'email',
    4 => 'mobile',
    5 => 'city',
    6 => 'status',
    7 => 'job',
    8 => 'secjob',  // Add this line
    9 => 'type_of_work'  // Add this line
);

// Initialize variables for server-side pagination
$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
$start = isset($_POST['start']) ? $_POST['start'] : 0;
$length = isset($_POST['length']) ? $_POST['length'] : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumn = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Build base SQL query
$sql = "SELECT * FROM users";

// Add search conditions
if (!empty($search)) {
    $sql .= " WHERE ";
    $searchConditions = array();
    foreach ($columns as $key => $column) {
        $searchConditions[] = "$column LIKE '%$search%'";
    }
    $sql .= implode(" OR ", $searchConditions);
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
$result = mysqli_query($con, $sql);

// Prepare data for DataTable
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $sub_array = array();
    $sub_array[] = $row['id'];
    $sub_array[] = $row['employeenumber'];
    
    // Add picture column
    $picturePath = $row['picture_path'] ? $row['picture_path'] : 'path/to/default/image.jpg';
    $sub_array[] = $picturePath; // Just store the path, not the HTML
    
    $sub_array[] = $row['username'];
    $sub_array[] = $row['email'];
    $sub_array[] = $row['mobile'];
    $sub_array[] = $row['city'];
    
    // Create clickable status button 
    if ($row['status'] === "active") {
        $sub_array[] = '<span class="badge bg-success changeStatus" data-id="' . $row['id'] . '" data-status="active">نشط</span>';
    } else {
        $sub_array[] = '<span class="badge bg-danger changeStatus" data-id="' . $row['id'] . '" data-status="inactive">غير نشط</span>';
    }

    $sub_array[] = $row['job']; 
    $sub_array[] = $row['secjob'];
    $sub_array[] = $row['type_of_work'];

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