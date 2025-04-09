<?php
header("Access-Control-Allow-Origin: http://localhost:5173"); // Allow the specific origin
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header("Access-Control-Allow-Credentials: true"); // If credentials (cookies) are involved

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

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['orderId']) && isset($data['status'])) {
    $orderId = $data['orderId'];
    $status = $data['status'];

    // Update order status to 'Delivered'
    $sql = "UPDATE placeorder SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $orderId);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Order status updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating order status"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid data"]);
}

$conn->close();
?>
