<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$id = $_GET['id'] ?? null;

// Handle POST request (Update food item)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id !== null) {
    $foodName = $_POST['FoodName'] ?? '';
    $price = $_POST['Price'] ?? '';
    $description = $_POST['Description'] ?? '';
    $imagePath = '';  // Initialize imagePath in case there's no new image

    if (empty($foodName) || empty($price) || empty($description)) {
        echo json_encode(['error' => 'All fields (FoodName, Price, Description) are required.']);
        exit();
    }

    // Check if a new image was uploaded
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Image']['tmp_name'];
        $fileName = $_FILES['Image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp','avif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Use the original basename of the file (without extension) for image naming
            $basename = pathinfo($fileName, PATHINFO_FILENAME);
            $uniqueFileName = $basename . '.' . $fileExtension;
            $uploadDir = 'Uploads/';  // Ensure the folder is 'Uploads'

            // Ensure Uploads directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Save the image to the 'Uploads' folder
            $imagePath = $uploadDir . $uniqueFileName;

            if (!move_uploaded_file($fileTmpPath, $imagePath)) {
                echo json_encode(['error' => 'Failed to move uploaded file.']);
                exit();
            }
        } else {
            echo json_encode(['error' => 'Invalid image file type. Allowed types: jpg, jpeg, png, webp.']);
            exit();
        }
    } else {
        // If no image is uploaded, keep the existing image
        $query = "SELECT Image FROM addfood WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $imagePath = $row['Image'];  // Keep the existing image path
        }
    }

    // Update food item
    $query = "UPDATE addfood SET FoodName = ?, Price = ?, Description = ?, Image = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL query.", "details" => $conn->error]);
        exit();
    }

    $stmt->bind_param("sdssi", $foodName, $price, $description, $imagePath, $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Food item updated successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to update food item.', 'details' => $stmt->error]);
    }

    $stmt->close();
    exit();
}

// Handle GET request (Fetch food item for editing)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id !== null) {
    $query = "SELECT FoodName, Price, Description, Image FROM addfood WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL query.", "details" => $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $foodItem = $result->fetch_assoc();
        // Provide the correct full path to the image
        $foodItem['Image'] = 'http://localhost/Fooddeliver/' . $foodItem['Image'];
        echo json_encode($foodItem);
    } else {
        echo json_encode(['error' => 'Food item not found.']);
    }

    $stmt->close();
}

$conn->close();
?>
