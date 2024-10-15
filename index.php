<?php
session_start();
error_log("Session data in index.php: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    error_log("Access denied. User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . ", Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not set'));
    header("Location: login.php");
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS --> 
  <link href="css/bootstrap5.0.1.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/datatables-1.10.25.min.css" />
  <title>EABR AL-ALAM</title>
  <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body>
  <div class="container-fluid">
    <div class="dashboard-header">
        <h2 class="dashboard-title">WELCOME TO KAHLIFA HOLDING CO</h2>
        <div class="header-separator"></div>
        <p class="dashboard-subtitle">حصر سيارات خليفة القابضة</p>
        
    </div>
    <div class="row">
      <div class="container">
        <div class="btnAdd">
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
          <a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-success btn-sm">اضافة عامل</a>
          <button id="exportToExcel" class="btn btn-info btn-sm">حمل ملف اكسل </button>
          <!-- New button and form for file upload -->
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importUsersModal">csvاضافة عمال من ملف</button>
        </div>
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <table id="example" class="table">
              <thead>
                <th>No.</th>
                <th>الصورة</th>
                <th>اسم السيارة</th>
                <th>رقم الهيكل (VIN)</th>
                <th>رقم اللوحة</th>
                <th>موديل السيارة</th>
                <th>لون السيارة</th>
                <th>اسم الشركة</th>
                <th>الموقع</th>
                <th>Options</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="col-md-2"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript; choose one of the two! -->
  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script type="text/javascript" src="js/dt-1.10.25datatables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
  -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('#example').DataTable({
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
          $(nRow).attr('id', aData[0]);
        },
        'serverSide': 'true',
        'processing': 'true',
        'paging': 'true',
        'order': [],
        'ajax': {
          'url': 'fetch_data.php',
          'type': 'post',
        },
        "aoColumnDefs": [
          {
            "bSortable": false,
            "aTargets": [1, 9]
          },
          {
            "render": function(data, type, row) {
              return '<img src="' + data + '" alt="Employee Picture" style="width: 50px; height: 50px; object-fit: cover;">';
            },
            "targets": 1
          },
          {
            "render": function(data, type, row) {
              return '<a href="javascript:void(0);" data-id="' + row[0] + '" class="btn btn-info btn-sm editbtn">تعديل</a> ' +
                     '<a href="#!" data-id="' + row[0] + '" class="btn btn-danger btn-sm deleteBtn">حذف</a> ' +
                     '<a href="javascript:void(0);" data-id="' + row[0] + '" class="btn btn-primary btn-sm workReportBtn">تقرير عمل</a>';
            },
            "targets": 9
          }
        ],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
      });
    });
    $(document).on('submit', '#addUser', function(e) {
      e.preventDefault();
      var pictureFile = $('#addPictureField')[0].files[0];
      var car_model = $('#addCarModelField').val();
      var carname = $('#addUserField').val();
      var vin = $('#addVinField').val();
      var plate_number = $('#addPlateNumberField').val();
      var car_color = $('#addCarColorField').val();
      var company_name = $('#addCompanyNameField').val();
      var location = $('#addLocationField').val();
      
      if (car_model != '' && carname != '' && vin != '' && plate_number != '' && car_color != '' && company_name != '' && location != '') {
        uploadPicture(pictureFile, function(err, picturePath) {
          if (err) {
            alert('Error uploading picture: ' + err);
            return;
          }
          
          $.ajax({
            url: "add_user.php",
            type: "post",
            data: {
              carname: carname,
              vin: vin,
              plate_number: plate_number,
              car_model: car_model,
              car_color: car_color,
              company_name: company_name,
              location: location,
              picture_path: picturePath
            },
            success: function(data) {
              var json = JSON.parse(data);
              var status = json.status;
              if (status == 'true') {
                mytable = $('#example').DataTable();
                mytable.draw();
                $('#addUserModal').modal('hide');
              } else {
                alert(json.message || 'Failed to add user');
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert('Error adding user: ' + textStatus);
            }
          });
        });
      } else {
        alert('Fill all the required fields');
      }
    });
    $(document).on('submit', '#updateUser', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      formData.append('currentPicturePath', $('#currentPicturePath').val());
      
      $.ajax({
        url: "update_user.php",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json', // Expect JSON response
        success: function(data) {
          if (data.status == 'true') {
            table = $('#example').DataTable();
            var button = '<a href="javascript:void(0);" data-id="' + data.id + '" class="btn btn-info btn-sm editbtn">تعديل</a> ' +
                         '<a href="#!" data-id="' + data.id + '" class="btn btn-danger btn-sm deleteBtn">حذف</a> ' +
                         '<a href="javascript:void(0);" data-id="' + data.id + '" class="btn btn-primary btn-sm workReportBtn">تقرير عمل</a>';
            var row = table.row("[id='" + $('#trid').val() + "']");
            var currentData = row.data();
            currentData[1] = data.picture_path; // Update picture path
            currentData[2] = $('#nameField').val();
            currentData[3] = $('#vinField').val();
            currentData[4] = $('#plateNumberField').val();
            currentData[5] = $('#carModelField').val();
            currentData[6] = $('#carColorField').val();
            currentData[7] = $('#companyNameField').val();
            currentData[8] = data.location;
            currentData[9] = button;
            row.data(currentData).draw();
            $('#exampleModal').modal('hide');
          } else {
            alert('Failed to update user: ' + (data.message || 'Unknown error'));
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('AJAX error:', textStatus, errorThrown);
          alert('Error updating user: ' + textStatus);
        }
      });
    });
    $('#example').on('click', '.editbtn', function(event) {
      var table = $('#example').DataTable();
      var trid = $(this).closest('tr').attr('id');
      var id = $(this).data('id');
      $('#exampleModal').modal('show');
      
      $.ajax({
        url: "get_single_data.php",
        data: {
          id: id
        },
        type: 'post',
        success: function(data) {
          var json = JSON.parse(data);
          console.log("Received data:", json); // Add this line for debugging
          $('#nameField').val(json.carname);
          $('#vinField').val(json.vin);
          $('#plateNumberField').val(json.plate_number);
          $('#carModelField').val(json.car_model);
          $('#carColorField').val(json.car_color);
          $('#companyNameField').val(json.company_name);
          $('#locationField').val(json.location);
          $('#id').val(id);
          $('#trid').val(trid);
          
          // Clear the file input
          $('#pictureField').val('');
          
          // Set the current picture and its path
          if (json.picture_path) {
            $('#currentPicture').attr('src', json.picture_path).show();
            $('#currentPicturePath').val(json.picture_path);
          } else {
            $('#currentPicture').attr('src', '').hide();
            $('#currentPicturePath').val('');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error("AJAX error:", textStatus, errorThrown);
          alert('Error fetching user data');
        }
      });
    });

    $(document).on('click', '.deleteBtn', function(event) {
      var table = $('#example').DataTable();
      event.preventDefault();
      var id = $(this).data('id');
      if (confirm("هل أنت متأكد أنك تريد حذف هذا المستخدم؟")) {
        $.ajax({
          url: "delete_user.php",
          data: {
            id: id
          },
          type: "post",
          success: function(data) {
            var json = JSON.parse(data);
            status = json.status;
            if (status == 'success') {
              //table.fnDeleteRow( table.$('#' + id)[0] );
              //$("#example tbody").find(id).remove();
              //table.row($(this).closest("tr")) .remove();
              $("#" + id).closest('tr').remove();
            } else {
              alert('Failed');
              return;
            }
          }
        });
      } else {
        return null;
      }
    })
    
    $('#exportToExcel').on('click', function() {
      var table = $('#example').DataTable();
      
      // Show a loading indicator
      $('#exportToExcel').text('Loading...').prop('disabled', true);
      
      // Request all data from the server
      $.ajax({
        url: 'fetch_all_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          console.log('Data received:', response);
          
          if (!response.data || !Array.isArray(response.data)) {
            console.error('Invalid data received from server');
            alert('Error: Invalid data received from server');
            $('#exportToExcel').text('Export to Excel').prop('disabled', false);
            return;
          }

          var headers = ['Id', 'اسم السيارة', 'رقم الهيكل (VIN)', 'رقم اللوحة', 'موديل السيارة', 'لون السيارة', 'اسم الشركة', 'الموقع'];
          
          // Process the data to extract status text and remove the Options column
          var exportData = response.data.map(function(row) {
            return [
              row.id,
              row.carname,
              row.vin,
              row.plate_number,
              row.car_model,
              row.car_color,
              row.company_name,
              row.location
            ];
          });
          
          try {
            var ws = XLSX.utils.aoa_to_sheet([headers, ...exportData]);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Users");
            
            var wbout = XLSX.write(wb, {bookType:'xlsx', type:'binary'});
            function s2ab(s) {
              var buf = new ArrayBuffer(s.length);
              var view = new Uint8Array(buf);
              for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
              return buf;
            }
            
            var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
            var url = URL.createObjectURL(blob);
            
            var a = document.createElement("a");
            a.href = url;
            a.download = "users_data.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
          } catch (error) {
            console.error('Error generating Excel file:', error);
            alert('Error generating Excel file. Please check the console for details.');
          }

          $('#exportToExcel').text('Export to Excel').prop('disabled', false);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('AJAX error:', textStatus, errorThrown);
          alert('Error fetching data from server. Please check the console for details.');
          $('#exportToExcel').text('Export to Excel').prop('disabled', false);
        }
      });
    });

    // Add this new event listener for the Work Report button
    $(document).on('click', '.workReportBtn', function() {
      var employeeNumber = $(this).data('employeenumber');
      console.log('Work Report button clicked. Employee number:', employeeNumber);
      if (!employeeNumber || employeeNumber === 'undefined') {
        console.error('Invalid employee number:', employeeNumber);
        alert('Error: Invalid employee number');
        return;
      }
      $('#workReportModal').modal('show');
      $('#workReportEmployeeNumber').val(employeeNumber);
      loadWorkReports(employeeNumber);
    });

    function loadWorkReports(employeeNumber) {
      console.log('Loading work reports for employee number:', employeeNumber);
      if ($.fn.DataTable.isDataTable('#workReportTable')) {
        $('#workReportTable').DataTable().destroy();
      }
      $('#workReportTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
          "url": "fetch_work_reports.php",
          "type": "POST",
          "data": function(d) {
            d.employeeNumber = employeeNumber;
            console.log('Sending data to server:', d);
            return d;
          },
          "dataSrc": function(json) {
            console.log("Received data from server:", json);
            if (json.error) {
              console.error("Server returned an error:", json.error);
              alert('Error: ' + json.error);
              return [];
            }
            return json.data || [];
          },
          "error": function(xhr, error, thrown) {
            console.error('DataTables AJAX error:', error, thrown);
            alert('Error loading work reports. Please check the console for details.');
          }
        },
        "columns": [
          { "data": 0, "title": "Date" },
          { "data": 1, "title": "Location" },
          { "data": 2, "title": "Description" },
          { "data": 3, "title": "Percentage Done" }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10,
        "lengthChange": false,
        "searching": false,
        "info": false,
        "language": {
          "emptyTable": "No work reports available"
        }
      });
    }

    // Handle Add Work Report button click
    $(document).on('click', '#addWorkReportBtn', function() {
      var employeeNumber = $('#workReportEmployeeNumber').val();
      $('#addWorkReportModal').modal('show');
      console.log('Add Work Report button clicked. Employee number:', employeeNumber);
    });

    // Add Work Report Form Submission
    $(document).on('submit', '#addWorkReportForm', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      console.log('Form data being sent:', formData);
      $.ajax({
        url: 'add_work_report.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          console.log('Server response:', response);
          if (response.status === 'true') {
            alert('تم اضافة التقرير بنجاح');
            $('#addWorkReportModal').modal('hide');
            var employeeNumber = $('#workReportEmployeeNumber').val();
            loadWorkReports(employeeNumber);
          } else {
            alert('Failed to add work report: ' + (response.error || 'Unknown error'));
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX error:', xhr.responseText);
          alert('Error submitting work report: ' + error);
        }
      });
    });

    // Add this event listener for the PDF download button
    $(document).on('click', '#downloadPdfBtn', function() {
      var employeeNumber = $('#workReportEmployeeNumber').val();
      window.open('generate_work_report_pdf.php?employeeNumber=' + employeeNumber, '_blank');
    });

    function uploadPicture(file, callback) {
      if (!file) {
        callback(null, ''); // No file selected, return empty path
        return;
      }

      var formData = new FormData();
      formData.append('picture', file);

      $.ajax({
        url: 'upload_picture.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          try {
            var json = JSON.parse(response);
            if (json.status === 'success') {
              callback(null, json.picturePath);
            } else {
              callback(json.message || 'Failed to upload picture');
            }
          } catch (error) {
            console.error('Error parsing JSON:', error);
            callback('Error parsing server response');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('AJAX error:', textStatus, errorThrown);
          callback('Error uploading picture: ' + textStatus);
        }
      });
    }

  </script>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">تحديث معلومات العامل</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="updateUser">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="trid" id="trid" value="">
            <div class="mb-3 row">
              <label for="nameField" class="col-md-3 form-label">اسم السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="nameField" name="carname">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="vinField" class="col-md-3 form-label">رقم الهيكل (VIN)</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="vinField" name="vin">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="plateNumberField" class="col-md-3 form-label">رقم اللوحة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="plateNumberField" name="plate_number">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="carModelField" class="col-md-3 form-label">موديل السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="carModelField" name="car_model">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="carColorField" class="col-md-3 form-label">لون السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="carColorField" name="car_color">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="companyNameField" class="col-md-3 form-label">اسم الشركة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="companyNameField" name="company_name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="locationField" class="col-md-3 form-label">الموقع</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="locationField" name="location">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="pictureField" class="col-md-3 form-label">صورة العامل</label>
              <div class="col-md-9">
                <input type="file" class="form-control" id="pictureField" name="picture" accept="image/*">
                <img id="currentPicture" src="" alt="Current Picture" style="max-width: 100px; max-height: 100px; margin-top: 10px;">
                <input type="hidden" id="currentPicturePath" name="currentPicturePath">
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">تحديث</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Add user Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">اضف معلومات العامل</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUser" action="">
            <div class="mb-3 row">
              <label for="addUserField" class="col-md-3 form-label">اسم السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addUserField" name="carname">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addVinField" class="col-md-3 form-label">رقم الهيكل (VIN)</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addVinField" name="vin">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addPlateNumberField" class="col-md-3 form-label">رقم اللوحة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addPlateNumberField" name="plate_number">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addCarModelField" class="col-md-3 form-label">موديل السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addCarModelField" name="car_model">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addCarColorField" class="col-md-3 form-label">لون السيارة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addCarColorField" name="car_color">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addCompanyNameField" class="col-md-3 form-label">اسم الشركة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addCompanyNameField" name="company_name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addLocationField" class="col-md-3 form-label">الموقع</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addLocationField" name="location">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addPictureField" class="col-md-3 form-label">صورة العامل</label>
              <div class="col-md-9">
                <input type="file" class="form-control" id="addPictureField" name="picture" accept="image/*">
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal for adding users from a file -->
  <div class="modal fade" id="importUsersModal" tabindex="-1" aria-labelledby="importUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importUsersModalLabel">اضف عمال من ملف</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="importUsersForm" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="fileToUpload" class="form-label">اختر ملف (Excel أو CSV):</label>
              <input type="file" class="form-control" id="fileToUpload" name="fileToUpload">
            </div>
            <button type="submit" class="btn btn-primary">اضف</button>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Work Report Modal -->
  <div class="modal fade" id="workReportModal" tabindex="-1" aria-labelledby="workReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="workReportModalLabel">تقرير عمل</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <button id="addWorkReportBtn" class="btn btn-primary mb-3">اضف تقرير عمل</button>
          <button id="downloadPdfBtn" class="btn btn-success mb-3 ms-2">تحميل ملف PDF</button>
          <table id="workReportTable" class="table table-striped w-100">
            <thead>
              <tr>
                <th>التاريخ</th>
                <th>الموقع</th>
                <th>الوصف</th>
                <th>النسبة المكتملة</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Work Report Modal -->
  <div class="modal fade" id="addWorkReportModal" tabindex="-1" aria-labelledby="addWorkReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addWorkReportModalLabel">اضف تقرير عمل</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addWorkReportForm">
            <input type="hidden" id="workReportEmployeeNumber" name="employeeNumber">
            <div class="mb-3">
              <label for="date" class="form-label">التاريخ</label>
              <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
              <label for="location" class="form-label">الموقع</label>
              <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">الوصف</label>
              <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
              <label for="percentage_done" class="form-label">النسبة المكتملة</label>
              <input type="number" class="form-control" id="percentage_done" name="percentage_done" min="0" max="100" required>
            </div>
            <button type="submit" class="btn btn-primary">اضف</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    $('#importUsersForm').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      $.ajax({
        url: 'import_users.php',
        type: 'POST',
        data: formData,
        success: function(data) {
          $('#importUsersModal').modal('hide');
          alert(data.message); // Use browser's alert function
          if (data.success) {
            location.reload(); // Optionally refresh the page or update the user list
          }
        },
        error: function(xhr, status, error) {
          alert('An error occurred: ' + error);
        },
        cache: false,
        contentType: false,
        processData: false
      });
    });
  });
  </script>

</body>

</html>