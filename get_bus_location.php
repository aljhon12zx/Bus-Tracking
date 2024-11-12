<?php
// get_bus_location.php
include 'db_connection.php'; // Include your database connection

$bus_number = $_GET['bus_number']; // Get the bus number from the query parameter

// Query to fetch the latest location
$sql = "SELECT origin_latitude, origin_longitude FROM driver WHERE bus_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $bus_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row); // Return the latitude and longitude as a JSON response
} else {
    echo json_encode(['origin_latitude' => null, 'origin_longitude' => null]);
}

$stmt->close();
$conn->close();
?>
