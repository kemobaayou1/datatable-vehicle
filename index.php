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
  <!-- Add this to the <head> section -->
  <style>
    .gps-available {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 4px;
    }
    .gps-unavailable {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 4px;
    }
  </style>
  
  <!-- Add Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Add custom styles for the icons -->
  <style>
    /* ... existing styles ... */
    
    .action-icon {
      cursor: pointer;
      margin: 0 5px;
      font-size: 1.2em;
    }
    .edit-icon {
      color: #17a2b8;
    }
    .delete-icon {
      color: #dc3545;
    }
  </style>
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
          <a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-success btn-sm">اضافة سيارة جديدة</a>
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
                <th>اسم السيارة</th>
                <th>رقم الهيكل (VIN)</th>
                <th>رقم اللوحة</th>
                <th>موديل السيارة</th>
                <th>لون السيارة</th>
                <th>اسم الشركة</th>
                <th>الموقع</th>
                <th>GPS</th>
                <th>Options</th>
              </thead>
              <tbody>
              </tbody>
            </table>
            <div id="totalCount" style="text-align: center; font-size: 1.25rem; font-weight: bold; color: #333; margin-top: 20px; padding: 10px; background-color: #f0f0f0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);"></div>
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
      var table = $('#example').DataTable({
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
        "columnDefs": [
          {
            "bSortable": false,
            "aTargets": [9] // Disable sorting for the Options column
          },
          {
            "render": function(data, type, row) {
              return '<i class="fas fa-edit action-icon edit-icon" data-id="' + row[0] + '"></i>' +
                     '<i class="fas fa-trash-alt action-icon delete-icon" data-id="' + row[0] + '"></i>';
            },
            "targets": 9 // Options column
          },
          {
            "targets": 8, // GPS column
            "render": function(data, type, row) {
                if (type === 'display') {
                    return data; // The data already includes the span with the appropriate class
                }
                return data.replace(/<[^>]+>/g, ''); // Strip HTML for sorting/filtering
            }
          },
          {
            "targets": [8, 9], // Disable filtering for GPS and Options columns
            "searchable": false // Disable searching for these columns
          }
        ],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                if (column.index() !== 8 && column.index() !== 9) { // Exclude GPS and Options columns
                    var select = $('<select><option value="">All</option></select>')
                        .appendTo($(column.header()))
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? val : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="'+d+'">'+d+'</option>')
                    });
                }
            });
        },
        "fnDrawCallback": function() {
          var filteredCount = this.api().rows({ filter: 'applied' }).count();
          var totalCount = this.api().data().count();
          $('#totalCount').html('المجموع المحدد:' + filteredCount );
        }
      });
    });
    $(document).on('submit', '#addUser', function(e) {
      e.preventDefault();
      var car_model = $('#addCarModelField').val();
      var carname = $('#addUserField').val();
      var vin = $('#addVinField').val();
      var plate_number = $('#addPlateNumberField').val();
      var car_color = $('#addCarColorField').val();
      var company_name = $('#addCompanyNameField').val();
      var location = $('#addLocationField').val();
      var gps = $('#addGpsField').val();
      
      if (car_model != '' && carname != '' && vin != '' && plate_number != '' && car_color != '' && company_name != '' && location != '' && gps != '') {
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
            gps: gps
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
      } else {
        alert('Fill all the required fields');
      }
    });
    $(document).on('submit', '#updateUser', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      
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
                         '<a href="#!" data-id="' + data.id + '" class="btn btn-danger btn-sm deleteBtn">حذف</a> ';
            var row = table.row("[id='" + $('#trid').val() + "']");
            var currentData = row.data();
            currentData[1] = $('#nameField').val();
            currentData[2] = $('#vinField').val();
            currentData[3] = $('#plateNumberField').val();
            currentData[4] = $('#carModelField').val();
            currentData[5] = $('#carColorField').val();
            currentData[6] = $('#companyNameField').val();
            currentData[7] = data.location;
            currentData[8] = $('#gpsField').val();
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
    $('#example').on('click', '.edit-icon', function(event) {
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
          $('#nameField').val(json.carname);
          $('#vinField').val(json.vin);
          $('#plateNumberField').val(json.plate_number);
          $('#carModelField').val(json.car_model);
          $('#carColorField').val(json.car_color);
          $('#companyNameField').val(json.company_name);
          $('#locationField').val(json.location);
          $('#gpsField').val(json.gps);
          $('#id').val(id);
          $('#trid').val(trid);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error("AJAX error:", textStatus, errorThrown);
          alert('Error fetching user data');
        }
      });
    });

    $(document).on('click', '.delete-icon', function(event) {
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
        // Show a loading indicator
        $('#exportToExcel').text('Loading...').prop('disabled', true);
        
        // Get the DataTable instance
        var table = $('#example').DataTable();
        
        // Check if there are any filters applied
        var filteredData = table.rows({ filter: 'applied' }).data();
        
        // Prepare the data to send to the server
        var exportData = [];
        if (filteredData.length > 0) {
            // If there are filtered rows, prepare them for export
            for (var i = 0; i < filteredData.length; i++) {
                // Exclude the last column (edit/delete buttons)
                exportData.push([
                    filteredData[i][0], // ID
                    filteredData[i][1], // Car Name
                    filteredData[i][2], // VIN
                    filteredData[i][3], // Plate Number
                    filteredData[i][4], // Car Model
                    filteredData[i][5], // Car Color
                    filteredData[i][6], // Company Name
                    filteredData[i][7], // Location
                    filteredData[i][8]  // GPS (will clean this up below)
                ]);
            }
        } else {
            // If no rows are filtered, fetch all data
            $.ajax({
                url: 'fetch_all_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && Array.isArray(response.data)) {
                        // Prepare the data for export
                        response.data.forEach(function(row) {
                            exportData.push([
                                row.id,
                                row.carname,
                                row.vin,
                                row.plate_number,
                                row.car_model,
                                row.car_color,
                                row.company_name,
                                row.location,
                                row.gps // Clean this up below
                            ]);
                        });
                    }
                    // Call the function to export the data
                    exportToExcel(exportData);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching all data:', error);
                    alert('Error fetching data for export.');
                }
            });
            return; // Exit the function to prevent further execution
        }
        
        // Clean up GPS data to remove HTML tags
        exportData = exportData.map(function(row) {
            row[8] = row[8].replace(/<[^>]+>/g, ''); // Remove HTML tags from GPS
            return row;
        });

        // Call the function to export the data
        exportToExcel(exportData);
    });

    // Function to handle exporting to Excel
    function exportToExcel(data) {
        // Define headers for the Excel file
        var headers = ['ID', 'اسم السيارة', 'رقم الهيكل (VIN)', 'رقم اللوحة', 'موديل السيارة', 'لون السيارة', 'اسم الشركة', 'الموقع', 'GPS'];
        
        // Convert the data to a format suitable for Excel
        var worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]);
        var workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Data");
        
        // Generate a file name
        var fileName = 'exported_data.xlsx';
        
        // Save the file
        XLSX.writeFile(workbook, fileName);
        
        // Reset the button text and state
        $('#exportToExcel').text('Export to Excel').prop('disabled', false);
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
              <label for="gpsField" class="col-md-3 form-label">GPS</label>
              <div class="col-md-9">
                <select class="form-control" id="gpsField" name="gps">
                  <option value="يوجد">يوجد</option>
                  <option value="لا يوجد">لا يوجد</option>
                </select>
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
              <label for="addGpsField" class="col-md-3 form-label">GPS</label>
              <div class="col-md-9">
                <select class="form-control" id="addGpsField" name="gps">
                  <option value="يوجد">يوجد</option>
                  <option value="لا يوجد">لا يوجد</option>
                </select>
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