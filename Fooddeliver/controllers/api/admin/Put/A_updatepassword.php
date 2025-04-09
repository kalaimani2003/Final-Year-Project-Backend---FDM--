
<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../../../models/put.php';
require_once '../../../../config/header.php';

function handleResponse($statusCode, $message)
{
    http_response_code($statusCode);
    echo json_encode(['message' => $message]);
    exit();
}

// Print received POST and FILES data for debugging
print_r($_POST);
print_r($_FILES);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $FoodName = $_POST['FoodName'] ?? null;
    $Price = $_POST['Price'] ?? null;
    $Description = $_POST['Description'] ?? null;
    $id = $_POST['id'] ?? null;  // Make sure this line correctly gets the id from POST data
    $Image = null;

    if (!$FoodName || !$Price || !$Description || !$id) {
        handleResponse(400, 'All fields (FoodName, Price, Description, id) are required');
    }

    // Image handling
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['Image']['name']);
        $uploadPath = 'uploads/' . $imageName;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($_FILES['Image']['tmp_name'], $uploadPath)) {
            $Image = $imageName;
        } else {
            handleResponse(500, 'Failed to upload the image');
        }
    }

    // Debugging - Check if Image is set
    echo 'Image Name: ' . $Image;
    echo '<br>';

    // Create object and call the update method
    $obj = new Put();
    try {
        $result = $obj->smart($FoodName, $Price, $Description, $Image, $id);
        echo json_encode($result);
    } catch (Exception $e) {
        handleResponse(500, 'Error: ' . $e->getMessage());
    }
}
?>

