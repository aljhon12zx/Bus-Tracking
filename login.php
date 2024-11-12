<?php
include("connect_db.php");

if (isset($_POST["Login"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $user_pass = mysqli_real_escape_string($conn, $_POST["password"]);

    // Query to check if the username and password match
    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username' AND user_pass = '$user_pass'");

    if (mysqli_num_rows($query) == 1) {
        echo "<script>window.location='index.php';</script>";
    } else {
        $error_message = "Check username and/or password.";
    }
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
