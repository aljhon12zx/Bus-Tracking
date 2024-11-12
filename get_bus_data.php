<?php
include("connect_db.php");

$query = "SELECT bus_number, latitude, longitude FROM buses";
$result = mysqli_query($conn, $query);

$buses = [];

while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;
}

echo json_encode($buses);
?>
