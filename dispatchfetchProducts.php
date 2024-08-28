<?php

include 'config.php';

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
