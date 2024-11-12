<?php
include("connect_db.php");

// Retrieve driver data
$sql = "SELECT driver_id, driver_name, bus_number, shift, bus_route FROM driver"; // Adjust SQL query if needed
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
 
// Return driver data as JSON
echo json_encode($data);

// Close the database connection
$conn->close();
?>
