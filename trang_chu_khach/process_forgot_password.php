<?php
session_start();
require_once 'connect.php';
require 'vendor/autoload.php'; // Đảm bảo đường dẫn đúng

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    try {
        // Kiểm tra email
        $sql = "SELECT t.id_tai_khoan, k.email 
                FROM tai_khoan t 
                JOIN khach_thue k ON t.id_khach_thue = k.id_khach_thue 
                WHERE k.email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Xóa bản ghi OTP cũ
            $sql = "DELETE FROM password_resets WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email]);

            // Tạo OTP và thời gian
            $otp = sprintf("%06d", rand(100000, 999999));
            $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $created_at = date('Y-m-d H:i:s');

            // Lưu OTP
            $sql = "INSERT INTO password_resets (email, otp, created_at, expires_at) VALUES (:email, :otp, :created_at, :expires_at)";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute([
                ':email' => $email,
                ':otp' => $otp,
                ':created_at' => $created_at,
                ':expires_at' => $expires_at
            ]);
            if (!$success) {
                error_log("Failed to insert OTP for email: $email, OTP: $otp");
                $_SESSION['error'] = "Lỗi hệ thống khi tạo OTP!";
                header("Location: forgot_password.php");
                exit();
            }
            error_log("OTP inserted: $otp for email: $email");

            // Gửi email
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'snqanh@gmail.com'; // Thay bằng email thực
            $mail->Password = 'sczp emfi uiqg tyar'; // Thay bằng App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 0; // Tắt debug trong production, 2 để debug

            $mail->setFrom('no-reply@tromaster.com', 'TroMaster');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Ma OTP khoi phuc mat khau cua TroMasTer';
            $mail->Body = "Mã OTP của bạn là: <strong>$otp</strong><br>Mã này sẽ hết hạn sau 10 phút.";
            $mail->AltBody = "Mã OTP của bạn là: $otp\nMã này sẽ hết hạn sau 10 phút.";

            $mail->send();
            error_log("Email sent with OTP: $otp to $email");

            $_SESSION['email'] = $email;
            $_SESSION['success'] = "Mã OTP đã được gửi đến email của bạn!";
            header("Location: verify_otp.php");
            exit();
        } else {
            $_SESSION['error'] = "Email không tồn tại trong hệ thống!";
            header("Location: forgot_password.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
        header("Location: forgot_password.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Không thể gửi email. Lỗi: " . $e->getMessage();
        header("Location: forgot_password.php");
        exit();
    }
}
?>