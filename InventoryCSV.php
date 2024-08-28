<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "camera_warehouse";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from Inventory
$sql = "SELECT * FROM Inventory";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=inventory_report.csv');

    $output = fopen('php://output', 'w');
    
    // Output column headings
    fputcsv($output, array('Product ID', 'Product Name', 'Brand', 'Type', 'SKU', 'Total Quantity', 'Last Received Date', 'Total Value'));

    // Output data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
} else {
    echo "No records found.";
}

$conn->close();
exit();
?>
