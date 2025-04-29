<?php
// Get info
$product_id = (int)($_GET['product_id'] ?? null);
$product = null;

if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM PRODUCTS WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// Get category name
$temp_category_id = $product['category_id'] ?? null;
$category_name = '';

if ($temp_category_id) {
    $stmt = $conn->prepare("SELECT name FROM CATEGORY WHERE id = ?");
    $stmt->bind_param('i', $temp_category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_data = $result->fetch_assoc();
    $category_name = $category_data['name'] ?? '';
    $stmt->close();
}

// Update info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product_id) {
    $name = $_POST['product-name'];
    $description = trim($_POST['description']);
    $price = (int)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = $_POST['category'];

    // Get category id
    $stmt = $conn->prepare("SELECT id FROM CATEGORY WHERE name = ?");
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_id = (int)($result->fetch_assoc()['id'] ?? 0);
    $stmt->close();

    // Process image
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/minimuji/assets/images/products/';
    $new_image_name = $product['image']; // Giữ tên ảnh cũ mặc định

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = basename($_FILES['image']['name']);
        $size = $_FILES['image']['size'];
        $file_type = mime_content_type($tmp_name);

        // Validate file
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($file_type, $allowed_types)) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG and PNG allowed."]);
            exit;
        }
        if ($size > 10 * 1024 * 1024) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "File is too large. Max 10MB."]);
            exit;
        }

        // Create folder if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Delete old image
        if (!empty($product['image'])) {
            $old_image_path = $upload_dir . $product['image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }

        // Move new image
        $destination = $upload_dir . $original_name;
        if (!move_uploaded_file($tmp_name, $destination)) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Error moving uploaded file."]);
            exit;
        }

        // Update new image name
        $new_image_name = $original_name;
    }

    // Update product
    $stmt = $conn->prepare("
        UPDATE PRODUCTS SET 
            name = ?, 
            description = ?, 
            price = ?, 
            category_id = ?, 
            quantity = ?, 
            image = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssiiisi",
        $name,
        $description,
        $price,
        $category_id,
        $quantity,
        $new_image_name,
        $product_id
    );

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'id' => $product_id
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => $stmt->error]);
        exit;
    }
}
?>




<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/form.css">
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
                    <!-- Upload image -->
                    <div class="upload-container">
                        <label for="upload-image">Upload product image*</label>
                        <div class="upload-box">
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept=".png, .jpg"
                                data-filename="<?php echo htmlspecialchars($product['image'] ?? ''); ?>">
                            <span id="file-name">Upload product image</span>
                        </div>
                        <p>File type: .jpg, .png.</p>
                    </div>
                    <!-- Submit -->
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

<script src = "/minimuji/assets/js/edit-product.js"></script>