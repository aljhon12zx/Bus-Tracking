<?php
include("connect_db.php");

// Fetch bus data from the database
$query = "SELECT * FROM driver";
$result = mysqli_query($conn, $query);

// Initialize an array to hold bus data
$buses = [];
$selectedBus = null;  // Variable to store the selected bus

// Variables to count the buses by status
$totalBuses = 0;
$movingBuses = 0;
$underMaintenanceBuses = 0;

// Fetch bus data into the array and count bus statuses
while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;
    $totalBuses++;

    // Set the first bus as the selected bus
    if (!$selectedBus) {
        $selectedBus = $row;  // Use this bus for the "Bus Details" card
    }

    // Count buses by status (you need to implement this logic)
    // e.g., $movingBuses++, $parkedBuses++, etc.
    
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking System with Data Analytics</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Leaflet CSS for the map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
         /* Styling for the button */
    .status-btn {
        background-color: #2c6fbb; /* Green background */
        border: none;
        color: white;
        padding: 5px 10px;
        text-align: center;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        transition: background-color 0.3s ease;
    }

    .status-btn:hover {
        background-color: #2c6fbb; /* Darker green on hover */
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
        body {
            background-color: #f8f9fa;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
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
        /* Table Styling */
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

        /* Card styling for bus icons */
        .bus-icons {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .bus-icons .card {
            text-align: center;
            padding: 20px;
            flex: 1;
            margin: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .bus-icons .card i {
            font-size: 3rem;
            margin-bottom: 10px;
            color: #007bff;
        }
        .bus-icons .card p {
            font-size: 1.2rem;
            font-weight: bold;
        }
        #map { height: 350px }
        
        
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Bus Tracking System Using GPS with Data Analytics</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
        <h1 class="mt-5">DASHBOARD</h1>

        
        <!-- Bus Icon Cards Section -->
        <div class="bus-icons">
            <div class="card">
                <i class="fas fa-bus"></i>
                <p>Total Buses: <?php echo $totalBuses; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-tools"></i>
                <p>Under Maintenance: <?php echo $underMaintenanceBuses; ?></p>
            </div>
        </div>

        <!-- Map and Bus Details Section -->
        <div class="row">
            <div class="col-md-8">
                <div id="map"></div>
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
       <p><strong>Total Distance Traveled (km):</strong> <span id="total_distance">0 km</span></p>
    </div>
</div>

            </div>
        </div>
        <div class="card">
    <div class="card-header">List of Buses</div>
    <div class="card-body">
        <!-- Search Bar -->
<div class="search-container text-right">
    <input type="text" id="search-input" class="form-control" placeholder="Search by Bus Number" style="width: 300px; display: inline-block;">
</div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Bus Number</th>
                    <th>Bus Route</th>
                    <th>Bus Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bus['driver_name']); ?></td>
                        <td>
    <a href="javascript:void(0);" 
       onclick="
           showBusRoute(
               <?php echo htmlspecialchars($bus['origin_latitude']); ?>, 
               <?php echo htmlspecialchars($bus['origin_longitude']); ?>, 
               <?php echo htmlspecialchars($bus['destination_latitude']); ?>, 
               <?php echo htmlspecialchars($bus['destination_longitude']); ?>, 
               '<?php echo htmlspecialchars($bus['bus_number']); ?>'
           );
           trackBusRealTime('<?php echo htmlspecialchars($bus['bus_number']); ?>');
           showBusDetails('<?php echo htmlspecialchars($bus['bus_number']); ?>');
       ">
        <?php echo htmlspecialchars($bus['bus_number']); ?>
    </a>
</td>


                        <td><?php echo htmlspecialchars($bus['bus_route']); ?></td>
                        <td>
    <button class="status-btn" onclick="window.location.href='status.php?bus_number=<?php echo htmlspecialchars($bus['bus_number']); ?>'">
        View Status
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


    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

    <script>
      // Search functionality for Bus Number
$('#search-input').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('table tbody tr').filter(function() {
        $(this).toggle($(this).find('td:nth-child(2)').text().toLowerCase().indexOf(value) > -1);
    });
});

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

       // Initialize the map
var map = L.map('map').setView([7.8397, 123.4343], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// Marker variables
var originMarker, destinationMarker, routeLayer;

// Define custom bus icon
var busIcon = L.icon({
    iconUrl: 'images/bus_icon.png', // Replace with the path to your bus icon image
    iconSize: [30, 30], // Size of the icon [width, height]
    iconAnchor: [15, 30], // Point of the icon which will correspond to marker's location
    popupAnchor: [0, -30] // Point from which the popup should open relative to the iconAnchor
});

// Function to plot route between origin and destination using OSRM
function plotRoute(originLat, originLng, destinationLat, destinationLng) {
    // Remove any existing route and markers
    if (routeLayer) {
        map.removeLayer(routeLayer);
    }
    if (originMarker) {
        map.removeLayer(originMarker);
    }
    if (destinationMarker) {
        map.removeLayer(destinationMarker);
    }

    // Add origin and destination markers
    originMarker = L.marker([originLat, originLng], { icon: busIcon }).addTo(map)
        .bindPopup("Origin").openPopup();
    destinationMarker = L.marker([destinationLat, destinationLng]).addTo(map)
        .bindPopup("Destination").openPopup();

    // Set view to fit both markers
    map.fitBounds([
        [originLat, originLng],
        [destinationLat, destinationLng]
    ]);

    // Call OSRM to get the route
    var osrmURL = `https://router.project-osrm.org/route/v1/driving/${originLng},${originLat};${destinationLng},${destinationLat}?overview=full&geometries=geojson`;

    $.getJSON(osrmURL, function (data) {
        var route = data.routes[0].geometry;

        // Add route to the map
        routeLayer = L.geoJSON(route).addTo(map);
    });
}


// Handle bus number click event
// Handle bus number click event
$('.bus-link').on('click', function (event) {
    event.preventDefault();

    var originLat = $(this).data('origin-lat');
    var originLng = $(this).data('origin-lng');
    var destinationLat = $(this).data('destination-lat');
    var destinationLng = $(this).data('destination-lng');
    var busNumber = $(this).data('bus-number');

    // Plot route between the origin and destination
    plotRoute(originLat, originLng, destinationLat, destinationLng);

    // Update bus details in the card
    $('#bus_number').text(busNumber);
    $('#origin_latitude').text(originLat);
    $('#origin_longitude').text(originLng);
});



// Call this function every 5 seconds to update bus position
setInterval(updateBusData, 5000);


        function viewBusStatus(bus_number) {
            const busButton = event.target;
            const busNumber = busButton.getAttribute('data-bus-number');
            alert(`Viewing status for Bus_number: ${bus_number} (Number: ${bus_number})`);
            window.location.href = `/bus-tracking/${bus_number}`;
        }
        const options = {
    enableHighAccuracy: true, 
    // Get high accuracy reading, if available (default false)
    timeout: 5000, 
    // Time to return a position successfully before error (default infinity)
    maximumAge: 2000, 
    // Milliseconds for which it is acceptable to use cached position (default 0)
};

navigator.geolocation.watchPosition(success, error, options);
// Fires success function immediately and when user position changes

function success(pos) {

    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    const accuracy = pos.coords.accuracy; // Accuracy in metres

}

function error(err) {

    if (err.code === 1) {
        alert("Please allow geolocation access");
        // Runs if user refuses access
    } else {
        alert("Cannot get current location");
        // Runs if there was a technical problem.
    }

}


 // Watch for location changes using the phone's GPS
 function startGPSUpdate() {
        const options = {
            enableHighAccuracy: true, // Request high-accuracy GPS data
            timeout: 5000, // Wait up to 5 seconds for GPS data
            maximumAge: 0 // Always fetch fresh data
        };

        // Get location updates
        navigator.geolocation.watchPosition(success, error, options);
    }

   // Start updating GPS data when the page loads
window.onload = function () {
    startGPSUpdate();
};

// Function to request GPS updates
function startGPSUpdate() {
    const options = {
        enableHighAccuracy: true, // Request high-accuracy GPS data
        timeout: 5000, // Wait up to 5 seconds for GPS data
        maximumAge: 0 // Always fetch fresh data
    };

    // Get location updates
    navigator.geolocation.watchPosition(success, error, options);
}

// Success callback for geolocation
function success(pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;

    // Display the new position on the webpage (optional, for debugging)
    document.getElementById('latitude').textContent = lat;
    document.getElementById('longitude').textContent = lng;

    // Send the updated GPS coordinates to the server using AJAX
    $.post('update_location.php', {
        driver_id: '123', // Replace with the actual driver ID
        latitude: lat,
        longitude: lng
    }, function (response) {
        console.log("Location data sent to the server:", response);
    });
}

// Error callback for geolocation
function error(err) {
    if (err.code === 1) {
        alert("Please allow geolocation access for the bus tracking system.");
    } else {
        alert("Unable to get the current location. Error: " + err.message);
    }
}

// Start updating GPS data when the page loads
window.onload = function () {
    startGPSUpdate();
};
$(document).ready(function() {
        // Sample notifications array - replace this with your actual notification data
        const notifications = [
            { message: "Bus 9387 is on route to Pagadian.", id: 1 },
            { message: "Bus 2345 has completed its trip.", id: 2 },
            { message: "Bus 6789 is currently delayed due to traffic.", id: 3 },
            { message: "Bus 6789 is currently delayed due to traffic.", id: 3 }
       
        ];

       // Update the notification badge count
$('.notification .badge').text(notifications.length);


        // Handle notification icon click
        $('.notification').on('click', function() {
            // Clear previous notifications
            $('#notificationList').empty();

            // Populate the modal with notifications
            notifications.forEach(notification => {
                const notificationItem = $('<li class="list-group-item"></li>')
                    .text(notification.message)
                    .data('id', notification.id) // Store the notification ID
                    .on('click', function() {
                        // Handle the click event for this notification
                        alert(`You clicked on notification: ${notification.message}`);
                        // You can add more actions here, like redirecting to a specific page
                        // window.location.href = `notificationDetail.php?id=${notification.id}`;
                    });

                $('#notificationList').append(notificationItem);
            });

            // Show the notification modal
            $('#notificationModal').modal('show');
        });
    });

     // Call the function to start tracking real-time after the page loads
     $(document).ready(function() {
    // Walang default na tracking, ang tracking ay magsisimula kapag pinindot ang bus number
});
     // Function to track bus in real-time
function trackBusRealTime(busNumber) {
    var busMarker;

    setInterval(function() {
        $.ajax({
            url: 'update_location.php', // Make sure this script returns the latest bus location
            method: 'GET',
            data: { bus_number: busNumber },
            dataType: 'json',
            success: function(data) {
                if (data.origin_latitude && data.origin_longitude) {
                    var newLatLng = new L.LatLng(data.origin_latitude, data.origin_longitude);

                    if (!busMarker) {
                        // Create a new marker if one doesn't exist
                        busMarker = L.marker(newLatLng, { icon: busIcon }).addTo(map)
                            .bindPopup('Bus ' + busNumber + ' Location').openPopup();
                    } else {
                        // Update marker position
                        busMarker.setLatLng(newLatLng);
                    }

                    // Optionally, update the map view to center on the new location
                    map.setView(newLatLng, 13); // Adjust zoom level as needed
                }
            }
        });
    }, 5000); // Interval for real-time updates, in milliseconds (5000 = 5 seconds)
}

var map = L.map('map').setView([7.8397, 123.4343], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Define custom bus icon
        var busIcon = L.icon({
            iconUrl: 'images/bus_icon.png', // Replace with the path to your bus icon image
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });

        var busMarker;

        // Function to show bus route on map
        function showBusRoute(originLat, originLng, destinationLat, destinationLng, busNumber) {
            // Clear existing layers
            map.eachLayer(function(layer) {
                if (layer instanceof L.GeoJSON) {
                    map.removeLayer(layer);
                }
            });

            // Add bus marker at the origin with custom bus icon
            L.marker([originLat, originLng], { icon: busIcon }).addTo(map)
                .bindPopup('Bus ' + busNumber + ' Origin').openPopup();

            // Add bus marker at the destination (default icon)
            L.marker([destinationLat, destinationLng]).addTo(map)
                .bindPopup('Bus ' + busNumber + ' Destination').openPopup();

            // Get the route using OSRM
            getRoute(originLat, originLng, destinationLat, destinationLng, busNumber);
        }

        // Function to get route using OSRM
        function getRoute(startLat, startLng, destLat, destLng, busNumber) {
            var osrm_url = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${destLng},${destLat}?overview=full&geometries=geojson`;
            
            $.getJSON(osrm_url, function(data) {
                if (data.routes.length > 0) {
                    var route = data.routes[0].geometry;
                    var routeLine = L.geoJSON(route, { color: 'blue' }).addTo(map);
                    
                    // Estimate travel time in minutes
                    var eta = Math.round(data.routes[0].duration / 60);
                    $('#eta-' + busNumber).html(eta + ' mins');
                }
            });
        }

        // Assuming the bus number is stored in a variable
var busNumber = '<?php echo htmlspecialchars($selectedBus["bus_number"]); ?>';

// Function to update bus location
function updateBusLocation() {
    $.ajax({
        url: 'get_bus_location.php',
        method: 'GET',
        data: { bus_number: busNumber },
        dataType: 'json',
        success: function(data) {
            if (data.origin_latitude && data.origin_longitude) {
                $('#origin_latitude').text(data.origin_latitude);
                $('#origin_longitude').text(data.origin_longitude);
                
                // Optionally, update the map position
                if (busMarker) {
                    busMarker.setLatLng([data.origin_latitude, data.origin_longitude]);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching bus location:", error);
        }
    });
}

// Call this function every 5 seconds to update bus location
setInterval(updateBusLocation, 5000);

function showBusDetails(busNumber) {
    $.ajax({
        url: 'get_bus_details.php',
        type: 'POST',
        data: { bus_number: busNumber },
        success: function(response) {
            const busData = JSON.parse(response);

            // Check if the response contains the distance data
            if (busData.total_distance_travelled_km !== undefined) {
                document.getElementById('total_distance_travelled').innerText = busData.total_distance_travelled_km + " km";
            } else {
                document.getElementById('total_distance_travelled').innerText = "Data not found";
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}
// Function para tawagin ang API at ipakita ang total distance
function fetchTotalDistance(busNumber) {
    // Make a request to fetch_distance.php with the bus number
    fetch(`fetch_distance.php?bus_number=${busNumber}`)
        .then(response => response.json())
        .then(data => {
            // Update the distance value in the Analytics card
            document.getElementById("total_distance").innerText = `${data.total_distance_travelled_km} km`;
        })
        .catch(error => console.error("Error fetching distance:", error));
}

// Update the showBusDetails function to also call fetchTotalDistance
function showBusDetails(busNumber) {
    // Fetch and display total distance in Analytics card
    fetchTotalDistance(busNumber);
}
// Function to fetch moving buses count and update the page
function fetchMovingBuses() {
        $.ajax({
            url: 'get_moving_buses.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#movingBusesCount').text(response.movingBuses);
            },
            error: function() {
                console.error("Failed to fetch moving buses count.");
            }
        });
    }

    // Fetch moving buses count every 5 seconds
    setInterval(fetchMovingBuses, 5000);

    // Fetch the count immediately on page load
    fetchMovingBuses();

    </script>
    

</body>
</html>
