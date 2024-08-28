<?php
include 'db.php';

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
