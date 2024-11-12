<?php
include("connect_db.php");

if (isset($_POST["submit"])) {
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $user_pass = mysqli_real_escape_string($conn, $_POST["user_pass"]);
    $confirmpassword = mysqli_real_escape_string($conn, $_POST["confirmpassword"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $contact_number = mysqli_real_escape_string($conn, $_POST["contact_number"]);

    if ($user_pass === $confirmpassword) {
        $query = "INSERT INTO admin (first_name, last_name, username, email, user_pass, address, contact_number) VALUES ('$first_name', '$last_name', '$username', '$email', '$user_pass', '$address', '$contact_number')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Create Account Successful.'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not save data.');</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match.');</script>";
    }
}
?>

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
        .registration-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .registration-header h2 {
            font-weight: 700;
            color: #333;
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
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .form-group a {
            color: #9b59b6;
            font-weight: bold;
        }
        .form-group a:hover {
            color: #8e44ad;
            text-decoration: underline;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="registration-container">
    <div class="registration-header">
        <h2>Create Account</h2>
    </div>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required tabindex="1">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required tabindex="5">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required tabindex="7">
                </div>
                <div class="form-group">
                    <label for="user_pass">Password</label>
                    <input type="password" id="user_pass" name="user_pass" class="form-control" required tabindex="9">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required tabindex="2">
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control" required tabindex="6">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required tabindex="8">
                </div>
                <div class="form-group">
                    <label for="confirmpassword">Confirm Password</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" required tabindex="10">
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-primary btn-block" tabindex="11">Register</button>

        <div class="form-group mt-3 text-center">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </form>
    <p id="signupErrorMessage" class="error-message"></p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const user_pass = document.getElementById('user_pass').value;
        const confirmpassword = document.getElementById('confirmpassword').value;

        if (user_pass !== confirmpassword) {
            event.preventDefault();
            document.getElementById('signupErrorMessage').textContent = 'Passwords do not match.';
        }
    });
</script>

</body>
</html>




