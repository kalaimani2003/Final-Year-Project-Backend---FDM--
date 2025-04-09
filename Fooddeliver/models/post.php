<?php
include_once '../../../../config/database.php';

class Post {
    public $conn;

    // Constructor to initialize database connection
    function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Method to insert food item into the database
    public function A_InsertFood($FoodName, $Price, $Description, $Image = null) {
        // Check database connection
        if (!$this->conn) {
            return "Database connection failed: " . mysqli_connect_error();
        }

        // Prepare the SQL query
        $query = "INSERT INTO addfood (FoodName, Price, Description, Image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return "Preparation failed: " . mysqli_error($this->conn);
        }

        // Bind parameters to the query
        mysqli_stmt_bind_param($stmt, 'ssss', $FoodName, $Price, $Description, $Image);

        // Execute the query
        $exec = mysqli_stmt_execute($stmt);

        // Check for execution errors
        if (!$exec) {
            return "Execution failed: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        return "Food Item Added Successfully";
    }
    public function InsertUser($Username, $Email, $Password, $Confirmpassword,$Address,$Contact) {
        // Check database connection
        if (!$this->conn) {
            return "Database connection failed: " . mysqli_connect_error();
        }

        // Prepare the SQL query
        $query = "INSERT INTO adduser (Username, Email, Password, Confirmpassword,Address,Contact) VALUES (?, ?, ?, ?,?,?)";
        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return "Preparation failed: " . mysqli_error($this->conn);
        }

        // Bind parameters to the query
        mysqli_stmt_bind_param($stmt, 'ssssss', $Username, $Email, $Password, $Confirmpassword,$Address,$Contact);

        // Execute the query
        $exec = mysqli_stmt_execute($stmt);

        // Check for execution errors
        if (!$exec) {
            return "Execution failed: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        return "User Added Successfully";
    }
    // Method to get the user count
    public function getUserCount() {
        // Check database connection
        if (!$this->conn) {
            return "Database connection failed: " . mysqli_connect_error();
        }

        // Query to get the user count
        $query = "SELECT COUNT(*) as user_count FROM users"; // Replace 'users' with your actual table name
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            return "Query failed: " . mysqli_error($this->conn);
        }

        // Fetch the result
        $row = mysqli_fetch_assoc($result);
        $userCount = $row['user_count'];

        return $userCount;
    }
}
?>
