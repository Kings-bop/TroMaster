<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $tenDangNhap = $data['tenDangNhap'] ?? '';
    $matKhau = $data['matKhau'] ?? '';
    $vaiTro = $data['vaiTro'] ?? 'khach_thue';
    $idKhachThue = $data['idKhachThue'] ?? null;
    $email = $data['email'] ?? null;

    if (empty($id) || empty($tenDangNhap)) {
        throw new Exception('Vui lòng điền đầy đủ ID và tên đăng nhập!');
    }

    $hashedPassword = !empty($matKhau) ? password_hash($matKhau, PASSWORD_DEFAULT) : null;

    $query = "UPDATE tai_khoan SET ten_dang_nhap = :tenDangNhap, mat_khau = COALESCE(:matKhau, mat_khau), vai_tro = :vaiTro, id_khach_thue = :idKhachThue, email = :email, ngay_cap_nhat = NOW() WHERE id_tai_khoan = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':tenDangNhap', $tenDangNhap, PDO::PARAM_STR);
    $stmt->bindParam(':matKhau', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':vaiTro', $vaiTro, PDO::PARAM_STR);
    $stmt->bindParam(':idKhachThue', $idKhachThue, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật tài khoản thành công!'
        ]);
    } else {
        throw new Exception('Cập nhật tài khoản thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>