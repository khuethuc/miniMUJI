<?php 
include 'config.php'; 

// Kiểm tra AJAX request
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// Nhận tham số GET
$category = isset($_GET['category']) ? str_replace('-', ' ', trim($_GET['category'])) : null;
$sort_price = isset($_GET['sort_price']) ? trim($_GET['sort_price']) : null; 
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;

// Pagination setup
$limit = 5;
$page = isset($_GET['pgn']) ? max(1, (int)$_GET['pgn']) : 1;
$offset = ($page - 1) * $limit;

// Xây dựng WHERE
$where_clauses = [];

if ($category) {
    $stmt = $conn->prepare("SELECT id FROM CATEGORY WHERE name = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $category_id = $row['id'];
        $where_clauses[] = "category_id = " . (int)$category_id;
    }
    $stmt->close();
}

if ($keyword) {
    $keyword_safe = $conn->real_escape_string($keyword);
    $where_clauses[] = "name LIKE '$keyword_safe%'";
}

// Kết hợp điều kiện
$condition_clause = '';
if (!empty($where_clauses)) {
    $condition_clause = 'WHERE ' . implode(' AND ', $where_clauses);
}

// ORDER BY
switch ($sort_price) {
    case 'asc':
        $sort_clause = "ORDER BY price ASC";
        break;
    case 'desc':
        $sort_clause = "ORDER BY price DESC";
        break;
    default:
        $sort_clause = "ORDER BY id ASC";
        break;
}

// Tổng số sản phẩm
$total_products = 0;
$total_query = $conn->query("SELECT COUNT(*) as total FROM PRODUCTS $condition_clause");
if ($total_query) {
    $total_products = (int)$total_query->fetch_assoc()['total'];
}
$total_pages = (int)ceil($total_products / $limit);

// Lấy sản phẩm
$products = [];
$product_query = $conn->query("SELECT * FROM PRODUCTS $condition_clause $sort_clause LIMIT $limit OFFSET $offset");
if ($product_query) {
    while ($row = $product_query->fetch_assoc()) {
        $products[] = $row;
    }
}

// Nếu là AJAX: chỉ trả tbody
if ($is_ajax) {
    if (!empty($products)) {
        foreach ($products as $index => $row): ?>
            <tr class="clickable-row" data-id="<?= $row['id'] ?>">
                <td><?= $row['id'] ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="/minimuji/assets/images/products/<?= htmlspecialchars($row['image']) ?>" width="80">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price']) ?>₫</td>
                <td><?= (int)$row['quantity'] ?></td>
                <td><?= (int)$row['sold'] ?></td>
                <td>
                    <?php
                    $cat_query = $conn->query("SELECT name FROM CATEGORY WHERE id = " . (int)$row['category_id']);
                    $cat_row = $cat_query->fetch_assoc();
                    echo htmlspecialchars($cat_row['name'] ?? 'Unknown');
                    ?>
                </td>
                <td>
                    <button class = "white-button" onclick="window.location.href='/minimuji/edit-product/<?= $row['id'] ?>'">Edit</button>
                    <button class = "red-button" onclick="deleteProduct(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
    <?php endforeach;
    } 
    else {
        echo '<tr><td colspan="8">No products found.</td></tr>';
    }
    exit;
}
?>


<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/products-admin.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/pagination.css">
</head>

<body>
    <?php include 'src/components/header.php';?>
    <main>
        <section class = "container">
            <h1>Products Management</h1>
            <!-- Filter bar + Add product -->
            <div class = "filters">
                <form method = "GET" id = "filterForm" action = "/minimuji/products-admin">
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
                        <option value = "Traveling items" <?= ($_GET['category'] ?? '') == 'Traveling items' ? 'selected' : '' ?>>Traveling items</option>
                    </select>
                    <!-- Sort by Price -->
                    <select name = "sort_price" id= "sort_price" onchange= "updateURL()">
                        <option value = "">Price</option>
                        <option value = "asc" <?= ($_GET['sort_price'] ?? '') == 'asc' ? 'selected' : '' ?>>Low to High</option>
                        <option value = "desc" <?= ($_GET['sort_price'] ?? '') == 'desc' ? 'selected' : '' ?>>High to Low</option>
                    </select>
                </form>
                <button class = "red-button" onclick="window.location.href='/minimuji/add-product'">Add new product</button>
            </div>

            <!-- Search bar -->
            <div class = "search-bar">
                <input type="text" id="search" placeholder="Search products...">
            </div>

            <!-- Products list -->
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price (VND)</th>
                    <th>Quantity</th>
                    <th>Sold</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $row): ?>
                        <tr class="clickable-row" data-id="<?= $row['id'] ?>">
                            <td><?= $offset + $index + 1 ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="/minimuji/assets/images/products/<?= htmlspecialchars($row['image']) ?>" width="80">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= number_format($row['price']) ?>₫</td>
                            <td><?= (int)$row['quantity'] ?></td>
                            <td><?= (int)$row['sold'] ?></td>
                            <td>
                                <?php
                                $cat_query = $conn->query("SELECT name FROM CATEGORY WHERE id = " . (int)$row['category_id']);
                                $cat_row = $cat_query->fetch_assoc();
                                echo htmlspecialchars($cat_row['name'] ?? 'Unknown');
                                ?>
                            </td>
                            <td>
                                <button class = "white-button" onclick="window.location.href='/minimuji/edit-product/<?= $row['id'] ?>'">Edit</button>
                                <button class = "red-button" onclick="deleteProduct(<?= $row['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        </section>
    </main>
    <!-- Pagination -->
    <?php 
    include "src/components/pagination.php";
    renderPagination($total_pages); 
    ?>
    <?php include 'src/components/footer.php';?>
</body>

<script src="/minimuji/assets/js/delete-product.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    let timer = null;

    function bindClickableRows() {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (id) {
                    window.location.href = '/minimuji/view-product/' + id;
                }
            });
        });
        // Prevent row click when clicking on buttons inside the row
        const buttons = document.querySelectorAll('.clickable-row button');
        buttons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation(); // Stop the event from propagating to the row
            });
        });
    }

    bindClickableRows(); 

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            const keyword = searchInput.value.trim();
            const url = new URL(window.location.href);
            url.searchParams.set('keyword', keyword);
            url.searchParams.set('ajax', '1');

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('tbody').innerHTML = html;
                    bindClickableRows(); 
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });
});

</script>

<script>
    function updateURL() {
        const category = document.getElementById('category').value.replace(/\s+/g, '-'); // Replace spaces with dashes
        const sortPrice = document.getElementById('sort_price').value;

        let url = '/minimuji/products-admin';

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
