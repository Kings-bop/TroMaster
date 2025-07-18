<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

if (isset($_SESSION['id_tai_khoan'])) {
    try {
        $id_khach_thue = isset($_GET['id_khach_thue']) ? (int)$_GET['id_khach_thue'] : 0;
        $sql_tk = "SELECT id_khach_thue FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
        $stmt_tk = $conn->prepare($sql_tk);
        $stmt_tk->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
        $stmt_tk->execute();
        $tk = $stmt_tk->fetch(PDO::FETCH_ASSOC);

        if ($tk && $tk['id_khach_thue'] === $id_khach_thue) {
            $sql = "SELECT ho_ten, ngay_sinh, so_dien_thoai, so_cccd, gioi_tinh, email FROM khach_thue WHERE id_khach_thue = :id_khach_thue";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $response['success'] = true;
                $response['data'] = $user;
            } else {
                $response['message'] = 'Không tìm thấy thông tin khách thuê.';
            }
        } else {
            $response['message'] = 'ID khách thuê không khớp.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Chưa đăng nhập.';
}

echo json_encode($response);
?>