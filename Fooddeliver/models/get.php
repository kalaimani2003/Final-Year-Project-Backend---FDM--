<?php
include_once '../../../../config/database.php';

class FoodModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Function to fetch all food items
    public function getAllFoodItems()
    {
        $query = "SELECT id, Description, Image FROM addfood";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            return ['error' => 'Internal server error'];
        }

        $result = mysqli_stmt_get_result($stmt);
        $foodItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Add full path to the image
        foreach ($foodItems as &$item) {
            $item['Image'] = 'http://localhost/Fooddeliver/controllers/api/admin/post/uploads/' . basename($item['Image']);
        }

        mysqli_stmt_close($stmt);
        return $foodItems;
    }

    // Function to fetch a food item by ID
    public function getFoodById($id)
    {
        $query = "SELECT id, Description, Image FROM addfood WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            mysqli_stmt_close($stmt);
            return ['error' => 'Internal server error'];
        }

        $result = mysqli_stmt_get_result($stmt);
        $foodItem = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        return $foodItem ? $foodItem : ['error' => 'Food item not found'];
    }

    // Function to update a food item
    public function updateFood($id, $description)
    {
        $query = "UPDATE addfood SET Description = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        mysqli_stmt_bind_param($stmt, "si", $description, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        return $success ? ['message' => 'Food item updated successfully'] : ['error' => 'Failed to update food item'];
    }
    public function A_viewAchievement($id = null)
    {
        // Fetch all users
        $query = "SELECT Username, Email, Address, Contact FROM adduser";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            mysqli_stmt_close($stmt);
            return ['error' => 'Internal server error'];
        }

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $achievementContent = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $achievementContent;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No users found'];
        }
    }
    
    public function checkLogin($Username, $Password)
    {
        // Prepare the SQL query to check username and password
        $query = "SELECT * FROM adduser WHERE Username = ? AND Password = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        // Bind the username and password parameters
        mysqli_stmt_bind_param($stmt, "ss", $Username, $Password);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            mysqli_stmt_close($stmt);
            return ['error' => 'Internal server error'];
        }

        // Get the result of the query
        $result = mysqli_stmt_get_result($stmt);

        // If a user is found, return the user data
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $user;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'Invalid username or password'];
        }
    }
    public function Userfood()
    {
        $query = "SELECT id,FoodName,Price, Description, Image FROM addfood";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            return ['error' => 'Internal server error'];
        }

        $result = mysqli_stmt_get_result($stmt);
        $foodItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Add full path to the image
        foreach ($foodItems as &$item) {
            $item['Image'] = 'http://localhost/Fooddeliver/controllers/api/user/post/uploads/' . basename($item['Image']);
        }

        mysqli_stmt_close($stmt);
        return $foodItems;
    }
}


    // Function to view achievements by admin ID

?>
