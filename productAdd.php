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

if (isset($_POST['submit'])) {
    // Sanitize and validate user inputs
    $productName = filter_var($_POST['productname'], FILTER_SANITIZE_STRING);
    $brand = filter_var($_POST['brand'], FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
    $sku = filter_var($_POST['sku'], FILTER_SANITIZE_STRING);
    $dateAdded = filter_var($_POST['dateadded'], FILTER_SANITIZE_STRING);

    // Prepare SQL statement
    $stmt = $mysqli->prepare("INSERT INTO products (ProductName, Brand, Type, SKU, DateAdded) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sssss", $productName, $brand, $type, $sku, $dateAdded);
        
        if ($stmt->execute()) {
            header("Location: productGet.php?msg=New record created successfully");
            exit();
        } else {
            echo "Failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement: " . $mysqli->error;
    }
}

$mysqli->close();
?>
