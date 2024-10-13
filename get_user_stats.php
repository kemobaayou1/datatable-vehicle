<?php
// Include the database connection file
include('connection.php');

// Get selected status
$status = $_POST['status'];

// Query to fetch users based on status
$sql = "SELECT * FROM users WHERE status = '$status'";
$result = $conn->query($sql);

// Display stats in HTML format
if ($result->num_rows > 0) {
    echo "<p>Total " . $status . " users: " . $result->num_rows . "</p>";
    // You can also display user details here if needed
} else {
    echo "<p>No " . $status . " users found.</p>";
}

// Close the database connection (optional but good practice)
$conn->close();
?>