<?php
// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

// Check if 'username' is provided in the request body
if (!isset($input['username']) || empty($input['username'])) {
    echo json_encode(["error" => "Username is required"]);
    exit;
}

$username = $input['username'];

// Delete the user from the database
$sql = "DELETE FROM adduser WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    echo json_encode(["message" => "User deleted successfully"]);
} else {
    echo json_encode(["error" => "Failed to delete user"]);
}

$stmt->close();
$conn->close();
?>
