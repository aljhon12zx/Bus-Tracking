<?php
include("connect_db.php");
session_start();

if (isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['total_distance'])) {
    $bus_number = $_POST['bus_number']; 
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $total_distance = $_POST['total_distance'];

    // Update bus location in the database
    $query = "UPDATE buses SET origin_latitude='$latitude', origin_longitude='$longitude' WHERE bus_number='$bus_number'";
    mysqli_query($conn, $query);

    // Update total distance traveled in the database
    $distance_query = "UPDATE buses SET total_distance_travelled_km = '$total_distance' WHERE bus_number = '$bus_number'";
    $result = mysqli_query($conn, $distance_query);

    if ($result) {
        echo "Distance updated successfully.";
    } else {
        echo "Error updating distance: " . mysqli_error($conn);
    }
} else {
    echo "Location or distance data not received.";
}
?>
