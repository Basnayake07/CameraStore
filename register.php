<?php
session_start();

// Database configuration
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = '/home/site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$conn = new mysqli();
$conn->ssl_set(null, null, $ssl_ca, null, null);
$conn->real_connect($host, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    handlePost($conn);
} else {
    echo '<script>alert("Invalid request method");</script>';
}

function handlePost($conn) {
    // Ensure POST data is received
    if (!isset($_POST['reg-password'], $_POST['conf_reg-password'], $_POST['reg-username'], $_POST['PhoneNum'], $_POST['role'])) {
        echo '<script>alert("Required fields are missing");</script>';
        return;
    }

    if ($_POST['reg-password'] !== $_POST['conf_reg-password']) {
        echo '<script>alert("Passwords do not match");</script>';
        return;
    }

    $username = htmlspecialchars($_POST['reg-username']);
    $passwordHash = password_hash($_POST['reg-password'], PASSWORD_BCRYPT);
    $phoneNumber = htmlspecialchars($_POST['PhoneNum']);
    $role = htmlspecialchars($_POST['role']);

    // Check if the username already exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE Username = ?");
    $checkStmt->bind_param('s', $username);
    $checkStmt->execute();
    $checkStmt->bind_result($userCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($userCount > 0) {
        echo '<script>alert("Username already exists. Please choose a different username.");</script>';
        return;
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (Username, PasswordHash, PhoneNumber, Role, CreatedAt) VALUES (?, ?, ?, ?, NOW())");

    if ($stmt === false) {
        echo '<script>alert("Failed to prepare SQL statement");</script>';
        return;
    }

    // Bind parameters and execute the statement
    $stmt->bind_param('ssss', $username, $passwordHash, $phoneNumber, $role);

    if ($stmt->execute()) {
        echo '<script>
                alert("User created successfully");
                window.location.href = "index.html";
              </script>';
    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }

    // Close the statement
    $stmt->close();
}
?>
