<?php
$product_id = (int)$_GET['product_id'] ?? null;
$product = null;




if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM PRODUCTS WHERE id = $product_id");
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

$temp_category_id = $product['category_id'];
$query = $conn->query("SELECT name FROM CATEGORY WHERE id = $temp_category_id");
$category_name = $query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product_id) {
    $name = $_POST['product-name'];
    $description = trim($_POST['description']);
    $price = (int)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = $_POST['category'];

    $query = $conn->query("SELECT id FROM CATEGORY WHERE name = '$category'");
    $category_id = (int)$query->fetch_assoc()['id'];
    
    $stmt = $conn->prepare("
        UPDATE PRODUCTS SET 
            name = ?, description = ?, price = ?, category_id = ?, quantity = ?
        WHERE id = ?
    ");
    

    $stmt->bind_param(
        "ssiiii",
        $name,
        $description,
        $price,
        $category_id,
        $quantity,
        $product_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='?page=products-admin';</script>";
        exit;
    } 
    else {
        echo "Error updating job: " . $stmt->error;
    }

    $stmt->close();
}


?>






<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel = "stylesheet" href = "assets/css/style.css">
    <link rel = "stylesheet" href = "assets/css/form.css">
</head>

<body>
    <?php include 'src/components/header.php';?>
    <mainma>
        <section>
            <div class='form'>
                <h1>EDIT PRODUCT</h1>
                <hr>
                <form action="" id="edit-product" method="POST">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="product-name">Product name*</label>
                        <input type="text" id="product-name" name="product-name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                    </div>
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <input type="text" id="description" name="description" value="<?= htmlspecialchars($product['description'] ?? '') ?>" required>
                    </div>
                    <!-- Price -->
                    <div class="form-group">
                        <label for="price">Price*</label>
                        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" required>
                    </div>
                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="quantity">Quantity*</label>
                        <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity'] ?? '') ?>" required>
                    </div>
                    <!-- Category -->
                    <div>
                        <label for="category">Category*</label>
                        <select class="filter-select" name="category" required>
                            <option value="Furniture" <?= ($category_name ?? '') == "Furniture" ? 'selected' : '' ?>>Furniture</option>
                            <option value="Stationery" <?= ($category_name ?? '') == "Stationery" ? 'selected' : '' ?>>Stationery</option>
                            <option value="Travelling items" <?= ($category_name ?? '') == "Travelling items" ? 'selected' : '' ?>>Travelling items</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </section>
    </>
    
    <?php include 'src/components/footer.php';?>
</body>

</html>