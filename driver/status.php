<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking Bus Status</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- FontAwesome for icons -->
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
    <a class="navbar-brand" href="index.php">Bus Tracking System Using GPS with Data Analytics</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
     <!-- Notification Icon -->
     <div class="notification" style="cursor: pointer;">
        <span class="badge"></span> <!-- Notification count will be added here -->
        <i class="fas fa-bell fa-2x"></i>
    </div>
</li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="status.php">Status</a>
            </li>
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
    <h1 class="mt-5">BUS STATUS</h1>

    <!-- Historical Data Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    Historical Data
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bus Number</th>
                                <th>Distance Travelled (km)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="historicalData">
                            <!-- Data will be injected here by JavaScript -->
                        </tbody>
                    </table>
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

    <div class="footer">
        <p>&copy; 2024 Bus Tracking System Using GPS With Data Analytics. All Rights Reserved.</p>
    </div>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
     $(document).ready(function() {
        // Sample notifications array - replace this with your actual notification data
        const notifications = [
            "Bus 9387 is on route to Pagadian.",
            "Bus 2345 has completed its trip.",
            "Bus 6789 is currently delayed due to traffic."
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
    // Example data for historical table
    const historicalData = [
        { date: '2024-09-01', busId: '9387', distance: '50', avgSpeed: ' ' },
        { date: '2024-09-02', busId: '9387', distance: '75', avgSpeed: ' ' },
        { date: '2024-09-03', busId: '9387', distance: '100', avgSpeed: ' ' }
    ];

    function loadHistoricalData() {
        const tableBody = document.getElementById('historicalData');
        historicalData.forEach(data => {
            const row = `<tr>
                <td>${data.date}</td>
                <td>${data.busId}</td>
                <td>${data.distance}</td>
                <td>${data.avgSpeed}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }

    loadHistoricalData();

    // Example data for maintenance table
    const maintenanceData = [
        { date: '2024-09-01', busId: 'B001', type: 'Engine Check', status: 'Completed' },
        { date: '2024-09-02', busId: 'B002', type: 'Tire Replacement', status: 'Pending' },
        { date: '2024-09-03', busId: 'B003', type: 'Brake Inspection', status: 'Completed' }
    ];

    function loadMaintenanceData() {
        const tableBody = document.getElementById('maintenanceData');
        maintenanceData.forEach(data => {
            const row = `<tr>
                <td>${data.date}</td>
                <td>${data.busId}</td>
                <td>${data.type}</td>
                <td>${data.status}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }

    loadMaintenanceData();

    
</script>

</body>
</html>
