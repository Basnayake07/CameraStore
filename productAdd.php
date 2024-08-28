<?php

include "config.php";

if (isset($_POST['submit'])) {
    $productName = $_POST['productname'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $sku = $_POST['sku'];
    $dateAdded = $_POST['dateadded'];

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
        echo "Failed to prepare the SQL statement: " . $conn->error;
    }
}

$mysqli->close();
?>
