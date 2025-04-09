<?php
// Allow CORS for your frontend
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database configuration
$host = "localhost"; // Replace with your database host
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "food"; // Replace with your database name

// Establish database connection
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    http_response_code(500); // Internal server error
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Function to categorize items as veg or non-veg
function getItemCategory($item) {
    $nonVegItems = ['ChickenBriyani', 'Pizza', 'Noodles','MuttonBriyani','ChickenShawarma'];  // Add non-veg items here
    $vegItems = ['Idly', 'Dosa', 'Pasta'];  // Add veg items here

    $item = trim($item); // Clean item names from any extra spaces

    if (in_array($item, $nonVegItems)) {
        return 'non-veg';
    } elseif (in_array($item, $vegItems)) {
        return 'veg';
    }
    return 'unknown'; // Unknown category, just in case
}

// Get user count from the 'adduser' table
$userCountSql = "SELECT COUNT(*) as userCount FROM adduser";
$userCountResult = $conn->query($userCountSql);
$userCount = 0;

if ($userCountResult && $userCountResult->num_rows > 0) {
    $userCountRow = $userCountResult->fetch_assoc();
    $userCount = (int)$userCountRow['userCount'];
}

// Initialize counts for veg and non-veg items
$vegCount = 0;
$nonVegCount = 0;

// Get distinct FoodNames from the 'addfood' table
$foodSql = "SELECT DISTINCT FoodName FROM addfood";
$foodResult = $conn->query($foodSql);

if ($foodResult && $foodResult->num_rows > 0) {
    // Array to store unique food names
    $uniqueFoodNames = [];

    while ($row = $foodResult->fetch_assoc()) {
        $foodName = trim($row['FoodName']);  // Clean item names from any extra spaces
        if (!in_array($foodName, $uniqueFoodNames)) {
            $uniqueFoodNames[] = $foodName;  // Add unique food name
        }
    }

    // Count veg and non-veg items based on their categories
    foreach ($uniqueFoodNames as $item) {
        $category = getItemCategory($item);  // Get the item category

        if ($category === 'veg') {
            $vegCount += 1;
        } elseif ($category === 'non-veg') {
            $nonVegCount += 1;
        }
    }
}

// Debugging: Check the counts before inserting
error_log("User Count: " . $userCount);
error_log("Veg Count: " . $vegCount);
error_log("Non-Veg Count: " . $nonVegCount);

// Insert the counts into the 'food_counts' table
$insertSql = "INSERT INTO food_counts (userCount, vegCount, nonVegCount) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("iii", $userCount, $vegCount, $nonVegCount);
$stmt->execute();

// Check if the insert was successful
if ($stmt->affected_rows > 0) {
    error_log("Data successfully inserted into food_counts table.");
} else {
    error_log("Error inserting data into food_counts table.");
}

// Return the counts as a JSON response
echo json_encode([
    'userCount' => $userCount,
    'vegCount' => $vegCount,
    'nonVegCount' => $nonVegCount
]);

// Close the database connection
$stmt->close();
$conn->close();
exit;
?>
