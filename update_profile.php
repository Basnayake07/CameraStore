<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once 'config.php'; // Include config file

$username = $_SESSION['username'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $phoneNumber = $_POST['PhoneNumber'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['con-password'];

    // Validate password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

    // Prepare SQL to update phone number and/or password
    $sql = "UPDATE users SET PhoneNumber = ?";

    // If a new password is provided, add it to the SQL query
    if (!empty($newPassword)) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", PasswordHash = ?";
    }
    $sql .= " WHERE Username = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        if (!empty($newPassword)) {
            $stmt->bind_param("sss", $phoneNumber, $passwordHash, $username);
        } else {
            $stmt->bind_param("ss", $phoneNumber, $username);
        }

        // Execute query
        if ($stmt->execute()) {
            echo "Profile updated successfully.";
        } else {
            echo "ERROR: Could not execute query: $sql. " . $mysqli->error;
        }
        // Close statement
        $stmt->close();
    } else {
        echo "ERROR: Could not prepare query: $sql. " . $mysqli->error;
    }
    
    // Close connection
    $mysqli->close();
    
    // Redirect to profile page
    header("Location: profile.php");
    exit();
} else {
    // Not a POST request
    echo "Invalid request.";
    exit();
}
?>
