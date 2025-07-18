<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_tai_khoan'])) {
    $id_khach_thue = isset($_POST['id_khach_thue']) ? (int)$_POST['id_khach_thue'] : 0;
    $ho_ten = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $so_cccd = trim($_POST['so_cccd']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $email = trim($_POST['email']);

    try {
        $sql_tk = "SELECT id_khach_thue FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
        $stmt_tk = $conn->prepare($sql_tk);
        $stmt_tk->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
        $stmt_tk->execute();
        $tk = $stmt_tk->fetch(PDO::FETCH_ASSOC);

        if ($tk && $tk['id_khach_thue'] === $id_khach_thue) {
            $sql = "UPDATE khach_thue SET ho_ten = :ho_ten, ngay_sinh = :ngay_sinh, so_dien_thoai = :so_dien_thoai, so_cccd = :so_cccd, gioi_tinh = :gioi_tinh, email = :email WHERE id_khach_thue = :id_khach_thue";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
            $stmt->bindParam(':ho_ten', $ho_ten, PDO::PARAM_STR);
            $stmt->bindParam(':ngay_sinh', $ngay_sinh, PDO::PARAM_STR);
            $stmt->bindParam(':so_dien_thoai', $so_dien_thoai, PDO::PARAM_STR);
            $stmt->bindParam(':so_cccd', $so_cccd, PDO::PARAM_STR);
            $stmt->bindParam(':gioi_tinh', $gioi_tinh, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Thông tin đã được cập nhật thành công.';
            } else {
                $response['message'] = 'Cập nhật thất bại. Vui lòng thử lại.';
            }
        } else {
            $response['message'] = 'ID khách thuê không khớp hoặc không tồn tại.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Yêu cầu không hợp lệ hoặc chưa đăng nhập.';
}

echo json_encode($response);
?>