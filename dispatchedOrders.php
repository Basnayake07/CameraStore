<?php
session_start();
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
    <title>Dispatch Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sidebar"
                aria-expanded="false"
                aria-controls="sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar navigation -->
            <nav
                id="sidebar"
                class="col-md-2 d-md-block collapse sidebar">
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

                <!-- show alert messages -->
                <?php
                if (isset($_SESSION['status']) && isset($_SESSION['operation'])) {
                    $status = $_SESSION['status'];
                    $operation = $_SESSION['operation'];
                    if ($status == 'success') {
                        $alertClass = "alert-success";
                    } else {
                        $alertClass = "alert-danger";
                    }

                    if ($status == 'success') {
                        $message = "Order " . $operation . "d successfully";
                    } else if ($status == 'inventory_error') {
                        $message = "Fail to " . $operation . " order. Available inventory is not enough.";
                    } else {
                        $message = "Fail to " . $operation . " order. Try again later.";
                    }

                    echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                            <strong>$message</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";

                    //clear the session status
                    unset($_SESSION['status']);
                    unset($_SESSION['operation']);
                }
                ?>


                <!-- Main content can be added here -->
                <!--methana idala oyalage part eka gahanna-->
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-1 mb-3 clearfix">
                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                        <!-- place new order button -->
                                        <a href="dispatchOrder.php" class="btn btn-primary"><i class="fa fa-plus"></i> Place New Order</a>
                                        <form action="dispatchOrderCSV.php" method="post">
                                            <button type="submit"
                                                name="export"
                                                id="export-btn"
                                                class="btn btn-primary">
                                                Download CSV Report
                                            </button>
                                        </form>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <form method="GET" action="dispatchSearch.php" class="d-flex align-items-center w-46">
                                            <input
                                                type="text"
                                                name="search"
                                                id="search"
                                                class="form-control me-2"
                                                placeholder="Search order"><br>
                                        </form>
                                    </div>
                                    <br>
                                    <div style="height: 350px; overflow-y: auto;">
                                        <table class="table table-hover" id="table">
                                            <thead style="position: sticky; top: 0; background-color: white; z-index: 100;">
                                                <tr>
                                                    <th scope="col">DispatchOrderID</th>
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Manager Name</th>

                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Order Date</th>
                                                    <th scope="col">Operation</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                // connect to the database
                                                include "config.php";

                                                //  query to select details about dispatched order
                                                $sql = "SELECT 
                                                        so.DispatchOrderID, p.ProductName, s.Man_name, so.Quantity, so.OrderDate
                                                        FROM DispatchOrders so
                                                        JOIN products p ON so.ProductID = p.ProductID
                                                        JOIN shop s ON so.ShopID = s.ShopID
                                                        ORDER BY so.OrderDate DESC
                                                        ";

                                                //shop results
                                                $result = mysqli_query($mysqli, $sql);
                                                if (mysqli_num_rows($result) > 0) {

                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $saleOrderID = $row['DispatchOrderID'];
                                                        $productName = $row['ProductName'];
                                                        $shopName = $row['Man_name'];
                                                        $quantity = $row['Quantity'];
                                                        $orderDate = $row['OrderDate'];

                                                        echo '<tr>
                                                        <td>' . $saleOrderID . '</td>
                                                        <td>' . $productName . '</td>
                                                        <td>' . $shopName . '</td>
                                                      
                                                        <td>' . $quantity . '</td>                                                                                                             
                                                        <td>' . $orderDate . '</td>
                                                        <td>';

                                                        // check user role and display appropriate actions
                                                        if ($_SESSION['role'] !== 'Worker') {
                                                            echo "<a href='dispatchOrderUpdate.php?updateID=$saleOrderID' class='link-dark update-link'><i class='fa-solid fa-pen-to-square fs-5 me-3'></i></a>";
                                                            echo "<a href='dispatchOrderDelete.php?deleteID=$saleOrderID' class='link-dark delete-link'><i class='fa-solid fa-trash-alt fs-5'></i></a>";
                                                        } else {
                                                            echo "<i class='fa-solid fa-pen-to-square fs-5 me-3' style='color: gray; cursor: not-allowed;' title='Edit (disabled)'></i>";
                                                            echo "<i class='fa-solid fa-trash-alt fs-5' style='color: gray; cursor: not-allowed;' title='Delete (disabled)'></i>";
                                                        }

                                                        echo '</td></tr>';
                                                    }

                                                    // Free result set
                                                    mysqli_free_result($result);
                                                } else {
                                                    if (mysqli_num_rows($result) == 0) {
                                                        echo "No orders placed yet.";
                                                    } else {
                                                        echo "Database connection error.";
                                                    }
                                                }
                                                // Close connection
                                                mysqli_close($mysqli);
                                                ?>
                                            </tbody>
                                        </table>

                                        <!-- to show search results -->
                                        <div id="search-result"></div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dispatchSearchList.js"></script>

    <!-- alert function created by @senuda -->
    <script>
        function fadeAlerts() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('hide');
                    setTimeout(() => alert.remove(), 500); // Remove after fade-out
                }, 3000); // Adjust delay as needed
            });
        }

        // call the function after page loads
        window.onload = function() {
            fadeAlerts();
        }
    </script>
    <script>
        $(document).ready(function() {
            // display pop up message before deletion
            $('.delete-link').on('click', function(e) {
                // Prevent the default process
                e.preventDefault();

                var result = confirm("Are you sure you want to delete this item?");

                // If click ok, delete the record
                if (result) {
                    window.location.href = $(this).attr('href');
                }

            });
        });
    </script>
    </script>


</body>

</html>
