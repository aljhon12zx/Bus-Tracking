<?php
include('connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bus_number = $_POST['bus_number'];
    $maintenance_type = $_POST['maintenance_type'];
    $date = $_POST['date'];
    $driver_name = $_POST['driver_name'];
    $bus_route = $_POST['bus_route'];
    $total_distance = $_POST['total_distance'];

    $sql = "INSERT INTO historicaldata (bus_number, maintenance_type, date, driver_name, bus_route, total_distance, status)
            VALUES ('$bus_number', '$maintenance_type', '$date', '$driver_name', '$bus_route', '$total_distance', 'Completed')";

    if ($conn->query($sql) === TRUE) {
        echo "Maintenance record successfully saved.";
    } else {
        echo "Error saving data: " . $conn->error;
    }
    $conn->close();
}
?>
