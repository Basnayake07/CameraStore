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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .panel {
            height: 20vh;
            background-color: #003C43;
            color: white;
            border: 2px outset black;
        }

        .panel:hover {
            transform: translateY(-5px);
            transition: 0.3s;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Modal styles */
        #help-support-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content1 {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="profile-container dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons" style="font-size:48px;">account_circle</i>
                            <div class="profile-text ms-2">
                                <span><?php echo htmlspecialchars($username); ?></span>
                                <span><?php echo htmlspecialchars($role); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="#" id="help-link">Help & Support</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                        </ul>
                    </div>

                </div>
            <!-- Main content can be added here -->
            <div class="row">
                <div class="col-md-3">
                    <div class="panel">
                        <h4>No. of Products</h4>
                        <p id="Categories"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <h4>No. of Suppliers</h4>
                        <p id="Suppliers"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <h4>Total Inventory Values</h4>
                        <p id="inventoryvalues"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <h4>Total Dispatch Quantity</h4>
                        <p id="dispatchquantity"></p>
                    </div>
                </div>
                
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-6">                    
                        
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
                    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
                    <script>
                        // Initialize the chart with empty data
                        var chart = new Chart("myChart", {
                            type: "bar",
                            data: {
                                labels: [], // Initially empty
                                datasets: [{
                                    backgroundColor: ["#FF5733", "#33FF57", "#3357FF", "#FF33A6", "#FFD133", "#33FFF5", "#FF8C33", "#8C33FF", 
                                    "#FF3333", "#33FFB8", "#336BFF", "#FF3388", "#FF5733", "#FF9A33", "#A833FF", "#33FF77", "#FF33FF", "#FF5733", "#33B8FF", "#FF33D1"], // Example colors
                                    data: [] // Initially empty
                                }]
                            },
                            options: {
                                legend: { display: false },
                                title: {
                                    display: true,
                                    text: "Total quantity of products dispatched in the current month"
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true, // Ensures the y-axis starts at 0
                                            min: 0, // Minimum y-axis value
                                        }
                                    }]
                                }
                            }
                        });
                
                        // Fetch data from the server
                        fetch('chart.php') 
                            .then(response => response.json())
                            .then(data => {
                                // Update chart data
                                chart.data.labels = data.xValues;
                                chart.data.datasets[0].data = data.yValues;
                                chart.update(); // Refresh the chart
                            })
                            .catch(error => console.error('Error fetching data:', error));
                    </script>                            
                    
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <h4>No. of Stores</h4>
                        <p id="storescount"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <h4>Orders yet to be received</h4>
                        <p id="yetReceived"></p>
                    </div>
                </div>
            </div>                
            
            </main>
        </div>
    </div>

    <!-- help and support section-->
    <div id="help-support-modal" class="modal">
        <div class="modal-content1">
            <span class="close" id="close-help">&times;</span>
            <!--Just text part from here-->
            <h2>Help & Support</h2>
            <div class="faq-section">
                <h4>Frequently Asked Questions</h4>
                <div class="faq-item">
                    <h5>How do I view purchased orders and download reports?</h5>
                    <p>Purchase orders can be viewed in the purchased order section in the sidebar. You can view product names, supplier Names, quantities, prices, ordered date and Statuses. 
                        You can click on the button “Download CSV Report” to download the reports.</p>
                </div>
                <div class="faq-item">
                    <h5>How do I manage Suppliers?</h5>
                    <p>To manage the Suppliers, go to the Suppliers section in the sidebar, and from there, you can add, update, and search Supplier details.</p>
                </div>
                <div class="faq-item">
                    <h5>How do I update the user account details?</h5>
                    <p>Go to the user profile and click on "Edit Profile". You can change your Phone Number and password. Then click on “Save Changes” button.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Fetch categories and suppliers counts from the server
        fetch('get_counts.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Display the counts in the corresponding elements
                document.getElementById('Categories').textContent = data.categories;
                document.getElementById('Suppliers').textContent = data.suppliers;
                document.getElementById('inventoryvalues').textContent = data.inventoryvalues;
                document.getElementById('dispatchquantity').textContent = data.dispatchquantity;
                document.getElementById('storescount').textContent = data.storescount;
                document.getElementById('yetReceived').textContent = data.yetReceived;
            })
            .catch(error => console.error('Error fetching data:', error));

        // Open the Help & Support modal
        document.getElementById('help-link').addEventListener('click', function(event) {
            event.preventDefault();
            const helpModal = document.getElementById('help-support-modal');
            helpModal.style.display = 'block';
        });

        // Close the Help & Support modal
        document.getElementById('close-help').addEventListener('click', function() {
            const helpModal = document.getElementById('help-support-modal');
            helpModal.style.display = 'none';
        });

        // Close the modal when clicking outside of it
        window.addEventListener('click', function(event) {
            const helpModal = document.getElementById('help-support-modal');
            if (event.target == helpModal) {
                helpModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
