<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true); // Đọc dữ liệu từ POST dưới dạng JSON 
$page = isset($data['page']) ? (int)$data['page'] : 1;
$limit = isset($data['limit']) ? (int)$data['limit'] : 10;
$search = isset($data['search']) ? trim($data['search']) : ''; // Loại bỏ khoảng trắng thừa
$offset = ($page - 1) * $limit;

try {
    // Truy vấn đếm tổng
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM khu_tro WHERE dia_chi LIKE :search");
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $total = $countStmt->fetchColumn();

    // Truy vấn lấy dữ liệu
    $stmt = $conn->prepare("
        SELECT * FROM khu_tro 
        WHERE dia_chi LIKE :search 
        ORDER BY id_khu_tro DESC 
        LIMIT :offset, :limit
    ");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPages = ceil($total / $limit);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'total' => (int)$total, 
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'limit' => $limit
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi: " . $e->getMessage()]);
}
?>