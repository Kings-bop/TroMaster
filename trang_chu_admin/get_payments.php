<?php
header('Content-Type: application/json');
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$page = isset($data['page']) ? (int)$data['page'] : 1;
$limit = isset($data['limit']) ? (int)$data['limit'] : 10;
$search = isset($data['search']) ? trim($data['search']) : "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($id) {
        // Lấy thông tin hóa đơn cụ thể theo id
        $stmt = $conn->prepare("
            SELECT hd.*, kt.ho_ten, pt.so_phong
            FROM hoa_don hd
            JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue
            JOIN phong_tro pt ON hd.id_phong = pt.id_phong
            WHERE hd.id_hoa_don = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $payment = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $payment]);
    } else {
        // Lấy danh sách hóa đơn
        $offset = ($page - 1) * $limit;
        $count = $conn->prepare("SELECT COUNT(*) FROM hoa_don hd JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue WHERE kt.ho_ten LIKE :search");
        $count->execute(['search' => "%$search%"]);
        $total = $count->fetchColumn();

        $stmt = $conn->prepare("
            SELECT hd.*, kt.ho_ten, pt.so_phong
            FROM hoa_don hd
            JOIN khach_thue kt ON hd.id_khach_thue = kt.id_khach_thue
            JOIN phong_tro pt ON hd.id_phong = pt.id_phong
            WHERE kt.ho_ten LIKE :search
            ORDER BY hd.id_hoa_don DESC
            LIMIT :offset, :limit
        ");
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $payments, "total" => (int)$total, "page" => $page, "limit" => $limit]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi: " . $e->getMessage()]);
}
?>