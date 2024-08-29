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
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}


try {
    // Query to count the number of categories
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $categoriesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Query to count the number of suppliers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers");
    $suppliersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT SUM(TotalValue) AS count FROM Inventory");
    $inventoryvaluesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT SUM(Quantity) AS count FROM `dispatchorders`");
    $dispatchQuantityCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM shop");
    $storesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM PurchaseOrders WHERE Status != 'Complete'");
    $ordersNotReceivedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Return the counts as JSON
    echo json_encode([
        'categories' => $categoriesCount,
        'suppliers' => $suppliersCount,
        'inventoryvalues' => $inventoryvaluesCount,
        'dispatchquantity' => $dispatchQuantityCount,
        'storescount' => $storesCount,
        'yetReceived' => $ordersNotReceivedCount
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
