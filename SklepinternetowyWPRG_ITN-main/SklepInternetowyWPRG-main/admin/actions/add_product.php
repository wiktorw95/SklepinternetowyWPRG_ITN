<?php
include '../../actions/connection.php';
$expire = 30 * 60; // 30 minutes
session_set_cookie_params($expire);
session_start();

// Process form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["add_product"])) {
    if (!isset($_SESSION["logged_in"])) {
        header("location: login.php?message=Please login/register first to place a product");
        exit;
    }

    // Collect form inputs
    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $image = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../../assets/imgs/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Error uploading image.");
        }
    }

    $price = $_POST["price"];
    $quantity = 0;
    $amount = $_POST["amount"];

    $stmt = $conn1->prepare("INSERT INTO products (product_name, product_category, product_description, product_image, product_price, product_quantity, product_amount) VALUES (?,?,?,?,?,?,?);");
    $stmt->bind_param('ssssiii', $name, $category, $description, $image, $price, $quantity, $amount);
    $stmt->execute();

    header("Location: ../products.php?add_success_message=Product added successfully!");
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
                </li>
            </ul>
        </div>
    </div>
</nav>


<section class="my-5 py-5">
    <div class="container-fluid">
        <div class="row" style="min-height: 85vh">
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                </div>
                <div class="table-responsive">
                    <h2>Add Product</h2>
                    <hr>
                    <!-- Add Product Form -->
                    <div class="admin-panel mt-5">

                        <div class="container mb-4">
                            <form id="edit-form" method="POST" action="add_product.php" enctype="multipart/form-data">
                                <?php if (isset($product)) { ?>
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <?php } ?>

                                <div class="row">
                                    <!-- Product Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product-name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product-name" name="name"
                                               value="<?php echo isset($product) ? $product['product_name'] : ''; ?>"
                                               placeholder="Enter product name" required/>
                                    </div>

                                    <!-- Product Price -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product-price" class="form-label">Price ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="product-price" name="price"
                                               value="<?php echo isset($product) ? $product['product_price'] : ''; ?>"
                                               placeholder="0.00" required/>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Category Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product-category" class="form-label">Category</label>
                                        <select class="form-select" id="product-category" name="category" required>
                                            <option value="" disabled selected>Select a category</option>
                                            <option value="electronics" <?php echo (isset($product) && $product['product_category'] == 'electronics') ? 'selected' : ''; ?>>Electronics</option>
                                            <option value="clothing" <?php echo (isset($product) && $product['product_category'] == 'clothing') ? 'selected' : ''; ?>>Clothing</option>
                                            <option value="furniture" <?php echo (isset($product) && $product['product_category'] == 'furniture') ? 'selected' : ''; ?>>Furniture</option>
                                            <option value="books" <?php echo (isset($product) && $product['product_category'] == 'books') ? 'selected' : ''; ?>>Books</option>
                                            <option value="beauty" <?php echo (isset($product) && $product['product_category'] == 'beauty') ? 'selected' : ''; ?>>Beauty & Personal Care</option>
                                            <option value="sports" <?php echo (isset($product) && $product['product_category'] == 'sports') ? 'selected' : ''; ?>>Sports & Outdoors</option>
                                            <option value="toys" <?php echo (isset($product) && $product['product_category'] == 'toys') ? 'selected' : ''; ?>>Toys & Games</option>
                                        </select>
                                    </div>

                                    <!-- Product Amount/Quantity in Stock -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product-amount" class="form-label">Quantity in Stock</label>
                                        <input type="number" min="0" class="form-control" id="product-amount" name="amount"
                                               value="<?php echo isset($product) ? $product['product_amount'] : ''; ?>"
                                               placeholder="0" required/>
                                    </div>
                                </div>

                                <!-- Product Description -->
                                <div class="mb-3">
                                    <label for="product-description" class="form-label">Description</label>
                                    <textarea class="form-control" id="product-description" name="description" rows="4"
                                              placeholder="Enter product description" required><?php echo isset($product) ? htmlspecialchars($product['product_description']) : ''; ?></textarea>
                                </div>

                                <!-- Product Image -->
                                <div class="mb-3">
                                    <label for="product-image" class="form-label">Product Image</label>

                                    <?php if (isset($product) && !empty($product['product_image'])) { ?>
                                        <div class="mb-2">
                                            <img src="assets/imgs/<?php echo htmlspecialchars($product['product_image']); ?>"
                                                 alt="Current product image" class="img-thumbnail" style="max-height: 100px;">
                                            <p class="form-text">Current image: <?php echo htmlspecialchars($product['product_image']); ?></p>
                                        </div>
                                    <?php } ?>

                                    <input type="file" class="form-control" id="product-image" name="image"
                                           accept="image/jpeg, image/png, image/gif"/>
                                    <div class="form-text">
                                            Upload a product image (JPEG, PNG, or GIF format).
                                    </div>

                                    <!-- Keep current image flag -->
                                    <?php if (isset($product) && !empty($product['product_image'])) { ?>
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['product_image']); ?>">
                                    <?php } ?>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="../products.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" name="add_product" class="btn buy-btn">Save Changes</button>
                                </div>
                            </form>
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