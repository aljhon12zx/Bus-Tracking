<?php
include('connect_db.php');

if (isset($_GET['bus_number'])) {
    $bus_number = $_GET['bus_number'];

    // Update the maintenance status to completed
    $update_sql = "UPDATE maintenance SET status = 'completed' WHERE bus_number = '$bus_number' AND status = 'pending'";
    if ($conn->query($update_sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
