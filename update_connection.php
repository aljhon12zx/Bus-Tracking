<?php
include('connect_db.php');

$busId = $_POST['busId'];
$isConnected = $_POST['isConnected'];

// Update the driver's connection status based on bus ID
$query = "UPDATE drivers SET is_connected = ? WHERE bus_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $isConnected, $busId);

$response = [];

if ($stmt->execute()) {
  $response['success'] = true;
} else {
  $response['success'] = false;
}

$stmt->close();
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
