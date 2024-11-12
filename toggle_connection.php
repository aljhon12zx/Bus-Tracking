<?php
// Include the database connection file
include('connect_db.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $busId = filter_input(INPUT_POST, 'busId', FILTER_VALIDATE_INT);
    $isConnected = filter_input(INPUT_POST, 'isConnected', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    // Check if inputs are valid
    if ($busId !== false && $isConnected !== null) {
        // Prepare the SQL statement to update the connection status
        $query = "UPDATE buses SET is_connected = ? WHERE bus_id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            // Bind parameters: (1 = isConnected, 2 = busId)
            $stmt->bind_param('ii', $isConnected, $busId);
            
            // Execute the statement
            if ($stmt->execute()) {
                // Return a success response
                echo json_encode(['success' => true]);
            } else {
                // Return an error response if execution fails
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
            }
            $stmt->close(); // Close the prepared statement
        } else {
            // Return an error response if the statement preparation fails
            echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
        }
    } else {
        // Return an error response for invalid input
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    // Return an error response if the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close(); // Close the database connection
?>
