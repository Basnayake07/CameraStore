<?php
//use this to debug
//die("Error executing query: " . mysqli_error($mysqli));
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

session_start();

if (isset($_GET['deleteID'])) {
    //get the DispatchOrderID that want to be deleted
    $deleteID = $_GET['deleteID'];

    $sqlDelete = "SELECT ProductID, Quantity
                  FROM DispatchOrders
                  WHERE DispatchOrderID = ?";
    $stmt = $mysqli->prepare($sqlDelete);
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    $resultDelete = $stmt->get_result();

    //checking errors. 
    if (!$resultDelete || mysqli_num_rows($resultDelete) == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //echo "Something went wrong. Can not perform operation now.";
        exit;
    }

    $rowDelete = $resultDelete->fetch_assoc();
    $deleteProductID = $rowDelete['ProductID'];
    $deleteQuantity = $rowDelete['Quantity'];

    // Fetch quantity
    $sqlQuantity = "SELECT TotalQuantity 
                    FROM inventory 
                    WHERE ProductID = ?";
    $stmtQuantity = $mysqli->prepare($sqlQuantity);
    $stmtQuantity->bind_param("i", $deleteProductID);
    $stmtQuantity->execute();
    $resultQuantity = $stmtQuantity->get_result();

    if ($resultQuantity->num_rows > 0) {
        $rowQuantity = $resultQuantity->fetch_assoc();
        $availableQuantity = $rowQuantity['TotalQuantity'];

        // Calculate the updated quantity
        $updatedQuantity = $availableQuantity + $deleteQuantity;

        // Fetch the latest unit price
        $sqlFetchUnitPrice = "SELECT UnitPrice 
                              FROM purchaseorders 
                              WHERE ProductID = ? 
                              ORDER BY OrderDate DESC LIMIT 1";
        $stmtFetchUnitPrice = $mysqli->prepare($sqlFetchUnitPrice);
        $stmtFetchUnitPrice->bind_param("i", $deleteProductID);
        $stmtFetchUnitPrice->execute();
        $resultFetchUnitPrice = $stmtFetchUnitPrice->get_result();

        if ($resultFetchUnitPrice->num_rows > 0) {
            $row = $resultFetchUnitPrice->fetch_assoc();
            $unitPrice = $row['UnitPrice'];

            // Calculate the total amount using the updated total quantity
            $totalValue = $unitPrice * $updatedQuantity;

            //check quantity and total value are positive numbers
            if ($updatedQuantity < 0 || $totalValue < 0) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'place';
                header('location: dispatchedOrders.php');
                exit;
            }

            // Update the inventory table
            $sqlUpdateInventory = "UPDATE inventory 
                                   SET TotalQuantity = ?, TotalValue = ? 
                                   WHERE ProductID = ?";
            $stmtUpdateInventory = $mysqli->prepare($sqlUpdateInventory);
            $stmtUpdateInventory->bind_param("idi", $updatedQuantity, $totalValue, $deleteProductID);

            if ($stmtUpdateInventory->execute()) {
                //delete record from the DispatchOrders table
                $sql = "DELETE 
                        FROM `DispatchOrders` 
                        WHERE DispatchOrderID = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $deleteID);
                $stmt->execute();
                //$result = mysqli_query($mysqli, $sql);

                if ($stmt->affected_rows > 0) {
                    //to store alert messages
                    $_SESSION['status'] = 'success';
                    $_SESSION['operation'] = 'delete';
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['operation'] = 'delete';
                }
            }
        }
        $stmt->close();
        $mysqli->close();
    }
}
header('location: dispatchedOrders.php');
exit;
