<?php
session_start();
require_once 'connect.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['email']) || !isset($_POST['otp'])) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
    header("Location: verify_otp.php");
    exit();
}

$email = $_SESSION['email'];
$otp = trim($_POST['otp']);

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

    // Lưu OTP vào session
    $_SESSION['verified_otp'] = $otp;
    $_SESSION['success'] = "Xác minh OTP thành công! Vui lòng đặt lại mật khẩu.";
    header("Location: reset_password.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
    header("Location: verify_otp.php");
    exit();
}
?>