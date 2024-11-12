<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bar</title>
    <style>
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
        }
        input {
            padding: 10px;
            margin: 5px;
            width: 200px;
        }
        button {
            padding: 10px;
            margin: 0 10px; /* Adjust margins */
        }
        .arrow-button {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px; /* Match height with input fields */
        }
    </style>
</head>
<body>

    <div class="search-container">
        <input type="text" id="origin" placeholder="Origin" />
        <div class="arrow-button">
            <button onclick="swapLocations()">↔️</button>
        </div>
        <input type="text" id="destination" placeholder="Destination" />
        <button onclick="searchRoute()">Search</button>
    </div>

<script>
    function swapLocations() {
        const originInput = document.getElementById('origin');
        const destinationInput = document.getElementById('destination');
        const temp = originInput.value;
        originInput.value = destinationInput.value;
        destinationInput.value = temp;
    }

    function searchRoute() {
        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        if (origin && destination) {
            alert(`Searching route from ${origin} to ${destination}`);
            // Add your search logic here
        } else {
            alert('Please enter both origin and destination.');
        }
    }
</script>

</body>
</html>
