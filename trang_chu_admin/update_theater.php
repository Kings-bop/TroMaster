<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$address = $data['address'];
$overview = $data['overview'];

$sql = "UPDATE khu_tro SET dia_chi = ?, tong_quan = ?, ngay_cap_nhat = CURRENT_TIMESTAMP WHERE id_khu_tro = ?";
$stmt = $conn->prepare($sql);
$stmt-> $conn->prepare("ssi", $address, $overview, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật khu trọ thất bại']);
}
?>