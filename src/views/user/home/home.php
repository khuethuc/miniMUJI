<?php include 'config.php'; 

$sql = "SELECT * FROM PRODUCTS LIMIT 6";
$query = $conn->query($sql);

// Kiểm tra nếu có sản phẩm trong cơ sở dữ liệu
if ($query->num_rows > 0) {
    $products = [];
    while ($product = $query->fetch_assoc()) {
        $products[] = $product;
    }
} else {
    $products = [];
}
?>

<head>
    <meta charset="UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>miniMUJI</title>
    <link rel = "stylesheet" href = "assets/css/style.css">
    <link rel = "stylesheet" href = "assets/css/home.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main>
        <div class = "background">
            <img src="assets/images/home.png" alt="Home Image" class="background-image">
        </div>

        
    </main>

    <?php include 'src/components/footer.php'; ?>
</body>

