<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $page = $data['page'] ?? 1;
    $limit = $data['limit'] ?? 100; // Tăng limit để lấy tất cả phòng
    $search = $data['search'] ?? '';

    $offset = ($page - 1) * $limit;

    $query = "SELECT p.id_phong, p.so_phong, p.id_khu_tro, k.dia_chi, p.gia_thue, p.trang_thai 
              FROM phong_tro p 
              LEFT JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro 
              WHERE p.so_phong LIKE :search 
              LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countQuery = "SELECT COUNT(*) FROM phong_tro WHERE so_phong LIKE :search";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $total = $countStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'data' => $rooms,
        'total' => $total,
        'page' => $page,
        'limit' => $limit
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>