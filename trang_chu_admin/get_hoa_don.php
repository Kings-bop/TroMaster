<?php
header('Content-Type: application/json');
require_once 'connect.php';
session_start();

try {
    if (!isset($_SESSION['ten_dang_nhap'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        exit;
    }

    $ten_dang_nhap = $_SESSION['ten_dang_nhap'];
    $stmt = $conn->prepare("SELECT id_khach_thue FROM tai_khoan WHERE ten_dang_nhap = ? AND vai_tro = 'khach_thue'");
    $stmt->execute([$ten_dang_nhap]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không hợp lệ!']);
        exit;
    }

    $id_khach_thue = $user['id_khach_thue'];
    $stmt = $conn->prepare("SELECT * FROM hoa_don WHERE id_khach_thue = ? ORDER BY thang_ap_dung DESC");
    $stmt->execute([$id_khach_thue]);
    $hoa_don = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $hoa_don]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
?>