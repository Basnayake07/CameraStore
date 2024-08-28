<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once 'config.php'; // Include your database configuration file

$username = $_SESSION['username'];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete the user record from the database
    $sql = "DELETE FROM users WHERE username = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind the username parameter
        $stmt->bind_param("s", $username);
        
        // Attempt to execute the statement
        if ($stmt->execute()) {
            // If the user is deleted, destroy the session and redirect to the login page
            session_destroy();
            header("Location: index.html");
            exit();
        } else {
            echo "Error: Could not execute the query. " . $mysqli->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: Could not prepare the query. " . $mysqli->error;
    }

    // Close the connection
    $mysqli->close();
} else {
    echo "Invalid request.";
}
?>
