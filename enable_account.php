<?php
include("connect_db.php");

if (isset($_GET['driver_id'])) {
    $driver_id = $_GET['driver_id'];

    // Update the driver's account status to 'active'
    $query = "UPDATE driver SET account_status = 'active' WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();

    // Redirect back to the account management page
    header("Location: account_management.php");
    exit();
}
?>
