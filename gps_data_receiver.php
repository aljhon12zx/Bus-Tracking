<?php
include("connect_db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busNumber = $_POST['bus_number'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Update the database with the new GPS coordinates for the bus
    $query = "UPDATE buses SET latitude = '$latitude', longitude = '$longitude' WHERE bus_number = '$busNumber'";
    if (mysqli_query($conn, $query)) {
        echo "Bus location updated successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
