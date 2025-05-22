<?php

$expire = 30 * 60; // 30 minut
session_set_cookie_params($expire);
session_start();

include 'actions/connection.php';


if(isset($_GET['product_id'])){
    $product_id = $_GET['product_id'];
    $stmt = $conn1->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $product_id = (int)$_GET['product_id'];

    if (!isset($_SESSION['recently_viewed']) || !is_array($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = [];
    }

    if (($key = array_search($product_id, $_SESSION['recently_viewed'])) !== false) {
        unset($_SESSION['recently_viewed'][$key]);
    }

    array_unshift($_SESSION['recently_viewed'], $product_id);

    if (count($_SESSION['recently_viewed']) > 4) {
        $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 4);
    }
} else {
    header("location: index.php");
    exit;
}

$reviews = [];

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    $stmt = $conn1->prepare("
        SELECT r.Rating_ID, r.Users_ID, r.Rating, r.Comment, u.user_name 
        FROM ratings r
        JOIN users u ON r.Users_ID = u.user_id
        WHERE r.Products_ID = ?
        ORDER BY r.Rating DESC
    ");

    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    $rating = (int)$_POST['rating'];
    $user_id = $_SESSION['user_id'];
    $rating_id = $_POST['rating_id'];

    $stmt = $conn1->prepare("INSERT INTO ratings (Rating_ID, Users_ID, Products_ID, Rating, Comment) VALUES ( ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $rating_id, $user_id, $product_id, $rating, $comment);
    $stmt->execute();


    header("Location: single_product.php?product_id=$product_id");
    exit;
}

$userRating = null;
$userComment = "";

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = $_GET['product_id'];

    $stmt = $conn1->prepare("SELECT Rating, Comment FROM ratings WHERE Users_ID = ? AND Products_ID = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($userReview = $result->fetch_assoc()) {
        $userRating = $userReview['Rating'];
        $userComment = $userReview['Comment'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

<!--navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">FAKE<img src="../assets/imgs/2560px-Allegro.pl_sklep.svg.png" style="height: 50px; width: 150px;"><?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin') {?> ADMIN <?php } ?></a>
        <div id="menu-search">
            <div id="search-block">
                <form class="search-bar"  method="GET" action="search_result.php">
                    <input type="text" placeholder="Wpisz czego szukasz" name="search">
                </form>
            </div>
        </div>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'admin')) {?>
                        <a class="nav-link" href="account/account.php">Admin Konto</a>
                    <?php } else if (isset($_SESSION['account_type']) && ($_SESSION['account_type'] == 'worker') ){ ?>
                        <a class="nav-link" href="account/account.php">Worker Konto</a>
                    <?php } else if (isset($_SESSION['user_id'])){ ?>
                        <a class="nav-link" href="account/account.php">Konto</a>
                    <?php } else { ?>
                        <a class="nav-link" href="account/login.php">Zaloguj się</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!--Single Product-->
<section class="container single-product my-5 pt-5">
    <div class="row mt-5">
        <div class="col-lg-5 col-md-6 col-sm-12">
            <img class="img-fluid w-100 pb-1" src="assets/imgs/<?php echo $product['product_image']; ?>"/>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">
            <h3 class="py-4"><?php echo $product['product_name']; ?></h3>
            <h2><?php echo $product['product_price']; ?>zł</h2>
            <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>"/>
                <input type="hidden" name="product_image" value="<?php echo $product['product_image']; ?>"/>
                <input type="hidden" name="product_name" value="<?php echo $product['product_name'] ?>"/>
                <input type="hidden" name="product_price" value="<?php echo $product['product_price'] ?>">
                <input type="number" name="product_quantity" value="1"/>
                <p style="color: red"><?php if(isset($_GET['TooMuch'])){echo $_GET['TooMuch'];}?></p>
                <button class="buy-btn" type="submit" name="add_to_cart">Add To Cart</button>
            </form>
            <h3 class="mt-5 mb-5">Product details</h3>
            <hr>
            <h4>Stan produktu w magazynie:</h4>
            <p class="description-text">
                <?php echo "Aktualna ilość: " . $product['product_amount']; ?>
            </p>
            <h4>Description:</h4>
            <span><?php echo $product['product_description']; ?></span>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])) { ?>
        <h3 class="text-center mt-5">Dodaj komentarz i oceń!</h3>
        <div id="add-comment-rating-container">
            <form id="add-comment-rating-form" method="post" class="container text-center">
                <input id="comment-area" name="comment" placeholder="Dodaj komentarz" style="width: 500px;"  required/>
                <input id="rating-input" type="number" name="rating" min="1" max="5" required placeholder="Ocena (1-5)" value="<?php echo $userRating ? $userRating : ''; ?>">
                <button id="add-comment-input" type="submit" class="buy-btn">
                    <?php echo 'Dodaj ocenę'; ?>
                </button>
            </form>
        </div>
    <?php } else { ?>
        <h3 style="font-family: 'Montserrat', sans-serif;" class="text-center mt-5"> Musisz być <a style="text-decoration: underline" href="account/login.php">zalogowanym!</a> by dodawać komentarze</h3>
    <?php } ?>

    <div id="reviews" class="text-center my-5">
        <h3>Oceny produktu:</h3>
        <hr class="mx-auto">
        <?php if (count($reviews) > 0) { ?>
            <?php foreach ($reviews as $review) { ?>
                <div class="review">
                    <div class="rating"><strong>User: </strong><span><?= htmlspecialchars($review['user_name']) ?></span></div>
                    <div class="rating"><strong>Ocena: </strong><span><?= $review['Rating'] ?>/5</span></div>
                    <div class="comment"><strong>Komentarz: </strong><?= htmlspecialchars($review['Comment']) ?></div>
                    <hr class="mx-auto">
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>Brak ocen dla tego produktu. Bądź pierwszy!</p>
        <?php } ?>
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