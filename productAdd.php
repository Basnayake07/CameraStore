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
    $productName = $_POST['productname'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $sku = $_POST['sku'];
    $dateAdded = $_POST['dateadded'];
    $status = 'continue'; // Default status value

    // Include status in the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO products (ProductName, Brand, Type, SKU, dateAdded, status) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        // Bind status parameter
        $stmt->bind_param("ssssss", $productName, $brand, $type, $sku, $dateAdded, $status);
        
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
