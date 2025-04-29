<?php
include 'config.php';

// Get user_id
$user_id = (int)$_SESSION['id']; 

// Get cart_id
$cart_id = (int)$_GET['cart_id'];

// Truy vấn để lấy thông tin giỏ hàng từ bảng CARTS
$cart_id_query = $conn->prepare("SELECT id, price FROM CARTS WHERE id = ? AND user_id = ?");
$cart_id_query->bind_param('ii', $cart_id, $user_id);
$cart_id_query->execute();
$cart_result = $cart_id_query->get_result();

// Kiểm tra nếu giỏ hàng tồn tại
if ($cart_result->num_rows > 0) {
    $cart_info = $cart_result->fetch_assoc();
} 
else {
    echo "No cart found.";
    exit;
}

// Cart items
$sql = "
    SELECT 
        carts.id AS cart_id,
        carts.user_id,
        carts.num_of_products,
        carts.price AS cart_total_price,
        cart_products.products_id,
        cart_products.quantity AS cart_product_quantity,
        products.name,
        products.description,
        products.price AS product_price,
        products.image,
        products.category_id,
        products.quantity AS product_quantity_available,
        products.sold
    FROM carts
    JOIN cart_products ON carts.id = cart_products.cart_id
    JOIN products ON cart_products.products_id = products.id
    WHERE carts.user_id = ?
      AND carts.status = 'Unfinished'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $fullname = $_POST['fullname'];
    $phone_number = $_POST['phone_number'];
    $street = $_POST['street'];
    $ward = $_POST['ward'];
    $city = $_POST['city'];

    // Nối địa chỉ thành 1 chuỗi
    $full_address = $street . ', ' . $ward . ', ' . $city;

    // Kiểm tra số điện thoại hợp lệ (10 chữ số)
    if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        echo "Invalid phone number. Please enter a 10-digit phone number.";
        exit;
    }

    // Insert vào bảng ORDERS
    $sql = "
        INSERT INTO ORDERS (user_id, cart_id, receiver_name, phone_number, address, price, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Waiting')
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisssi', $user_id, $cart_id, $fullname, $phone_number, $full_address, $cart_info['price']);

    if ($stmt->execute()) {
        // Sau khi đặt hàng thành công, cập nhật giỏ hàng thành "Finished"
        $update_cart_status_query = $conn->prepare("UPDATE CARTS SET status = 'Finished' WHERE id = ?");
        $update_cart_status_query->bind_param('i', $cart_id);
        $update_cart_status_query->execute();

        // Giảm số lượng sản phẩm trong bảng PRODUCTS
        $cart_products_query = $conn->prepare("SELECT products_id, quantity FROM CART_PRODUCTS WHERE cart_id = ?");
        $cart_products_query->bind_param('i', $cart_id);
        $cart_products_query->execute();
        $cart_products_result = $cart_products_query->get_result();

        // Cập nhật số lượng trong bảng PRODUCTS
        while ($product = $cart_products_result->fetch_assoc()) {
            $product_id = $product['products_id'];
            $quantity = $product['quantity'];

            // Giảm số lượng sản phẩm trong bảng PRODUCTS
            $update_product_quantity_query = $conn->prepare("UPDATE PRODUCTS SET quantity = quantity - ? WHERE id = ?");
            $update_product_quantity_query->bind_param('ii', $quantity, $product_id);
            $update_product_quantity_query->execute();
        }

        echo "<script>alert('Order placed successfully!'); window.location.href = '/minimuji/products';</script>";
    } 
    else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/check-out.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main class="check-out-page">
        <h1>Check out</h1>
        <section class="check-out-content">
            <!-- Check out form -->
            <div class = 'form'>                
                <form action = "#" method = "POST" id = "form" margin = "0 0">
                    <!-- Full Name -->
                    <div class = "form-group">
                        <label for = "fullname">Full name*</label>
                        <input type = "text" id = "fullname" name = "fullname" required>
                    </div>
                    <!-- Phone number -->
                    <div class = "form-group">
                        <label for = "phone_number">Phone number*</label>
                        <input type = "tel" id = "phone_number" name = "phone_number" required>
                    </div>
                    <!-- City / Province -->
                    <div class = "form-group">
                        <label for = "city">City/Province*</label>
                        <input type = "text" id = "city" name = "city" required>
                    </div>
                    <!-- Ward -->
                    <div class = "form-group">
                        <label for = "ward">Ward*</label>
                        <input type = "text" id = "ward" name = "ward" required>
                    </div>
                    <!-- Street address -->
                    <div class = "form-group">
                        <label for = "street">Street Address*</label>
                        <input type = "text" id = "street" name = "street" required>
                    </div>
                    <!-- Submit -->
                    <div class="form-group">
                        <button type = "submit" class="btn btn-dark">Check out</button>
                    </div>
                    
                </form>
            </div>

            <!-- Cart items -->
            <aside class="cart-items">
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>" data-product-id="<?= $item['products_id'] ?>">
                            <img src="/minimuji/assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="Product Image">
                            <div class="cart-item-info">
                                <h2><?= htmlspecialchars($item['name']) ?></h2>
                                <p>Price: <?= number_format($item['product_price']) ?>₫</p>
                                <p>Quantity: <?= $item['cart_product_quantity'] ?></p>
                                <div class="item-subtotal">
                                    Subtotal: <?= number_format($item['product_price'] * $item['cart_product_quantity']) ?>₫
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No items in your cart.</p>
                <?php endif; ?>
            </aside>
        </section>
    </main>

    <?php include 'src/components/footer.php';?> 
</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form');
    const phoneNumber = document.getElementById('phone_number');

    // Regex pattern for phone number (10 digits)
    const phonePattern = /^[0-9]{10}$/;

    form.addEventListener('submit', function (e) {
        // Clear previous errors
        phoneNumber.setCustomValidity('');

        // Phone number validation
        if (!phonePattern.test(phoneNumber.value)) {
            phoneNumber.setCustomValidity('Phone number must be exactly 10 digits.');
        }

        // If the form is invalid, prevent submission and show errors
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
        }
    });

    // Clear validation errors on input
    phoneNumber.addEventListener('input', () => {
        phoneNumber.setCustomValidity('');
    });
});
</script>