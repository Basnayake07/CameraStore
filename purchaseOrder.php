<?php

// Include config file
require_once "config.php";

$supplierid = $unitprice = $productid = $qtyorder = $suppliername = $productname = "";
$supplierid_err = $unitprice_err = $productname_err = $qtyorder_err = $suppliername_err = $productid_err = "";

session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];



// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate productname
    $input_pname = trim($_POST["productname"]);
    if (empty($input_pname)) {
        $productname_err = "Please select a product.";
    } else {
        $productname = $input_pname;
    }

    //validate product id
    $input_pid = trim($_POST["productID"]);
    if (empty($input_pid)) {
        $productid_err = "Please select a product.";
    } else {
        $productid = $input_pid;
    }

    // Validate suppliername
    $input_name = trim($_POST["suppliername"]);
    if (empty($input_name)) {
        $suppliername_err = "Please select a supplier.";
    } else {
        $suppliername = $input_name;
    }

    //validate supplier id
    $input_supid = trim($_POST["supplierID"]);
    if (empty($input_supid)) {
        $supplierid_err = "Please select a supplier.";
    } else {
        $supplierid = $input_supid;
    }

    // Validate quantity
    $input_qty = trim($_POST["qtyorder"]);
    if (empty($input_qty)) {
        $qtyorder_err = "Please enter the quantity.";
    } elseif (!is_numeric($input_qty) || $input_qty < 0) {
        $qtyorder_err = "Please enter a valied value.";
    } else {
        $qtyorder = $input_qty;
    }

    // Validate unitprice
    $input_price = trim($_POST["unitprice"]);
    if (empty($input_price)) {
        $unitprice_err = "Please enter the unitprice.";
    } elseif (!is_numeric($input_price) || $input_price <= 0) {
        $unitprice_err = "Please enter a valied value.";
    } else {
        $unitprice = $input_price;
    }

    if (empty($productname_err) && empty($suppliername_err) && empty($unitprice_err) && empty($qtyorder_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO purchaseorders (SupplierID, ProductID, QuantityOrdered,UnitPrice,Status) VALUES (?, ?, ?,?,?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $para_supplierid, $para_productid, $para_qtyorder, $para_unitprice, $para_status);

            // Set parameters
            $para_supplierid = $supplierid;
            $para_productid = $productid;
            $para_qtyorder = $qtyorder;
            $para_unitprice = $unitprice;
            $para_status = "Pending";




            if ($stmt->execute()) {
                // Redirect to the purchaseView.php page with a success message
                header("Location: purchaseView.php?status=success&message=Data+successfully+submitted");
                exit();
            } else {
                // Redirect with an error message
                header("Location: purchaseView.php?status=error&message=Oops!+Something+went+wrong.+Please+try+again+later.");
                exit();
            }
            

        }

        // Close statement
        $stmt->close();
    }





}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>purchase order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">



    <!--links for dropdown select box-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        #orderform {
            min-height: 100vh;

            padding: 20px;
            overflow-y: auto;
        }

        #sidebar {

            position: fixed;
        }

        @media (max-width: 767px) {
            #sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            main {
                margin-left: 0;
            }

            .table-responsive {
                overflow-x: auto;

            }
        }
    </style>



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
                            <a class="nav-link active" href="dashboard.php"><i
                                    class="material-icons">home</i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="InventoryUpdate.php"><i
                                    class="material-icons">inventory</i>Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productGet.php"><i class="material-icons">category</i>Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="purchaseView.php"><i
                                    class="material-icons">shopping_cart</i>Purchase Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dispatchedOrders.php"><i class="material-icons">sell</i>Dispatch
                                Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="suppliers.php"><i
                                    class="material-icons">local_shipping</i>Suppliers</a>
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
                    <h1 class="h2">purchase order</h1>
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

                            <div class="col-md-8">

                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                    id="orderform">


                                    <div class="form-group">
                                        <label>Product Name</label>

                                        <select name="productname" id="productname"
                                            class="form-control selectpicker <?php echo (!empty($productname_err)) ? 'is-invalid' : ''; ?>"
                                            value="<?php echo $productname; ?>" data-live-search="true"
                                            onchange="updateInputField1()">
                                            <option value="">Select a product</option>
                                            <?php

                                            //retrive the data from db
                                            $sql = "SELECT ProductID, ProductName FROM products ";
                                            $result = $mysqli->query($sql);

                                            //  Display data in the dropdown
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<option value="' . $row['ProductID'] . '">' . $row['ProductName'] . '</option>';

                                                }
                                            } else {
                                                echo '<option value="">No products available</option>';
                                            }
                                            ?>
                                        </select>
                                        <span class="invalid-feedback"><?php echo $productname_err; ?></span>
                                    </div>


                                    <div class="form-group">
                                        <label>Product ID</label>
                                        <input type="text" name="productID" id="productID" class="form-control"
                                            readonly>

                                    </div>

                                    <div class="selectbox">
                                        <label>Supplier Name</label>

                                        <select name="suppliername" id="suppliername"
                                            class="form-control selectpicker <?php echo (!empty($suppliername_err)) ? 'is-invalid' : ''; ?>"
                                            value="<?php echo $suppliername; ?>" data-live-search="true"
                                            onchange="updateInputField2()">
                                            <option value="">Select the supplier</option>
                                            <?php

                                            //retrive the data from db
                                            $sql = "SELECT SupplierID, SupplierName FROM suppliers ";
                                            $result = $mysqli->query($sql);
                                            // Step 3: Display data in the dropdown
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<option value="' . $row['SupplierID'] . '">' . $row['SupplierName'] . '</option>';

                                                }
                                            } else {
                                                echo '<option value="">No supplier available</option>';
                                            }
                                            ?>
                                        </select>
                                        <span class="invalid-feedback"><?php echo $suppliername_err; ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label>Supplier ID</label>
                                        <input type="text" name="supplierID" id="supplierID" class="form-control "
                                            readonly>

                                    </div>

                                    <div class="form-group">
                                        <label>Unit Price</label>
                                        <input type="text" name="unitprice" id="unitprice"
                                            class="form-control <?php echo (!empty($unitprice_err)) ? 'is-invalid' : ''; ?>"
                                            value="<?php echo $unitprice; ?>">
                                        <span class="invalid-feedback"><?php echo $unitprice_err; ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label>Quantity Ordered</label>
                                        <input type="text" name="qtyorder"
                                            class="form-control <?php echo (!empty($qtyorder_err)) ? 'is-invalid' : ''; ?>"
                                            value="<?php echo $qtyorder; ?>">
                                        <span class="invalid-feedback"><?php echo $qtyorder_err; ?></span>
                                    </div> <br>

                                    <input type="submit" class="btn btn-primary" value="Submit">
                                    <a href="PurchaseView.php" class="btn btn-secondary ml-2">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>

        function updateInputField1() { //update product ID auto according to product name

            var dropdown = document.getElementById('productname');
            var selectedValue = dropdown.value;

            var inputField = document.getElementById('productID');
            inputField.value = selectedValue;
        }

        function updateInputField2() { //update supplier ID auto according to selected supplier name

            var dropdown = document.getElementById('suppliername');
            var selectedValue = dropdown.value;

            var inputField = document.getElementById('supplierID');
            inputField.value = selectedValue;
        }


        


        // drop down select box
        $(document).ready(function () {
            $('selectbox').selectpicker();
        });


    </script>
    <!-- links for dropdown select box -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>
