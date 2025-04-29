<?php
include '../../config.php'; 

header('Content-Type: application/json');

// Kiểm tra xem từ khóa có tồn tại và không rỗng
if (!isset($_GET['keyword']) || trim($_GET['keyword']) === '') {
    echo json_encode(['error' => 'Keyword is not set or empty']);
    exit;
}

$keyword = trim($_GET['keyword']);
$keyword_safe = $conn->real_escape_string($keyword); // An toàn SQL

// Câu lệnh SQL để lấy sản phẩm theo tên
$sql = "SELECT id, name FROM products WHERE name LIKE '$keyword_safe%' LIMIT 5";

// Thực thi câu lệnh SQL
$result = $conn->query($sql); // Dùng $conn->query thay vì mysqli_query

$suggestions = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

// Trả về kết quả dưới dạng JSON
echo json_encode($suggestions);
?>

