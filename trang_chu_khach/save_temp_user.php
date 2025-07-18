<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json');

// Lấy dữ liệu từ POST
$ho_ten = trim($_POST['ho_ten'] ?? '');
$so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
$email = trim($_POST['email'] ?? '');

// Lấy id_phong từ session (đã được lưu từ dang_ky_thue.php)
$id_phong = $_SESSION['temp_id_phong'] ?? null;

try {
    // Kiểm tra dữ liệu đầu vào
    if (!$ho_ten || !$so_dien_thoai || !$email) {
        throw new Exception('Vui lòng nhập đầy đủ thông tin: họ tên, số điện thoại và email.');
    }

    // Kiểm tra định dạng cơ bản
    if (!preg_match('/^[0-9]{10,11}$/', $so_dien_thoai)) {
        throw new Exception('Số điện thoại phải là dãy số từ 10 đến 11 chữ số.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email không hợp lệ.');
    }

    // Lưu thông tin vào session
    $_SESSION['temp_ho_ten'] = $ho_ten;
    $_SESSION['temp_so_dien_thoai'] = $so_dien_thoai;
    $_SESSION['temp_email'] = $email;
    // id_phong đã được lưu từ trước, không cần cập nhật lại

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400); // Trả về mã lỗi 400 cho yêu cầu không hợp lệ
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn = null;
?>