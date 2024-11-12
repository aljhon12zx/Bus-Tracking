<?php
include("connect_db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_number = $_POST['bus_number'];
    $new_driver_name = $_POST['new_driver_name'];

    // Update the driver name in the database
    $sql = "UPDATE driver SET driver_name = ? WHERE driver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $new_driver_name, $bus_number);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Driver changed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to change driver']);
    }

    $stmt->close();
    $conn->close();
}
?>
