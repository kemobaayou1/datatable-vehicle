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
    /* Add these styles for the new layout */
    body {
        display: flex;
        min-height: 100vh;
        margin: 0;
    }
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        height: 100vh;
        background-color: #343a40; /* Darker background color */
        color: #fff; /* Light text color for contrast */
        padding: 20px;
        transition: width 0.3s;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .sidebar.collapsed {
        width: 80px;
    }
    .content {
        flex: 1;
        margin-left: 250px; /* Same as sidebar width */
        transition: margin-left 0.3s;
        padding: 20px;
        overflow-y: auto;
    }
    .content.sidebar-collapsed {
        margin-left: 80px; /* Same as collapsed sidebar width */
    }
    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #f8f9fa; /* Light color for better visibility */
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .nav-link:hover {
        background-color: #495057; /* Slightly lighter on hover for feedback */
    }
    .nav-link.active {
        background-color: #007bff; /* Highlight active link */
    }
    .nav-link i {
        margin-right: 10px;
    }
    .nav-link span {
        display: inline-block;
    }
    .sidebar.collapsed .nav-link span {
        display: none;
    }
    .logout-btn {
        margin-top: auto;
        padding: 10px;
        color: #dc3545;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
    }
    .logout-btn:hover {
        background-color: #495057; /* Slightly lighter on hover */
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
  <div class="sidebar collapsed" id="sidebar">
    <div class="logo">
        <!-- Add your logo here -->
        <img src="Logo/khglogo.png" alt="Logo" style="max-width: 100%; height: auto;">
    </div>
    <nav>
        <a href="#" class="nav-link active" data-tab="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="#" class="nav-link" data-tab="history">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>
    </nav>
    <a href="#" class="nav-link logout-btn" id="logoutBtn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
  </div>
  <div class="content sidebar-collapsed" id="content">
    <div id="dashboard-tab" class="tab-content active">
        <!-- Your existing dashboard content goes here -->
        <div class="container-fluid">
            <div class="row">
              <div class="container">
                <div class="btnAdd">
                  <button data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-rounded btn-add">
                    <i class="fas fa-plus"></i> اضافة سيارة جديدة
                  </button>
                  <button id="exportToExcel" class="btn btn-rounded btn-export">
                    <i class="fas fa-file-excel"></i> حمل ملف اكسل
                  </button>
                  <button type="button" class="btn btn-rounded btn-import" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                    <i class="fas fa-file-import"></i> اضافة عمال من ملف csv
                  </button>
                </div>
                <div class="row">
                  <div class="col-md-12"></div>
                  
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
                </div>
              </div>
            </div>
        </div>
    </div>
    <div id="history-tab" class="tab-content" style="display: none;">
        <div class="history-content">
            <h2>تاريخ التحركات</h2>
            <div class="event-log-container">
                <!-- Remove this div containing the date filter -->
                <!-- <div class="event-log-filters">
                    <input type="date" id="dateFilter" class="form-control">
                </div> -->
                <div class="event-log-box" id="eventLogBox">
                    <!-- Logs will be loaded here -->
                </div>
                <div class="event-log-pagination">
                    <button id="loadMoreLogs" class="btn btn-primary">Load More</button>
                </div>
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
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        initComplete: function () {
            var api = this.api();
            
            // Function to get unique values from all data
            function getUniqueValues(columnIndex) {
                return api.column(columnIndex)
                    .data()
                    .flatten() // Flatten the data array
                    .unique() // Get unique values
                    .sort() // Sort the values
                    .toArray(); // Convert to array
            }
            
            api.columns().every(function () {
                var column = this;
                if (column.index() !== 8 && column.index() !== 9) { // Exclude GPS and Options columns
                    // Create select element
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
                    
                    // Get all unique values for this column from server
                    $.ajax({
                        url: 'get_column_values.php',
                        type: 'POST',
                        data: {
                            column: column.index()
                        },
                        success: function(response) {
                            // Add options to select
                            response.values.forEach(function(value) {
                                if (value) { // Only add non-empty values
                                    select.append('<option value="' + value + '">' + value + '</option>');
                                }
                            });
                        }
                    });
                }
            });
        },
        "fnDrawCallback": function(oSettings) {
          var api = this.api();
          var filteredCount = api.page.info().recordsDisplay;
          var totalCount = api.page.info().recordsTotal;
          $('#totalCount').html('المجموع المحدد: ' + filteredCount + ' / إجمالي السجلات: ' + totalCount);
        }
      });
      // Add this new code for sidebar functionality
      $('.nav-link').on('click', function(e) {
        e.preventDefault();
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        var tabId = $(this).data('tab');
        $('.tab-content').hide();
        $('#' + tabId + '-tab').show();
      });

      // Add this for sidebar collapse functionality
      $('#sidebar').on('mouseenter', function() {
        $(this).removeClass('collapsed');
        $('#content').removeClass('sidebar-collapsed');
      }).on('mouseleave', function() {
        $(this).addClass('collapsed');
        $('#content').addClass('sidebar-collapsed');
      });

      // Add this new code for logout functionality
      $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        {
          window.location.href = 'logout.php';
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
    
    // Replace the existing exportToExcel function with this updated version
    $('#exportToExcel').on('click', function() {
        console.log('Export button clicked');
        $('#exportToExcel').text('Loading...').prop('disabled', true);
        
        var table = $('#example').DataTable();
        
        // Get the current search value and column-specific filters
        var search = table.search();
        var columnFilters = table.columns().search().toArray();
        
        // Get header filter values
        var headerFilters = [];
        table.columns().every(function(index) {
            var column = this;
            var select = $(column.header()).find('select');
            if (select.length > 0) {
                headerFilters.push(select.val() || '');
            } else {
                headerFilters.push('');
            }
        });

        console.log('Search:', search);
        console.log('Column filters:', columnFilters);
        console.log('Header filters:', headerFilters);

        // Make an AJAX call to fetch all filtered data
        $.ajax({
            url: 'fetch_all_data.php',
            type: 'POST',
            data: {
                filtered: true,
                search: search,
                columns: columnFilters,
                headerFilters: headerFilters
            },
            success: function(response) {
                console.log('Server response:', response);
                var json = JSON.parse(response);
                if (json.data && json.data.length > 0) {
                    prepareAndExportData(json.data);
                } else {
                    alert('No data to export.');
                }
                $('#exportToExcel').text('Export to Excel').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data for export:', error);
                alert('Error fetching data for export: ' + error);
                $('#exportToExcel').text('Export to Excel').prop('disabled', false);
            }
        });
    });

    function prepareAndExportData(rawData) {
        console.log('Preparing data for export. Raw data length:', rawData.length);
        var exportData = rawData.map(function(row) {
            return [
                row.id,
                row.carname,
                row.vin,
                row.plate_number,
                row.car_model,
                row.car_color,
                row.company_name,
                row.location,
                row.gps
            ];
        });
        console.log('Export data prepared. Length:', exportData.length);
        exportToExcel(exportData);
    }

    // The exportToExcel function remains the same
    function exportToExcel(data) {
        console.log('Exporting to Excel. Data length:', data.length);
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
        console.log('Excel file created and downloaded');
    }

    // Event Log functionality
    $(document).ready(function() {
        let currentPage = 1;
        let loading = false;
        let hasMore = true;

        function loadEventLogs(page = 1, append = false) {
            $.ajax({
                url: 'get_event_logs.php',
                type: 'GET',
                data: {
                    page: page
                },
                success: function(response) {
                    const eventLogBox = $('#eventLogBox');
                    let html = '';
                    
                    response.logs.forEach(log => {
                        // Check if username exists and is not null/empty
                        const displayUsername = log.username ? log.username : 'Unknown';
                        
                        html += `
                            <div class="event-log-item">
                                <div class="event-icon ${log.event_type}">
                                    ${getEventIcon(log.event_type)}
                                </div>
                                <div class="event-details">
                                    <div class="event-timestamp">${formatTimestamp(log.timestamp)}</div>
                                    <div class="event-user">User: ${displayUsername}</div>
                                    <div class="event-message">${log.message}</div>
                                </div>
                            </div>
                        `;
                    });

                    if (append) {
                        eventLogBox.append(html);
                    } else {
                        eventLogBox.html(html);
                    }

                    // Show/hide load more button based on whether there are more logs
                    $('#loadMoreLogs').toggle(response.hasMore);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading event logs:', error);
                }
            });
        }

        function getEventIcon(eventType) {
            switch(eventType) {
                case 'add': return '<i class="fas fa-plus"></i>';
                case 'update': return '<i class="fas fa-edit"></i>';
                case 'delete': return '<i class="fas fa-trash"></i>';
                default: return '<i class="fas fa-info"></i>';
            }
        }

        function formatTimestamp(timestamp) {
            return new Date(timestamp).toLocaleString();
        }

        // Initial load
        loadEventLogs();

        // Load more
        $('#loadMoreLogs').on('click', function() {
            currentPage++;
            loadEventLogs(currentPage, true);
        });
    });

  </script>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">تحديث معلومات ا السيارة</h5>
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
              <label for="addCarColorField" class="col-md-3 form-label">لن السيارة</label>
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
        
        // Show loading state
        var submitButton = $(this).find('button[type="submit"]');
        var originalText = submitButton.text();
        submitButton.prop('disabled', true).text('Uploading...');
        
        $.ajax({
            url: 'import_users.php',
            type: 'POST',
            data: formData,
            dataType: 'json', // Explicitly expect JSON response
            success: function(response) {
                console.log("Response received:", response);
                
                if (response.success) {
                    $('#importUsersModal').modal('hide');
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error details:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                // Try to parse the response if it exists
                let errorMessage = 'An error occurred while importing the file';
                try {
                    if (xhr.responseText) {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    errorMessage += '\nServer response: ' + xhr.responseText;
                }
                
                alert(errorMessage);
            },
            complete: function() {
                // Reset button state
                submitButton.prop('disabled', false).text(originalText);
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
