<?php
include 'connect_db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the data from the POST request
    $driver_id = $_POST['driver_id'];
    $shift = $_POST['shift'];
    $bus_route = $_POST['bus_route'];
    $origin_latitude = $_POST['origin_latitude'];
    $origin_longitude = $_POST['origin_longitude'];
    $destination_latitude = $_POST['destination_latitude'];
    $destination_longitude = $_POST['destination_longitude'];
    $fare = $_POST['fare'];

    // Update the driver rotation data (assumed to be in a table named 'driver')
    $update_driver_sql = "UPDATE driver SET 
        shift = '$shift',
        bus_route = '$bus_route',
        origin_latitude = '$origin_latitude',
        origin_longitude = '$origin_longitude',
        destination_latitude = '$destination_latitude',
        destination_longitude = '$destination_longitude'
        WHERE driver_id = '$driver_id'";

    if (mysqli_query($conn, $update_driver_sql)) {
        // If the driver data is successfully updated, get the bus number of the driver
        $bus_number_query = "SELECT bus_number FROM driver WHERE driver_id = '$driver_id'";
        $result = mysqli_query($conn, $bus_number_query);
        $row = mysqli_fetch_assoc($result);
        $bus_number = $row['bus_number'];

        // Update the buses table with the new route and fare for the matching bus_number
        $update_buses_sql = "UPDATE buses SET 
            bus_route = '$bus_route', 
            fare = '$fare'
            WHERE bus_number = '$bus_number'";

        if (mysqli_query($conn, $update_buses_sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Personnel rotation and bus data updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update bus data.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update personnel rotation.']);
    }
}
?>
