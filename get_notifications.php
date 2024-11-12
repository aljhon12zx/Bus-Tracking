<?php
include 'db_connection.php';

$sql = "SELECT * FROM notifications WHERE is_read = 0";
$result = $conn->query($sql);

$notifications = array();

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);

$conn->close();
?>
