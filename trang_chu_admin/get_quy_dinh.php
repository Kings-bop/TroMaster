<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    // Lấy tham số từ request dưới dạng JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $page = isset($data['page']) ? (int)$data['page'] : 1;
    $limit = isset($data['limit']) ? (int)$data['limit'] : 10;
    $search = isset($data['search']) ? trim($data['search']) : '';

    // Tính offset
    $offset = ($page - 1) * $limit;

    // Log để debug
    error_log("Search query: page=$page, limit=$limit, search=$search");

    // Chuẩn bị câu truy vấn với điều kiện tìm kiếm theo đuôi file
    $query = "SELECT * FROM quy_dinh WHERE noi_dung LIKE :search OR noi_dung LIKE :search_ext ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset";
    $countQuery = "SELECT COUNT(*) FROM quy_dinh WHERE noi_dung LIKE :search OR noi_dung LIKE :search_ext";

    $stmt = $conn->prepare($query);
    $searchPattern = "%$search%"; // Tìm kiếm theo từ khóa
    $searchExtPattern = "%.$search%"; // Tìm kiếm theo đuôi file
    $stmt->bindValue(':search', $searchPattern, PDO::PARAM_STR);
    $stmt->bindValue(':search_ext', $searchExtPattern, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $conn->prepare($countQuery);
    $countStmt->bindValue(':search', $searchPattern, PDO::PARAM_STR);
    $countStmt->bindValue(':search_ext', $searchExtPattern, PDO::PARAM_STR);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    error_log("Query result: " . json_encode($data)); // Ghi log để kiểm tra
    echo json_encode([
        'success' => true,
        'data' => $data,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalRecords' => $totalRecords
    ]);
} catch (PDOException $e) {
    error_log("PDO Error: " . $e->getMessage()); // Ghi log lỗi
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()
    ]);
}
?>