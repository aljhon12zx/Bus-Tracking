<?php
include("connect_db.php");

// Fetch account data from the 'driver' table
$query = "SELECT driver_id, driver_name, phone_number, bus_number, account_status FROM driver";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Rotation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
         /* Styling the notification icon */
         .notification {
            position: relative;
            display: inline-block;
            cursor: pointer;
            font-size: small;
            margin-right: 20px;
            margin-top: 5px;
        }
        .notification .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            padding: 3px 8px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 8px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bus Tracking System Using GPS with Data Analytics</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
             <!-- Notification Icon -->
    <div class="notification" style="cursor: pointer;">
        <span class="badge"></span> <!-- Notification count will be added here -->
        <i class="fas fa-bell fa-2x"></i>
    </div>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="account_management.php">Account Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="status.php">Status</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bars"></i> Menu
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="profile.php">Profile</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="mt-5">ACCOUNT MANAGEMENT</h1>
    <!-- Search Bar -->
    <div class="search-container text-right">
        <input type="number" id="search-input" class="form-control" placeholder="Search by Driver ID" style="width: 300px; display: inline-block;">
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">Personnel Rotation Schedule</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Driver ID</th>
                                <th>Driver Name</th>
                                <th>Bus Number</th>
                                <th>Shift</th>
                                <th>Route</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rotationData">
                            <!-- Data will be injected here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Updating Personnel Rotation -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Personnel Rotation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <!-- Shift Field with Day and Night Options Only -->
                    <div class="form-group">
                        <label for="shift">Shift</label>
                        <select class="form-control" id="shift" required>
                            <option value="Day">Day</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bus_route">Route</label>
                        <input type="text" class="form-control" id="bus_route" placeholder="Enter route" required>
                    </div>
                    <div class="form-group">
                        <label for="origin_latitude">Origin Latitude</label>
                        <input type="text" class="form-control" id="origin_latitude" placeholder="Enter origin latitude" required>
                    </div>
                    <div class="form-group">
                        <label for="origin_longitude">Origin Longitude</label>
                        <input type="text" class="form-control" id="origin_longitude" placeholder="Enter origin longitude" required>
                    </div>
                    <div class="form-group">
                        <label for="destination_latitude">Destination Latitude</label>
                        <input type="text" class="form-control" id="destination_latitude" placeholder="Enter destination latitude" required>
                    </div>
                    <div class="form-group">
                        <label for="destination_longitude">Destination Longitude</label>
                        <input type="text" class="form-control" id="destination_longitude" placeholder="Enter destination longitude" required>
                    </div>
                    <div class="form-group">
                        <label for="fare">Fare</label>
                        <input type="number" class="form-control" id="fare" placeholder="Enter fare amount" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="notificationList" class="list-group">
                    <!-- Notification items will be injected here -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="search-container text-right">
        <input type="number" id="search-input" class="form-control" placeholder="Search by Driver ID" style="width: 300px; display: inline-block;">
    </div>

    <div class="card">
        <div class="card-header">List of Accounts</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Driver ID</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Bus Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="driver-table-body">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr id="driver-<?php echo htmlspecialchars($row['driver_id']); ?>">
                            <td><?php echo htmlspecialchars($row['driver_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['bus_number']); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if ($row['account_status'] == 'active'): ?>
                                        <button class="btn btn-danger btn-sm toggle-status" data-driver-id="<?php echo $row['driver_id']; ?>" data-status="disable">Disable</button>
                                        <button class="btn btn-secondary btn-sm" disabled>Enable</button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Disable</button>
                                        <button class="btn btn-success btn-sm toggle-status" data-driver-id="<?php echo $row['driver_id']; ?>" data-status="enable">Enable</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <ul id="notificationList" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="footer">
        <p>&copy; 2024 Bus Tracking System Using GPS With Data Analytics. All Rights Reserved.</p>
    </div>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
       // Search functionality for Driver ID only
$('#search-input').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#rotationData tr').filter(function() {
        var driverId = $(this).find('td:first').text().toLowerCase(); // Get the Driver ID (first cell)
        $(this).toggle(driverId.indexOf(value) > -1); // Show/hide the row based on Driver ID
    });
});

