<?php
header('Content-Type: application/json');

require_once 'vendor/autoload.php';
use League\Csv\Reader;
use League\Csv\Writer;

// Database connections
$conn = new mysqli("localhost", "root", "", "datatables_crud");
$id_tracker_conn = new mysqli("localhost", "root", "", "id_tracker");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

if ($id_tracker_conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection to id_tracker failed: " . $id_tracker_conn->connect_error]));
}

// Check for memory limits (adjust as needed)
if (ini_get('memory_limit') < '128M') {
    ini_set('memory_limit', '128M');
}

if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
    $allowedFileTypes = ['text/csv'];

    // Get file MIME type using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE); 
    $fileType = finfo_file($finfo, $_FILES["fileToUpload"]["tmp_name"]); 
    finfo_close($finfo); 

    if (in_array($fileType, $allowedFileTypes)) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            $reader = Reader::createFromPath($targetFile, 'r');
            $reader->setHeaderOffset(0);

            $records = $reader->getRecords();

            $conn->begin_transaction();
            $id_tracker_conn->begin_transaction();

            try {
                // Get the maximum ID from the users table
                $sql = "SELECT MAX(id) as max_id FROM users";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $maxId = $row['max_id'];

                $startId = $maxId + 1;

                $insertedRows = 0;
                foreach ($records as $record) {
                    $carname = $record['carname'];
                    $vin = $record['vin'];
                    $plate_number = $record['plate_number'];
                    $car_model = $record['car_model'];
                    $car_color = $record['car_color'];
                    $company_name = $record['company_name'];
                    $location = $record['location'];

                    $sql = "INSERT INTO users (id, carname, vin, plate_number, car_model, car_color, company_name, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssssss", $startId, $carname, $vin, $plate_number, $car_model, $car_color, $company_name, $location);
                    $stmt->execute();

                    if ($stmt->error) {
                        throw new Exception("Error inserting record: " . $stmt->error);
                    } else {
                        $insertedRows++;
                        $startId++;
                    }
                }

                $sql = "UPDATE id_tracker SET next_id = ?";
                $stmt = $id_tracker_conn->prepare($sql);
                $stmt->bind_param("i", $startId);
                $stmt->execute();

                if ($stmt->error) {
                    throw new Exception("Error updating id_tracker: " . $stmt->error);
                }

                $conn->commit();
                $id_tracker_conn->commit();

                unlink($targetFile);
                echo json_encode(['success' => true, 'message' => "تمت اضافة   " . $insertedRows . "  عامل بنجاح"]);
            } catch (Exception $e) {
                $conn->rollback();
                $id_tracker_conn->rollback();
                echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Error uploading file."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid file type. Please upload a CSV file."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Please select a file to upload."]);
}

$conn->close();
$id_tracker_conn->close();
?>
