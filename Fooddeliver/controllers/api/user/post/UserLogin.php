<?php
session_start();

// CORS headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header('Content-Type: application/json'); // Set the response type to JSON

// Handle pre-flight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Pre-flight request, no need to process further
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// User login function
function userLogin($Username, $Password, $conn) {
    // Prepare the SQL query to fetch user
    $query = "SELECT * FROM adduser WHERE Username = ? AND Password = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        die(json_encode(["error" => "Preparation failed: " . mysqli_error($conn)]));
    }

    // Bind parameters to the query
    mysqli_stmt_bind_param($stmt, 'ss', $Username, $Password);
    
    // Execute the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Successful login, set session
        $_SESSION['loggedin_user'] = $Username;
        echo json_encode(["message" => "Login successful"]);
    } else {
        echo json_encode(["error" => "Invalid username or password"]);
    }
    
    mysqli_stmt_close($stmt);
}

// Call the login function with the POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['Username']) && isset($data['Password'])) {
        userLogin($data['Username'], $data['Password'], $conn);
    } else {
        echo json_encode(["error" => "Username and Password are required"]);
    }
}
?>
