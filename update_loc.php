<?php
include("connect_db.php");

$bus_number = $_POST['bus_number'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$total_distance = $_POST['total_distance']; // Receive the total distance

// Update the bus's location and total distance in the database
$query = "UPDATE buses SET current_latitude = '$latitude', current_longitude = '$longitude', total_distance_travelled_km = '$total_distance' WHERE bus_number = '$bus_number'";

if (mysqli_query($conn, $query)) {
    echo "Location and distance updated successfully.";
} else {
    echo "Error updating location and distance: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
