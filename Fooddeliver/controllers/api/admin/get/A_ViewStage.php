<?php
header('Content-Type: application/json');
include_once '../../../../config/database.php';
header("Access-Control-Allow-Origin: http://localhost:5173"); // Allow the specific React app domain
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Allow methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow headers


class Get
{ 
    public $conn;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Function to fetch all food items
    public function A_viewAchievement() 
    {        
        $query = "SELECT id, Description, Image FROM addfood";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            exit();
        }

        $result = mysqli_stmt_get_result($stmt);
        $foodItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Add full path to the image
        foreach ($foodItems as &$item) {
            $item['Image'] = 'http://localhost/Fooddeliver/Fooddeliver/controllers/api/admin/post/uploads/' . basename($item['Image']);
        }
        echo json_encode($foodItems);
        exit();
    }
}

require_once '../../../../models/get.php';
$obj = new Get();
$obj->A_viewAchievement();
?>
