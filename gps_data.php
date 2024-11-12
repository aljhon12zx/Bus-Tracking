<?php
header('Content-Type: application/json');

// Sample GPS data
$gps_data = array(
    "latitude" => 14.5995,
    "longitude" => 120.9842
);

echo json_encode($gps_data);
?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Save latitude and longitude in a JSON file
    file_put_contents('gps_data.json', json_encode(['latitude' => $latitude, 'longitude' => $longitude]));
    echo json_encode(['status' => 'success', 'message' => 'GPS data received']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Serve the latest GPS data
    if (file_exists('gps_data.json')) {
        echo file_get_contents('gps_data.json');
    } else {
        echo json_encode(['latitude' => '0', 'longitude' => '0']);
    }
}
?>