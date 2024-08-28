<?php
session_start();
include 'db.php';

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
