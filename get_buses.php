<?php
// Include your database connection file
include('connect_db.php');

// Prepare the SQL query to fetch all buses and their associated drivers
$query = "SELECT buses.bus_id, buses.bus_number, drivers.driver_name AS driver_name, driver.is_connected
          FROM buses
          LEFT JOIN driver ON buses.bus_id = driver.bus_id";

$result = $conn->query($query);

// Create an array to store the buses and driver data
$buses = [];

if ($result->num_rows > 0) {
  // Loop through the result set and append each bus with driver details to the array
  while ($row = $result->fetch_assoc()) {
    $buses[] = $row;
  }
}


// Close the database connection
$conn->close();

// Return the buses data as JSON
header('Content-Type: application/json');
echo json_encode($buses);

?>
