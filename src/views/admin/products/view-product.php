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
                <div class = "product-buttons">
                    <button class = "white-button" onclick="window.location.href='/minimuji/edit-product/<?= $product['id'] ?>'">Edit</button>
                    <button class = "red-button" onclick="deleteProduct(<?= $product['id'] ?>)">Delete</button>
                </div>
                
            </div>
        </section>
        
    </main>

    <!-- Footer -->
    <?php include 'src/components/footer.php'; ?>
</body>
</html>

<script src="/minimuji/assets/js/delete-product.js"></script>