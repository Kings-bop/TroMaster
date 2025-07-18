<?php
header('Content-Type: application/json');
require_once 'connect.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if (!$id || !isset($_SESSION['id_khach_thue']) || $_SESSION['id_khach_thue'] != $id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($action === 'get_user_info') {
    $stmt = $conn->prepare("SELECT ho_ten, email, so_dien_thoai FROM khach_thue WHERE id_khach_thue = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(['success' => true, 'ho_ten' => $user['ho_ten'], 'email' => $user['email'], 'so_dien_thoai' => $user['so_dien_thoai']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin']);
    }
} elseif ($action === 'get_invoices') {
    $stmt = $conn->prepare("SELECT pt.so_phong, hd.thang_ap_dung, hd.tong_tien, hd.trang_thai_thanh_toan FROM hoa_don hd JOIN phong_tro pt ON hd.id_phong = pt.id_phong WHERE hd.id_khach_thue = :id");
    $stmt->execute([':id' => $id]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($invoices) {
        echo json_encode(['success' => true, 'invoices' => $invoices]);
    } else {
        echo json_encode(['success' => true, 'invoices' => []]);
    }
}
?>