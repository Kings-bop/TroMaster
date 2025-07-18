<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['tenantId']) || !isset($data['roomId']) || !isset($data['thangApDung']) || !isset($data['tienPhong'])) {
    echo json_encode(["success" => false, "message" => "Dữ liệu không đầy đủ!"]);
    exit;
}

$id = $data['id'];
$tenantId = $data['tenantId'];
$roomId = $data['roomId'];
$thangApDung = $data['thangApDung'];
$tienPhong = $data['tienPhong'];
$tienDien = $data['tienDien'] ?? 0;
$tienNuoc = $data['tienNuoc'] ?? 0;
$tienInternet = $data['tienInternet'] ?? 0;
$phiKhac = $data['phiKhac'] ?? 0;
$ngayThanhToan = $data['ngayThanhToan'] ?? null;
$trangThai = $data['trangThai'];

try {
    $stmt = $conn->prepare("
        UPDATE hoa_don 
        SET id_khach_thue = :tenantId, id_phong = :roomId, thang_ap_dung = :thangApDung, 
            tien_phong = :tienPhong, tien_dien = :tienDien, tien_nuoc = :tienNuoc, 
            tien_internet = :tienInternet, phi_khac = :phiKhac, tong_tien = :tongTien, 
            ngay_thanh_toan = :ngayThanhToan, trang_thai_thanh_toan = :trangThai
        WHERE id_hoa_don = :id
    ");
    $tongTien = $tienPhong + $tienDien + $tienNuoc + $tienInternet + $phiKhac;
    $stmt->execute([
        ':id' => $id,
        ':tenantId' => $tenantId,
        ':roomId' => $roomId,
        ':thangApDung' => $thangApDung,
        ':tienPhong' => $tienPhong,
        ':tienDien' => $tienDien,
        ':tienNuoc' => $tienNuoc,
        ':tienInternet' => $tienInternet,
        ':phiKhac' => $phiKhac,
        ':tongTien' => $tongTien,
        ':ngayThanhToan' => $ngayThanhToan,
        ':trangThai' => $trangThai
    ]);

    echo json_encode(["success" => true, "message" => "Cập nhật hóa đơn thành công"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi: " . $e->getMessage()]);
}
?>