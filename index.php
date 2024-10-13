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
  <link rel="stylesheet" type="text/css" href="css/datatables-1.10.25.min.css" />
  <title>EABR AL-ALAM</title>
  <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body>
  <div class="container-fluid">
    <div class="dashboard-header">
        <h2 class="dashboard-title">WELCOME TO ABER-AL-ALAM DASHBOARD</h2>
        <div class="header-separator"></div>
        <p class="dashboard-subtitle">حصر عمالة عبر العالم</p>
        
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
                <th>الرقم الوظيفي </th>
                <th>الصورة</th> <!-- New column for picture -->
                <th>الاسم</th>
                <th>ايميل</th>
                <th>الهاتف</th>
                <th>المدينة</th>
                <th>الحالة</th>
                <th>الوظيفة</th>
                <th>الوظيفة الثانية</th>
                <th>تصنيف العامل</th>
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
            "aTargets": [2, 11] // Make picture and options columns unsortable
          },
          {
            "render": function(data, type, row) {
              return '<img src="' + data + '" alt="Employee Picture" style="width: 50px; height: 50px; object-fit: cover;">';
            },
            "targets": 2 // Apply to the picture column
          },
          {
            "render": function(data, type, row) {
              if (data === "active") {
                return '<span class="badge bg-success changeStatus" data-id="' + row[0] + '" data-status="active">نشط</span>';
              } else if (data === "inactive") {
                return '<span class="badge bg-danger changeStatus" data-id="' + row[0] + '" data-status="inactive">غير نشط</span>';
              }
              return data;
            },
            "targets": 7 // Apply to the status column
          },
          {
            "render": function(data, type, row) {
              return '<a href="javascript:void(0);" data-id="' + row[0] + '" class="btn btn-info btn-sm editbtn">تعديل</a> ' +
                     '<a href="#!" data-id="' + row[0] + '" class="btn btn-danger btn-sm deleteBtn">حذف</a> ' +
                     '<a href="javascript:void(0);" data-employeenumber="' + row[1] + '" class="btn btn-primary btn-sm workReportBtn">تقرير عمل</a>';
            },
            "targets": 11 // Apply to the options column
          }
        ],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
      });
    });
    $(document).on('submit', '#addUser', function(e) {
      e.preventDefault();
      var employeeNumber = $('#addEmployeeNumberField').val();
      var pictureFile = $('#addPictureField')[0].files[0];
      var city = $('#addCityField').val();
      var username = $('#addUserField').val();
      var mobile = $('#addMobileField').val();
      var email = $('#addEmailField').val();
      var job = $('#addJobField').val();
      var secjob = $('#addSecJobField').val();
      var typeOfWork = $('#addTypeOfWorkField').val(); // New field
      
      if (employeeNumber != '' && city != '' && username != '' && mobile != '' && email != '' && job != '' && secjob != '' && typeOfWork != '') {
        uploadPicture(pictureFile, employeeNumber, function(err, picturePath) {
          if (err) {
            alert('Error uploading picture: ' + err);
            return;
          }

          $.ajax({
            url: "add_user.php",
            type: "post",
            data: {
              employeeNumber: employeeNumber,
              picturePath: picturePath,
              city: city,
              username: username,
              mobile: mobile,
              email: email,
              job: job,
              secjob: secjob,
              typeOfWork: typeOfWork // Include new field
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
      var employeeNumber = $('#employeeNumberField').val();
      var city = $('#cityField').val();
      var username = $('#nameField').val();
      var mobile = $('#mobileField').val();
      var email = $('#emailField').val();
      var job = $('#jobField').val();
      var secjob = $('#secJobField').val();
      var trid = $('#trid').val();
      var id = $('#id').val();
      
      if (employeeNumber != '' && city != '' && username != '' && mobile != '' && email != '' && job != '' && secjob != '') {
        var formData = new FormData(this);
        formData.append('employeeNumber', employeeNumber);
        formData.append('city', city);
        formData.append('username', username);
        formData.append('mobile', mobile);
        formData.append('email', email);
        formData.append('job', job);
        formData.append('secjob', secjob);
        formData.append('id', id);
        
        $.ajax({
          url: "update_user.php",
          type: "post",
          data: formData,
          processData: false,
          contentType: false,
          success: function(data) {
            var json = JSON.parse(data);
            var status = json.status;
            if (status == 'true') {
              table = $('#example').DataTable();
              var button = '<a href="javascript:void(0);" data-id="' + id + '" class="btn btn-info btn-sm editbtn">Edit</a>  <a href="#!"  data-id="' + id + '"  class="btn btn-danger btn-sm deleteBtn">Delete</a>';
              var row = table.row("[id='" + trid + "']");
              var currentData = row.data();
              currentData[1] = employeeNumber;
              // Add a timestamp to force image reload
              var timestamp = new Date().getTime();
              currentData[2] = '<img src="' + json.picture_path + '?t=' + timestamp + '" alt="Employee Picture" style="width: 50px; height: 50px; object-fit: cover;">';
              currentData[3] = username;
              currentData[4] = email;
              currentData[5] = mobile;
              currentData[6] = city;
              currentData[7] = json.status_value;
              currentData[8] = job;
              currentData[9] = secjob;
              currentData[11] = button;
              row.data(currentData).draw();
              $('#exampleModal').modal('hide');
            } else {
              alert('Failed to update user: ' + (json.message || 'Unknown error'));
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            alert('Error updating user: ' + textStatus);
          }
        });
      } else {
        alert('Please fill all the required fields');
      }
    });
    $(document).on('click', '.changeStatus', function() {
  var id = $(this).data('id');
  var status = $(this).data('status'); 
  var button = $(this);
  var table = $('#example').DataTable();
  var currentPage = table.page();

  // Confirmation prompt
  if (confirm("هل أنت متأكد أنك تريد تغيير حالة هذا المستخدم؟")) {
    $.ajax({
      url: 'update_user_status.php',
      type: 'POST',
      data: {
        id: id,
        status: status 
      },
      success: function(response) {
        var data = JSON.parse(response);
        if (data.status == "success") {
          button.data('status', data.newStatus); 
          button.text(data.newStatus); 
          table.page(currentPage).draw(false);
        } else {
          alert('Error updating status!');
        }
      },
      error: function() {
        alert('Error updating status!');
      }
    });
  } 
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
          $('#employeeNumberField').val(json.employeenumber);
          $('#nameField').val(json.username);
          $('#emailField').val(json.email);
          $('#mobileField').val(json.mobile);
          $('#cityField').val(json.city);
          $('#jobField').val(json.job);
          $('#secJobField').val(json.secjob);
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

          var headers = ['Id', 'رقم الموظف', 'الاسم', 'ايميل', 'الهاتف', 'المدينة', 'الحالة', 'الوظيفة', 'الوظيفة الثانية', 'نوع العمل'];
          
          // Process the data to extract status text and remove the Options column
          var exportData = response.data.map(function(row) {
            return [
              row.id,
              row.employeenumber,
              row.username,
              row.email,
              row.mobile,
              row.city,
              row.status,
              row.job,
              row.secjob,
              row.type_of_work
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

    function uploadPicture(file, employeeNumber, callback) {
      var formData = new FormData();
      formData.append('picture', file);
      formData.append('employeeNumber', employeeNumber);

      $.ajax({
        url: 'upload_picture.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          var json = JSON.parse(response);
          if (json.status === 'success') {
            callback(null, json.picturePath);
          } else {
            callback(json.message || 'Failed to upload picture');
          }
        },
        error: function() {
          callback('Error uploading picture');
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
              <label for="employeeNumberField" class="col-md-3 form-label">الرقم الوظيفي </label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="employeeNumberField" name="employeeNumber">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="nameField" class="col-md-3 form-label">الاسم</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="nameField" name="name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="emailField" class="col-md-3 form-label">ايميل</label>
              <div class="col-md-9">
                <input type="email" class="form-control" id="emailField" name="email">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="mobileField" class="col-md-3 form-label">الهاتف</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="mobileField" name="mobile">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="cityField" class="col-md-3 form-label">المدينة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="cityField" name="City">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="jobField" class="col-md-3 form-label">الوظيفة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="jobField" name="job">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="secJobField" class="col-md-3 form-label">الوظيفة الثانية</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="secJobField" name="secjob">
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
              <label for="addEmployeeNumberField" class="col-md-3 form-label">الرقم الوظيفي </label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addEmployeeNumberField" name="employeeNumber">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addUserField" class="col-md-3 form-label">الاسم</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addUserField" name="name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addEmailField" class="col-md-3 form-label">ايميل</label>
              <div class="col-md-9">
                <input type="email" class="form-control" id="addEmailField" name="email">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addMobileField" class="col-md-3 form-label">الهاتف</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addMobileField" name="mobile">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addCityField" class="col-md-3 form-label">المدينة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addCityField" name="City">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addJobField" class="col-md-3 form-label">الوظيفة</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addJobField" name="job">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addSecJobField" class="col-md-3 form-label">الوظيفة الثانية</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addSecJobField" name="secjob">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addTypeOfWorkField" class="col-md-3 form-label">تصنيف العامل</label>
              <div class="col-md-9">
                <select class="form-select" id="addTypeOfWorkField" name="typeOfWork">
                  <option value="فني درجة اولى">فني درجة اولى</option>
                  <option value="فني">فني</option>
                  <option value="عامل">عامل</option>
                  <option value="فورمان">فورمان</option>
                </select>
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