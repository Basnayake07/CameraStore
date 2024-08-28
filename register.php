<?php
// Database configuration
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = 'site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$mysqli = new mysqli($host, $username, $password, $dbname, $port, MYSQLI_CLIENT_SSL);

// Set SSL parameters
$mysqli->ssl_set(null, null, $ssl_ca, null, null);

// Real connect with SSL
if (!$mysqli->real_connect($host, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL)) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit();
}

// Check connection
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    handlePost($mysqli);
} else {
    echo '<script>alert("Invalid request method");</script>';
}

function handlePost($mysqli) {
    if ($_POST['reg-password'] !== $_POST['conf_reg-password']) {
        echo '<script>alert("Passwords do not match");</script>';
        return;
    }

    $username = htmlspecialchars($_POST['reg-username']);
    $passwordHash = password_hash($_POST['reg-password'], PASSWORD_BCRYPT);
    $phoneNumber = htmlspecialchars($_POST['PhoneNum']);
    $role = htmlspecialchars($_POST['role']);

    $sql = "INSERT INTO users (Username, PasswordHash, PhoneNumber, Role, CreatedAt) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        echo '<script>alert("Error preparing statement: ' . $mysqli->error . '");</script>';
        return;
    }

    $stmt->bind_param("ssss", $username, $passwordHash, $phoneNumber, $role);

    try {
        $stmt->execute();
        echo '<script>
                alert("User created successfully");
                window.location.href = "index.html";
              </script>';
    } catch (Exception $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    } finally {
        $stmt->close(); // Close statement
    }
}

$mysqli->close(); // Close the database connection
?>
