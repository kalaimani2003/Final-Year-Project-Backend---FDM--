<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "food";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch orders
$sql = "SELECT * FROM placeorder";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'items' => $row['orderNames'],
            'quantity' => $row['orderQuantities'],
            'totalAmount' => $row['totalAmount'],
            'status' => $row['status']
        ];
    }
    echo json_encode($orders);
} else {
    echo json_encode([]);
}

$conn->close();
?>
