// Wait for the document to fully load
$(document).ready(function () {
    // Initial rendering of notifications
    renderNotifications();

    // Handle notification icon click to show the modal
    $('.notification').on('click', function () {
        $('#notificationModal').modal('show');
    });
});

// Function to render notifications list
function renderNotifications() {
    $('#notificationList').empty(); // Clear existing notifications

    notifications.forEach((notification, index) => {
        const notificationItem = $('<li class="list-group-item"></li>')
            .text(notification.message)
            .data('index', index)
            .on('click', function () {
                goToStatusSection(notification, index); // Pass index for removal
            });

        $('#notificationList').append(notificationItem); // Add notification item to the list
    });

    // Update the notification badge count
    $('.notification .badge').text(notifications.length);
}

// Function to handle clicking on a notification item
function goToStatusSection(notification, index) {
    const maintenanceTableBody = document.getElementById('maintenanceData');

    // Add a row with detailed information in the maintenance section
    const row = `
        <tr id="maintenanceRow-${notification.bus_number}">
            <td>${new Date().toLocaleDateString()}</td>
            <td>${notification.bus_number}</td>
            <td>${notification.driver_name}</td>
            <td>${notification.bus_route}</td>
            <td>${notification.maintenance_type}</td>
            <td>${notification.total_distance}</td>
            <td><button class="btn btn-success btn-sm" onclick="markAsCompleted(${JSON.stringify(notification)}, ${index})">Complete</button></td>
        </tr>`;

    maintenanceTableBody.innerHTML += row;

    $('#notificationModal').modal('hide'); // Close the modal
    window.location.href = '#status'; // Scroll to status section
}

// Function to mark maintenance as completed
function markAsCompleted(notification, index) {
    const currentDate = new Date().toLocaleDateString();

    // Remove the row from Maintenance section
    const maintenanceRow = document.getElementById(`maintenanceRow-${notification.bus_number}`);
    if (maintenanceRow) {
        maintenanceRow.remove();
    }



    // Send AJAX request to save the maintenance record in the database
    $.post("save_maintenance.php", {
        bus_number: notification.bus_number,
        maintenance_type: notification.maintenance_type,
        date: currentDate,
        driver_name: notification.driver_name,
        bus_route: notification.bus_route,
        total_distance: notification.total_distance
    })
    .done(function (response) {
        // Show server response as confirmation
        alert(response);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX call failed:", textStatus, errorThrown);
        alert("Failed to save maintenance data.");
    });

    // Remove the notification from the array and update the notification list
    notifications.splice(index, 1);
    renderNotifications();
}
