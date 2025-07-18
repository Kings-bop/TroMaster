<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$address = $conn->quote($data['address']);
$overview = $conn->quote($data['overview'] ?? '');

$sql = "INSERT INTO khu_tro (dia_chi, tong_quan) VALUES ($address, $overview)";
if ($conn->exec($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Thêm khu trọ thất bại']);
}
?>