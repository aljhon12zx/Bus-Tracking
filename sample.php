<?php
include('connect_db.php');

// Query to get bus details from database
$sql = "SELECT bus_number, total_distance_travelled_km, driver_id, driver_name, bus_route FROM buses";
$result = $conn->query($sql);

$buses = array();
$notifications = array();  // To store maintenance notifications

// Define maintenance thresholds
$maintenanceThresholds = array(
    "Engine Check" => 500,
    "Tire Replacement" => 2274,
    "Change Oil" => 10000
);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buses[] = $row;

        // Check if any maintenance threshold is reached
        foreach ($maintenanceThresholds as $type => $limit) {
            if ($row['total_distance_travelled_km'] >= $limit) {
                $notifications[] = array(
                    "message" => "Bus number {$row['bus_number']} needs {$type} maintenance.",
                    "bus_number" => $row['bus_number'],
                    "maintenance_type" => $type,
                    "driver_id" => $row['driver_id'],
                    "driver_name" => $row['driver_name'],
                    "bus_route" => $row['bus_route'],
                    "total_distance" => $row['total_distance_travelled_km']
                );
                break;
            }
        }
    }
}
$conn->close();
?>

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
            <!-- Notification Icon -->
            <div class="notification">
                <span class="badge"></span> <!-- Notification count will be added here -->
                <i class="fas fa-bell fa-2x"></i>
            </div>
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="account_management.php">Account Management</a></li>
            <li class="nav-item"><a class="nav-link" href="status.php">Status</a></li>
            <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

    <!-- Bus Maintenance Section -->
    <div class="row" id="status">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">Bus Maintenance</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bus Number</th>
                                <th>Driver</th>
                                <th>Route</th>
                                <th>Maintenance Type</th>
                                <th>Distance Travelled (km)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="maintenanceData">
                            <!-- Data will be injected here by JavaScript -->
                        </tbody>
                        <tbody id="historicalData">
                            <!-- Historical data rows will be injected here -->
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
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts -->
    <script src="notifications.js"></script>
    <script>
        const notifications = <?php echo json_encode($notifications); ?>;
        const busesData = <?php echo json_encode($buses); ?>;
    </script>

</body>
</html>
