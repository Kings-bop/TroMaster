<?php
session_start();
require_once 'connect.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['email']) || !isset($_POST['otp']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['email'];
$otp = trim($_POST['otp']);
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

try {
    // Kiểm tra OTP
    $sql = "SELECT expires_at FROM password_resets WHERE email = :email AND otp = :otp";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email, ':otp' => $otp]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset || date('Y-m-d H:i:s') > $reset['expires_at']) {
        $_SESSION['error'] = "Mã OTP không hợp lệ hoặc đã hết hạn!";
        header("Location: verify_otp.php");
        exit();
    }

    // Kiểm tra mật khẩu
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Mật khẩu không khớp!";
        header("Location: reset_password.php");
        exit();
    }

    // Kiểm tra độ mạnh mật khẩu
    if (strlen($new_password) < 8 || !preg_match("/[A-Za-z]/", $new_password) || !preg_match("/[0-9]/", $new_password)) {
        $_SESSION['error'] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái và số!";
        header("Location: reset_password.php");
        exit();
    }

    // Cập nhật mật khẩu
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE tai_khoan t 
            JOIN khach_thue k ON t.id_khach_thue = k.id_khach_thue 
            SET t.mat_khau = :mat_khau 
            WHERE k.email = :email";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':mat_khau' => $hashed_password, ':email' => $email]);

    if ($result) {
        error_log("Password updated successfully for email: $email");
    } else {
        error_log("Failed to update password for email: $email");
        $_SESSION['error'] = "Lỗi hệ thống khi cập nhật mật khẩu!";
        header("Location: reset_password.php");
        exit();
    }

    // Xóa OTP cũ
    $sql = "DELETE FROM password_resets WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);

    $_SESSION['success'] = "Mật khẩu đã được đặt lại thành công!";
    unset($_SESSION['email']);
    unset($_SESSION['verified_otp']);
    header("Location: login.php");
    exit();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
    header("Location: reset_password.php");
    exit();
}
?>