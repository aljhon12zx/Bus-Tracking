<?php
include("connect_db.php");

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to find routes with partial match
$query = "
    SELECT DISTINCT buses.bus_route
    FROM buses
    WHERE buses.bus_route LIKE '%$search%'
    LIMIT 10
";

$result = mysqli_query($conn, $query);

$routes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $routes[] = $row['bus_route'];
}

// Return as JSON
echo json_encode($routes);
?>
