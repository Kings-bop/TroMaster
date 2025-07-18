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
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa dữ liệu này!']);
    exit();
}

// Nhận dữ liệu từ client
$data = json_decode(file_get_contents("php://input"), true);
$id_hop_dong = isset($data['id_hop_dong']) ? (int)$data['id_hop_dong'] : null;

if (!$id_hop_dong) {
    echo json_encode(['success' => false, 'message' => 'ID hợp đồng không hợp lệ!']);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM hop_dong WHERE id_hop_dong = ?");
    $stmt->execute([$id_hop_dong]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Xóa hợp đồng thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy hợp đồng để xóa!']);
    }
} catch (PDOException $e) {
    error_log("Lỗi xóa hợp đồng: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa hợp đồng: ' . $e->getMessage()]);
}
?>