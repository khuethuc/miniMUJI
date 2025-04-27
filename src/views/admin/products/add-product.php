<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product-name'];
    $description = trim($_POST['description']);
    $price = (int)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = $_POST['category'];

    $query = $conn->query("SELECT id FROM CATEGORY WHERE name = '$category'");
    $category_id = (int)$query->fetch_assoc()['id'];

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/mini_muji/assets/images/products/';

    $original_name = null;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    // print_r($upload_dir);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Lấy thông tin tệp
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = $_FILES['image']['name'];
        $size = $_FILES['image']['size'];
        $error = $_FILES['image']['error'];

        // Kiểm tra lỗi khi tải lên
        if ($error !== UPLOAD_ERR_OK) {
            error_log("Upload failed for $original_name with error code: $error");
            echo "<script>alert('Error uploading the file.');";
        }

        // Kiểm tra kích thước tệp (dưới 10MB)
        if ($size > 10 * 1024 * 1024) {
            error_log("File $original_name is too large.");
            echo "<script>alert('File is too large. Maximum size is 10MB.');";
        }

        // Kiểm tra định dạng tệp (chỉ cho phép .jpg hoặc .png)
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = mime_content_type($tmp_name);

        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Invalid file type. Only JPG and PNG files are allowed.');";
        }

        // Tạo tên tệp duy nhất
        $unique_name = uniqid() . "_" . basename($original_name);

        if (!is_dir($upload_dir)) {
            // Nếu thư mục không tồn tại, tạo thư mục
            mkdir($upload_dir, 0777, true);
        }

        // Đường dẫn đích để lưu tệp
        $destination = $upload_dir . $original_name;

        // Di chuyển tệp từ thư mục tạm thời vào thư mục đích
        if (move_uploaded_file($tmp_name, $destination)) {
            echo "<script>alert('File uploaded successfully.');";
        } 
        else {
            echo "<script>alert('Error moving the uploaded file.');";
        }
    } 
    else {
        echo "<script>alert('No file uploaded or there was an error.');";
    }

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
        $quantity,
    );

    if ($stmt->execute()) {
        // echo "<script>alert('New job created');";
        echo "<script>window.location.href='?page=dashboard';</script>";
    } 
    else {
        echo "Error: " . $stmt->error;
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

    <main>
        <section>
            <div class='form'>
                <h1>ADD NEW PRODUCT</h1>
                <hr>
                <form id="add-product" action="" method="POST" enctype="multipart/form-data">  
                    <!-- Name -->
                    <div class="form-group">
                        <label for="product-name">Product name*</label>
                        <input type="text" id="product-name" name="product-name" placeholder="Wooden Dining Bench" required>
                    </div>
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <input type="text" id="description" name="description" placeholder="The compact couch can be used for both the living ..." required>
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
                            <option disabled hidden <?= !isset($_GET['category']) ? 'selected' : '' ?>>Furniture</option>
                            <option value="Furniture" <?= ($_GET['category'] ?? '') == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                            <option value="Stationery" <?= ($_GET['category'] ?? '') == 'Stationery' ? 'selected' : '' ?>>Stationery</option>
                            <option value="Travelling items" <?= ($_GET['category'] ?? '') == 'Travelling items' ? 'selected' : '' ?>>Travelling items</option>
                        </select>
                    </div>
                    <!-- Upload image -->
                    <div class="upload-container">
                        <div class="upload-box">
                            <input type="file" id="image" name="image" accept=".png, .jpg">
                            <span> Upload product image</span>
                        </div>
                        <p>Attach file. File type: .jpg, .png.</p>
                    </div>
                    <!-- Display image -->
                    <div class="file-info" style="display: none;">
                        <p><strong>Uploaded image:</strong></p>
                        <div id="file"></div>
                    </div>
                    <!-- Submit -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark">Send appilication</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
    
    <?php include 'src/components/footer.php';?>
</body>