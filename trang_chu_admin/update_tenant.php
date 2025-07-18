<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;
    $ho_ten = $data['ho_ten'] ?? null;
    $so_dien_thoai = $data['so_dien_thoai'] ?? null;
    $email = $data['email'] ?? null;
    $so_cccd = $data['so_cccd'] ?? null;
    $phong_tro = $data['phong_tro'] ?? null;
    $ngay_sinh = $data['ngay_sinh'] ?? null; // Thêm trường ngay_sinh
    $gioi_tinh = $data['gioi_tinh'] ?? null; // Thêm trường gioi_tinh

    if (empty($id) || empty($ho_ten) || empty($so_dien_thoai) || empty($so_cccd) || empty($phong_tro)) {
        throw new Exception('Thiếu thông tin bắt buộc!');
    }

    // Kiểm tra độ dài CCCD (12 số)
    if (strlen((string)$so_cccd) != 12) {
        throw new Exception('Số CCCD phải có đúng 12 chữ số!');
    }

    // Kiểm tra giới tính (nếu có, chỉ chấp nhận 'nam', 'nu', 'khac')
    if ($gioi_tinh && !in_array($gioi_tinh, ['nam', 'nu', 'khac'])) {
        throw new Exception('Giới tính không hợp lệ!');
    }

    $query = "UPDATE khach_thue SET ho_ten = :ho_ten, so_dien_thoai = :so_dien_thoai, email = :email, so_cccd = :so_cccd, id_phong = :phong_tro, ngay_sinh = :ngay_sinh, gioi_tinh = :gioi_tinh WHERE id_khach_thue = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':ho_ten', $ho_ten, PDO::PARAM_STR);
    $stmt->bindParam(':so_dien_thoai', $so_dien_thoai, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':so_cccd', $so_cccd, PDO::PARAM_STR);
    $stmt->bindParam(':phong_tro', $phong_tro, PDO::PARAM_INT);
    $stmt->bindParam(':ngay_sinh', $ngay_sinh, PDO::PARAM_STR); // Thêm binding
    $stmt->bindParam(':gioi_tinh', $gioi_tinh, PDO::PARAM_STR); // Thêm binding
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Cập nhật khách thuê thành công!']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>