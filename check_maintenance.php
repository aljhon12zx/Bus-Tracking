<?php
include 'connect_db.php';

// Query to fetch current distance of each bus and maintenance thresholds
$sql = "
    SELECT b.bus_number, b.total_distance_travelled_km, m.maintenance_type, m.distance_threshold, m.notification_message
    FROM buses AS b
    JOIN maintenance_thresholds AS m ON b.bus_number = m.bus_number
    WHERE b.total_distance_travelled_km >= m.distance_threshold
";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    // Check if a notification for this type and bus number already exists
    $checkSql = "
        SELECT id FROM notifications 
        WHERE bus_number = ? 
        AND maintenance_type = ? 
        AND is_read = 0
    ";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $row['bus_number'], $row['maintenance_type']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        // Insert a new notification if one doesn't already exist
        $insertSql = "
            INSERT INTO notifications (message, bus_number, driver_name, bus_route, maintenance_type, total_distance, type, is_read)
            VALUES (?, ?, 'Driver Name', 'Route', ?, ?, 'maintenance', 0)
        ";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ssss", $row['notification_message'], $row['bus_number'], $row['maintenance_type'], $row['total_distance_travelled_km']);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();
}

$conn->close();
?>
