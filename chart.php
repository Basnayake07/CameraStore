<?php
// db.php connection
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = '/home/site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$conn = new mysqli();
$conn->ssl_set(null, null, $ssl_ca, null, null);
$conn->real_connect($host, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Fetch the total quantity dispatched for each product in the current month
$sql = "SELECT p.ProductName, SUM(d.Quantity) as TotalDispatched
        FROM DispatchOrders d
        JOIN Products p ON d.ProductID = p.ProductID
        WHERE MONTH(d.OrderDate) = MONTH(CURRENT_DATE()) AND YEAR(d.OrderDate) = YEAR(CURRENT_DATE())
        GROUP BY p.ProductName";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]);
    exit();
}

$productNames = [];
$quantities = [];

// Prepare data for Chart
while ($row = $result->fetch_assoc()) {
    $productNames[] = $row['ProductName'];
    $quantities[] = $row['TotalDispatched'];
}

// Return the data in JSON format
echo json_encode([
    'xValues' => $productNames,
    'yValues' => $quantities
]);

// Close the connection
$conn->close();
?>
