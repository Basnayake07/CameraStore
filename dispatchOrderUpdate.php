<?php
//use this for debugging purposes
//die("Error executing query: " . mysqli_error($mysqli));

//mysqliect to the database
include 'config.php';
session_start();
// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];


$updateID = $_GET['updateID'];

//to fill form with already assigned values
$sqlFill = "SELECT
            p.ProductName, s.Man_name, so.Quantity                                                       
            FROM dispatchorders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN shop s ON so.ShopID = s.ShopID
            WHERE DispatchOrderID = ?";
$stmtFill = $mysqli->prepare($sqlFill);
$stmtFill->bind_param("i", $updateID);
$stmtFill->execute();
$resultFill = $stmtFill->get_result();

if (!$resultFill) {
    $_SESSION['status'] = 'error';
    $_SESSION['operation'] = 'update';
    header('location: dispatchedOrders.php');
    exit;
    //echo "Something went wrong. Please try again later";
}

$rowFill = $resultFill->fetch_assoc();
$fillProductName = $rowFill['ProductName'];
$fillMan_name = $rowFill['Man_name'];
$fillQuantity = $rowFill['Quantity'];

//get data from html
if (isset($_POST['confirm'])) {

    //declare variables
    $productName = $_POST['product-name']; //stored user entered product name
    $Man_name = $_POST['store-name']; //stored user entered store name
    $quantity = $_POST['quantity']; //stored user entered quantity

    // Fetch productID using productName
    $sql = "SELECT ProductID 
            FROM products 
            WHERE ProductName = ?";
    $stmtProduct = $mysqli->prepare($sql);
    $stmtProduct->bind_param("s", $productName);
    $stmtProduct->execute();
    $result = $stmtProduct->get_result();

    // Check the query is successful executed
    if (!$result || $result->num_rows == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'update';
        header('location: dispatchedOrders.php');
        exit;
        //echo "Your request is can not be done now. Please try again later.";
    }

    if (mysqli_num_rows($result)) {
        //fetch an item from result
        $row = $result->fetch_assoc();
        $productID = $row['ProductID'];

        //get ShopID using Man_name
        $sqlStore = "SELECT ShopID 
                     FROM shop 
                     WHERE Man_name = ?";
        $stmtStore = $mysqli->prepare($sqlStore);
        $stmtStore->bind_param("s", $Man_name);
        $stmtStore->execute();
        $resultStore = $stmtStore->get_result();

        if (!$resultStore || $resultStore->num_rows == 0) {
            $_SESSION['status'] = 'error';
            $_SESSION['operation'] = 'update';
            header('location: dispatchedOrders.php');
            exit;
            //echo "Your request is can not be done now. Please try again later";
        } else {
            $rowStore = $resultStore->fetch_assoc();
            $ShopID = $rowStore['ShopID'];

            // Fetch quantity
            $sqlQuantity = "SELECT TotalQuantity 
                            FROM inventory 
                            WHERE ProductID = ?";
            $stmtQuantity = $mysqli->prepare($sqlQuantity);
            $stmtQuantity->bind_param("i", $productID);
            $stmtQuantity->execute();
            $resultQuantity = $stmtQuantity->get_result();

            if ($resultQuantity->num_rows > 0) {
                $rowQuantity = $resultQuantity->fetch_assoc();
                $availableQuantity = $rowQuantity['TotalQuantity'];

                // Calculate the updated quantity
                $updatedQuantity = $availableQuantity - ($quantity - $fillQuantity);

                // Fetch the latest unit price
                $sqlFetchUnitPrice = "SELECT UnitPrice 
                                      FROM purchaseorders 
                                      WHERE ProductID = ? 
                                      ORDER BY OrderDate DESC LIMIT 1";
                $stmtFetchUnitPrice = $mysqli->prepare($sqlFetchUnitPrice);
                $stmtFetchUnitPrice->bind_param("i", $productID);
                $stmtFetchUnitPrice->execute();
                $resultFetchUnitPrice = $stmtFetchUnitPrice->get_result();

                if ($resultFetchUnitPrice->num_rows > 0) {
                    $row = $resultFetchUnitPrice->fetch_assoc();
                    $unitPrice = $row['UnitPrice'];

                    // Calculate the updated total amount
                    $totalValue = $unitPrice * $updatedQuantity;

                    //checking enough quantity is available to dispatch order
                    if ($quantity > $availableQuantity) {

                        $_SESSION['status'] = 'inventory_error';
                        $_SESSION['operation'] = 'place';
                        header('location: dispatchedOrders.php');
                        exit;
                        //echo "Available quantity is not enough.";
                    }

                    //check quantity and total value are positive numbers
                    if ($updatedQuantity < 0 || $totalValue < 0) {
                        $_SESSION['status'] = 'error';
                        $_SESSION['operation'] = 'place';
                        header('location: dispatchedOrders.php');
                        exit;
                    }

                    // Update the inventory table
                    $sqlUpdateInventory = "UPDATE inventory 
                                           SET TotalQuantity = ?, TotalValue = ? 
                                           WHERE ProductID = ?";
                    $stmtUpdateInventory = $mysqli->prepare($sqlUpdateInventory);
                    $stmtUpdateInventory->bind_param("idi", $updatedQuantity, $totalValue, $productID);

                    if ($stmtUpdateInventory->execute()) {
                        $sqlInsertQuery = "UPDATE dispatchorders
                                           SET ProductID = ?,
                                           ShopID = ?,
                                           Quantity = ?,
                                           OrderDate = NOW()
                                           WHERE DispatchOrderID = ?";
                        $stmtUpdate = $mysqli->prepare($sqlInsertQuery);
                        $stmtUpdate->bind_param("iiii", $productID, $ShopID, $quantity, $updateID);

                        if ($stmtUpdate->execute()) {
                            $_SESSION['status'] = 'success';
                            $_SESSION['operation'] = 'update';
                            //echo "Order placed successfully!";
                            header('location: dispatchedOrders.php');
                        } else {
                            $_SESSION['status'] = 'error';
                            $_SESSION['operation'] = 'update';
                            header('location: dispatchedOrders.php');
                            //echo "Can not update inventory now. Try again later.";
                        }
                    }
                }
            }
        }
    }
}
$mysqli->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name</title>
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
                    <h1 class="h2">Dispatch Orders</h1>
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


                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <h2>Update Dispatched Order</h2>
                        </div>
                    </div class="row">
                    <div class="col-12 col-md-6">
                        <form method="post">
                            <div class="mb-3">
                                <label>Product Name:</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="product-name"
                                    name="product-name"
                                    value="<?php echo htmlspecialchars($fillProductName); ?>"
                                    placeholder="Search Product"
                                    required />
                                <div class="product-list" id="product-list"></div>
                            </div>
                            <div class="mb-3">
                                <label>Store Name:</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="store-name"
                                    name="store-name"
                                    value="<?php echo htmlspecialchars($fillMan_name); ?>"
                                    placeholder="Search Store"
                                    required />
                                <div class="store-list" id="store-list"></div>
                            </div>
                            <div class="mb-3">
                                <label>Quantity:</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="quantity"
                                    name="quantity"
                                    value="<?php echo htmlspecialchars($fillQuantity); ?>"
                                    placeholder="Enter Product Quantity"
                                    min="1"
                                    required />
                            </div>
                            <div class="mt-4">
                                <button type="submit"
                                    id="update-btn"
                                    name="confirm"
                                    class="btn btn-primary">
                                    Update
                                </button>
                                <button type="button" class="btn btn-secondary">
                                    <a href="dispatchedOrders.php" class="text-light link-offset-2 link-underline link-underline-opacity-0">Cancel</a>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="dispatchProductList.js"></script>
    <script src="dispatchStoreList.js"></script>
    <script>
        //validate quantity
        $(document).ready(function() {
            $("#quantity").on("input", function() {
                var value = $(this).val()
                if (value < 1) {
                    $(this).val('')
                }
            })
        })
    </script>
    <script>
        //validate all inputs
        $(document).ready(function() {
            function validateInputs() {
                var productName = $("#product-name").val().trim();
                var Man_name = $("#store-name").val().trim();
                var quantity = $("#quantity").val().trim();

                if (productName == "" || Man_name == "" || quantity < 1) {
                    $("#update-btn").attr("disabled", true);
                } else {
                    $("#update-btn").attr("disabled", false);
                }
            }
            $("#product-name, #store-name, #quantity").on("input", function() {
                validateInputs();
            })

            validateInputs();
        })
    </script>
</body>

</html>
