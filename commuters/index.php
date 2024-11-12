<?php
include("connect_db.php");

// Get origin and destination from the search
$origin = isset($_GET['origin']) ? $_GET['origin'] : '';
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';

// Prepare the SQL query with a join to the buses table and filter for active accounts
$query = "
    SELECT driver.bus_number, driver.*, buses.fare, buses.estimated_arrival_time, 
           driver.origin_latitude, driver.origin_longitude, 
           driver.destination_latitude, driver.destination_longitude 
    FROM driver
    LEFT JOIN buses ON driver.bus_number = buses.bus_number
    WHERE driver.account_status = 'active'
";

// Filter results by origin and destination, if both are provided
if ($origin && $destination) {
    $query .= " AND ((driver.bus_route LIKE '%$origin%' AND driver.bus_route LIKE '%$destination%')
                OR (driver.bus_route LIKE '%$destination%' AND driver.bus_route LIKE '%$origin%'))";
}

// Add grouping by bus number
$query .= " GROUP BY driver.bus_number";

$result = mysqli_query($conn, $query);
$buses = [];

// Fetch bus data into an array
while ($row = mysqli_fetch_assoc($result)) {
    $eta = $row['estimated_arrival_time']; // Assuming 'H:i:s' format
    $dateTime = DateTime::createFromFormat('H:i:s', $eta);
    $row['formattedETA'] = $dateTime->format('g:i A'); // Format to '2:00 PM'
    $buses[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking System Using GPS with Data Analytics</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Leaflet CSS for the map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2.5rem;
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
            gap: 10px;
        }
        input {
            padding: 10px;
            margin: 5px;
            width: 220px;
            border: 2px solid #007bff;
            border-radius: 25px;
            outline: none;
            transition: 0.3s;
        }
        input:focus {
            border-color: #0056b3;
            box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.3);
        }
        button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
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
        .table thead th {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .suggestions-box {
    position: absolute;
    z-index: 1000;
    width: calc(100% - 20px); /* Adjust to fit with margins */
    border: 1px solid #ccc;
    background: #fff;
    max-height: 150px;
    overflow-y: auto;
    display: none; /* Default hidden */
    border-radius: 5px; /* Optional for rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
    margin-top: 5px; /* Add margin to separate from input */
}


.suggestion-item {
    padding: 8px;
    cursor: pointer;
}

.suggestion-item:hover {
    background-color: #f1f1f1;
}

#searchButton {
    padding: 10px 25px;
    background-color: #007bff; /* Change to a distinct color */
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

#searchButton:hover {
    background-color: #007bff; /* Darken on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
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
    <!-- Search Container at the Bottom -->
    <div class="search-container">
    <input list="locationList" id="origin" placeholder="Origin" onchange="setAbbreviation('origin')" oninput="filterLocations(this.value, 'origin')" />
    <button class="arrow-button" onclick="swapLocations()">↔️</button>
    <input list="locationList" id="destination" placeholder="Destination" onchange="setAbbreviation('destination')" oninput="filterLocations(this.value, 'destination')" />
    <button id="searchButton" onclick="searchRoute()">
    <i class="fas fa-search"></i> Search Route
</button>

    <div id="suggestions" class="suggestions-box"></div>
</div>

<!-- Datalist for predefined locations -->
<datalist id="locationList">
    <option value="Pagadian City (PAG)">
    <option value="Ozamis City (OZC)">
    <option value="Zamboanga City (ZAM)">
    <option value="Cagayan de Oro City (CGY)">
</datalist>





</div>

    </nav>

    <div class="container">
        <h1 class="mt-5">Bus Tracking System Using GPS with Data Analytics</h1>

        <!-- Map Section -->
        <div id="map"></div>

        <!-- List of Buses Section -->
        <div class="card">
            <div class="card-header">List of Buses</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bus Number</th>
                                <th>Bus Route</th>
                                <th>Fare</th>
                                <th>Estimated Time of Arrival (ETA)</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php if (empty($buses)): ?>
        <tr>
            <td colspan="4" class="text-center">No buses found for the selected route.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($buses as $bus): ?>
            <tr>
                <td>
                    <a href="javascript:void(0);" 
                       onclick="showBusRoute(
                           <?= htmlspecialchars($bus['origin_latitude']); ?>, 
                           <?= htmlspecialchars($bus['origin_longitude']); ?>, 
                           <?= htmlspecialchars($bus['destination_latitude']); ?>, 
                           <?= htmlspecialchars($bus['destination_longitude']); ?>, 
                           '<?= htmlspecialchars($bus['bus_number']); ?>'
                       ); trackBusRealTime('<?= htmlspecialchars($bus['bus_number']); ?>');">
                        <?= htmlspecialchars($bus['bus_number']); ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($bus['bus_route']); ?></td>
                <td>Php<?= htmlspecialchars($bus['fare']); ?></td>
                <td><?= htmlspecialchars($bus['formattedETA']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Bus Tracking System Using GPS With Data Analytics. All Rights Reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
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

                    var etaInMinutes = Math.round(data.routes[0].duration / 60);
                    var hours = Math.floor(etaInMinutes / 60);
                    var minutes = etaInMinutes % 60;
                    var etaFormatted = hours + 'h ' + minutes + 'm';
                    
                    // Update formatted ETA directly within each bus row
                    document.querySelector(`#eta-${busNumber}`).innerHTML = etaFormatted;
                }
            });
        }


       // Function to track bus in real-time
