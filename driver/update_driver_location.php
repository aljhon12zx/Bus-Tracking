// update_driver_location.php
<?php
include("connect_db.php");
// Get lat and lng from request
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$driver_id = $_SESSION['driver_id']; // Use session to identify the driver

// Update the driver's location in the database
$sql = "UPDATE driver SET origin_latitude = '$lat', origin_longitude = '$lng', last_updated = NOW() WHERE driver_id = '$driver_id'";
$conn->query($sql);
?>
