<?php
header('Content-Type: application/json');
require_once 'connect.php';
require '../trang_chu_khach/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID hóa đơn không hợp lệ!"]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT hd.*, kt.email, kt.ho_ten, pt.so_phong 
        FROM hoa_don hd 
        JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue 
        JOIN phong_tro pt ON hd.id_phong = pt.id_phong 
        WHERE hd.id_hoa_don = :id
    ");
    $stmt->execute([':id' => $id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        echo json_encode(["success" => false, "message" => "Không tìm thấy hóa đơn!"]);
        exit;
    }

    $email = $payment['email'];
    $hoTen = $payment['ho_ten'];
    $soPhong = $payment['so_phong'];
    $thangApDung = $payment['thang_ap_dung'];
    $tongTien = $payment['tong_tien'];
    $trangThai = $payment['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'snqanh@gmail.com';
    $mail->Password = 'sczp emfi uiqg tyar';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('snqanh@gmail.com', 'Quan Ly Phong Tro');
    $mail->addAddress($email, $hoTen);
    $mail->Subject = 'Hoa Don Tien Phong Thang ' . $thangApDung;
    $mail->Body = "Kính gửi $hoTen,\n\nHóa đơn tháng $thangApDung:\nPhòng: $soPhong\nTổng tiền: $tongTien VNĐ\nTrạng thái: $trangThai\nCảm ơn bạn!\nQuản lý phòng trọ";

    $mail->send();

    echo json_encode(["success" => true, "message" => "Gửi email thành công"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Lỗi email: " . $e->getMessage()]);
}
exit; // Đảm bảo không có output nào sau này
?>