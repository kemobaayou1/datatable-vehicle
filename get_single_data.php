<?php 
include('connection.php');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    $sql = "SELECT *, picture_path FROM users WHERE id = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid ID']);
}

$stmt->close();
$con->close();
?>
