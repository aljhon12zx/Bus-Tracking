<?php
include("connect_db.php");
session_start(); // Start the session

// Check if a session exists and the username is set
if (!isset($_SESSION['username'])) {
    echo "Please log in first.";
    exit; // Stop further execution if the user is not logged in
}

$username = $_SESSION['username']; // Get the logged-in user's username

// Fetch the data from the database based on the logged-in username
$query = "SELECT * FROM driver WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Initialize an array to hold bus data
$buses = [];
$selectedBus = null;  // Variable to store the selected bus

// Fetch bus data into the array
while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;

    // Set the first bus as the selected bus (you can change this logic as needed)
    if (!$selectedBus) {
        $selectedBus = $row;  // Use this bus for the "Bus Details" card
    }
}

// Check if a bus is selected
if (!$selectedBus) {
    // Handle the case where no bus data is found for this user
    echo "No bus data available for the selected user.";
    exit; // Stop further execution if no bus is found
}

// Ensure you have destination coordinates and other necessary information
$destinationLatitude = htmlspecialchars($selectedBus['destination_latitude']);
$destinationLongitude = htmlspecialchars($selectedBus['destination_longitude']);
$averageSpeed = 60; // Set an average speed in km/h (You can adjust this based on your logic)

// Continue with your other logic here...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking System with Data Analytics</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative; /* Allow absolute positioning for the loader */
        }
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none; /* Hidden by default */
        }
        .card {
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .card-body p {
            font-size: 1.1rem;
        }
        h1 {
            font-size: 2.5rem;
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
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
                <li class="nav-item active">
    <div class="notification" style="cursor: pointer;" data-toggle="modal" data-target="#notificationModal">
        <span class="badge"></span> <!-- Notification count will be added here -->
        <i class="fas fa-bell fa-2x"></i>
    </div>
</li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
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
        <h1 class="mt-5">DASHBOARD</h1>

        <!-- Map and Bus Details Section -->
        <div class="row">
            <div class="col-md-8">
                <div id="map">
                    <div class="loading">
                        <img src="loading.gif" alt="Loading..."> <!-- Add a loading GIF or spinner -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Bus Details</div>
                    <div class="card-body">
                        <p><strong>Bus Number:</strong> <span id="bus_number"><?php echo htmlspecialchars($selectedBus['bus_number']); ?></span></p>
                        <p><strong>Latitude:</strong> <span id="origin_latitude"><?php echo htmlspecialchars($selectedBus['origin_latitude']); ?></span></p>
                        <p><strong>Longitude:</strong> <span id="origin_longitude"><?php echo htmlspecialchars($selectedBus['origin_longitude']); ?></span></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Analytics</div>
                    <div class="card-body">
                       <p><strong>Total Distance Traveled (km):</strong> <span id="total_distance">120 km</span></p>

                    </div>
                </div>
            </div>
        </div>

        <!-- Bus Data Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Driver Name</th>
                            <th>Bus Number</th>
                            <th>Bus Route</th>
                            <th>Estimated Time of Arrival (ETA)</th>
                            <th>Change Route</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($buses as $bus): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bus['driver_name']); ?></td>
                                <td><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                                <td><?php echo htmlspecialchars($bus['bus_route']); ?></td>
                                <td id="eta-<?php echo htmlspecialchars($bus['bus_number']); ?>">Loading...</td>
                                <td>
                                <button class="btn btn-warning change-route-btn" data-toggle="modal" data-target="#changeRouteModal" data-bus-number="<?php echo htmlspecialchars($bus['bus_number']); ?>" onclick="saveAvoidancePoints(<?php echo htmlspecialchars($bus['bus_number']); ?>)">
    Change Route
</button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                <p>No new notifications at this time.</p>
            </div>
        </div>
    </div>
</div>


 <!-- Change Route Modal -->
<div class="modal fade" id="changeRouteModal" tabindex="-1" role="dialog" aria-labelledby="changeRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeRouteModalLabel">Change Route for <span id="busChangeNumber"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- List of possible traffic reasons -->
                <div class="form-group">
                    <label>Possible Traffic Reasons</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Accident" id="reasonAccident">
                        <label class="form-check-label" for="reasonAccident">Accident</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Road Work" id="reasonRoadWork">
                        <label class="form-check-label" for="reasonRoadWork">Road Work</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Heavy Traffic" id="reasonHeavyTraffic">
                        <label class="form-check-label" for="reasonHeavyTraffic">Heavy Traffic</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Weather Conditions" id="reasonWeatherConditions">
                        <label class="form-check-label" for="reasonWeatherConditions">Weather Conditions</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="submitRouteChange()">Submit</button>
            </div>
        </div>
    </div>
</div>



    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Bus Tracking System. All rights reserved.</p>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/osrm-client/3.0.1/osrm.min.js"></script>


    <script>
        var map; // Global variable to hold the map instance
    var marker; // Global variable for the bus marker
    var destinationMarker; // Global variable for the destination marker
    var routeLine; // Global variable for the route line

    function initMap() {
        // Initialize the map and set its view to the bus's starting position
        map = L.map('map').setView([<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>], 13);

        // Load and set the tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Initialize the bus marker at the starting position
        marker = L.marker([<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>]).addTo(map);

        // Define destination coordinates
        var destinationLatitude = <?php echo $destinationLatitude; ?>;
        var destinationLongitude = <?php echo $destinationLongitude; ?>;

        // Add a destination marker
        destinationMarker = L.marker([destinationLatitude, destinationLongitude], {
            icon: L.icon({
                iconUrl: 'images/destination.png', // Change this to your destination pin icon URL
                iconSize: [25, 41], // Adjust the size of the icon
                iconAnchor: [12, 41],
            })
            
        }).addTo(map);


        // Draw a route from origin to destination
        var routeCoordinates = [
            [<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>],
            [destinationLatitude, destinationLongitude]
        ];
        routeLine = L.polyline(routeCoordinates, { color: 'blue' }).addTo(map);

        // Start watching the GPS position
        startGPSUpdate();
    }
    var totalDistance = <?php echo isset($selectedBus['total_distance_travelled_km']) ? $selectedBus['total_distance_travelled_km'] : 0; ?>; // Start from the value in the database
var previousPosition = { lat: <?php echo $selectedBus['origin_latitude']; ?>, lon: <?php echo $selectedBus['origin_longitude']; ?> };

function startGPSUpdate() {
    document.querySelector('.loading').style.display = 'block';

    navigator.geolocation.watchPosition(function(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        marker.setLatLng([latitude, longitude]);
        map.setView([latitude, longitude]);

        var distanceMoved = map.distance(
            L.latLng(previousPosition.lat, previousPosition.lon),
            L.latLng(latitude, longitude)
        ) / 1000; 
        totalDistance += distanceMoved; // Accumulate distance
        document.querySelector('#total_distance').textContent = totalDistance.toFixed(2) + ' km';

        previousPosition = { lat: latitude, lon: longitude };

        document.getElementById('origin_latitude').textContent = latitude.toFixed(6);
        document.getElementById('origin_longitude').textContent = longitude.toFixed(6);

        $.post('update_location.php', {
            bus_number: '<?php echo htmlspecialchars($selectedBus['bus_number']); ?>',
            latitude: latitude,
            longitude: longitude,
            total_distance: totalDistance // Send the accumulated distance
        }, function(response) {
            console.log(response); // For debugging response from server
        });

    }, function(error) {
        console.error("Error obtaining GPS position: ", error);
    }, {
        enableHighAccuracy: true,
        maximumAge: 30000,
        timeout: 27000
    });
}




    // Initialize the map when the document is ready
    document.addEventListener("DOMContentLoaded", initMap);

        var destinationMarker; // Global variable for the destination marker

function initMap() {
    // Initialize the map and set its view to a default location
    map = L.map('map').setView([<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>], 13);

    // Load and set the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Initialize the bus marker at the starting position
    marker = L.marker([<?php echo $selectedBus['origin_latitude']; ?>, <?php echo $selectedBus['origin_longitude']; ?>]).addTo(map);

    // Add a destination marker
    destinationMarker = L.marker([<?php echo $destinationLatitude; ?>, <?php echo $destinationLongitude; ?>], {
        icon: L.icon({
            iconUrl: 'images/destination.png', // Change this to your destination pin icon URL
            iconSize: [25, 41], // Adjust the size of the icon
            iconAnchor: [12, 41],
        })
    }).addTo(map);
 // Call the function to plot the route using OSRM
 plotRouteWithOSRM(<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>, <?php echo $destinationLatitude; ?>, <?php echo $destinationLongitude; ?>);
    // Start watching the GPS position
    startGPSUpdate();
}
// Function to plot route using OSRM
function plotRouteWithOSRM(originLat, originLon, destLat, destLon) {
            var osrmUrl = `https://router.project-osrm.org/route/v1/driving/${originLon},${originLat};${destLon},${destLat}?overview=full&geometries=geojson`;

            // Make an AJAX request to OSRM API
            $.getJSON(osrmUrl, function(data) {
                // Extract the coordinates from the OSRM response
                var routeCoordinates = L.GeoJSON.coordsToLatLngs(data.routes[0].geometry.coordinates);

                // Draw the route on the map
                routeLine = L.polyline(routeCoordinates, { color: 'blue' }).addTo(map);

                // Fit the map to the route
                map.fitBounds(routeLine.getBounds());
            });
        }

        // Initialize the map when the document is ready
        document.addEventListener("DOMContentLoaded", initMap);

     
        
var averageSpeed = <?php echo $averageSpeed; ?>; // Average speed in km/h
var destinationCoordinates = [<?php echo $destinationLatitude; ?>, <?php echo $destinationLongitude; ?>];
var busNumber = "<?php echo htmlspecialchars($selectedBus['bus_number']); ?>"; // Current bus number

function calculateETA(distanceRemaining) {
    // Calculate time remaining in hours and minutes
    var timeRemainingHours = distanceRemaining / averageSpeed;
    var timeRemainingMinutes = Math.round(timeRemainingHours * 60);

    // Calculate the current time and add remaining minutes to get ETA time
    var etaDate = new Date();
    etaDate.setMinutes(etaDate.getMinutes() + timeRemainingMinutes);

    // Format ETA in 12-hour format with AM/PM
    var hours = etaDate.getHours();
    var minutes = etaDate.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // The hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var displayETA = `${hours}:${minutes} ${ampm}`;

    // Convert to database format (HH:MM:SS) to save in MySQL
    var databaseETA = etaDate.toTimeString().split(' ')[0]; // HH:MM:SS format

    // Update the ETA display on the card
    document.getElementById(`eta-${busNumber}`).textContent = displayETA;

    // Return the database format for server update
    return databaseETA;
}

// Function to update the ETA in the database
function updateETAOnServer(eta) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save_eta.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(`bus_number=${busNumber}&eta=${encodeURIComponent(eta)}`);
}

