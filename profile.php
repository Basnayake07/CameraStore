<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Include config file
require_once 'config.php';

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE username = ?";
if ($stmt = $mysqli->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $username);
    
    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Store result
        $result = $stmt->get_result();
        
        // Check if the user exists, if yes then fetch the result
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        } else {
            echo "No user found.";
            exit();
        }
    } else {
        echo "ERROR: Could not execute query: $sql. " . $mysqli->error;
        exit();
    }
    // Close statement
    $stmt->close();
} else {
    echo "ERROR: Could not prepare query: $sql. " . $mysqli->error;
    exit();
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .profile-info {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
            max-width: 400px;
            margin-left:25%;
            margin-top:5%;
        }
        .profile-info h3 {
            border-bottom: 2px solid #77B0AA;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .profile-info .row {
            margin-bottom: 10px;
        }
        .panel {
            height: 20vh;
            background-color: #77B0AA;
            border: 2px outset black;
        }

        .panel:hover {
            transform: translateY(-5px);
            transition: 0.3s;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Profile Dropdown */
        .dropdown-menu {
            right: 0;
            left: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar navigation -->
            <nav id="sidebar" class="col-md-2 d-md-block collapse sidebar">
                <div class="sidebar">
                    <!-- Sidebar header with company logo and name -->
                    <div class="sidebar-header">
                        <img src="logo.png" alt="Logo" class="img-fluid">                        
                    </div>
                    <!-- Sidebar navigation links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="material-icons">home</i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="InventoryUpdate.php"><i class="material-icons">inventory</i>Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productGet.php"><i class="material-icons">category</i>Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="purchaseView.php"><i class="material-icons">shopping_cart</i>Purchase Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dispatchedOrders.php"><i class="material-icons">sell</i>Dispatch Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="suppliers.php"><i class="material-icons">local_shipping</i>Suppliers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shopIndex.php"><i class="material-icons md-18">store</i>Shops</a>
                        </li>
                    </ul>
                    
                </div>
            </nav>

            <!-- Main content area -->
            <main role="main" class="col-md-6 ml-sm-auto col-lg-10 px-4">
                <!-- Header for the main content with title and user information -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">User Profile</h1>
                    <div class="profile-container">
                        <span href="#" class="d-flex align-items-center text-dark text-decoration-none">
                            <i class="material-icons" style="font-size:48px;">account_circle</i>
                            <div class="profile-text ms-2">
                                <span><?php echo htmlspecialchars($username); ?></span>
                                <span><?php echo htmlspecialchars($role); ?></span>
                            </div>
                        </span>                        
                    </div>
                </div>

                <!-- User profile details -->
                <div class="profile-info">
                    <h3>User Information</h3>
                    <div class="row">
                        <div class="col-md-6"><strong>Username:</strong></div>
                        <div class="col-md-4"><?php echo htmlspecialchars($user['Username']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><strong>Phone Number:</strong></div>
                        <div class="col-md-4"><?php echo htmlspecialchars($user['PhoneNumber']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><strong>Role:</strong></div>
                        <div class="col-md-4"><?php echo htmlspecialchars($user['Role']); ?></div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal"> Edit Profile</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProfileModal"> Delete Profile</button>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to change profile pic/name/role -->
                    <form id="edit-profile-form" action="update_profile.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="PhoneNumber" class="form-label">Phone Number:</label>
                            <input type="text" id="PhoneNumber" name="PhoneNumber" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password:</label>
                            <input type="password" id="password" name="password" minlength="8" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="con-password" class="form-label">Confirm Password:</label>
                            <input type="password" id="con-password" name="con-password" minlength="8" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteProfileModal" tabindex="-1" aria-labelledby="deleteProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProfileModalLabel">Delete Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to delete profile -->
                    <form id="delete-profile-form" action="profileDelete.php" method="post">
                        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
