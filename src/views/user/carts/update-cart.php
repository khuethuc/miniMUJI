<?php
include '../../../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Ensure valid input
    if ($cart_id > 0 && $product_id > 0 && $quantity > 0) {
        // Get the product price
        $product_price_query = $conn->prepare("SELECT price FROM PRODUCTS WHERE id = ?");
        $product_price_query->bind_param('i', $product_id);
        $product_price_query->execute();
        $product_price_result = $product_price_query->get_result();
        $product_price = $product_price_result->fetch_assoc()['price'];

        // Get the old quantity from CART_PRODUCTS
        $old_quantity_query = $conn->prepare("SELECT quantity FROM CART_PRODUCTS WHERE cart_id = ? AND products_id = ?");
        $old_quantity_query->bind_param('ii', $cart_id, $product_id);
        $old_quantity_query->execute();
        $old_quantity_result = $old_quantity_query->get_result();
        $old_quantity = $old_quantity_result->fetch_assoc()['quantity'];

        // Calculate the difference in quantity
        $quantity_difference = $quantity - $old_quantity;

        // Step 1: Update the quantity in CART_PRODUCTS
        $update_quantity_query = $conn->prepare("UPDATE CART_PRODUCTS SET quantity = quantity + ? WHERE cart_id = ? AND products_id = ?");
        $update_quantity_query->bind_param('iii', $quantity_difference, $cart_id, $product_id);
        $update_quantity_query->execute();

        // Step 2: Calculate new subtotal for the product
        $new_subtotal = $product_price * $quantity;
        $gap_price = $quantity_difference * $product_price;


        // Step 3: Update the price in the CARTS table
        // To update the total price in the cart
        $update_cart_query = $conn->prepare("UPDATE CARTS SET price = price + ? WHERE id = ?");
        $update_cart_query->bind_param('ii', $gap_price, $cart_id);
        $update_cart_query->execute();

        // Step 4: Return the new subtotal, product price, and grand total to update frontend
        $new_total_query = $conn->prepare("SELECT price FROM CARTS WHERE id = ?");
        $new_total_query->bind_param('i', $cart_id);
        $new_total_query->execute();
        $new_total_result = $new_total_query->get_result();
        $new_total = $new_total_result->fetch_assoc()['price'];

        echo json_encode([
            'success' => true, 
            'new_subtotal' => number_format($new_subtotal), 
            'new_total' => number_format($new_total),
        ]);
    } 
    else {
        echo json_encode(['success' => false]);
    }
}
?>

