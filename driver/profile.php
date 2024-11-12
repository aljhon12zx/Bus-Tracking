<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Bus Tracking System</title>

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
                <li class="nav-item active">
                  <!-- Notification Icon -->
     <div class="notification" style="cursor: pointer;">
        <span class="badge"></span> <!-- Notification count will be added here -->
        <i class="fas fa-bell fa-2x"></i>
    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
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
    <h1 class="mt-5">User Profile</h1>

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="text-center">
            <img src="images/aljhon.jpg" id="profileImage" alt="Profile Picture">
            <h2 class="mt-3" id="profileName">Aljhon Caangay</h2>
            <p id="profileEmail">aljhoncaangay@gmail.com</p>
        </div>
        <form id="profileForm">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" value="Aljhon Caangay">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" value="aljhoncangay@gmail.com">
            </div>
            <div class="form-group">
                <label for="profileImageUpload">Profile Image:</label>
                <input type="file" class="form-control-file" id="profileImageUpload">
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

<script>
      $(document).ready(function() {
        // Sample notifications array - replace this with your actual notification data
        const notifications = [
            "Bus 9387 is on route to Pagadian.",
            "Bus 2345 has completed its trip.",
            "Bus 6789 is currently delayed due to traffic."
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
    // Handle profile form submission
    $('#profileForm').on('submit', function(event) {
        event.preventDefault();

        var name = $('#name').val();
        var email = $('#email').val();
        var profileImage = $('#profileImageUpload')[0].files[0];

        // Here you would typically send the data to the server using AJAX
        // For demonstration, we'll just log the data
        console.log('Name:', name);
        console.log('Email:', email);
        console.log('Profile Image:', profileImage);

        // Update the profile display
        $('#profileName').text(name);
        $('#profileEmail').text(email);

        // Handle profile image update
        if (profileImage) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImage').attr('src', e.target.result);
            };
            reader.readAsDataURL(profileImage);
        }
    });
</script>

</body>
</html>
