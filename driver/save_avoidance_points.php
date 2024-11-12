<?php
include 'connect_db.php';
session_start();

$data = json_decode(file_get_contents("php://input"));

// Verify received data
var_dump($data); // Debug data received in AJAX
$driver_id = $_SESSION['driver_id'];
$bus_id = $data->bus_id;

// Confirm driver_id and bus_id are set
if (!$driver_id || !$bus_id) {
    echo json_encode(['message' => 'Driver ID or Bus ID not found.']);
    exit;
}

// Fetch origin latitude and longitude from driver table
$query = "SELECT origin_latitude, origin_longitude FROM driver WHERE driver_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

if ($driver) {
    $avoidance_points = json_encode([
        'latitude' => $driver['origin_latitude'],
        'longitude' => $driver['origin_longitude']
    ]);

    $update_query = "UPDATE buses SET avoidance_points = ? WHERE bus_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $avoidance_points, $bus_id);

    if ($update_stmt->execute()) {
        echo json_encode(['message' => 'Route changed successfully!']);
    } else {
        echo json_encode(['message' => 'Failed to change route.', 'error' => $conn->error]);
    }
} else {
    echo json_encode(['message' => 'Driver information not found.']);
}
?>