// Use navigator.geolocation to keep updating the ETA
navigator.geolocation.watchPosition(function (position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    var currentCoordinates = L.latLng(latitude, longitude);
    var destination = L.latLng(destinationCoordinates[0], destinationCoordinates[1]);
    var distanceRemaining = map.distance(currentCoordinates, destination) / 1000;

    var eta = calculateETA(distanceRemaining);
    updateETAOnServer(eta);

}, function (error) {
    console.error("Error obtaining GPS position: ", error);
}, {
    enableHighAccuracy: true,
    maximumAge: 30000,
    timeout: 27000
});
// Function to fetch the latest distance from the server
function fetchDistanceFromDatabase() {
    fetch("fetch_distance.php")
        .then(response => response.json())
        .then(data => {
            if (data.total_distance_travelled_km !== undefined) {
                total_distance_travelled_km = data.total_distance_travelled_km;
                checkMaintenance(); // Ensure checkMaintenance is called right after updating
            } else {
                console.error("Error: Distance data not found.");
            }
        })
        .catch(error => console.error("Error fetching distance:", error));
}

// Function to check distance and add notifications at specific limits
function checkMaintenance() {
    notifications.length = 0; // Clear notifications array before re-populating

    if (total_distance_travelled_km >= 17000 && !tireChangeNotified) {
        notifications.push("You need to change the tire.");
        tireChangeNotified = true; // Mark as notified
    }
    if (total_distance_travelled_km >= 20000 && !oilChangeNotified) {
        notifications.push("You need to change the oil.");
        oilChangeNotified = true; // Mark as notified
    }

    if (notifications.length > 0) {
        updateNotificationBadge();
        updateNotificationModal();
    }
}