function trackBusRealTime(busNumber) {
    var busMarker;

    setInterval(function() {
        $.ajax({
            url: 'get_bus_location.php', // Make sure this script returns the latest bus location
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


        // Function to swap origin and destination locations
        function swapLocations() {
            var origin = $('#origin').val();
            var destination = $('#destination').val();
            $('#origin').val(destination);
            $('#destination').val(origin);
        }

        // Function to search buses based on the route
        function searchRoute() {
            var origin = $('#origin').val();
            var destination = $('#destination').val();
            if (origin && destination) {
                window.location.href = '?origin=' + origin + '&destination=' + destination;
            } else {
                alert('Please enter both origin and destination.');
            }
        }
        // Call the function to start tracking real-time after the page loads
        $(document).ready(function() {
    // Walang default na tracking, ang tracking ay magsisimula kapag pinindot ang bus number
});
// Convert ETA to "2h 58m" format
var eta = data.routes[0].duration; // assuming this is in seconds
var hours = Math.floor(eta / 3600);
var minutes = Math.floor((eta % 3600) / 60);
var formattedETA = hours + 'h ' + minutes + 'm';

// Display formatted ETA
$('#eta-' + busNumber).html(formattedETA);
const locations = [
    { name: "Pagadian City", code: "PAG" },
    { name: "Zamboanga City", code: "ZAM" },
    { name: "Ozamis City", code: "OZC" },
    { name: "Cagayan de Oro City", code: "CGY" },
    // Add more locations here if needed
];

// Function to handle location filtering (kept for dynamic suggestions if needed)
function filterLocations(input, type) {
    const suggestionsBox = document.getElementById('suggestions');
    suggestionsBox.innerHTML = ''; // Clear previous suggestions
    suggestionsBox.style.display = 'none'; // Hide initially

    if (input.length > 0) {
        const filteredLocations = locations.filter(location => 
            location.name.toLowerCase().includes(input.toLowerCase())
        );

        filteredLocations.forEach(location => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = `${location.name} (${location.code})`;
            div.onclick = () => selectLocation(location.code, type);
            suggestionsBox.appendChild(div);
        });

        if (filteredLocations.length > 0) {
            suggestionsBox.style.display = 'block'; // Show suggestions if any are found
        }
    }
}

// Function to handle selection of a location from suggestions
function selectLocation(code, type) {
    document.getElementById(type).value = code; // Set only the code in the search bar
    document.getElementById('suggestions').style.display = 'none'; // Hide suggestions
}



// Function to handle selection of a location from suggestions
function selectLocation(code, type) {
    document.getElementById(type).value = code; // Set only the code in the search bar
    document.getElementById('suggestions').style.display = 'none'; // Hide suggestions
}


function setAbbreviation(inputId) {
    const inputElement = document.getElementById(inputId);
    const value = inputElement.value;
    
    // Match abbreviation in parentheses, e.g., "PAG" in "Pagadian City (PAG)"
    const match = value.match(/\(([^)]+)\)/);
    
    if (match) {
        inputElement.value = match[1]; // Set only the abbreviation
    }
}

function swapLocations() {
    const originInput = document.getElementById('origin');
    const destinationInput = document.getElementById('destination');
    const temp = originInput.value;
    originInput.value = destinationInput.value;
    destinationInput.value = temp;
}

function searchRoute() {
    // Add your search functionality here
}
function searchRoute() {
    var origin = $('#origin').val();
    var destination = $('#destination').val();
    
    if (origin && destination) {
        // Reload the page with search parameters
        window.location.href = '?origin=' + origin + '&destination=' + destination;
    } else {
        alert('Please enter both origin and destination.');
    }
}




    </script>

</body>
</html>
