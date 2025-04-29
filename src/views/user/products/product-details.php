<?php
/* Get products */
include 'config.php';

$product_id = isset($_GET['id']) ? $_GET['id'] : null;

$sql = "SELECT * FROM PRODUCTS WHERE id = '$product_id'";
$query = $conn->query($sql);
$product = $query->fetch_assoc();

if (!$product) {
    echo "Product not found!!!!!";
    exit;
}

/* Add to cart */
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role'])) {
        echo '<script>window.location.href="/minimuji/login"</script>';
    }
    else {
        // Get product from form
        $product_id = $product['id']; 
        $user_id = (int)$_SESSION['id'];    
        $product_quantity = (int)$_POST['quantity'];
        $cart_id = null;
    
        // Check valid quantity
        if ($product['quantity'] < $product_quantity) {
            echo "<script>alert('Add to cart failed: Not enough stock available.'); window.location.href = \"/minimuji/product-details/{$product['id']}\";</script>";
            exit;
        }
        else {
            $check_cart_query = mysqli_query($conn, "SELECT * FROM CART_PRODUCTS WHERE products_id = '$product_id' AND cart_id = (SELECT id FROM CARTS WHERE user_id = '$user_id' AND status = 'Unfinished')") or die('query failed');

            // Product is already in cart
            if (mysqli_num_rows($check_cart_query) > 0) {
                $query = mysqli_fetch_assoc($check_cart_query);
                $temp_quantity = $query['quantity'] + $product_quantity;
                if ($product['quantity'] < $temp_quantity){
                    echo "<script>alert('Add to cart failed: Not enough stock available.'); window.location.href = \"/minimuji/product-details/{$product['id']}\";</script>";
                    exit;
                }
                $cart_query = mysqli_query($conn, "SELECT id FROM CARTS WHERE user_id = '$user_id' AND status = 'Unfinished'") or die('query failed');
                $cart_data = mysqli_fetch_assoc($cart_query);
                $cart_id = $cart_data['id'];
                mysqli_query($conn, "UPDATE CART_PRODUCTS SET quantity = quantity + $product_quantity WHERE products_id = '$product_id' AND cart_id = (SELECT id FROM CARTS WHERE user_id = '$user_id' AND status = 'Unfinished')") or die('query failed');
                $new_product_total = $product['price'] * $product_quantity;
                mysqli_query($conn, "UPDATE CARTS SET price = price + $new_product_total WHERE id = '$cart_id'") or die('query failed');
            } 
            // Product isn't in cart
            else {
                $cart_query = mysqli_query($conn, "SELECT id FROM CARTS WHERE user_id = '$user_id' AND status = 'Unfinished'") or die('query failed');
                // Create cart
                if (mysqli_num_rows($cart_query) == 0) {
                    // Nếu chưa có giỏ hàng, tạo mới giỏ hàng
                    mysqli_query($conn, "INSERT INTO CARTS (user_id, num_of_products, price) VALUES ($user_id, 0, 0)") or die(mysqli_error($conn));
                    $cart_id = (int)mysqli_insert_id($conn); 
                } 
                // Cart existed
                else {
                    $cart_data = mysqli_fetch_assoc($cart_query);
                    $cart_id = (int)$cart_data['id'];
                }
                mysqli_query($conn, "INSERT INTO CART_PRODUCTS (cart_id, products_id, quantity) VALUES ($cart_id, '$product_id', $product_quantity)") or die('query failed');
                $new_product_total = $product['price'] * $product_quantity;
                mysqli_query($conn, "UPDATE CARTS SET num_of_products = num_of_products + 1, price = price + $new_product_total WHERE id = '$cart_id'") or die('query failed');
            }
        }
    }
    if (empty($errors)) {
        echo "<script>alert('Add to cart successful!'); window.location.href = '/minimuji/products';</script>";
    } 
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="/minimuji/assets/css/style.css">
    <link rel="stylesheet" href="/minimuji/assets/css/products-detail.css">
</head>
<body>
    <!-- Header -->
    <?php include 'src/components/header.php'; ?>

    <main>
        <section class="product-details">
            <div class="product-left">
                <img src="/minimuji/assets/images/products/<?= $product['image']?>" alt="<?= $product['name'] ?>" class="product-image">
            </div>
            <div class = "product-right">
                <h1 class="product-name"><?= $product['name'] ?></h1>
                <div class = "short-info">
                    <p>Product ID: <?= $product['id'] ?></p>
                    <p>Sold: <?= $product['sold'] ?></p>
                    <p>In stock: <?= $product['quantity'] ?></p>
                </div>
                <p class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> VND</p>
                <p>Description: <?= $product['description'] ?></p>
                <form action = "#" method = "POST" class = "product-right">
                    <div class = "quantity-container">
                        <label for = "quantity">Quantity:</label>
                        <input type = "number" id = "quantity" name = "quantity" min = "1" max = "<?= $product['quantity'] ?>" value = "1" class = "quantity-input">
                    </div>
                    <div class="product-buttons">
                        <button type = "submit" class = "red-button">Add to Cart</button>
                    </div>
                </form>
            </div>
        </section>
        
    </main>

    <!-- Footer -->
    <?php include 'src/components/footer.php'; ?>
</body>
</html>
