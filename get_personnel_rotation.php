<?php
include("connect_db.php");

// Prepare the SELECT statement to retrieve personnel rotation data from the driver table
$stmt = $conn->prepare("SELECT driver_id, shift, bus_route FROM driver");

if ($stmt) {
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch the driver data and build the table rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['shift']}</td>
                    <td>{$row['bus_route']}</td>
                    <td><button class='btn btn-primary edit-button' data-id='{$row['driver_id']}'>Edit</button></td>
                  </tr>";
        }
    } else {
        // Return a message if no drivers are found
        echo "<tr><td colspan='4'>No personnel rotation data available.</td></tr>";
    }
    
    // Close the statement
    $stmt->close();
} else {
    // Return an error if statement preparation fails
    echo "<tr><td colspan='4'>Error preparing statement: " . $conn->error . "</td></tr>";
}

// Close the database connection
$conn->close();
?>
