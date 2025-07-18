<?php
header('Content-Type: application/json');
require_once 'connect.php';

$id_phong = $_GET['id_phong'] ?? $_POST['id_phong'] ?? null;
$trang_thai = $_GET['trang_thai'] ?? $_POST['trang_thai'] ?? 'da-thue';

try {
    if ($id_phong === null) {
        throw new Exception('ID phòng không được cung cấp');
    }

    $sql = "UPDATE phong_tro SET trang_thai = :trang_thai WHERE id_phong = :id_phong AND trang_thai = 'trong'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':trang_thai', $trang_thai, PDO::PARAM_STR);
    $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        throw new Exception('Phòng không tồn tại hoặc đã được thuê');
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt = null;
$conn = null;
?>