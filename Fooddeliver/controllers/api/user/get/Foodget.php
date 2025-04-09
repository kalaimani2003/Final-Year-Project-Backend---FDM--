<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173"); // Allow the specific React app domain
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Allow methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow headers

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Function to fetch all food items
function Userfood() {
    global $conn;

    // SQL query to fetch food items
    $query = "SELECT id, FoodName, Price, Description, Image FROM addfood";
    $stmt = mysqli_prepare($conn, $query);  // Prepare the SQL statement
    mysqli_stmt_execute($stmt);  // Execute the query

    if (mysqli_stmt_errno($stmt)) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
        exit();
    }

    $result = mysqli_stmt_get_result($stmt);  // Get the result set
    $foodItems = mysqli_fetch_all($result, MYSQLI_ASSOC);  // Fetch all rows as associative array

    // Add full path to the image for each food item
    foreach ($foodItems as &$item) {
        if (!empty($item['Image'])) {
            // If image is not empty, append the image base URL
            $item['Image'] = 'http://localhost/Fooddeliver/Fooddeliver/controllers/api/admin/post/Uploads/' . basename($item['Image']);
        } else {
            // If image is empty, set an empty string
            $item['Image'] = ''; 
        }
    }

    echo json_encode($foodItems);  // Return the food items as a JSON response
    exit();
}

// Call the function to fetch food items
Userfood();
?>
