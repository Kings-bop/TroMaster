<?php
header('Content-Type: application/json');
require_once 'connect.php';

$input = json_decode(file_get_contents("php://input"), true);
$page = isset($input['page']) ? (int)$input['page'] : 1;
$limit = isset($input['limit']) ? (int)$input['limit'] : 10;
$search = isset($input['search']) ? $input['search'] : '';

$offset = ($page - 1) * $limit;

try {
    $params = [];
    $where = "";

    if (!empty($search)) {
        // Tìm kiếm theo tên hoặc số điện thoại
        $where = "WHERE kt.ho_ten LIKE :search OR kt.so_dien_thoai LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Tổng số dòng cho phân trang
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM khach_thue kt $where");
    $countStmt->execute($params);
    $totalRows = $countStmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Dữ liệu khách thuê
    $sql = "
        SELECT kt.id_khach_thue, kt.ho_ten, kt.email, kt.so_dien_thoai,
               kt.so_cccd, kt.ngay_sinh, kt.gioi_tinh,
                COALESCE(kt.ngay_bat_dau_thue, hd.ngay_ky) AS ngay_bat_dau_thue,
               pt.so_phong AS phong_tro, kt.id_phong
        FROM khach_thue kt
        LEFT JOIN phong_tro pt ON kt.id_phong = pt.id_phong
        LEFT JOIN hop_dong hd ON kt.id_khach_thue = hd.id_khach_thue AND hd.trang_thai = 'con_hieu_luc'
        $where
        ORDER BY kt.id_khach_thue DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tenants)) {
        error_log("No tenants found. Page: $page, Limit: $limit, Search: $search");
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy khách thuê!',
            'data' => [],
            'totalPages' => 0,
            'currentPage' => $page
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => $tenants,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $e->getMessage(),
        'data' => [],
        'totalPages' => 0,
        'currentPage' => $page
    ]);
}
?>