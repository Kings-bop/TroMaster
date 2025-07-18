<?php
header('Content-Type: application/json');
require_once 'connect.php';

session_start();
if (!isset($_SESSION['ten_dang_nhap'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

$ten_dang_nhap = $_SESSION['ten_dang_nhap'];
$stmt = $conn->prepare("SELECT vai_tro FROM tai_khoan WHERE ten_dang_nhap = ?");
$stmt->execute([$ten_dang_nhap]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !in_array($user['vai_tro'], ['admin', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền cập nhật dữ liệu này!']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
error_log("Received data: " . print_r($data, true)); // Thêm log để debug

$id_hop_dong = isset($data['id_hop_dong']) ? (int)$data['id_hop_dong'] : null;
$ngay_bat_dau = isset($data['ngay_bat_dau']) ? $data['ngay_bat_dau'] : null;
$ngay_ket_thuc = isset($data['ngay_ket_thuc']) ? $data['ngay_ket_thuc'] : null;
$trang_thai = isset($data['trang_thai']) ? $data['trang_thai'] : null;
$noi_dung = isset($data['noi_dung']) ? $data['noi_dung'] : null;

if (!$id_hop_dong || !$ngay_bat_dau || !$ngay_ket_thuc || !$trang_thai) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin! Dữ liệu nhận được: ' . json_encode($data)]);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE hop_dong SET ngay_ky = ?, ngay_het_han = ?, trang_thai = ?, noi_dung = ? WHERE id_hop_dong = ?");
    $stmt->execute([$ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $noi_dung, $id_hop_dong]);
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!']);
} catch (PDOException $e) {
    error_log("Lỗi cập nhật hợp đồng: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()]);
}
?>