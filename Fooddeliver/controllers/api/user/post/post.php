<?php
// Define paths to required files
$modelsPath = '../../../../models/post.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist and include them
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    handleError(500, 'Required files are missing');
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function handleError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Validate input data
if (empty($data->Username) || empty($data->Email) || empty($data->Password) || empty($data->Confirmpassword) || empty($data->Address) || empty($data->Contact)) {
    handleError(400, 'All fields are required.');
}

// Validate email ID format
if (!filter_var($data->Email, FILTER_VALIDATE_EMAIL)) {
    handleError(400, 'Invalid email format.');
}

// Check if Password and Confirm Password match
if ($data->Password !== $data->Confirmpassword) {
    handleError(400, 'Password and Confirm Password do not match.');
}

// Create an instance of the Post class
$obj = new Post();

// Insert user data into the database
$result = $obj->InsertUser(
  
    $data->Username,
    $data->Email,
    $data->Password,
    $data->Confirmpassword,
    $data->Address,
    $data->Contact
);

// Check for errors during insertion
if ($result === false) {
    handleError(500, 'Internal server error');
}

// Send success response
echo json_encode(['message' => $result]);
?>
