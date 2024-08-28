<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <style>
        .fade-away {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-away.hide {
            opacity: 0;
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
                            <a class="nav-link" href="dispatchedOrders.php"><i class="material-icons">sell</i>Dispatch Orders</a>
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
                    <h1 class="h2">Inventory</h1>
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

                <?php
                require_once "Config.php";

                // Handle quantity update
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateQuantity'])) {

                    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                        die("CSRF token validation failed");
                    }
                    $productID = $_POST['productID'];
                    $quantityToRemove = $_POST['quantity'];

                    // Fetch the current details for the product from Inventory
                    $stmt = $mysqli->prepare("SELECT TotalQuantity, TotalValue, (TotalValue / TotalQuantity) AS UnitPrice FROM Inventory WHERE ProductID = ?");
                    $stmt->bind_param("i", $productID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();

                    if ($product) {
                        $currentQuantity = $product['TotalQuantity'];
                        $currentTotalValue = $product['TotalValue'];
                        $unitPrice = $product['UnitPrice'];

                        if ($quantityToRemove <= $currentQuantity) {
                            // Calculate new total quantity and total value
                            $newQuantity = $currentQuantity - $quantityToRemove;
                            $newTotalValue = $newQuantity * $unitPrice;

                            // Update Inventory table with new values
                            $stmt = $mysqli->prepare("UPDATE Inventory SET TotalQuantity = ?, TotalValue = ? WHERE ProductID = ?");
                            $stmt->bind_param("idi", $newQuantity, $newTotalValue, $productID);
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success fade-away' role='alert'>Inventory updated successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger fade-away' role='alert'>Error updating inventory: " . $stmt->error . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-warning fade-away' role='alert'>Quantity to remove exceeds current inventory.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger fade-away' role='alert'>Product not found in inventory.</div>";
                    }

                    $stmt->close();
                }



                // Fetch data from Inventory
                $sql = "SELECT * FROM Inventory";
                $result = $mysqli->query($sql);

                if (!$result) {
                    die("Error executing query: " . $mysqli->error);
                }

                // Fetch distinct brands and types for the dropdowns
                $brandSql = "SELECT DISTINCT Brand FROM Inventory";
                $brandResult = $mysqli->query($brandSql);

                $typeSql = "SELECT DISTINCT Type FROM Inventory";
                $typeResult = $mysqli->query($typeSql);
                ?>

                <div class="container mt-5">
                    <button id="downloadCSV" class="btn btn-primary me-2 mr-5" style="margin-bottom: 1rem;">Download CSV
                        Report</button>

                    <!-- Filters -->
                    <div class="d-flex mb-3">
                        <select id="filterBrand" class="form-control me-2" onchange="filterTable()">
                            <option value="">Select Brand</option>
                            <?php
                            if ($brandResult->num_rows > 0) {
                                while ($row = $brandResult->fetch_assoc()) {
                                    echo "<option value='" . $row["Brand"] . "'>" . $row["Brand"] . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <select id="filterType" class="form-control" onchange="filterTable()">
                            <option value="">Select Type</option>
                            <?php
                            if ($typeResult->num_rows > 0) {
                                while ($row = $typeResult->fetch_assoc()) {
                                    echo "<option value='" . $row["Type"] . "'>" . $row["Type"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Search Bar -->
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for products..."
                        class="form-control mb-3">



                    <div style="height: 300px; overflow-y: auto;">
                        <!-- Inventory Table -->
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>SKU</th>
                                    <th>Total Quantity</th>
                                    <th>Last Received Date</th>
                                    <th>Total Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["ProductID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["ProductName"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["Brand"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["Type"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["SKU"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["TotalQuantity"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["LastReceivedDate"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["TotalValue"]) . "</td>";

                                        if ($role !== 'Worker') {
                                            echo "<td><span class='material-symbols-outlined' style='cursor: pointer;' onclick='showRemoveModal(" . $row["ProductID"] . ", \"" . htmlspecialchars($row["ProductName"]) . "\")'>do_not_disturb_on</span></td>";
                                        } else if ($role === 'Worker') {
                                            echo "<td><span class='material-symbols-outlined' style='cursor: not-allowed; color: gray;' onclick='alert(\"You do not have permission to remove this item.\")'>do_not_disturb_on</span></td>";
                                        } else {
                                            echo "<td>No Actions Available</td>";
                                        }

                                        echo "</tr>";

                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal for removing quantity -->
                <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="removeModalLabel">Remove Quantity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" id="removeForm">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="productID" id="productID">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity to Remove</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-danger" name="updateQuantity">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </main>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Function to show the remove modal with the correct Product ID
            window.showRemoveModal = function (productID) {
                document.getElementById('productID').value = productID;
                var removeModal = new bootstrap.Modal(document.getElementById('removeModal'));
                removeModal.show();
            }

            // Function to handle form submission with confirmation
            window.handleFormSubmit = function (event) {
                var form = event.target;
                var quantity = document.getElementById('quantity').value;
                var productName = document.getElementById('quantity').dataset.productName; // Get productName

                var confirmRemove = confirm(`Are you sure you want to remove ${quantity}?`);

                if (confirmRemove) {
                    form.submit(); // Proceed with form submission
                } else {
                    event.preventDefault(); // Prevent form submission
                }
            }

            // Bind handleFormSubmit function to form submission event
            var removeForm = document.getElementById('removeForm');
            removeForm.addEventListener('submit', handleFormSubmit);
            // Function to filter the table by brand and type
            window.filterTable = function () {
                var brand = document.getElementById('filterBrand').value.toLowerCase();
                var type = document.getElementById('filterType').value.toLowerCase();
                var rows = document.querySelectorAll('#inventoryTableBody tr');

                rows.forEach(row => {
                    var rowBrand = row.children[2].textContent.toLowerCase();
                    var rowType = row.children[3].textContent.toLowerCase();

                    if ((brand === '' || rowBrand.includes(brand)) &&
                        (type === '' || rowType.includes(type))) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Function to search the table
            window.searchTable = function () {
                var input = document.getElementById('searchInput').value.toLowerCase();
                var rows = document.querySelectorAll('#inventoryTableBody tr');

                rows.forEach(row => {
                    var cells = row.getElementsByTagName('td');
                    var found = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(input));

                    row.style.display = found ? '' : 'none';
                });
            }

            // Function to handle alert fading away
            function fadeAlerts() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide');
                        setTimeout(() => alert.remove(), 500); // Remove after fade-out
                    }, 3000); // Adjust delay as needed
                });
            }

            fadeAlerts();
        });


        document.getElementById('downloadCSV').addEventListener('click', function () {
            window.location.href = 'inventoryCSV.php';
        })
    </script>
</body>

</html>
