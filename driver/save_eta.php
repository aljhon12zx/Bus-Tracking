<?php
include("connect_db.php");

if (isset($_POST['bus_number']) && isset($_POST['eta'])) {
    $bus_number = mysqli_real_escape_string($conn, $_POST['bus_number']);
    $eta = mysqli_real_escape_string($conn, $_POST['eta']);

    $query = "UPDATE buses SET estimated_arrival_time = '$eta' WHERE bus_number = '$bus_number'";
    mysqli_query($conn, $query);
}
?>
