<?php

include '../../actions/connection.php';
$expire = 30 * 60; // 30 minut
session_set_cookie_params($expire);
session_start();

if(isset($_GET['order_id'])) {
    $product_id = $_GET['order_id'];
    $stmt = $conn1 -> prepare("SELECT * FROM orders o JOIN users u ON o.user_id = u.user_id WHERE order_id = ?");
    $stmt -> bind_param("i", $product_id);
    $stmt -> execute();

    $order = $stmt -> get_result();

} else if(isset($_POST['edit_order'])) {
    $order_status = $_POST['order_status'];
    $order_id = $_POST['order_id'];

    $stmt = $conn1->prepare("UPDATE orders SET order_status=? WHERE order_id=?");
    $stmt->bind_param("si", $order_status, $order_id);
    if($stmt->execute()) {

        header('location: ../orders.php?order_updated= Edit order successfully!');
    } else {
        header("Location: ../orders.php?order_fail= Edit order fail!");
    }
} else{
    header("Location: ../orders.php?order_fail= Edit order fail!");
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css"/>
</head>
<body>

<!--navbar-->

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">FAKE<img src="../../assets/imgs/2560px-Allegro.pl_sklep.svg.png" style="height: 50px; width: 150px;"><?php if ($_SESSION['account_type'] == 'admin') {?> ADMIN <?php } ?></a>
        <div id="menu-search">
            <div id="search-block">
                <form class="search-bar"  action="../../search_result.php">
                    <input type="text" placeholder="Wpisz czego szukasz" name="search">
                </form>
            </div>
        </div>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'admin')) {?>
                        <a class="nav-link" href="../../account/account.php">Admin Konto</a>
                    <?php } else if (isset($_SESSION['user_id'])){ ?>
                        <a class="nav-link" href="../../account/account.php">Konto</a>
                    <?php } else { ?>
                        <a class="nav-link" href="../../account/login.php">Zaloguj się</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="my-5 py-5">
    <div class="container-fluid">
        <div class="row" style="min-height: 85vh">

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div id="edit-order">
                    <h1 class="h2"> Edit Order</h1>
                    <a href="../orders.php" class="btn btn-outline-secondary">Back to Orders</a>
                </div>

                <!-- Order edit form -->
                        <div class="card mb-4">
                            <div class="card-header  align-items-center">
                                <span>Order Information</span>
                            </div>
                            <div class="card-body">
                                <form id="edit-order-form" method="POST" action="edit_order.php">
                                    <?php foreach($order as $r){ ?>
                                        <input type="hidden" name="order_id" value="<?php echo $r['order_id']; ?>">

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="order-detail">
                                                    <div class="order-detail-title">Order ID</div>
                                                    <div class="order-detail-value">#<?php echo $r['order_id']; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="order-detail">
                                                    <div class="order-detail-title">Order Date</div>
                                                    <div class="order-detail-value"><?php echo date('F j, Y', strtotime($r['order_date'])); ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="order-detail">
                                                    <div class="order-detail-title">Customer</div>
                                                    <div class="order-detail-value"><?php echo $r['user_name']; ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="order-detail">
                                                    <div class="order-detail-title">Order Total</div>
                                                    <div class="order-detail-value">$<?php echo number_format($r['order_cost'], 2); ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="order_status" class="form-label fw-bold">Order Status</label>
                                            <select class="form-select form-select-lg" id="order_status" required name="order_status">
                                                <option value="On Hold" <?php if($r['order_status']=='On Hold'){echo "selected";}?>>On Hold</option>
                                                <option value="Not Paid" <?php if($r['order_status']=='Not Paid'){echo "selected";}?>>Not Paid</option>
                                                <option value="Paid" <?php if($r['order_status']=='Paid'){echo "selected";}?>>Paid</option>
                                                <option value="Shipped" <?php if($r['order_status']=='Shipped'){echo "selected";}?>>Shipped</option>
                                                <option value="Delivered" <?php if($r['order_status']=='Delivered'){echo "selected";}?>>Delivered</option>
                                            </select>
                                        </div>

                                        <div class="d-grid mt-4">
                                            <button type="submit" class="btn btn-primary" name="edit_order"> Update Order Status
                                            </button>
                                        </div>
                                    <?php } ?>
                                </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>


<!--Footer-->
<footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pt-3 text-uppercase">Wiktor Wilk</h5>
            <h5 class="pt-3">s30897@pjwstk.edu.pl</h5>
            <h5 class="pt-3 text-uppercase">82-500, Kwidzyn</h5>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>