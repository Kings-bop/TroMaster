<?php
header('Content-Type: application/json');
require_once 'connect.php';

// Kiểm tra đăng nhập
session_start();
if (!isset($_SESSION['ten_dang_nhap'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

// Kiểm tra vai trò
$ten_dang_nhap = $_SESSION['ten_dang_nhap'];
$stmt = $conn->prepare("SELECT vai_tro FROM tai_khoan WHERE ten_dang_nhap = ?");
$stmt->execute([$ten_dang_nhap]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !in_array($user['vai_tro'], ['admin', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền truy cập dữ liệu này!']);
    exit();
}

// Nhận dữ liệu từ client
$input_data = file_get_contents("php://input");
error_log("Raw input data: " . $input_data); // Debug raw input
$data = json_decode($input_data, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg() . " - Input: " . $input_data);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu gửi lên không hợp lệ!']);
    exit();
}

$action = isset($data['action']) ? $data['action'] : 'list';
$page = isset($data['page']) ? (int)$data['page'] : 1;
$limit = isset($data['limit']) ? (int)$data['limit'] : 10;
$search = isset($data['search']) ? trim($data['search']) : "";
$id = isset($data['id']) ? (int)$data['id'] : null;

$offset = ($page - 1) * $limit;

try {
    if ($action === 'get_by_id' && $id) {
        error_log("Fetching contract with ID: $id");
        $stmt = $conn->prepare("
            SELECT 
                hd.id_hop_dong AS so_hop_dong,
                hd.id_khach_thue,
                hd.id_phong,
                hd.ngay_ky AS ngay_bat_dau,
                hd.ngay_het_han AS ngay_ket_thuc,
                hd.trang_thai,
                hd.noi_dung,
                kt.ho_ten,
                pt.so_phong
            FROM hop_dong hd
            JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue
            JOIN phong_tro pt ON hd.id_phong = pt.id_phong
            WHERE hd.id_hop_dong = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Fetched contracts: " . print_r($contracts, true));
        echo json_encode([
            "success" => true,
            "data" => $contracts,
            "total" => count($contracts),
            "totalPages" => 1,
            "currentPage" => 1,
            "limit" => 1
        ]);
    } else {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM hop_dong hd JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue WHERE kt.ho_ten LIKE :search");
        $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $limit);

        $stmt = $conn->prepare("
            SELECT 
                hd.id_hop_dong AS so_hop_dong,
                hd.id_khach_thue,
                hd.id_phong,
                hd.ngay_ky AS ngay_bat_dau,
                hd.ngay_het_han AS ngay_ket_thuc,
                hd.trang_thai,
                kt.ho_ten,
                pt.so_phong
            FROM hop_dong hd
            JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue
            JOIN phong_tro pt ON hd.id_phong = pt.id_phong
            WHERE kt.ho_ten LIKE :search
            ORDER BY hd.id_hop_dong DESC
            LIMIT :offset, :limit
        ");
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "data" => $contracts,
            "total" => (int)$total,
            "totalPages" => $totalPages,
            "currentPage" => $page,
            "limit" => $limit
        ]);
    }
} catch (PDOException $e) {
    error_log("Lỗi truy vấn get_contracts: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Lỗi khi truy vấn dữ liệu: " . $e->getMessage()]);
}
?>