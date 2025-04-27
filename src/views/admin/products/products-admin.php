<?php 
include 'config.php'; 

// Get parameters GET from form
$category = isset($_GET['category']) && $_GET['category'] !== "all" ? $_GET['category'] : null;
$sort_price = isset($_GET['sort_price']) ? $_GET['sort_price'] : null; 

$limit = 5;
$page = isset($_GET['pgn']) ? (int)$_GET['pgn'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Category filtering
$condition_clause = null;
if ($category) {
    $category_query = $conn->query("SELECT id FROM CATEGORY WHERE name = '$category'");
    $category_row = $category_query->fetch_assoc();
    $category_id = $category_row['id'];
    $condition_clause = " WHERE category_id = '$category_id'";
}

// Price sorting
$sort_clause = null;
if ($sort_price == 'asc') {
    $sort_clause = " ORDER BY price ASC";
} 
elseif ($sort_price == 'desc') {
    $sort_clause = " ORDER BY price DESC";
} 
else {
    $sort_clause = " ORDER BY id ASC"; // Default sort by id
}

// Get total pages
$query = $conn->query("SELECT COUNT(*) as total FROM PRODUCTS $condition_clause");
if ($query) {
    $total_products = (int)$query->fetch_assoc()['total'];
} 
else {
    $total_products = 0;
}
$total_pages = ceil($total_products / $limit);

// Get products
$query = $conn->query("SELECT * FROM PRODUCTS $condition_clause $sort_clause LIMIT $limit OFFSET $offset");
$products = [];

while ($row = $query->fetch_assoc()) {
    $products[] = $row;
}
?>

<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel = "stylesheet" href = "assets/css/style.css">
    <link rel = "stylesheet" href = "assets/css/products-admin.css">
</head>

<body>
    <?php include 'src/components/header.php';?>
    <main>
        <button id="add-btn" onclick="window.location.href='?page=add-product'">Add new product</button>
        <section class = "products">
            <!-- Filter sidebar -->
            <div class = "sidebar">
                <form method = "GET" id = "filterForm">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if (!in_array($key, ['category', 'sort_price', 'pgn'])): ?>
                            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <!-- Category Filter -->
                    <div class = "filter-item">
                        <label for = "category">Category</label>
                        <select name = "category" id = "category" onchange = "this.form.submit()">
                            <option value = "">All Categories</option>
                            <option value = "Furniture" <?= ($_GET['category'] ?? '')  == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                            <option value = "Stationery" <?= ($_GET['category'] ?? '') == 'Stationery' ? 'selected' : '' ?>>Stationery</option>
                            <option value = "Traveling items" <?= ($_GET['category'] ?? '') == 'Traveling items' ? 'selected' : '' ?>>Traveling items</option>
                        </select>
                    </div>
                    <!-- Sort by Price -->
                    <div class = "filter-item">
                        <label for = "sort_price">Price (VND)</label>
                        <select name = "sort_price" id= "sort_price" onchange= "this.form.submit()">
                            <option value = "">Price</option>
                            <option value = "asc" <?= ($_GET['sort_price'] ?? '') == 'asc' ? 'selected' : '' ?>>Low to High</option>
                            <option value = "desc" <?= ($_GET['sort_price'] ?? '') == 'desc' ? 'selected' : '' ?>>High to Low</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Products list -->
            <div class = "product-container">
                <div class = "box-container">
                    <?php foreach($products as $product): ?>
                        <div class = "box">
                            <button onclick="window.location.href='?page=view-product&id=<?=$product['id']?>'">
                                <img src = "assets/images/products/<?=$product['image']?>" alt = "<?=$product['image']?>" class = "image">
                                <h3 class = "name"><?=$product['name']?></h3> <br>
                                <p class = "price"><?=number_format($product['price'], 0, ',', '.')?> VND</p>
                            </button>
                            <button class = "btn-white" onclick="window.location.href='?page=edit-product&product_id=<?=$product['id']?>'">Edit</button>
                            <button class="btn" onclick="deleteProduct(<?= $product['id'] ?>)">Delete</button>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

        </section>
    </main>
    <!-- Pagination -->
    <?php 
    include "src/components/pagination.php";
    renderPagination($total_pages); 
    ?>
    <?php include 'src/components/footer.php';?>
</body>

<script src="assets/js/deleteProduct.js"></script>