// Function to fetch and display personnel rotation data
function fetchPersonnelRotation() {
    $.ajax({
        url: 'fetch_rotation.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            let tableContent = '';
            data.forEach(function(row) {
                tableContent += `
                    <tr>
                        <td>${row.driver_id}</td>
                        <td>${row.driver_name}</td> <!-- Added driver_name -->
                        <td>${row.bus_number}</td>   <!-- Added bus_number -->
                        <td>${row.shift}</td>
                        <td>${row.bus_route}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateModal" data-id="${row.driver_id}">Update</button>
                        </td>
                    </tr>`;
            });
            $('#rotationData').html(tableContent);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching rotation data:', error);
        }
    });
}
$(document).ready(function() {
        // Sample notifications array - replace this with your actual notification data
        const notifications = [
            "Bus 9387 is on route to Pagadian.",
            "Bus 2345 has completed its trip.",
            "Bus 6789 is currently delayed due to traffic.",
        ];

        // Update the notification badge count
        $('.notification .badge').text(notifications.length);

        // Handle notification icon click
        $('.notification').on('click', function() {
            // Clear previous notifications
            $('#notificationList').empty();
            
            // Populate the modal with notifications
            notifications.forEach(notification => {
                $('#notificationList').append(`<li class="list-group-item">${notification}</li>`);
            });

            // Show the notification modal
            $('#notificationModal').modal('show');
        });
    });


// Handle Update button click to set driver ID in modal
$(document).on('click', '.btn-primary[data-toggle="modal"]', function() {
    const driverId = $(this).data('id');
    $('#updateModal').data('driver-id', driverId); // Store the driver ID in modal
});

// Handle Save Changes button in modal
$('#saveChanges').on('click', function() {
    const formData = {
    shift: $('#shift').val(),
    bus_route: $('#bus_route').val(),
    origin_latitude: $('#origin_latitude').val(),
    origin_longitude: $('#origin_longitude').val(),
    destination_latitude: $('#destination_latitude').val(),
    destination_longitude: $('#destination_longitude').val(),
    fare: $('#fare').val(),
    driver_id: $('#updateModal').data('driver-id')
};



    $.ajax({
        url: 'update_personnel_rotation.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert(response.message);
                $('#updateModal').modal('hide');
                fetchPersonnelRotation();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating rotation:', error);
            alert('Failed to update personnel rotation');
        }
    });
});


// Fetch the personnel rotation data when the page loads
$(document).ready(function() {
    fetchPersonnelRotation();

    
});

$(document).ready(function() {
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#driver-table-body tr').filter(function() {
                var driverId = $(this).find('td:first').text().toLowerCase();
                $(this).toggle(driverId.indexOf(value) > -1);
            });
        });

        $('.toggle-status').click(function() {
    var driverId = $(this).data('driver-id');
    var action = $(this).data('status');

    $.ajax({
        url: 'toggle_account_status.php',
        type: 'POST',
        data: { driver_id: driverId, action: action },
        success: function(response) {
            var data = JSON.parse(response);
            var row = $('#driver-' + driverId);
            
            if (data.success) {
                if (action === 'disable') {
                    row.find('.toggle-status[data-status="disable"]').prop('disabled', true);
                    row.find('.toggle-status[data-status="enable"]').prop('disabled', false);
                    row.find('.toggle-status[data-status="disable"]').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', true);
                    row.find('.toggle-status[data-status="enable"]').removeClass('btn-secondary').addClass('btn-success').prop('disabled', false);
                } else {
                    row.find('.toggle-status[data-status="enable"]').prop('disabled', true);
                    row.find('.toggle-status[data-status="disable"]').prop('disabled', false);
                    row.find('.toggle-status[data-status="enable"]').removeClass('btn-success').addClass('btn-secondary').prop('disabled', true);
                    row.find('.toggle-status[data-status="disable"]').removeClass('btn-secondary').addClass('btn-danger').prop('disabled', false);
                }
            } else {
                alert('Error updating status: ' + data.message);
            }
        },
        error: function() {
            alert('An error occurred while processing your request.');
        }
    });
});


        const notifications = [
            "Bus 9387 is on route to Pagadian.",
            "Bus 2345 has completed its trip.",
            "Bus 6789 is currently delayed due to traffic."
        ];
        $('.notification .badge').text(notifications.length);
        
        $('.notification').on('click', function() {
            $('#notificationList').empty();
            notifications.forEach(notification => {
                $('#notificationList').append(`<li class="list-group-item">${notification}</li>`);
            });
            $('#notificationModal').modal('show');
        });
    });


</script>

</body>
</html>
