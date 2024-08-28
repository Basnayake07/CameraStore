<?php
// Database configuration
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = '/home/site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$conn = new mysqli($host, $username, $password, $dbname, $port, MYSQLI_CLIENT_SSL);
$conn->ssl_set(null, null, $ssl_ca, null, null);

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
    if ($_POST['reg-password'] !== $_POST['conf_reg-password']) {
        echo '<script>alert("Passwords do not match");</script>';
        return;
    }

    $username = htmlspecialchars($_POST['reg-username']);
    $passwordHash = password_hash($_POST['reg-password'], PASSWORD_BCRYPT);
    $phoneNumber = htmlspecialchars($_POST['PhoneNum']);
    $role = htmlspecialchars($_POST['role']);

    $sql = "INSERT INTO users (Username, PasswordHash, PhoneNumber, Role, CreatedAt) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo '<script>alert("Error preparing statement");</script>';
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
    }
}
?>
