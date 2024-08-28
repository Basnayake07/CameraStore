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
    <title>Product Update</title>
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
                            <a class="nav-link" href="InventoryUpdate.php"><i class="material-icons">inventory</i>inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productGet.php"><i class="material-icons">category</i>Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="purchaseView.php"><i class="material-icons">shopping_cart</i>Purchase Orders</a>                            
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">sell</i>Dispatch Orders</a>                            
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
                    <h1 class="h2">Product</h1>
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
            <?php
               include "config.php";
                $Product_Id = $_GET['ProductID'];

                if (isset($_POST['submit'])) {
                    $productName = $_POST['productname'];
                    $brand = $_POST['brand'];
                    $type = $_POST['type'];
                    $sku = $_POST['sku'];
                    // $dateAdded = $_POST['dateadded'];
                    $status = $_POST['status'];

                    $sql = "UPDATE `products` SET `ProductName`=?, `Brand`=?, `Type`=?, `SKU`=?, `DateAdded`=NOW(), `status`=? WHERE `ProductID`=?";

                    $stmt = $mysqli->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param("sssssi", $productName, $brand, $type, $sku, $status, $Product_Id);

                        if ($stmt->execute()) {
                            header("Location: productGet.php?msg=Record Updated successfully");
                            exit();
                        } else {
                            echo "Failed: " . $stmt->error;
                        }

                        $stmt->close();
                    } else {
                        echo "Failed to prepare the statement: " . $mysqli->error;
                        }
                    }
            ?>

            <div class="container">
                <div class="text-center mb-5">
                    <h4>Update Product Information</h4>
                </div>

                <?php
                $sql = "SELECT * FROM `products` WHERE ProductID = $Product_Id LIMIT 1";
                $result = mysqli_query($mysqli,$sql);
                $row = mysqli_fetch_assoc($result);
                ?>

                <div class="container d-flex justify-content-center">
                    <form method="post" onsubmit="return validateForm()" style="width: 50vw; min-width: 300px;">
                        <div class="row mb-4">

                            <div class="mb-3">
                                <label class="form-label">Product Name:</label>
                                <input type="text" class="form-control" id="productname" name="productname" value ="<?php echo $row ['ProductName']?>">
                                <small id="productNameError" class="text-danger"></small>
                            </div>

                            <div class="col mb-3">
                                <label class="form-label">Brand:</label>
                                <input type="text" class="form-control" id="brand" name="brand" value ="<?php echo $row ['Brand']?>">
                                <small id="brandError" class="text-danger"></small>
                            </div>

                            <div class="col mb-3">
                                <label class="form-label">Type:</label>
                                <input type="text" class="form-control" id="type" name="type" value ="<?php echo $row ['Type']?>">
                                <small id="typeError" class="text-danger"></small>
                            </div>

                            <div class="col mb-3">
                                <label class="form-label">SKU:</label>
                                <input type="text" class="form-control" id="sku" name="sku" value ="<?php echo $row ['SKU']?>">
                                <small id="skuError" class="text-danger"></small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Status:</label>
                                <select name="status" class="form-control selectpicker" required>
                                    <option value="">Select the Status</option>
                                    <option value="Continue" >Continue</option>
                                    <option value="Not-Continue" >Not-Continue</option>                                    
                                </select>
                            </div>


                            <div>
                                <button type="submit" class="btn btn-success" name="submit">Update</button>
                                <a href="productGet.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
            <script src="productValidations.js"></script>              
            
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
