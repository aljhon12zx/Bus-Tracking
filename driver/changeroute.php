<?php
include("connect_db.php");
session_start(); // Start the session

// Check if a session exists and the username is set
if (!isset($_SESSION['username'])) {
    echo "Please log in first.";
    exit; // Stop further execution if the user is not logged in
}

$username = $_SESSION['username']; // Get the logged-in user's username
$data = json_decode(file_get_contents("php://input"), true);

// Get data from the request
$reasons = isset($data['reasons']) ? implode(", ", $data['reasons']) : '';

// Query to fetch bus_number, origin_latitude, and origin_longitude from the driver table
$select_query = "SELECT bus_number, origin_latitude, origin_longitude FROM driver WHERE username = ?";
$stmt = $conn->prepare($select_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($bus_number, $origin_latitude, $origin_longitude);
$stmt->fetch();
$stmt->close();

// Check if the necessary data were found
if ($bus_number === null || $origin_latitude === null || $origin_longitude === null) {
    echo json_encode(["success" => false, "message" => "Driver information not found."]);
    exit;
}

// Insert data into the changeroute table with the retrieved username, bus_number, latitude, and longitude
$insert_query = "INSERT INTO changeroute (username, bus_number, rason, avoidance_latitude, avoidance_longitude) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("sssss", $username, $bus_number, $reasons, $origin_latitude, $origin_longitude);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Route change saved successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save route change."]);
}

$stmt->close();
$conn->close();
?>
