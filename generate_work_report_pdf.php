<?php
require_once __DIR__ . '/vendor/autoload.php';
include('connection.php');

$employeeNumber = isset($_GET['employeeNumber']) ? intval($_GET['employeeNumber']) : 0;

if ($employeeNumber <= 0) {
    die('Invalid employee number');
}

// Fetch employee details including picture_path
$sql = "SELECT username, job, picture_path FROM users WHERE employeenumber = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $employeeNumber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$employee = mysqli_fetch_assoc($result);

if (!$employee) {
    die('Employee not found');
}

// Fetch work reports
$sql = "SELECT * FROM work_reports WHERE employeenumber = ? ORDER BY date DESC";
$stmt = mysqli_prepare($con, $sql); // Changed $conn to $con
mysqli_stmt_bind_param($stmt, "i", $employeeNumber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$workReports = [];
while ($row = mysqli_fetch_assoc($result)) {
    $workReports[] = $row;
}


// Create PDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'P',
    'direction' => 'rtl',
    'default_font' => 'aealarabiya',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 16,
    'margin_bottom' => 16,
    'margin_header' => 9,
    'margin_footer' => 9,
]);

$mpdf->SetTitle('Work Report - ' . $employee['username']);

// Add custom Arabic font
$mpdf->fontdata['aealarabiya'] = [
    'R' => 'aealarabiya.ttf',
    'useOTL' => 0xFF,
];

// Logo path
$logoPath = __DIR__ . '/Logo/logo.png';

// User picture path
$userPicturePath = $employee['picture_path'] ? __DIR__ . '/' . $employee['picture_path'] : __DIR__ . '/default_user_picture.png';

// Generate PDF content
$html = '
<html>
<head>
    <style>
        @page {
            margin: 0;
        }
        body { 
            font-family: aealarabiya, sans-serif; 
            margin: 0; 
            padding: 0; 
            position: relative;
        }
        .logo-container {
            position: absolute;
            top: 10mm;  /* Adjusted from 5mm to 15mm */
            right: 15mm;
            width: 30mm;
            height: 30mm;
            z-index: 1000;
        }
        .logo { 
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .user-picture-container {
            position: absolute;
            top: 10mm;  /* Adjusted from 5mm to 15mm */
            left: 15mm;
            width: 30mm;
            height: 30mm;
            z-index: 1000;
        }
        .user-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .content {
            padding: 15mm 15mm 15mm 15mm;  /* Adjusted top padding from 25mm to 15mm */
        }
        .header { 
            text-align: center; 
            margin-bottom: 5mm;
        }
        h3 { 
            margin: 0;
            padding: 5px 0;
            font-size: 24px;
        }
        .subheader {
            font-size: 16px;
            margin: 3px 0;
            font-weight: normal;
        }
        .label {
            font-weight: bold;
            margin-right: 5px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10mm;  /* Increased from 5mm to 10mm for more space after header */
        }
        th { 
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: center; 
        }
        td {
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: right; 
        }
        th { 
            background-color: #6cf06c;
            color: #000000;
            font-weight: bold;
        }
        .footer { 
            text-align: center; 
            font-size: 10pt; 
            margin-top: 5mm;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="' . $logoPath . '" class="logo" alt="Company Logo">
    </div>
    <div class="user-picture-container">
        <img src="' . $userPicturePath . '" class="user-picture" alt="User Picture">
    </div>
    <div class="content">
        <div class="header">
            <h3>تقرير عمل يومي</h3>
            <p class="subheader">
                <span class="label">اسم الموظف:</span> ' . $employee['username'] . ' - 
                <span class="label">رقم الموظف:</span> ' . $employeeNumber . ' 
            </p>
            <p class="subheader">
                <span class="label">الوظيفة:</span> ' . $employee['job'] . ' 
            </p>
        </div>
        <table>
            <tr>
                <th>نسبة الإنجاز</th>
                <th>الوصف</th>
                <th>الموقع</th>
                <th>التاريخ</th>
            </tr>';

foreach ($workReports as $report) {
    $html .= '
            <tr>
                <td>' . $report['percentage_done'] . '%</td>
                <td>' . $report['description'] . '</td>
                <td>' . $report['location'] . '</td>
                <td>' . $report['date'] . '</td>
            </tr>';
}

$html .= '
        </table>
        <div class="footer">
            تم إنشاء هذا التقرير في ' . date('Y-m-d H:i:s') . '
        </div>
    </div>
</body>
</html>';

$mpdf->WriteHTML($html);

// Sanitize the username for the filename
$sanitizedUsername = preg_replace('/[^A-Za-z0-9\p{Arabic}_\-]/u', '_', $employee['username']);
$sanitizedUsername = trim($sanitizedUsername, '_');

if (empty($sanitizedUsername)) {
    $sanitizedUsername = 'User';
}

$outputFilename = 'تقرير عمل الموظف -' . $sanitizedUsername . '-' . $employeeNumber . '.pdf';

// Encode the filename for URL
$encodedFilename = rawurlencode($outputFilename);

// Set headers for file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $encodedFilename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$mpdf->Output($outputFilename, 'I');