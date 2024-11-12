<?php
include("connect_db.php");

// Fetch admin data from the admin table
$adminId = 1; // Change this to the logged-in admin's ID
$sql = "SELECT first_name, last_name, email, profile_image FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Handle the case where no admin was found
    $admin = [
        'first_name' => 'N/A',
        'last_name' => 'N/A',
        'email' => 'N/A',
        'profile_image' => 'path/to/default/image.jpg' // Placeholder image
    ];
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update admin data
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];

    // Handle file upload
    $targetFile = $admin['profile_image']; // Keep the old image as default
    if (isset($_FILES['profileImageUpload']) && $_FILES['profileImageUpload']['error'] == 0) {
        $targetDir = "images/"; // Ensure this directory exists
        $fileName = basename($_FILES["profileImageUpload"]["name"]);
        $targetFile = $targetDir . uniqid() . '-' . $fileName; // Prevent file name collisions

        if (!move_uploaded_file($_FILES["profileImageUpload"]["tmp_name"], $targetFile)) {
            echo "Error uploading file.";
        }
    }

    $updateSql = "UPDATE admin SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE admin_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssi", $firstName, $lastName, $email, $targetFile, $adminId);
    $updateStmt->execute();

    // Check for update errors
    if ($updateStmt->error) {
        echo "Error updating record: " . $updateStmt->error;
    } else {
        echo "Profile updated successfully!";
    }

    // Refresh admin data
    $stmt->execute(); // Re-execute the original statement
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Bus Tracking System</title>

    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
         /* Styling the notification icon */
         .notification {
            position: relative;
            display: inline-block;
            cursor: pointer;
            font-size: small;
            margin-right: 20px;
            margin-top: 5px;
        }
        .notification .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            padding: 3px 8px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 8px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bus Tracking System Using GPS with Data Analytics</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
           <!-- Notification Icon -->
    <div class="notification" style="cursor: pointer;">
        <span class="badge"></span> <!-- Notification count will be added here -->
        <i class="fas fa-bell fa-2x"></i>
    </div>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="personel.php">Personnel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="status.php">Status</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bars"></i> Menu
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="profile.php">Profile</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="mt-5">Admin Profile</h1>

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="text-center">
            <!-- Profile Image -->
            <img src="<?php echo htmlspecialchars($admin['profile_image']); ?>" id="profileImage" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
            <h2 class="mt-3" id="profileName"><?php echo htmlspecialchars($admin['first_name']) . ' ' . htmlspecialchars($admin['last_name']); ?></h2>
            <p id="profileEmail"><?php echo htmlspecialchars($admin['email']); ?></p>
        </div>
        <form id="profileForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profileImageUpload">Profile Image:</label>
                <input type="file" class="form-control-file" name="profileImageUpload" id="profileImageUpload">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="notificationList" class="list-group">
                    <!-- Notification items will be injected here -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="footer">
        <p>&copy; 2024 Bus Tracking System Using GPS With Data Analytics. All Rights Reserved.</p>
    </div>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    $(document).ready(function() {
        // Sample notifications array - replace this with your actual notification data
        const notifications = [
            "Bus 9387 is on route to Pagadian.",
            "Bus 2345 has completed its trip.",
            "Bus 6789 is currently delayed due to traffic.",
        ];

        // Update the notification badge count
        $('.notification .badge').text(notifications.length);

        // Handle notification icon click
        $('.notification').on('click', function() {
            // Clear previous notifications
            $('#notificationList').empty();
            
            // Populate the modal with notifications
            notifications.forEach(notification => {
                $('#notificationList').append(`<li class="list-group-item">${notification}</li>`);
            });

            // Show the notification modal
            $('#notificationModal').modal('show');
        });
    });
</script>
</html>
