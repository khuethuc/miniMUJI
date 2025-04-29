<?php
include 'config.php';

$user_id = (int)$_SESSION['id'];

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

?>


<head>
    <meta charset= "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel = "stylesheet" href = "/minimuji/assets/css/style.css">
    <link rel = "stylesheet" href = "/minimuji/assets/css/cart.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main class="cart-page">
        <h1>Your Cart</h1>
        <section class="cart-content">

            <!-- Cart Items List -->
            <div class="cart-items">
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>" data-product-id="<?= $item['products_id'] ?>">
                            <img src="/minimuji/assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="Product Image">
                            <div class="cart-item-info">
                                <h2><?= htmlspecialchars($item['name']) ?></h2>
                                <p>Price: <?= number_format($item['product_price']) ?>₫</p>

                                <div class="quantity-control">
                                    <button class="decrease" data-product-id="<?= $item['products_id'] ?>">-</button>
                                    <input type="number" value="<?= $item['cart_product_quantity'] ?>" readonly data-cart-id="<?= $item['cart_id'] ?>" class="quantity-input">
                                    <button class="increase" data-product-id="<?= $item['products_id'] ?>">+</button>
                                </div>

                                <div class="item-subtotal">
                                    Subtotal: <?= number_format($item['product_price'] * $item['cart_product_quantity']) ?>₫
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No items in your cart.</p>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <aside class="order-summary">
                <h2>Order Summary</h2>
                <p>Subtotal (<?= count($cart_items) ?> items): <span class="grand-total"><?= number_format($cart_items[0]['cart_total_price']) ?>₫</span></p>

                <p>Shipping Fee: <strong>Free ship</strong></p>
                
                <p>Grand Total: <strong><span class="grand-total"><?= number_format($cart_items[0]['cart_total_price']) ?>₫</span></strong></p>
                
                <button 
                    id="checkout-btn" 
                    onclick="window.location.href='/minimuji/check-out/<?=$cart_items[0]['cart_id']?>'" 
                    <?= empty($cart_items) ? 'disabled' : '' ?>>
                    Proceed to Checkout
                </button>
                <a href="/minimuji/products">Continue Shopping</a>
            </aside>


        </section>
    </main>

    <?php include 'src/components/footer.php';?>

</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Xử lý khi bấm nút giảm số lượng
    $('.decrease').on('click', function() {
        const productId = $(this).data('product-id');
        const quantityInput = $(this).siblings('.quantity-input');
        let quantity = parseInt(quantityInput.val());

        if (quantity > 1) {
            quantity--; // Giảm số lượng
            quantityInput.val(quantity);
            updateCartQuantity(productId, quantity);
        }
    });

    // Xử lý khi bấm nút tăng số lượng
    $('.increase').on('click', function() {
        const productId = $(this).data('product-id');
        const quantityInput = $(this).siblings('.quantity-input');
        let quantity = parseInt(quantityInput.val());

        quantity++; // Tăng số lượng
        quantityInput.val(quantity);
        updateCartQuantity(productId, quantity);
    });

    // Hàm gửi AJAX để cập nhật số lượng vào database
    function updateCartQuantity(productId, quantity) {
        const cartId = $('[data-product-id="' + productId + '"]').data('cart-id');

        $.ajax({
            url: '/minimuji/src/views/user/carts/update-cart.php',  // Đường dẫn tới file PHP xử lý
            method: 'POST',
            data: {
                cart_id: cartId,
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update the subtotal for the product
                    const productSubtotalElement = $(`[data-product-id="${productId}"]`).find('.item-subtotal');
                    if (productSubtotalElement.length) {
                        productSubtotalElement.text('Subtotal: ' + data.new_subtotal + '₫');
                    }

                    // Update the grand total in the order summary
                    const grandTotalElement = $('.grand-total');
                    if (grandTotalElement.length) {
                        grandTotalElement.text(data.new_total + '₫');
                    }
                } else {
                    alert('Failed to update cart quantity.');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>

