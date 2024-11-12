<?php

include("connect_db.php");
session_start(); // Start the session
// Fetch bus data from the database based on the logged-in bus number
$bus_number = isset($_SESSION['bus_number']) ? $_SESSION['bus_number'] : null;
$query = "SELECT * FROM driver WHERE bus_number = '$bus_number'";
$result = mysqli_query($conn, $query);

$buses = [];
$selectedBus = null;

while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;
    if (!$selectedBus) {
        $selectedBus = $row;
    }
}

// Ensure you have destination coordinates
$destinationLatitude = htmlspecialchars($selectedBus['destination_latitude']);
$destinationLongitude = htmlspecialchars($selectedBus['destination_longitude']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking System with Data Analytics</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">DASHBOARD</h1>
        <div class="row">
            <div class="col-md-8">
                <div id="map"></div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Bus Details</div>
                    <div class="card-body">
                        <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($selectedBus['bus_number']); ?></p>
                        <p><strong>Latitude:</strong> <?php echo htmlspecialchars($selectedBus['origin_latitude']); ?></p>
                        <p><strong>Longitude:</strong> <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var map;
        var marker;
        var destinationMarker;
        var routeLine;

        function initMap() {
            // Initialize the map at the bus's starting position
            map = L.map('map').setView([<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>], 13);

            // Load and set the tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Initialize the bus marker at the starting position
            marker = L.marker([<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>]).addTo(map);

            // Destination marker
            destinationMarker = L.marker([<?php echo $destinationLatitude; ?>, <?php echo $destinationLongitude; ?>], {
                icon: L.icon({
                    iconUrl: 'images/destination.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map);

            // Call the function to plot the route using OSRM
            plotRouteWithOSRM(<?php echo htmlspecialchars($selectedBus['origin_latitude']); ?>, <?php echo htmlspecialchars($selectedBus['origin_longitude']); ?>, <?php echo $destinationLatitude; ?>, <?php echo $destinationLongitude; ?>);
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
    </script>
</body>
</html>
