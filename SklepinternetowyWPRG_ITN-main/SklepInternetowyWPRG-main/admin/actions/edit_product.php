<?php

include '../../actions/connection.php';
$expire = 30 * 60; // 30 minut
session_set_cookie_params($expire);
session_start();

if(isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn1 -> prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
} else if(isset($_POST['edit_btn'])){
    $product_id = $_POST['product_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn1->prepare("UPDATE products SET product_name=?, product_description=?, product_price=?, product_category=? WHERE product_id=?");
    $stmt->bind_param("ssssi", $title, $description, $price, $category, $product_id);
    if($stmt->execute()) {

        header('location: ../products.php?edit_success_message= Edit product successfully!');
    } else {
        header("Location: ../products.php?edit_fail_message= Edit product fail!");
    }

} else{
    header("Location: ../products.php?edit_fail_message= Edit product fail!");
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
                    <?php } else if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'worker') ){ ?>
                        <a class="nav-link" href="../../account/account.php">Worker Konto</a>
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-5 mt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="bi bi-pencil-square"></i> Edit Product</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="../products.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>

                <!-- Error message display -->
                <?php if(isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_GET['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>

                <!-- Product edit form -->
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Product Information</span>
                            </div>
                            <div class="card-body">
                                <form id="edit-form" method="POST" action="edit_product.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                                    <div class="mb-3">
                                        <label for="product-name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product-name" name="title"
                                               value="<?php echo $product['product_name']; ?>" placeholder="Enter product name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="product-description" class="form-label">Description</label>
                                        <textarea class="form-control" id="product-description" name="description"
                                                  rows="4" placeholder="Enter product description" required><?php echo $product['product_description']; ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="product-price" class="form-label">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control" id="product-price" name="price"
                                                   value="<?php echo $product['product_price']; ?>" placeholder="0.00" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="product-category" class="form-label">Category</label>
                                        <select class="form-select" id="product-category" name="category" required>
                                            <option value="electronics" <?php echo (isset($product) && $product['product_category'] == 'electronics') ? 'selected' : ''; ?>>Electronics</option>
                                            <option value="clothing" <?php echo (isset($product) && $product['product_category'] == 'clothing') ? 'selected' : ''; ?>>Clothing</option>
                                            <option value="furniture" <?php echo (isset($product) && $product['product_category'] == 'furniture') ? 'selected' : ''; ?>>Furniture</option>
                                            <option value="books" <?php echo (isset($product) && $product['product_category'] == 'books') ? 'selected' : ''; ?>>Books</option>
                                            <option value="beauty" <?php echo (isset($product) && $product['product_category'] == 'beauty') ? 'selected' : ''; ?>>Beauty & Personal Care</option>
                                            <option value="sports" <?php echo (isset($product) && $product['product_category'] == 'sports') ? 'selected' : ''; ?>>Sports & Outdoors</option>
                                            <option value="toys" <?php echo (isset($product) && $product['product_category'] == 'toys') ? 'selected' : ''; ?>>Toys & Games</option>
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="../products.php" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn buy-btn" name="edit_btn">
                                            <i class="bi bi-check-circle"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Product preview card -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <span>Product Preview</span>
                            </div>
                            <div class="card-body text-center">
                                <?php if (isset($product['product_image']) && !empty($product['product_image'])) { ?>
                                    <img src="../../assets/imgs/<?php echo $product['product_image']; ?>" class="preview-image mb-3" alt="Product Image">
                                <?php } ?>
                                <h5 class="fw-bold"><?php echo $product['product_name']; ?></h5>
                                <p class="text-muted small"><?php echo substr($product['product_description'], 0, 100) . (strlen($product['product_description']) > 100 ? '...' : ''); ?></p>
                                <h4 class="text-primary fw-bold">$<?php echo number_format($product['product_price'], 2); ?></h4>
                            </div>
                        </div>
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