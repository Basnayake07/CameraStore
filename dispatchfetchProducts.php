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


if (isset($_POST['query'])) {

    $query = $_POST['query'] . '%'; // Append '%' for LIKE clause
    $sql = "SELECT ProductName 
            FROM products 
            WHERE ProductName LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<ul class="list-group">';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list-group-item product-list-item">' . htmlspecialchars($row['ProductName']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="text-danger">No products found</p>';
    }
    
    $stmt->close();
}
?>
