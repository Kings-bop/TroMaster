<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $id = $_GET['id'] ?? null;
if (empty($id) || !is_numeric($id)) {
    throw new Exception('ID khách thuê không hợp lệ!');
}

    $query = "SELECT id_khach_thue, ho_ten, so_dien_thoai FROM khach_thue WHERE id_khach_thue = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant) {
        echo json_encode([
            'success' => true,
            'data' => $tenant
        ]);
    } else {
        throw new Exception('Không tìm thấy khách thuê!');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>