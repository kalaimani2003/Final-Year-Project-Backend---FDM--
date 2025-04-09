<?php
// Define paths to required files
$modelsPath = '../../../../models/get.php'; // Use absolute path
$headersPath = '../../../../config/header.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Check if required files exist
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    respondWithError(500, 'Required files are missing');
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Validate input data
if (empty($data->Username) || empty($data->Password)) {
    respondWithError(400, 'Username and password are required');
}

// Create an instance of the FoodModel class
$obj = new FoodModel();

// Call the checkLogin method to validate user credentials
$result = $obj->checkLogin($data->Username, $data->Password);

// Handle errors or success
if (isset($result['error'])) {
    respondWithError(400, $result['error']);
}

// Send success response with user data
echo json_encode(['message' => 'Login successful', 'user' => $result]);

/**
 * Function to handle errors and send response.
 *
 * @param int    $statusCode HTTP status code
 * @param string $message    Error message
 */
function respondWithError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}
?>
