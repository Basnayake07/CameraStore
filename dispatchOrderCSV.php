<?php
include 'config.php';

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