// Call fetchDistanceFromDatabase every 10 seconds to keep data updated
setInterval(fetchDistanceFromDatabase, 10000);

// Event listener to show the modal and reset the notification count
document.querySelector('.notification').addEventListener('click', () => {
    $('#notificationModal').modal('show');
    resetNotificationBadge(); // Clear the badge count after opening the modal
});

// Function to reset the notification badge count
function resetNotificationBadge() {
    const badge = document.querySelector('.notification .badge');
    badge.innerText = ""; // Clear the badge count
}



document.querySelector('.notification').addEventListener('click', () => {
        $('#notificationModal').modal('show');
    });
    function submitRouteChange() {
    // Get reasons and avoidance coordinates from the modal form
    const reasons = [];
    if (document.getElementById('reasonAccident').checked) reasons.push("Accident");
    if (document.getElementById('reasonRoadWork').checked) reasons.push("Road Work");
    if (document.getElementById('reasonHeavyTraffic').checked) reasons.push("Heavy Traffic");
    if (document.getElementById('reasonWeatherConditions').checked) reasons.push("Weather Conditions");

    // Example avoidance point (e.g., landslide location)
    const avoidanceLatitude = 7.842456;
    const avoidanceLongitude = 123.442616;

    // Send the avoidance point to the backend
    fetch('changeroute.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reasons, avoidanceLatitude, avoidanceLongitude })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Route change successfully saved! Recalculating...');
            recalculateRouteWithAvoidance(avoidanceLatitude, avoidanceLongitude);
            $('#changeRouteModal').modal('hide');
        } else {
            alert(result.message || 'Failed to save route change.');
        }
    })
    .catch(error => console.error('Error:', error));
}

function recalculateRouteWithAvoidance(avoidLat, avoidLon) {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${previousPosition.lon},${previousPosition.lat};${destinationLongitude},${destinationLatitude}?geometries=geojson&exclude=${avoidLat},${avoidLon}`;

    // Fetch the new route avoiding the specified point
    $.getJSON(osrmUrl, function(data) {
        if (routeLine) {
            map.removeLayer(routeLine); // Remove old route line
        }

        const newRouteCoordinates = L.GeoJSON.coordsToLatLngs(data.routes[0].geometry.coordinates);
        routeLine = L.polyline(newRouteCoordinates, { color: 'red' }).addTo(map);

        // Recenter map to fit the new route
        map.fitBounds(routeLine.getBounds());
    });
}






    </script>
</body>
</html>
