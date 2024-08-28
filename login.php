<?php
$host = 'camerastore.mysql.database.azure.com';
$port = 3306;
$username = 'camerastore';
$password = 'ognam@#123';
$dbname = 'Camera_Warehouse';

// Path to your SSL certificate
$ssl_ca = '/home/site/wwwroot/ca-cert.pem'; // Ensure this path is correct

// Create connection with SSL
$mysqli = new mysqli($host, $username, $password, $dbname, $port);

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
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);  
    $password = $_POST['password'];  


    // Check if the user exists
    $sql = "SELECT * FROM users WHERE Username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['PasswordHash'])) {
        // Password is correct, start a session and store user info
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];

        
        header("Location: dashboard.php");
        exit();
    } else {
        // Invalid credentials
        echo '<script>
                alert("Invalid username or password. Please try again.");
                window.location.href = "index.html";
              </script>';
        exit();
    }
} else {
    
    header("Location: index.html");
    exit();
}
?>
