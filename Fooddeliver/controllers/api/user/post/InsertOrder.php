<?php
// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// Get JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON data"]);
    exit;
}

// Validate received data
if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

// Extract data
$username = $data['username'] ?? '';
$address = $data['address'] ?? '';
$phone = $data['phone'] ?? '';
$orderNames = $data['orderNames'] ?? '';
$orderQuantities = $data['orderQuantities'] ?? '';
$totalAmount = $data['totalAmount'] ?? 0;
$date = $data['date'] ?? date('Y-m-d'); // Use current date if not provided

// SQL query to insert data into orders table
$sql = "INSERT INTO placeorder (username, address, phone, orderNames, orderQuantities, totalAmount, date) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssis", $username, $address, $phone, $orderNames, $orderQuantities, $totalAmount, $date);

if ($stmt->execute()) {
    echo json_encode("Order placed successfully");
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
