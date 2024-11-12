<?php
include("connect_db.php"); // Include your database connection
header('Content-Type: application/json'); // Set the content type to JSON

if (isset($_GET['bus_number'])) {
    $bus_number = $_GET['bus_number'];

    // Prepare and execute the SQL query to fetch the total distance for the bus
    $query = "SELECT total_distance_travelled_km FROM buses WHERE bus_number = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Check if the statement prepared successfully
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $bus_number);
        mysqli_stmt_execute($stmt);

        // Fetch the result
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Check if any result was found
        if ($row) {
            echo json_encode(['total_distance_travelled_km' => $row['total_distance_travelled_km']]);
        } else {
            // If no result, return 0 km as default
            echo json_encode(['total_distance_travelled_km' => 0]);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle query preparation error
        echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    }
} else {
    // Handle the case where bus_number is not provided
    echo json_encode(['error' => 'No bus number provided']);
}

// Close the database connection
mysqli_close($conn);
?>
