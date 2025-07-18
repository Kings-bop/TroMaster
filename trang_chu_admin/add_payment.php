<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $tenantId = $data['tenantId'] ?? null;
    $roomId = $data['roomId'] ?? null;
    $thangApDung = $data['thangApDung'] ?? null;
    $tienPhong = $data['tienPhong'] ?? 0;
    $tienDien = $data['tienDien'] ?? 0;
    $tienNuoc = $data['tienNuoc'] ?? 0;
    $tienInternet = $data['tienInternet'] ?? 0;
    $phiKhac = $data['phiKhac'] ?? 0;
    $ngayThanhToan = $data['ngayThanhToan'] ?? null;
    $trangThai = $data['trangThai'] ?? 'chua_thanh_toan'; // Đảm bảo giá trị mặc định

    error_log("Received data: " . print_r($data, true));
    error_log("Inserting trangThai: " . $trangThai);

    if (!$tenantId || !$roomId || !$thangApDung || !$tienPhong) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc!']);
        exit;
    }

    // Kiểm tra khách thuê tồn tại
    $stmt = $conn->prepare("SELECT id_phong FROM khach_thue WHERE id_khach_thue = ?");
    $stmt->execute([$tenantId]);
    $tenantRoom = $stmt->fetchColumn();
    error_log("Received tenantId: " . var_export($tenantId, true) . ", roomId: " . var_export($roomId, true));
    if ($tenantId === null || !is_numeric($tenantId)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng chọn khách thuê hợp lệ!']);
        exit;
    }
    if ($tenantRoom === null) {
        error_log("Warning: Khách $tenantId chưa được gán phòng.");
        // Tùy chọn: Cho phép thêm nếu chưa gán phòng, gán tạm $roomId
    } elseif ($tenantRoom != $roomId) {
        echo json_encode(['success' => false, 'message' => 'Khách không ở phòng đó!']);
        exit;
    }

    // Kiểm tra hóa đơn đã tồn tại cho phòng và tháng áp dụng
    $stmt = $conn->prepare("SELECT id_hoa_don FROM hoa_don WHERE id_phong = ? AND thang_ap_dung = ?");
    $stmt->execute([$roomId, $thangApDung]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Hóa đơn cho phòng này trong tháng này đã tồn tại!']);
        exit;
    }

    // Lưu hóa đơn
    $stmt = $conn->prepare("
    INSERT INTO hoa_don (id_khach_thue, id_phong, thang_ap_dung, tien_phong, tien_dien, tien_nuoc, tien_internet, phi_khac, trang_thai_thanh_toan, ngay_thanh_toan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$tenantId, $roomId, $thangApDung, $tienPhong, $tienDien, $tienNuoc, $tienInternet, $phiKhac, $trangThai, $ngayThanhToan]);

    echo json_encode(['success' => true, 'message' => 'Thêm hóa đơn thành công!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
?>