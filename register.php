<?php
// Database configuration
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'camerastore';

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
    handlePost($pdo);
} else {
    echo '<script>alert("Invalid request method");</script>';
}

function handlePost($pdo) {
    if ($_POST['reg-password'] !== $_POST['conf_reg-password']) {
        echo '<script>alert("Passwords do not match");</script>';
        return;
    }

    $username = htmlspecialchars($_POST['reg-username']);
    $passwordHash = password_hash($_POST['reg-password'], PASSWORD_BCRYPT);
    $phoneNumber = htmlspecialchars($_POST['PhoneNum']);
    $role = htmlspecialchars($_POST['role']);

    $sql = "INSERT INTO users (Username, PasswordHash, PhoneNumber, Role, CreatedAt) VALUES (:username, :passwordHash, :phoneNumber, :role, NOW())";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            'username' => $username,
            'passwordHash' => $passwordHash,
            'phoneNumber' => $phoneNumber,
            'role' => $role
        ]);
        echo '<script>
                alert("User created successfully");
                window.location.href = "index.html";
              </script>';
    } catch (PDOException $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    }
}
?>
