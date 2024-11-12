<?php
include("connect_db.php");

// Initialize count for moving buses
$movingBuses = 0;

// Fetch bus data from the database
$query = "SELECT * FROM driver";
$result = mysqli_query($conn, $query);

// Check for buses with changed coordinates
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['origin_latitude'] !== $row['previous_latitude'] || $row['origin_longitude'] !== $row['previous_longitude']) {
        $movingBuses++;

        // Update previous coordinates to current ones
        $updateQuery = "UPDATE driver 
                        SET previous_latitude = '{$row['origin_latitude']}', previous_longitude = '{$row['origin_longitude']}'
                        WHERE bus_id = '{$row['bus_id']}'";
        mysqli_query($conn, $updateQuery);
    }
}

// Return the count of moving buses as JSON
echo json_encode(['movingBuses' => $movingBuses]);
?>
