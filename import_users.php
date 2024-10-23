<?php
// Prevent any output before headers
ob_start();

// Set headers
header('Content-Type: application/json; charset=utf-8');

try {
    include('connection.php');
    include('includes/logger.php');

    if (!isset($_FILES['fileToUpload'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['fileToUpload'];
    
    // Log file upload details
    error_log("File upload details: " . print_r($file, true));
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Check if it's a CSV file
    if ($file_extension !== 'csv') {
        throw new Exception('Only CSV files are allowed');
    }

    // Set internal character encoding to UTF-8
    mysqli_set_charset($con, "utf8mb4");

    // Open the uploaded file with UTF-8 encoding
    $handle = fopen($file['tmp_name'], 'r');
    if ($handle === false) {
        throw new Exception('Failed to open file');
    }

    // Remove BOM if present
    fgets($handle, 4) === "\xEF\xBB\xBF" ? rewind($handle) : rewind($handle);

    // Force UTF-8 encoding for the file reading
    stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');

    // Read the header row
    $header = fgetcsv($handle);
    if ($header === false) {
        throw new Exception('Empty file');
    }

    // Clean header values
    $header = array_map(function($value) {
        return trim(str_replace("\xEF\xBB\xBF", '', $value)); // Remove BOM and trim
    }, $header);

    // Expected columns
    $expected_columns = ['carname', 'vin', 'plate_number', 'car_model', 'car_color', 'company_name', 'location', 'gps'];
    
    // Verify header matches expected columns
    if (count(array_diff($expected_columns, $header)) > 0) {
        throw new Exception('Invalid CSV format. Required columns: ' . implode(', ', $expected_columns));
    }

    // Begin transaction
    mysqli_begin_transaction($con);

    $success_count = 0; // Initialize this variable
    $row_number = 1;

    // Prepare the insert statement
    $stmt = $con->prepare("INSERT INTO users (carname, vin, plate_number, car_model, car_color, company_name, location, gps) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Read and insert data rows
    while (($data = fgetcsv($handle)) !== false) {
        $row_number++;
        
        // Skip empty rows
        if (empty(array_filter($data))) {
            continue;
        }

        // Validate row data
        if (count($data) !== count($expected_columns)) {
            throw new Exception("Row $row_number has invalid number of columns");
        }

        // Clean data values
        $data = array_map('trim', $data);

        // Bind parameters and execute
        $stmt->bind_param("ssssssss", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting row $row_number: " . $stmt->error);
        }

        $success_count++;
    }

    // Commit transaction
    mysqli_commit($con);
    
    fclose($handle);
    $stmt->close();

    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => "Successfully imported $success_count records"
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Import error: " . $e->getMessage());
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Make sure the script ends here
exit();
?>
