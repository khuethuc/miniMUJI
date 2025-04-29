<?php 
include 'config.php'; 

// Lấy tham số từ form
$category = isset($_GET['category']) ? str_replace('-', ' ', trim($_GET['category'])) : null;
$sort_price = isset($_GET['sort_price']) ? trim($_GET['sort_price']) : null; 
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;

// Pagination setup
$limit = 6;
$page = isset($_GET['pgn']) ? max(1, (int)$_GET['pgn']) : 1;
$offset = ($page - 1) * $limit;

// Xây dựng mệnh đề WHERE
$where_clauses = [];

if ($category) {
    $category_query = $conn->prepare("SELECT id FROM CATEGORY WHERE name = ?");
    $category_query->bind_param("s", $category);
    $category_query->execute();
    $result = $category_query->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $category_id = $row['id'];
        $where_clauses[] = "category_id = " . (int)$category_id;
    }
}

if ($keyword) {
    $keyword_safe = $conn->real_escape_string($keyword);
    $where_clauses[] = "name LIKE '$keyword_safe%'";
}

// Ghép tất cả điều kiện
$condition_clause = '';
if (!empty($where_clauses)) {
    $condition_clause = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Xây dựng mệnh đề ORDER BY
switch ($sort_price) {
    case 'asc':
        $sort_clause = "ORDER BY price ASC";
        break;
    case 'desc':
        $sort_clause = "ORDER BY price DESC";
        break;
    default:
        $sort_clause = "ORDER BY id ASC"; // Default sort
        break;
}

// Lấy tổng số sản phẩm (để phân trang)
$total_products = 0;
$total_query = $conn->query("SELECT COUNT(*) as total FROM PRODUCTS $condition_clause");
if ($total_query) {
    $total_products = (int)$total_query->fetch_assoc()['total'];
}
$total_pages = (int)ceil($total_products / $limit);

// Lấy danh sách sản phẩm
$products = [];
$product_query = $conn->query("SELECT * FROM PRODUCTS $condition_clause $sort_clause LIMIT $limit OFFSET $offset");

if ($product_query) {
    while ($row = $product_query->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/products.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/pagination.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main>
        <section class = "container">
            <!-- Filter bar -->
            <div class = "filters">
                <form method = "GET" id = "filterForm" action = "/minimuji/products">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if (!in_array($key, ['category', 'sort_price', 'pgn'])): ?>
                            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <!-- Category Filter -->
                    <select name = "category" id = "category" onchange = "updateURL()">
                        <option value = "">All Categories</option>
                        <option value = "Furniture" <?= ($_GET['category'] ?? '')  == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                        <option value = "Stationery" <?= ($_GET['category'] ?? '') == 'Stationery' ? 'selected' : '' ?>>Stationery</option>
                        <option value="Traveling-items" <?= ($_GET['category'] ?? '') == 'Traveling-items' ? 'selected' : '' ?>>Traveling items</option>
                    </select>
                    <!-- Sort by Price -->
                    <select name = "sort_price" id= "sort_price" onchange= "updateURL()">
                        <option value = "">Price</option>
                        <option value = "asc" <?= ($_GET['sort_price'] ?? '') == 'asc' ? 'selected' : '' ?>>Low to High</option>
                        <option value = "desc" <?= ($_GET['sort_price'] ?? '') == 'desc' ? 'selected' : '' ?>>High to Low</option>
                    </select>
                </form>
            </div>

            <!-- Products list -->
            

            <?php if (empty($products)): ?>
                <p class="no-products">No products found</p>
            <?php else: ?>
                <div class = "product-container">
                    <?php foreach($products as $product): ?>
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
            <?php endif; ?>
        </section>

    </main>
    <!-- Pagination -->
    <?php 
    include "src/components/pagination.php";
    renderPagination($total_pages); 
    ?>
    <?php include 'src/components/footer.php';?>
</body>

<script>
    function updateURL() {
        const category = document.getElementById('category').value.replace(/\s+/g, '-'); // Replace spaces with dashes
        const sortPrice = document.getElementById('sort_price').value;

        let url = '/minimuji/products';

        // Preserve the keyword parameter from the current URL
        const params = new URLSearchParams(window.location.search);
        if (params.has('keyword')) {
            const keyword = params.get('keyword');
            url += `/keyword/${encodeURIComponent(keyword)}`;
        }

        if (category) {
            url += `/category/${encodeURIComponent(category)}`;
        }
        if (sortPrice) {
            url += `/sort_price/${encodeURIComponent(sortPrice)}`;
        }

        // Redirect to the new URL
        window.location.href = url;
    }
</script>

