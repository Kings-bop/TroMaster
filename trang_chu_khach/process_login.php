<?php
session_start();
require_once 'connect.php';

// Thiết lập múi giờ GMT+7
date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['ten_dang_nhap']); // Sử dụng chung cho cả tên đăng nhập và email
    $mat_khau = $_POST['mat_khau'];

    try {
        // Sử dụng tham số riêng cho ten_dang_nhap và email
        $sql = "SELECT t.id_tai_khoan, t.ten_dang_nhap, t.mat_khau, k.ho_ten, t.email, t.vai_tro 
                FROM tai_khoan t 
                LEFT JOIN khach_thue k ON t.id_khach_thue = k.id_khach_thue 
                WHERE t.ten_dang_nhap = :login_input OR t.email = :login_input";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['login_input' => $login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            error_log("Login attempt for: $login_input, Stored hash: " . ($user['mat_khau'] ?? 'null'));
            if (password_verify($mat_khau, $user['mat_khau'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id_tai_khoan'] = $user['id_tai_khoan']; // Thêm ID tài khoản
                $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
                $_SESSION['ho_ten'] = $user['ho_ten'] ?? $user['ten_dang_nhap'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['vai_tro'] = $user['vai_tro'];

                if ($user['vai_tro'] === 'super_admin' || $user['vai_tro'] === 'admin') {
                    $_SESSION['success'] = "Đăng nhập thành công! Chào mừng đến trang quản lý.";
                    header("Location: /quan_ly_phong_tro/trang_chu_admin/admin_dashboard.php");
                } else {
                    $_SESSION['success'] = "Đăng nhập thành công!";
                    header("Location: /quan_ly_phong_tro/trang_chu_khach/khach_thue.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Mật khẩu không đúng!";
                error_log("Password verification failed for: $login_input, Input: $mat_khau, Stored: " . $user['mat_khau']);
                header("Location: /quan_ly_phong_tro/trang_chu_khach/login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Tên đăng nhập hoặc email không tồn tại!";
            error_log("No user found for: $login_input");
            header("Location: /quan_ly_phong_tro/trang_chu_khach/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
        error_log("PDO Exception: " . $e->getMessage());
        header("Location: /quan_ly_phong_tro/trang_chu_khach/login.php");
        exit();
    }
}
?>