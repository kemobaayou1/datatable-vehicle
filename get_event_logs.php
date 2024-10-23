<?php
include('connection.php');

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Number of logs per page
$offset = ($page - 1) * $limit;

$date = isset($_GET['date']) ? $_GET['date'] : '';

$where = [];
$params = [];
$types = '';

if (!empty($date)) {
    $where[] = "DATE(timestamp) = ?";
    $params[] = $date;
    $types .= 's';
}

// Join with auth_users table to get username
$sql = "SELECT el.*, au.username 
        FROM event_logs el 
        LEFT JOIN auth_users au ON el.user_id = au.id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY el.timestamp DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$logs = [];

while ($row = $result->fetch_assoc()) {
    $logs[] = [
        'id' => $row['id'],
        'event_type' => $row['event_type'],
        'message' => $row['message'],
        'user_id' => $row['user_id'],
        'username' => $row['username'],
        'timestamp' => $row['timestamp']
    ];
}

echo json_encode([
    'logs' => $logs,
    'hasMore' => count($logs) === $limit
]);
