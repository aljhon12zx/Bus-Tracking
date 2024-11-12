<?php
include("connect_db.php");

// Array to store "moving" buses
$movingBuses = 0;

// Query to fetch current bus positions
$query = "SELECT bus_id, origin_latitude, origin_longitude FROM driver";
$result = mysqli_query($conn, $query);

// Session to store previous positions
session_start();

// Initialize or get previous positions from the session
if (!isset($_SESSION['previous_positions'])) {
    $_SESSION['previous_positions'] = [];
}

// Check each bus to see if it has moved
while ($row = mysqli_fetch_assoc($result)) {
    $busId = $row['bus_id'];
    $currentLatitude = $row['origin_latitude'];
    $currentLongitude = $row['origin_longitude'];

    // Check if we have a previous position for this bus
    if (isset($_SESSION['previous_positions'][$busId])) {
        $previousLatitude = $_SESSION['previous_positions'][$busId]['latitude'];
        $previousLongitude = $_SESSION['previous_positions'][$busId]['longitude'];

        // Compare current and previous positions
        if ($currentLatitude != $previousLatitude || $currentLongitude != $previousLongitude) {
            $movingBuses++;
        }
    }

    // Update the session with the current position
    $_SESSION['previous_positions'][$busId] = [
        'latitude' => $currentLatitude,
        'longitude' => $currentLongitude
    ];
}

// Output JSON with the count of moving buses
echo json_encode(['movingBuses' => $movingBuses]);
?>
