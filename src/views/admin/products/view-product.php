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
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/products.css">
    
    <?php include "./products.php"?>
</head>
<body>
    <!-- Header -->
    <?php include 'src/components/header.php'; ?>

    <main>
        <section class="product-details">
            <div class="container">
                <div class="product-left">
                    <img src="assets/images/products/<?= $product['image']?>" alt="<?= $product['name'] ?>" class="product-image">
                </div>
                <div class = "product-right">
                    <form action = "" method = "POST" class = "product-right">
                        <h1 class="product-name"><?= $product['name'] ?></h1>
                        <p>Product ID: <?= $product['id'] ?></p>
                        <p>Sold: <?= $product['sold'] ?></p>
                        <p>In stock: <?= $product['quantity'] ?></p>
                        <p class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> VND</p>
                        <p class = "product-description"><strong>Description:</strong> <?= $product['description'] ?></p>
                        <div class = "quantity-container">
                            <label for = "quantity">Quantity:</label>
                            <input type = "number" id = "quantity" name = "quantity" min = "1" max = "<?= $product['quantity'] ?>" value = "1" class = "quantity-input">
                        </div>
                    </form>
                    <button class = "button-red" onclick="deleteProduct(<?= $product['id'] ?>)">Delete</button>
                    <button class = "button-white" onclick="window.location.href='?page=edit-product&product_id=<?=$product['id']?>'">Edit</button>
                    
                </div>
            </div>
        </section>
        
    </main>

    <!-- Footer -->
    <?php include 'src/components/footer.php'; ?>
</body>
</html>

<script src="assets/js/deleteProduct.js"></script>