<?php
include("connect_db.php");
session_start();

if (isset($_POST['bus_number']) && isset($_POST['reason'])) {
    $bus_number = $_POST['bus_number'];
    $reason = $_POST['reason'];

    // Log or handle the reason for route change as needed
    // Example: Insert into a log table or update bus route
    $query = "INSERT INTO route_changes (bus_number, reason) VALUES ('$bus_number', '$reason')";
    mysqli_query($conn, $query);
    echo "Route change logged for bus number: " . $bus_number;
} else {
    echo "Invalid request.";
}
?>
