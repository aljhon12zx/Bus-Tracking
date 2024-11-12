<?php
include 'db_connection.php';

// Retrieve data from a form submission or API request
$message = $_POST['message'];
$bus_number = $_POST['bus_number'];
$driver_name = $_POST['driver_name'];
$bus_route = $_POST['bus_route'];
$maintenance_type = $_POST['maintenance_type'];
$total_distance = $_POST['total_distance'];
$type = "maintenance";
$is_read = 0;

// SQL query to insert a new notification
$sql = "INSERT INTO notifications (message, bus_number, driver_name, bus_route, maintenance_type, total_distance, type, is_read)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $message, $bus_number, $driver_name, $bus_route, $maintenance_type, $total_distance, $type, $is_read);

if ($stmt->execute()) {
    echo "Notification added successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
