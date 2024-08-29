<?php
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = '/home/site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$mysqli = new mysqli();
$mysqli->ssl_set(null, null, $ssl_ca, null, null);
$mysqli->real_connect($host, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL);

// Check connection
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit();
}

try {
    // Query to count the number of categories
    $result = $mysqli->query("SELECT COUNT(*) as count FROM products");
    $categoriesCount = $result->fetch_assoc()['count'];

    // Query to count the number of suppliers
    $result = $mysqli->query("SELECT COUNT(*) as count FROM suppliers");
    $suppliersCount = $result->fetch_assoc()['count'];

    // Query to sum the total value from the inventory
    $result = $mysqli->query("SELECT SUM(TotalValue) AS count FROM Inventory");
    $inventoryvaluesCount = $result->fetch_assoc()['count'];

    // Query to sum the quantity from dispatch orders
    $result = $mysqli->query("SELECT SUM(Quantity) AS count FROM dispatchorders");
    $dispatchQuantityCount = $result->fetch_assoc()['count'];

    // Query to count the number of shops
    $result = $mysqli->query("SELECT COUNT(*) as count FROM shop");
    $storesCount = $result->fetch_assoc()['count'];

    // Query to count purchase orders that are not marked as 'Complete'
    $result = $mysqli->query("SELECT COUNT(*) AS count FROM PurchaseOrders WHERE Status != 'Complete'");
    $ordersNotReceivedCount = $result->fetch_assoc()['count'];

    // Return the counts as JSON
    echo json_encode([
        'categories' => $categoriesCount,
        'suppliers' => $suppliersCount,
        'inventoryvalues' => $inventoryvaluesCount,
        'dispatchquantity' => $dispatchQuantityCount,
        'storescount' => $storesCount,
        'yetReceived' => $ordersNotReceivedCount
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

// Close the connection
$mysqli->close();
?>
