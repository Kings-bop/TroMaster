<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)$data['id'];

$sql = "DELETE FROM khu_tro WHERE id_khu_tro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Xóa khu trọ thất bại']);
}
?>