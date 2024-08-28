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
    <title>Suppliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Suppliers</h1>
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
                
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-5 mb-3 clearfix">
                                    <div class="d-flex">                                        
                                            <a href="supplierCreate.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Supplier</a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <form method="GET" action="suppliers.php" class="d-flex align-items-center">
                                            <input type="text" name="search" class="form-control me-2" placeholder="Search by Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                                <button class="btn btn-outline-success" type="submit">Search</button>
                                        </form>
                                        
                                    </div>
                                </div>
            
                                <?php
                                // Include config file
                                require_once "config.php";
                                // Initialize search variable
                                $search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
                            
                                // Attempt select query execution
                                $sql = "SELECT * FROM suppliers";
            
                                if (!empty($search)) {
                                    $sql .= " WHERE SupplierName LIKE '%$search%'";
                                }
            
                                if($result = $mysqli->query($sql)){
                                    if($result->num_rows > 0){
                                        echo '<div style="height: 300px; overflow-y: auto;">';
                                        echo '<table class="table table-bordered table-striped text-center" id=suppliertable>';
                                            echo "<thead>";
                                                echo "<tr>";
                                                    echo "<th>SupplierID</th>";
                                                    echo "<th>Name</th>";
                                                    echo "<th>Location</th>";
                                                    echo "<th>Email</th>";
                                                    echo "<th>Action</th>";
                                                echo "</tr>";
                                            echo "</thead>";
                                            echo "<tbody>";
                                            while($row = $result->fetch_array()){
                                                echo "<tr>";
                                                    echo "<td>" . $row['SupplierID'] . "</td>";
                                                    echo "<td>" . $row['SupplierName'] . "</td>";
                                                    echo "<td>" . $row['Location'] . "</td>";
                                                    echo "<td>" . $row['ContactEmail'] . "</td>";
                                                    echo "<td>";
                                                        
                                                    if ($role !== 'Worker') {
                                                        echo "<a href='supplierUpdate.php?id=" . $row['SupplierID'] . "' class='link-dark'><i class='fa-solid fa-pen-to-square fs-5 me-3'></i></a>";
                                                        
                                                    } else {
                                                        // If the role is 'worker', disable the buttons
                                                        echo "<i class='fa-solid fa-pen-to-square fs-5 me-3' style='color: gray; cursor: not-allowed;' title='Edit (disabled)'></i>";
                                                    }
                                                echo "</tr>";
                                            }
                                            echo "</tbody>";                            
                                        echo "</table>";
                                        echo '</div>';
                                        // Free result set
                                        $result->free();
                                    } else{
                                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                                    }
                                } else{
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                
                                // Close connection
                                $mysqli->close();
                                ?>
                            </div>
                        </div>        
                    </div>
                </div>               
            
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this supplier record?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <form id="deleteForm" method="post" action="supplierDelete.php">
                <input type="hidden" name="id" id="deleteSupplierID">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        </div>
    </div>
    </div>

    <script>
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            // Extract info from data-* attributes
            var supplierID = button.getAttribute('data-supplierid');
            // Update the modal's form hidden input value
            var inputSupplierID = document.getElementById('deleteSupplierID');
            inputSupplierID.value = supplierID;
        });   
        
    </script>
</body>
</html>
