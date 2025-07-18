<?php
header('Content-Type: application/json');

require_once 'connect.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id_tai_khoan'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$id_tai_khoan = $_SESSION['id_tai_khoan'];
$oldPassword = $_POST['oldPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';

try {
    $sql = "SELECT mat_khau, ten_dang_nhap FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_tai_khoan', $id_tai_khoan, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        exit;
    }

    // Debug
    error_log("Debug - id_tai_khoan: $id_tai_khoan");
    error_log("Debug - ten_dang_nhap: " . $user['ten_dang_nhap']);
    error_log("Debug - oldPassword: $oldPassword");
    error_log("Debug - hashedPassword: " . $user['mat_khau']);
    error_log("Debug - password_verify: " . (password_verify($oldPassword, $user['mat_khau']) ? 'true' : 'false'));

    if (!password_verify($oldPassword, $user['mat_khau'])) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng']);
        exit;
    }

    $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE tai_khoan SET mat_khau = :mat_khau WHERE id_tai_khoan = :id_tai_khoan";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mat_khau', $newPasswordHashed, PDO::PARAM_STR);
    $stmt->bindParam(':id_tai_khoan', $id_tai_khoan, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật mật khẩu thất bại']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}

$stmt = null;
$conn = null;
?>