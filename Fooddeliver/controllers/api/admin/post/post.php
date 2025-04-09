<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

include_once '../../../../models/post.php';
include_once '../../../../config/header.php';

// Function to handle errors and send a JSON response
function handleError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError(405, 'Method Not Allowed');
}

// Validate that required fields are present
if (empty($_POST['FoodName']) || empty($_POST['Price']) || empty($_POST['Description'])) {
    handleError(400, 'FoodName, Price, and Description are required.');
}

// Extract form data
$FoodName = $_POST['FoodName'];
$Price = $_POST['Price'];
$Description = $_POST['Description'];

// Handle file upload
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size']; // Image size in bytes
    $allowedExtensions = ['jpg', 'jpeg', 'webp', 'png','avif'];

    // Check image size (should be less than 300KB)
    if ($imageSize > 300 * 1024) { // 300KB = 300 * 1024 bytes
        handleError(400, 'Image size must be less than 300KB.');
    }

    // Get the image extension
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    // Check if the image extension is allowed
    if (!in_array($imageExtension, $allowedExtensions)) {
        handleError(400, 'Invalid image format. Allowed formats are jpg, jpeg, webp, png.');
    }

    // Set the upload directory
    $uploadDir = 'Uploads/';

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Set the path where the image will be stored
    $imagePath = $uploadDir . basename($imageName);

    // Move the uploaded file to the desired directory
    if (!move_uploaded_file($imageTmpName, $imagePath)) {
        handleError(500, 'Failed to upload image');
    }
}

// Create an instance of the Post class
$obj = new Post();

// Insert data into the database
$result = $obj->A_InsertFood($FoodName, $Price, $Description, $imagePath);

// Check if insertion was successful
if ($result !== "Food Item Added Successfully") {
    handleError(500, 'Failed to insert data: ' . $result);
}

// Send success message
echo json_encode(['message' => 'Food item added successfully']);
?>
