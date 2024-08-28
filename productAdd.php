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
    $productName = htmlspecialchars($_POST['productname']);
    $brand = htmlspecialchars($_POST['brand']);
    $type = htmlspecialchars($_POST['type']);
    $sku = htmlspecialchars($_POST['sku']);
    $dateAdded = isset($_POST['dateadded']) ? htmlspecialchars($_POST['dateadded']) : '';

    // Validate the datetime format (YYYY-MM-DD) - adjust according to your needs
    $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($datePattern, $dateAdded)) {
        echo "Invalid date format. Please use YYYY-MM-DD.";
        exit();
    }

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
