<?php
$expire = 30 * 60; // 30 minut
session_set_cookie_params($expire);
session_start();

include '../actions/connection.php';

if(!isset($_SESSION['logged_in'])){
    header('Location: login.php');
    exit;
}

if(isset($_GET['logout'])){
    if(isset($_SESSION['logged_in'])){
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_id']);
        $_SESSION['account_type'] = '';
        header('Location: login.php');
    }
}

if(isset($_POST['change_password'])){
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirmpassword']);
    $user_email = $_SESSION['user_email'];

    if ($password !== $confirm_password) {
        header('location: account.php?error=Password not match!');
    } //if password is less than 6 characters
    else if (strlen($password) < 6) {
        header('location: account.php?error=Password must be at least 6 characters!');
        //no errors
    } else{
        $stmt = $conn1->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $stmt->bind_param("ss", $password, $user_email);

        if($stmt->execute()){
            header('location: account.php?message=Password changed successfully!');
        }
    }
}

if(isset($_POST['users'])){
    header('location: admin/users.php');
} else if(isset($_POST['orders'])){
    header('location: admin/orders.php');
} else if(isset($_POST['products'])){
    header('location: admin/products.php');
}

//get orders
if(isset($_SESSION['logged_in'])){
    $user_id = $_SESSION['user_id'];
    $stmt = $conn1->prepare("SELECT * FROM orders WHERE user_id=?");

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Account</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../assets/css/style.css"/>
    </head>
<body>

    <!--navbar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">FAKE<img src="../assets/imgs/2560px-Allegro.pl_sklep.svg.png" style="height: 50px; width: 150px;"><?php if ($_SESSION['account_type'] == 'admin') {?> ADMIN <?php } ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="menu-search">
                <div id="search-block">
                    <form class="search-bar" method="GET" action="../search_result.php">
                        <input type="text" placeholder="Wpisz czego szukasz" name="search">
                    </form>
                </div>
            </div>
            <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'admin')) {?>
                            <a class="nav-link active" href="account.php">Admin Konto</a>
                        <?php } else if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'worker') ){ ?>
                            <a class="nav-link" href="account.php">Worker Konto</a>
                        <?php } else if (isset($_SESSION['user_id'])){ ?>
                            <a class="nav-link active" href="account.php">Konto</a>
                        <?php } else { ?>
                            <a class="nav-link" href="login.php">Zaloguj się</a>
                        <?php } ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Account Header -->
    <section id="account-header" class="text-center">
        <div class="container">
            <h2 class="display-5 fw-bold">My Account</h2>
            <p class="text-muted">Manage your profile and view your orders</p>
            <div class="notification-area">
                <p class="text-success mb-0"><?php if(isset($_GET['register_success'])){echo $_GET['register_success'];}?></p>
                <p class="text-success mb-0"><?php if(isset($_GET['login_success'])){echo $_GET['login_success'];}?></p>
            </div>
        </div>
    </section>

    <!--Account-->
    <section id="account">
        <div class="container">
            <div class="row">
                <!-- Account Info Column -->
                <div class="col-lg-6 col-md-12">
                    <div class="account-info">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-user-circle fa-3x me-3 text-primary"></i>
                            <h3 class="font-weight-bold m-0">Account Information</h3>
                        </div>
                        <hr class="mb-4">

                        <div class="mb-3">
                            <h5><i class="fas fa-user me-2"></i> Name</h5>
                            <p class="ps-4"><?php echo $_SESSION['user_name'];?></p>
                        </div>

                        <div class="mb-3">
                            <h5><i class="fas fa-envelope me-2"></i> Email</h5>
                            <p class="ps-4"><?php echo $_SESSION['user_email'];?></p>
                        </div>

                        <div class="mb-3">
                            <h5><i class="fas fa-tag me-2"></i> Account Type</h5>
                            <p class="ps-4"><?php echo $_SESSION['account_type'];?></p>
                        </div>

                        <div class="text-end mt-4">
                            <a href="account.php?logout=1" id="logout-btn" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>

                    <!-- Admin Panel -->
                    <?php if ($_SESSION['account_type'] == 'worker' || $_SESSION['account_type'] == 'admin') {?>
                        <div class="admin-panel mt-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-cogs fa-2x me-3 text-primary"></i>
                                <?php if ($_SESSION['account_type'] == 'admin') {?>
                                    <h3 class="font-weight-bold m-0">Admin Panel</h3>
                                <?php } else if ($_SESSION['account_type'] == 'worker') {?>
                                    <h3 class="font-weight-bold m-0">Worker Panel</h3>
                                <?php } ?>
                            </div>
                            <hr class="mb-4">
                            <div class="d-flex flex-wrap justify-content-between">
                                <?php if ($_SESSION['account_type'] == 'admin') {?>
                                    <a href="../admin/users.php" class="btn w-100 btn-warning m-2">
                                        <i class="fas fa-users me-2"></i>Users
                                    </a>
                                <?php } ?>
                                <a href="../admin/orders.php" class="btn w-100 btn-primary m-2">
                                    <i class="fas fa-shopping-bag me-2"></i>Orders
                                </a>
                                <a href="../admin/products.php" class="btn w-100 btn-primary m-2">
                                    <i class="fas fa-boxes me-2"></i>Products
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Password Change Column -->
                <div class="col-lg-6 col-md-12">
                    <form id="account-form" method="POST" action="account.php">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-key fa-2x me-3 text-primary"></i>
                            <h3 class="font-weight-bold m-0">Change Password</h3>
                        </div>

                        <p class="text-danger  mb-0"><?php if(isset($_GET['error'])){echo $_GET['error'];}?></p>
                        <p class="text-success mb-0"><?php if(isset($_GET['message'])){echo $_GET['message'];}?></p>

                        <hr class="mb-4">

                        <div class="form-group mb-3">
                            <label for="account-password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="account-password" name="password" placeholder="Enter new password" required/>
                            </div>
                            <small class="form-text text-muted">Password must be at least 6 characters</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="account-confirm-password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="account-confirm-password" name="confirmpassword" placeholder="Confirm new password" required/>
                            </div>
                        </div>

                        <div class="form-group text-end">
                            <button type="submit" name="change_password" class="btn btn-primary" id="change-pass-btn">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!--Orders-->
<?php if ($_SESSION['account_type'] != 'admin' && $_SESSION['account_type'] != 'worker') {?>
    <section id="orders" class="py-5 bg-light">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-shopping-basket fa-2x me-3 text-primary"></i>
                <h2 class="font-weight-bold m-0">Your Orders</h2>
            </div>
            <hr class="mb-4">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>Order ID</th>
                            <th><i class="fas fa-money-bill-wave me-2"></i>Cost</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Date</th>
                            <th><i class="fas fa-search me-2"></i>Details</th>
                        </tr>
                        </thead>
                        <?php if (count($orders) > 0) { ?>
                            <tbody>
                            <?php foreach($orders as $order) { ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['order_cost']; ?> zł</td>
                                    <td>
                                <span class="badge <?php
                                echo $order['order_status'] == 'On Hold' ? 'bg-warning' :
                                    ($order['order_status'] == 'Delivered' ? 'bg-success' : ($order['order_status'] == 'Not Paid' ?  'bg-danger' : ($order['order_status'] == 'Shipped' ?  'bg-primary' :'bg-info')));
                                ?>">
                                    <?php echo $order['order_status']; ?>
                                </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <form method="GET" action="../order_details.php">
                                            <input type="hidden" value="<?php echo $order['order_status']; ?>" name="order_status"/>
                                            <input type="hidden" value="<?php echo $order['order_id']; ?>" name="order_id"/>
                                            <button type="submit" class="btn btn-sm btn-primary order-details-btn" name="order_details_btn">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                    <h4>You haven't placed any orders yet</h4>
                    <p class="text-muted">When you place an order, it will appear here</p>
                    <a href="../shop.php" class="btn btn-primary mt-3">Start Shopping</a>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
