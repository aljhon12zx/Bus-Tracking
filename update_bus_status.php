<?php
include("connect_db.php");

// Check if bus_number and action are set in the request
if (isset($_POST['bus_number']) && isset($_POST['action'])) {
    $busNumber = mysqli_real_escape_string($conn, $_POST['bus_number']);
    $action = $_POST['action']; // 'enable' or 'disable'

    // Determine the new status based on the action
    $newStatus = ($action === 'enable') ? 1 : 0; // 1 for enabled, 0 for disabled

    // Update the bus status in the database
    $query = "UPDATE buses SET status = '$newStatus' WHERE bus_number = '$busNumber'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Bus status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update bus status: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Bus number or action not provided.']);
}

// Close the database connection
mysqli_close($conn);
?>
