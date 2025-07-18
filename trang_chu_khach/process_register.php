<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
    $mat_khau = $_POST['mat_khau'];
    $ho_ten = trim($_POST['ho_ten']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $so_cccd = trim($_POST['so_cccd']);
    $email = trim($_POST['email']);

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Kiểm tra trùng lặp
        $check_sql = "SELECT ten_dang_nhap FROM tai_khoan WHERE ten_dang_nhap = :ten_dang_nhap";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute(['ten_dang_nhap' => $ten_dang_nhap]);
        if ($check_stmt->fetch()) {
            throw new Exception("Tên đăng nhập đã tồn tại!");
        }

        $check_sql = "SELECT so_dien_thoai FROM khach_thue WHERE so_dien_thoai = :so_dien_thoai";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute(['so_dien_thoai' => $so_dien_thoai]);
        if ($check_stmt->fetch()) {
            throw new Exception("Số điện thoại đã được sử dụng!");
        }

        $check_sql = "SELECT so_cccd FROM khach_thue WHERE so_cccd = :so_cccd";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute(['so_cccd' => $so_cccd]);
        if ($check_stmt->fetch()) {
            throw new Exception("Số CCCD đã được sử dụng!");
        }

        $check_sql = "SELECT email FROM khach_thue WHERE email = :email";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute(['email' => $email]);
        if ($check_stmt->fetch()) {
            throw new Exception("Email đã được sử dụng!");
        }

        // Thêm vào bảng khach_thue
        $khach_thue_sql = "INSERT INTO khach_thue (ho_ten, so_dien_thoai, so_cccd, email) VALUES (:ho_ten, :so_dien_thoai, :so_cccd, :email)";
        $khach_thue_stmt = $conn->prepare($khach_thue_sql);
        $khach_thue_stmt->execute([
            'ho_ten' => $ho_ten,
            'so_dien_thoai' => $so_dien_thoai,
            'so_cccd' => $so_cccd,
            'email' => $email
        ]);
        $id_khach_thue = $conn->lastInsertId();

        // Thêm vào bảng tai_khoan
        $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);
        $tai_khoan_sql = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, id_khach_thue, email) VALUES (:ten_dang_nhap, :mat_khau, :vai_tro, :id_khach_thue, :email)";
        $tai_khoan_stmt = $conn->prepare($tai_khoan_sql);
        $tai_khoan_stmt->execute([
            'ten_dang_nhap' => $ten_dang_nhap,
            'mat_khau' => $hashed_password,
            'vai_tro' => 'khach_thue',
            'id_khach_thue' => $id_khach_thue,
            'email' => $email
        ]);

        // Commit transaction
        $conn->commit();

        // Đăng nhập tự động
        
        $_SESSION['success'] = "Đăng ký thành công! Chào mừng bạn đến với TroMaster. Vui lòng đăng nhập để sử dụng hệ thống.";
        header("Location: register.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>