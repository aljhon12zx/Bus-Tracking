<?php
include("connect_db.php"); // Your database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bus_number = isset($_POST['bus_number']) ? $_POST['bus_number'] : null;
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    $total_distance = isset($_POST['total_distance']) ? $_POST['total_distance'] : null;

    if ($bus_number && $latitude && $longitude) {
        // Update the driver's location in the database
        $query = "UPDATE driver SET origin_latitude = ?, origin_longitude = ? WHERE bus_number = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "dds", $latitude, $longitude, $bus_number);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Location updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Location update failed: ' . mysqli_error($conn)]);
        }

        // Update total distance in the buses table
        if ($total_distance !== null) {
            // Get the existing distance first
            $distance_query = "SELECT total_distance_travelled_km FROM buses WHERE bus_number = ?";
            $distance_stmt = mysqli_prepare($conn, $distance_query);
            mysqli_stmt_bind_param($distance_stmt, "s", $bus_number);
            mysqli_stmt_execute($distance_stmt);
            $distance_result = mysqli_stmt_get_result($distance_stmt);
            $row = mysqli_fetch_assoc($distance_result);
            $existing_distance = $row ? (float)$row['total_distance_travelled_km'] : 0;

            // Calculate new total distance
            $new_total_distance = $existing_distance + $total_distance;

            $distance_update_query = "UPDATE buses SET total_distance_travelled_km = ? WHERE bus_number = ?";
            $distance_update_stmt = mysqli_prepare($conn, $distance_update_query);
            mysqli_stmt_bind_param($distance_update_stmt, "ds", $new_total_distance, $bus_number);
            $distance_update_result = mysqli_stmt_execute($distance_update_stmt);

            if ($distance_update_result) {
                echo json_encode(['status' => 'success', 'message' => 'Distance updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Distance update failed: ' . mysqli_error($conn)]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
