<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch average speed for a bus
$bus_id = 1;  // Change this to the bus ID you want to analyze
$sql = "SELECT AVG(speed) AS avg_speed FROM bus_data WHERE bus_id = '$bus_id'";
$result = $conn->query($sql);

$avgSpeed = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $avgSpeed['avg_speed'] = $row["avg_speed"];
    }
}

echo json_encode($avgSpeed);

$conn->close();
?>
