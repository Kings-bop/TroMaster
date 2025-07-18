<?php
header('Content-Type: application/json');

try {
    // Khởi tạo kết nối PDO
    $pdo = new PDO(
        "mysql:host=localhost;dbname=quan_ly_phong_tro", 
        "root", 
        ""  
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Bật chế độ lỗi

    // Nhận dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);

    $id_khach_thue = $input['id_khach_thue'] ?? null;
    $id_phong = $input['id_phong'] ?? null;
    $start_date = $input['start_date'] ?? null;
    $end_date = $input['end_date'] ?? null;

    // Kiểm tra dữ liệu
    if (!$id_khach_thue || !$id_phong || !$start_date || !$end_date) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
        exit;
    }

    // Sử dụng prepared statement
    $sql = "INSERT INTO hop_dong (id_khach_thue, id_phong, ngay_ky, ngay_het_han) VALUES (:id_khach_thue, :id_phong, :start_date, :end_date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_khach_thue' => $id_khach_thue,
        ':id_phong' => $id_phong,
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);

    echo json_encode(['success' => true, 'message' => 'Thêm hợp đồng thành công!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>