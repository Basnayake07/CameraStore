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


if (isset($_POST['export'])) {
    // Fetch data from Inventory
    $sql = "SELECT 
            so.DispatchOrderID, p.ProductName, s.Man_name, so.Quantity, so.OrderDate
            FROM dispatchorders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN shop s ON so.ShopID = s.ShopID
            ORDER BY so.OrderDate DESC";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=dispatched_orders_report.csv');

        $output = fopen('php://output', 'w');

        // Output column headings
        fputcsv($output, array('Dispatch ID', 'Product Name', 'Manager Name', 'Quantity', 'Order Date'));

        // Output data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
    } else {
        echo "No records found.";
    }
}

$mysqli->close();
exit();
