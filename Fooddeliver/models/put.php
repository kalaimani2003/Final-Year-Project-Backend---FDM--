<?php
include_once '../../../../config/database.php';

class Put
{
    public $conn;
    public $response;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Method to update food item details
    public function smart($FoodName, $Price, $Description, $Image, $id)
    {
        $updateQuery = "UPDATE addfood SET FoodName=?, Price=?, Description=?, Image=? WHERE id=?";
        $updateStmt = mysqli_prepare($this->conn, $updateQuery);

        if ($updateStmt === false) {
            $this->response = ["message" => "Preparation failed: " . mysqli_error($this->conn)];
            return $this->response;
        }

        // Bind parameters correctly (use 'ssdsi' for string, string, string, string, integer)
        mysqli_stmt_bind_param($updateStmt, 'ssssi', $FoodName, $Price, $Description, $Image, $id);

        $updateResult = mysqli_stmt_execute($updateStmt);

        if ($updateResult) {
            // Check if any rows were affected
            if (mysqli_stmt_affected_rows($updateStmt) > 0) {
                $this->response = ["message" => "success"];
            } else {
                $this->response = ["message" => "No rows were affected. ID may not exist."];
            }
        } else {
            $this->response = ["message" => "Update execution failed: " . mysqli_error($this->conn)];
        }

        // Close the statement
        mysqli_stmt_close($updateStmt);

        return $this->response;
    }
}
?>