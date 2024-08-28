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
require_once "config.php";

// Define variables and initialize with empty values
$receivedqty = $status = $orderedQty = "";
$receivedqty_err = $status_err = "";

// Check if `id` is set and is a valid value
if (isset($_POST["id"]) && !empty(trim($_POST["id"]))) {
    // Handle POST request
    $id = trim($_POST["id"]);

    // Retrieve the data from db
    $sql = "SELECT QuantityOrdered,QuantityRecieved FROM purchaseorders WHERE PurchaseOrderID = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $id;

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $orderedQty = $row["QuantityOrdered"];
                $currentQty = $row["QuantityRecieved"];
            } else {
                $receivedqty_err = "No data found";
            }
        } else {
            $receivedqty_err = "Oops! Something went wrong. Please try again later.";
        }
    } else {
        $receivedqty_err = "Failed to prepare SQL statement.";
    }

    // Validate quantity
    $input_reQty = trim($_POST["receivedqty"]);
    if (empty($input_reQty)) {
        $receivedqty_err = "Please fill the quantity field ";
    } elseif (!is_numeric($input_reQty) || $input_reQty < 0) {
        $receivedqty_err = "Please enter a valid value.";
    } elseif (($input_reQty+$currentQty) > $orderedQty) {
        $receivedqty_err = "Not valid (Quantity is greater than ordered Quantity).";
    } else {
        $receivedqty = $input_reQty;
    }

    // Validate status
    $input_status = trim($_POST["status"]);
    if (empty($input_status)) {
        $status_err = "Please select a status.";
    } else {
        $status = $input_status;
    }

    // If no errors, update the record
    if (empty($receivedqty_err) && empty($status_err)) {
        $sql = "UPDATE purchaseorders SET Status = ?, QuantityRecieved = COALESCE(QuantityRecieved, 0) + ? WHERE PurchaseOrderID = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("sii", $status, $receivedqty, $id);
            if ($stmt->execute()) {
                $sql = "INSERT INTO productreceiveddate (PurchaseOrderID,quantity) VALUES(?,? )";
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("ii", $id,$receivedqty);
                    if ($stmt->execute()) {
                        // Records updated successfully. Redirect to purchaseview page
                           header("Location: purchaseView.php?status=success&message=Data+successfully+Updated");
                            exit();   
                  
                         } else {
                        
                        header("Location: purchaseView.php?status=error&message=Oops!+Something+went+wrong.+Please+try+again+later.");
                        exit();
                    }
                } else {
                    
                    
                    header("Location: purchaseView.php?status=error&message=Failed+to+prepare+update+SQL+statement.");
                    exit();       

                }

            } else {
                
                     header("Location: purchaseView.php?status=error&message=Oops!+Something+went+wrong.+Please+try+again+later.");
                     exit();

            }
        } else {
           
                 header("Location: purchaseView.php?status=error&message=Failed+to+prepare+update+SQL+statement.");
                 exit();   

        }

    }
    // Close statement
    $stmt->close();
    $mysqli->close();
} elseif (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Handle GET request
    $id = trim($_GET["id"]);

    // Retrieve the data from db
    $sql = "SELECT QuantityOrdered,QuantityRecieved FROM purchaseorders WHERE PurchaseOrderID = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $id;

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $orderedQty = $row["QuantityOrdered"];
                $currentQty = $row["QuantityRecieved"];
            } else {
                $receivedqty_err = "No data found";
            }
        } else {
            $receivedqty_err = "Oops! Something went wrong. Please try again later.";
        }
    } else {
        $receivedqty_err = "Failed to prepare SQL statement.";
    }

    // Close statement
    $stmt->close();

    $mysqli->close();
} else {
    //  Redirect to error page
    header("location: purchaseError.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <!--links for dropdown select box-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <style>
        #updateform {
         min-height: 100vh;
         margin-left: 250px; 
         
         overflow-y: auto;
        }
        #sidebar {
    
         position: fixed; 
        }
        main {
         margin-left: 250px; 
         padding: 20px; 
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
                    <h1 class="h2">Update Purchase</h1>
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
                <!-- Main content form -->
                <div class="col-md-8">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="updateform">
                    <div class="form-group">
                        <label>Received Quantity</label>
                        <input type="text" name="receivedqty"
                            class="form-control <?php echo (!empty($receivedqty_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $receivedqty; ?>">
                        <span class="invalid-feedback"><?php echo $receivedqty_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control selectpicker <?php echo (!empty( $status_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $status; ?>">
                             <option value="">Select a product</option>
                            <option value="In-Progress" >In-Progress</option>
                            <option value="Complete" >Complete</option>
                        </select>
                        <span class="invalid-feedback"><?php echo $status_err; ?></span>
                    </div>

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="purchaseView.php" class="btn btn-secondary ml-2">Cancel</a>
                </form></div>
            </main>
        </div>
    </div>

 

    <!-- links for dropdown select box -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>
