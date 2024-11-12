<?php
include("connect_db.php");



$bus_number = $_GET['bus_number'];

$query = "SELECT origin_latitude, origin_longitude FROM driver WHERE bus_number = '$bus_number'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode([]);
}

?>
