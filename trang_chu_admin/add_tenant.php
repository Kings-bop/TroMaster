<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'] ?? '';
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? '';
    $roomId = $data['roomId'] ?? null;
    $cccd = $data['so_cccd'] ?? null;
    $ngaySinh = $data['ngay_sinh'] ?? null; 
    $gioiTinh = $data['gioi_tinh'] ?? null; 

    if (empty($name)) {
        throw new Exception('Họ tên không được để trống!');
    }
    if (empty($phone)) {
        throw new Exception('Số điện thoại không được để trống!');
    }
    if (empty($roomId) || !is_numeric($roomId)) {
        throw new Exception('Vui lòng chọn một phòng trọ hợp lệ!');
    }
    if (empty($cccd)) {
        throw new Exception('Số CCCD không được để trống!');
    }

    // Kiểm tra số điện thoại đã tồn tại chưa
    $checkPhoneStmt = $conn->prepare("SELECT COUNT(*) FROM khach_thue WHERE so_dien_thoai = :phone");
    $checkPhoneStmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $checkPhoneStmt->execute();
    if ($checkPhoneStmt->fetchColumn() > 0) {
        throw new Exception('Số điện thoại ' . $phone . ' đã được sử dụng!');
    }

    // Kiểm tra CCCD đã tồn tại chưa
    $checkCCCDStmt = $conn->prepare("SELECT COUNT(*) FROM khach_thue WHERE so_cccd = :cccd");
    $checkCCCDStmt->bindParam(':cccd', $cccd, PDO::PARAM_STR);
    $checkCCCDStmt->execute();
    if ($checkCCCDStmt->fetchColumn() > 0) {
        throw new Exception('Số CCCD ' . $cccd . ' đã được sử dụng!');
    }

    $query = "INSERT INTO khach_thue (ho_ten, email, so_dien_thoai, id_phong, so_cccd, ngay_sinh, gioi_tinh) VALUES (:name, :email, :phone, :roomId, :cccd, :ngay_sinh, :gioi_tinh)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->bindParam(':cccd', $cccd, PDO::PARAM_STR);
    $stmt->bindParam(':ngay_sinh', $ngaySinh, PDO::PARAM_STR); // Bind ngay_sinh
    $stmt->bindParam(':gioi_tinh', $gioiTinh, PDO::PARAM_STR); // Bind gioi_tinh

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm khách thuê thành công!',
            'id' => $conn->lastInsertId()
        ]);
    } else {
        throw new Exception('Thêm khách thuê thất bại: ' . implode(', ', $stmt->errorInfo()));
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>