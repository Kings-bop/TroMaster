<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($data['search']) ? trim($data['search']) : '';
    $offset = ($page - 1) * $limit;

try {   

    // Truy vấn đếm tổng
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM tai_khoan WHERE ten_dang_nhap LIKE :search");
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $total = $countStmt->fetchColumn();

    // Truy vấn lấy dữ liệu
    $stmt = $conn->prepare("
        SELECT * FROM tai_khoan 
        WHERE ten_dang_nhap LIKE :search 
        ORDER BY id_tai_khoan DESC 
        LIMIT :offset, :limit
    ");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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