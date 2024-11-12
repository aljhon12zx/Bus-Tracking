<?php
    # Obtain the JSON payload from an OwnTracks app POSTed via HTTP
    # and insert into database table.

    header("Content-type: application/json");

    $payload = file_get_contents("php://input");
    $data =  @json_decode($payload, true);

    if ($data['_type'] == 'location') {

        # CREATE TABLE locations (dt TIMESTAMP, tid CHAR(2), lat DECIMAL(9,6), lon DECIMAL(9,6));
        $mysqli = new mysqli("127.0.0.1", "user", "password", "bus-tracking");


        $tst = $data['tst'];
        $lat = $data['lat'];
        $lon = $data['lon'];
        $tid = $data['tid'];

        # Convert timestamp to a format suitable for mysql
        $dt = date('Y-m-d H:i:s', $tst);

        $sql = "INSERT INTO locations (dt, tid, lat, lon) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        # bind parameters (s = string, i = integer, d = double,  b = blob)
        $stmt->bind_param('ssdd', $dt, $tid, $lat, $lon);
        $stmt->execute();
        $stmt->close();
    }

    $response = array();
    # optionally add objects to return to the app (e.g.
    # friends or cards)
    print json_encode($response);
?>
