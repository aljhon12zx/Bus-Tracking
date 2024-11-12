<?php
include("connect_db.php");

// Check if driver_id is set in the GET request
if (isset($_GET['id'])) {
    $driver_id = $_GET['id'];

    // Prepare the SELECT statement
    $stmt = $conn->prepare("SELECT shift, bus_route, fare, origin_latitude, origin_longitude, destination_latitude, destination_longitude FROM driver WHERE driver_id = ?");
    
    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("i", $driver_id);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch the driver data as an associative array
            $driverData = $result->fetch_assoc();
            // Return the data in JSON format
            echo json_encode($driverData);
        } else {
            // Return an error if no driver found
            echo json_encode(["status" => "error", "message" => "No driver found with this ID."]);
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // Return an error if statement preparation fails
        echo json_encode(["status" => "error", "message" => "Error preparing statement: " . $conn->error]);
    }
} else {
    // Return an error if driver_id is not set
    echo json_encode(["status" => "error", "message" => "Invalid request: driver_id not provided."]);
}

// Close the database connection
$conn->close();
?>
