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
$name = $location = $email = "";
$name_err = $location_err = $email_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }

    // Validate location
    $input_location = trim($_POST["location"]);
    if(empty($input_location)){
        $location_err = "Please enter an address.";     
    } else{
        $location = $input_location;
    }
    
   // Validate email
    $input_email = trim($_POST["email"]);
    if (empty($input_email)) {
        $email_err = "Fill the email address!<br />";
    } else {
    // Check if email contains any uppercase letters
        if (preg_match('/[A-Z]/', $input_email)) {
        $email_err = "This email address must contain only lowercase letters.";
        } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "This email address is invalid.";
        } else {
        $email = $input_email;
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($location_err) && empty($email_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO suppliers (suppliername, location, contactemail) VALUES (?, ?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_name, $param_location, $param_email);
            
            // Set parameters
            $param_name = $name;
            $param_location = $location;
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: suppliers.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .wrapper{
            width: 800px;
            margin: 0 auto;
        }

        .form-group{
            padding: 10px;
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
                            <h2 class="mt-5">Create Record</h2>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                    <span class="invalid-feedback"><?php echo $name_err;?></span>
                                </div>
                                <div class="form-group">
                                    <label>location</label>
                                    <textarea name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>"><?php echo $location; ?></textarea>
                                    <span class="invalid-feedback"><?php echo $location_err;?></span>
                                </div>
                                <div class="form-group">
                                    <label>Supplier's Email</label>
                                    <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <span class="invalid-feedback"><?php echo $email_err;?></span>
                                </div><br>
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a href="suppliers.php" class="btn btn-secondary ml-2">Cancel</a>
                            </form>
                        </div>
                    </div>        
                </div>
            </div>               
                    
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
