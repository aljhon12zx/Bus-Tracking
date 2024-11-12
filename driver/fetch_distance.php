<?php
header('Content-Type: application/json');

include("connect_db.php");

// Check if the bus number is set in the session
if (isset($_SESSION['bus_number'])) {
    $bus_number = $_SESSION['bus_number'];

    // Fetch the total distance for the logged-in bus number
    $sql = "SELECT total_distance_travelled_km FROM buses WHERE bus_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bus_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["total_distance_travelled_km" => $row['total_distance_travelled_km']]);
    } else {
        echo json_encode(["error" => "No distance data found for this bus."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Bus number not set in session."]);
}

$conn->close();
?>
