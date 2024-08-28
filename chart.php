<?php
// db.php connection
include 'db.php';

// Fetch the total quantity dispatched for each product in the current month
$sql = "SELECT p.ProductName, SUM(d.Quantity) as TotalDispatched
        FROM DispatchOrders d
        JOIN Products p ON d.ProductID = p.ProductID
        WHERE MONTH(d.OrderDate) = MONTH(CURRENT_DATE()) AND YEAR(d.OrderDate) = YEAR(CURRENT_DATE())
        GROUP BY p.ProductName";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$dispatchData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$productNames = [];
$quantities = [];

// Prepare data for Chart
foreach ($dispatchData as $row) {
    $productNames[] = $row['ProductName'];
    $quantities[] = $row['TotalDispatched'];
}

// Return the data in JSON format
echo json_encode([
    'xValues' => $productNames,
    'yValues' => $quantities
]);

?>
