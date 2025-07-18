<?php
header('Content-Type: application/json');

require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$so_phong = $data['roomName'] ?? '';
$gia_thue = $data['price'] ?? 0;
$trang_thai = $data['status'] ?? 'trong';

if (!$id) {
    echo json_encode(["success" => false, "message" => "Thiếu ID phòng"]);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE phong_tro SET so_phong = ?, gia_thue = ?, trang_thai = ?, ngay_cap_nhat = NOW() WHERE id_phong = ?");
    $stmt->execute([$so_phong, $gia_thue, $trang_thai, $id]);

    echo json_encode(["success" => true, "message" => "Cập nhật phòng thành công"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật phòng: " . $e->getMessage()]);
}
