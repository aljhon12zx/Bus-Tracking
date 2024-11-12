
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bus Location</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add New Bus Location</h1>
    <form action="add_bus.php" method="POST">
        <label for="bus_name">Bus Name:</label>
        <input type="text" id="bus_name" name="bus_name" required><br><br>

        <label for="location">Location (Lat,Lng):</label>
        <input type="text" id="location" name="location" required><br><br>

        <button type="submit">Add Bus</button>
    </form>

    <p><a href="index.php">Back to Map</a></p>
</body>
</html>
