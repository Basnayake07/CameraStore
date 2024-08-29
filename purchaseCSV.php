<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
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


// Fetch data from Inventory
$sql = "SELECT ps.PurchaseOrderID, p.ProductName,s.SupplierName, ps.QuantityOrdered, ps.QuantityRecieved, ps.UnitPrice, ps.OrderDate  FROM products p, purchaseorders ps, suppliers s WHERE ps.ProductID = p.ProductID AND ps.SupplierID = s.SupplierID ORDER BY ps.PurchaseOrderID ;";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=purchase_report.csv');

    $output = fopen('php://output', 'w');
    
    // Output column headings
    fputcsv($output, array('PurchaseOrderID', 'Product Name', 'SupplierName', 'QuantityOrdered', 'QuantityRecieved', 'UnitPrice', 'OrderDate'));

    // Output data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
} else {
    echo "No records found.";
}

$mysqli->close();
exit();
?>


    
