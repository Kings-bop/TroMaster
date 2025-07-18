<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    $tenDangNhap = $data['tenDangNhap'] ?? '';
    $matKhau = $data['matKhau'] ?? '';
    $vaiTro = $data['vaiTro'] ?? 'khach_thue';
    $idKhachThue = $data['idKhachThue'] ?? null;
    $email = $data['email'] ?? null;

    // Kiểm tra dữ liệu đầu vào
    if (empty($tenDangNhap) || empty($matKhau)) {
        throw new Exception('Vui lòng điền đầy đủ tên đăng nhập và mật khẩu!');
    }

    // Mã hóa mật khẩu (sử dụng password_hash)
    $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);

    // Chuẩn bị và thực thi truy vấn thêm
    $query = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, id_khach_thue, email) VALUES (:tenDangNhap, :matKhau, :vaiTro, :idKhachThue, :email)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':tenDangNhap', $tenDangNhap, PDO::PARAM_STR);
    $stmt->bindParam(':matKhau', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':vaiTro', $vaiTro, PDO::PARAM_STR);
    $stmt->bindParam(':idKhachThue', $idKhachThue, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm tài khoản thành công!',
            'id' => $conn->lastInsertId()
        ]);
    } else {
        throw new Exception('Thêm tài khoản thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>