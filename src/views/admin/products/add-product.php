<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product-name'];
    $description = trim($_POST['description']);
    $price = (int)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = $_POST['category'];

    // Get category id
    $query = $conn->query("SELECT id FROM CATEGORY WHERE name = '$category'");
    $category_id = (int)$query->fetch_assoc()['id'];

    // Process image
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/minimuji/assets/images/products/';

    $original_name = null;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = $_FILES['image']['name'];
        $size = $_FILES['image']['size'];
        $error = $_FILES['image']['error'];

        if ($error !== UPLOAD_ERR_OK) {
            error_log("Upload failed for $original_name with error code: $error");
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Error uploading the file."]);
            exit;
        }

        if ($size > 10 * 1024 * 1024) {
            error_log("File $original_name is too large.");
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "File is too large. Maximum size is 10MB."]);
            exit;
        }

        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = mime_content_type($tmp_name);

        if (!in_array($file_type, $allowed_types)) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG and PNG files are allowed."]);
            exit;
        }

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $destination = $upload_dir . $original_name;

        if (!move_uploaded_file($tmp_name, $destination)) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Error moving the uploaded file."]);
            exit;
        }
    } 
    else {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "No file uploaded or there was an error."]);
        exit;
    }
    
    // Add product
    $stmt = $conn->prepare("
        INSERT INTO PRODUCTS (name, description, price, image, category_id, quantity)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssisii",
        $name,
        $description,
        $price,
        $original_name,
        $category_id,
        $quantity
    );

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "success"]);
        exit;
    } 
    else {
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

    <main>
        <section>
            <div class='form'>
                <h1>ADD NEW PRODUCT</h1>
                <hr>
                <form id="add-product" action="?page=add-product" method="POST" enctype="multipart/form-data">  
                    <!-- Name -->
                    <div class="form-group">
                        <label for="product-name">Product name*</label>
                        <input type="text" id="product-name" name="product-name" placeholder="Wooden Dining Bench" required>
                    </div>
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" rows="8" placeholder="The compact couch can be used for both the living..." required></textarea>
                    </div>
                    <!-- Price -->
                    <div class="form-group">
                        <label for="price">Price*</label>
                        <input type="number" id="price" name="price" placeholder="3000000" required>
                    </div>
                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="quantity">Quantity*</label>
                        <input type="number" id="quantity" name="quantity" placeholder="300" required>
                    </div>
                    <!-- Category -->
                    <div>
                        <label for="category">Category*</label>
                        <select class="filter-select" name="category">
                            <option value = "">Choose category</option>
                            <option value="Furniture" <?= ($_GET['category'] ?? '') == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                            <option value="Stationery" <?= ($_GET['category'] ?? '') == 'Stationery' ? 'selected' : '' ?>>Stationery</option>
                            <option value="Travelling items" <?= ($_GET['category'] ?? '') == 'Travelling items' ? 'selected' : '' ?>>Travelling items</option>
                        </select>
                    </div>
                    <!-- Upload image -->
                    <div class="upload-container">
                        <label for="upload-image">Upload product image*</label>
                        <div class="upload-box">
                            <input type="file" id="image" name="image" accept=".png, .jpg">
                            <span> Upload product image</span>
                        </div>
                        <p>File type: .jpg, .png.</p>
                    </div>
                    <!-- Submit -->
                    <div class="form-group">
                        <button type="submit" class="btn-dark">Add Product</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
    
    <?php include 'src/components/footer.php';?>
</body>

<script src = "/minimuji/assets/js/add-product.js"></script>