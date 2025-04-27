<?php
include '../../../../config.php';

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("DELETE FROM PRODUCTS WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
    } 
    catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} 
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID.']);
}
?>
