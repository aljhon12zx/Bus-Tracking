<?php
include("connect_db.php");

if (isset($_POST["submit"])) {
    // Retrieve form data and sanitize it
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $phone_number = mysqli_real_escape_string($conn, $_POST["phone_number"]);
    $bus_number = mysqli_real_escape_string($conn, $_POST["bus_number"]);
    $license_number = mysqli_real_escape_string($conn, $_POST["license_number"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $user_pass = $_POST["user_pass"]; // Don't escape the password, we'll hash it later
    $confirmpassword = $_POST["confirmpassword"];

    // Check if passwords match
    if ($user_pass === $confirmpassword) {
        // Hash the password
        $hashed_pass = password_hash($user_pass, PASSWORD_DEFAULT);

        // Prepare an SQL statement to insert into the driver table
        $stmt = $conn->prepare("INSERT INTO driver (username, phone_number, bus_number, license_number, driver_name, user_address, user_pass) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("MySQL Error: " . $conn->error);
        }
        
        $stmt->bind_param("sssssss", $username, $phone_number, $bus_number, $license_number, $name, $address, $hashed_pass);

        // Execute the statement
        if ($stmt->execute()) {
            // Now insert into the buses table
            $bus_stmt = $conn->prepare("INSERT INTO buses (bus_number, driver_name) VALUES (?, ?)");
            if ($bus_stmt === false) {
                die("MySQL Error: " . $conn->error);
            }
            $bus_stmt->bind_param("ss", $bus_number, $name);

            // Execute the bus insert statement
            if ($bus_stmt->execute()) {
                echo "<script>alert('Create Account Successful.'); window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Driver created, but error adding bus.');</script>";
            }

            // Close the bus statement
            $bus_stmt->close();
        } else {
            echo "<script>alert('Error: Could not save driver data.');</script>";
        }

        // Close the driver statement
        $stmt->close();
    } else {
        echo "<script>alert('Passwords do not match.');</script>";
    }
}
?>

<!-- The rest of your HTML code remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .registration-container {
            max-width: 600px;
            width: 100%;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #9b59b6;
            border-color: #9b59b6;
            border-radius: 20px;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #8e44ad;
            border-color: #8e44ad;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="registration-container">
    <form action="" method="POST" id="registrationForm">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div clas="form-group col-md-6">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </sdiv>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="bus_number">Bus Number</label>
                <input type="text" id="bus_number" name="bus_number" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-control" required pattern="[0-9]{10}">
                <small class="form-text text-muted">Enter a 10-digit phone number.</small>
            </div>
            <div class="form-group col-md-6">
                <label for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="user_pass">Password</label>
                <input type="password" id="user_pass" name="user_pass" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="confirmpassword">Confirm Password</label>
                <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" required>
            </div>
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
        <div class="form-group mt-3 text-center">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        const user_pass = document.getElementById('user_pass').value;
        const confirmpassword = document.getElementById('confirmpassword').value;

        if (user_pass !== confirmpassword) {
            event.preventDefault();
            alert('Passwords do not match.');
        }
    });
</script>

</body>
</html>
