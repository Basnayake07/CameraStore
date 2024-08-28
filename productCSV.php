<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
include 'config.php';

// Fetch data from Inventory
$sql = "SELECT * FROM products";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=product_report.csv');

    $output = fopen('php://output', 'w');
    
    // Output column headings
    fputcsv($output, array('Product ID', 'Product Name', 'Brand', 'Type', 'SKU', 'Date Added', 'Status'));

    // Output data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
} else {
    echo "No records found.";
}

$mysqli->close();
exit();
?>