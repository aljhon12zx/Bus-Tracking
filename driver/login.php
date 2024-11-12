<?php
include("connect_db.php");
session_start(); // Start the session

if (isset($_POST["Login"])) {
    // Sanitize the input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $user_pass = mysqli_real_escape_string($conn, $_POST["password"]);
    $bus_number = mysqli_real_escape_string($conn, $_POST["bus_number"]);

    // Prepare the SQL statement to fetch user data based on the username and bus number
    $stmt = $conn->prepare("SELECT user_pass, account_status FROM driver WHERE username = ? AND bus_number = ?");
    $stmt->bind_param("ss", $username, $bus_number);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows == 1) {
        // Bind the result variables
        $stmt->bind_result($hashed_pass, $account_status);
        $stmt->fetch();
        
        // Check if the account is disabled
        if ($account_status === 'disabled') {
            $error_message = "Your account has been disabled. Please contact the admin.";
        } else {
            // Verify the password
            if (password_verify($user_pass, $hashed_pass)) {
                // Store the bus number and username in the session
                $_SESSION['bus_number'] = $bus_number;
                $_SESSION['username'] = $username;
                // Redirect to the index page
                echo "<script>window.location='index.php';</script>";
            } else {
                $error_message = "Invalid username, password, or bus number.";
            }
        }
    } else {
        $error_message = "Invalid username, password, or bus number.";
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h2 {
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
            font-weight: bold;
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

<div class="login-container">
    <div class="login-header">
        <h2>Login</h2>
    </div>
    <form action="" method="POST">
        <div class="form-group">
            <label for="bus_number">Bus Number</label>
            <input type="text" id="bus_number" name="bus_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="form-group error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
        
        <button type="submit" name="Login" class="btn btn-primary btn-block">Login</button>
        <div class="form-group mt-3 text-center">
            <p>Don't have an account? <a href="create_account.php">Create one here</a>.</p>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
