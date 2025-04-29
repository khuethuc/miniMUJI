<?php include 'config.php'; 

// Best sellers
$sql = "SELECT * FROM PRODUCTS ORDER BY sold DESC LIMIT 5";
$query = $conn->query($sql);
$best_sellers = [];

if ($query->num_rows > 0) {
    while ($product = $query->fetch_assoc()) {
        $best_sellers[] = $product;
    }
} 

// New arrivals
$sql = "SELECT * FROM PRODUCTS ORDER BY id DESC LIMIT 5";
$query = $conn->query($sql);
$new_arrivals = [];

if ($query->num_rows > 0) {
    while ($product = $query->fetch_assoc()) {
        $new_arrivals[] = $product;
    }
}

?>

<head>
    <meta charset="UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>miniMUJI</title>
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/home.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main>
        <!-- Background image -->
        <div class = "background">
            <img src="/minimuji/assets/images/home.png" alt="Home Image" class="background-image">
        </div>
        <div class = "container">
            <!-- Best sellers -->
            <h1>Best Sellers</h1>
            <div class = "product-container">
                <?php foreach($best_sellers as $product): ?>
                    <div class = "box">
                        <button onclick="window.location.href='/minimuji/product-details/<?=$product['id']?>'">
                            <img src = "/minimuji/assets/images/products/<?=$product['image']?>" alt = "<?=$product['image']?>" class = "image">
                            <h3 class = "name"><?=$product['name']?></h3>
                            <div class = "info">
                                <p class = "price"><?=number_format($product['price'], 0, ',', '.')?> VND</p>
                                <p class = "sold">Sold: <?=$product['sold']?></p>
                            </div>
                        </button>
                    </div>
                <?php endforeach ?>
            </div>

            <!-- New arrivals -->
            <h1>New Arrivals</h1>
            <div class = "product-container">
                <?php foreach($new_arrivals as $product): ?>
                    <div class = "box">
                        <button onclick="window.location.href='/minimuji/product-details/<?=$product['id']?>'">
                            <img src = "/minimuji/assets/images/products/<?=$product['image']?>" alt = "<?=$product['image']?>" class = "image">
                            <h3 class = "name"><?=$product['name']?></h3>
                            <div class = "info">
                                <p class = "price"><?=number_format($product['price'], 0, ',', '.')?> VND</p>
                                <p class = "sold">Sold: <?=$product['sold']?></p>
                            </div>
                        </button>
                    </div>
                <?php endforeach ?>
            </div>
            
            <!-- Quote -->
        </div>

        
    </main>

    <?php include 'src/components/footer.php'; ?>
</body>
