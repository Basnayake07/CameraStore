<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar"
                aria-expanded="false" aria-controls="sidebar">
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
                            <a class="nav-link active" href="dashboard.php"><i class="material-icons">home</i>Dashboard</a>
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

                <!-- Header for the main content with title and user information -->
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">More Indormation</h1>
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
                <!-- Main content can be added here -->
                <!--methana idala oyalage part eka gahanna-->
                <div class=" col-lg-5 ">
                <?php
                // Check existence of id parameter before processing further
                if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                    // Include config file
                    require_once "config.php";

                    // Prepare a select statement
                    $sql = "SELECT * FROM productreceiveddate WHERE PurchaseOrderID = ? ORDER BY DateReceived";

                    if ($stmt = $mysqli->prepare($sql)) {
                        // Bind variables to the prepared statement as parameters
                        $stmt->bind_param("i", $param_id);

                        // Set parameters
                        $param_id = trim($_GET["id"]);

                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                echo '<table class="table table-bordered table-striped " id = "display_table">';
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Date</th>";
                                echo "<th>Quantity</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = $result->fetch_array()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['DateReceived']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";

                                // Free result set
                                $result->free();
                            } else {
                                echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                            }
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        // Prepare statement failed
                        echo "Failed to prepare statement.";
                    }

                    // Close connection
                    $mysqli->close();
                } else {
                    // URL doesn't contain id parameter. Redirect to error page
                    header("location: purchaseError.php");
                    exit();
                }
                ?>

                <p><a href="purchaseView.php" class="btn btn-primary">Back</a></p>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
