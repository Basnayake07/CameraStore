<?php
include 'config.php';

if (isset($_GET['input'])) {
    $input = '%' . $_GET['input'] . '%';
    $sql = "SELECT 
            so.DispatchOrderID, p.ProductName, s.Man_name, so.Quantity, so.OrderDate
            FROM dispatchorders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN shop s ON so.ShopID = s.ShopID
            WHERE so.DispatchOrderID LIKE ? OR 
            p.ProductName LIKE ? OR 
            s.Man_name LIKE ? OR 
            so.Quantity LIKE ? OR 
            so.OrderDate LIKE ?
            ORDER BY so.OrderDate DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssss", $input, $input, $input, $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || mysqli_num_rows($result) == 0) {
        echo '<div class="alert alert-danger" role="alert">
                No product found
              </div>';
        return;
    } else {
        echo "<br>";
        echo '<table class="table table-hover">';
        echo '<thead>
        <tr>
            <th>Order ID</th>
            <th>Product Name</th>
            <th>Store Name</th>
            <th>Quantity</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr></thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            $saleOrderID = $row['DispatchOrderID'];
            $productName = $row['ProductName'];
            $Man_name = $row['Man_name'];
            $quantity = $row['Quantity'];
            $orderDate = $row['OrderDate'];

            echo '<tr>
                    <td>' . $saleOrderID . '</td>
                    <td>' . $productName . '</td>
                    <td>' . $Man_name . '</td>
                    <td>' . $quantity . '</td>                                                                                                             
                    <td>' . $orderDate . '</td>
                    <td>
                        <button type="button" class="btn btn-link">
                            <a href="dispatchOrderUpdate.php?updateID=' . $saleOrderID . '" class="text-dark link-offset-2 link-underline link-underline-opacity-0">
                                <i class="material-icons">edit</i>
                            </a>
                        </button>
                        <button type="button" class="btn btn-link">
                            <a href="dispatchOrderDelete.php?deleteID=' . $saleOrderID . '" class="text-dark link-offset-2 link-underline link-underline-opacity-0">
                                <i class="material-icons">delete</i>
                            </a>
                        </button>
                    </td>
                    </tr>';
        }
        echo '</tbody></table>';
        // Free result set
        $result->free();
    }
    // Close connection

    $stmt->close();
    mysqli_close($mysqli);
}
