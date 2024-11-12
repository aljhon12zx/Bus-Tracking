<?php
include("connect_db.php");

$driver_id = $_POST['driver_id'];
$action = $_POST['action'];

// Get the bus_number of the driver being updated
$query = "SELECT bus_number FROM buses WHERE driver_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$bus_number = $row['bus_number'];

if ($action === 'disable') {
    // Enable all other buses with the same bus_number
    $query = "UPDATE buses SET account_status = 'active' WHERE bus_number = ? AND driver_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $bus_number, $driver_id);
    $stmt->execute();

    // Disable only the selected driver in buses table
    $query = "UPDATE buses SET account_status = 'disabled' WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();

    // Update status in the driver table
    $query = "UPDATE driver SET account_status = 'disabled' WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();

    $response = [
        'success' => true,
        'message' => 'Only one bus entry for this bus number has been disabled, and the driver status has been updated.'
    ];
} elseif ($action === 'enable') {
    // Enable the specific driver in buses table
    $query = "UPDATE buses SET account_status = 'active' WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();

    // Update status in the driver table
    $query = "UPDATE driver SET account_status = 'active' WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();

    $response = [
        'success' => true,
        'message' => 'The driver and associated bus have been enabled.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid action.'
    ];
}

echo json_encode($response);
?>
