<?php
include("connect_db.php");

if (isset($_POST['id'])) {
    $busId = $_POST['id'];
    $query = "SELECT * FROM buses WHERE id = $busId";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $bus = mysqli_fetch_assoc($result);
        echo json_encode($bus); // Return the bus data as JSON
    } else {
        echo json_encode(["error" => "Bus not found"]);
    }
}
?>
