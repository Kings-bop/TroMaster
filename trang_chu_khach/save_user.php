<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json');

$ho_ten = $_POST['ho_ten'] ?? '';
$so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
$email = $_POST['email'] ?? '';
$id_phong = $_POST['id_phong'] ?? null;

try {
    if (!$ho_ten || !$so_dien_thoai || !$email || !$id_phong) {
        throw new Exception('Thông tin khách thuê không hợp lệ');
    }

    // Kiểm tra tồn tại của id_phong
    $check_sql = "SELECT COUNT(*) FROM phong_tro WHERE id_phong = :id_phong";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
    $check_stmt->execute();
    if ($check_stmt->fetchColumn() == 0) {
        throw new Exception('ID phòng không tồn tại');
    }

    // Kiểm tra xem so_dien_thoai đã tồn tại trong bảng khach_thue chưa
    $check_phone_sql = "SELECT COUNT(*) FROM khach_thue WHERE so_dien_thoai = :so_dien_thoai";
    $check_phone_stmt = $conn->prepare($check_phone_sql);
    $check_phone_stmt->bindParam(':so_dien_thoai', $so_dien_thoai, PDO::PARAM_STR);
    $check_phone_stmt->execute();
    if ($check_phone_stmt->fetchColumn() > 0) {
        throw new Exception('Số điện thoại đã được sử dụng bởi một khách thuê khác. Vui lòng sử dụng số khác.');
    }

    // Lưu vào bảng khach_thue
    $sql = "INSERT INTO khach_thue (ho_ten, so_dien_thoai, email, id_phong) 
            VALUES (:ho_ten, :so_dien_thoai, :email, :id_phong)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ho_ten', $ho_ten, PDO::PARAM_STR);
    $stmt->bindParam(':so_dien_thoai', $so_dien_thoai, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
    $stmt->execute();

    $id_khach_thue = $conn->lastInsertId();

    // Kiểm tra tài khoản, tạo mới nếu chưa có
    $check_tai_khoan_sql = "SELECT COUNT(*) FROM tai_khoan WHERE ten_dang_nhap = :ten_dang_nhap";
    $check_tai_khoan_stmt = $conn->prepare($check_tai_khoan_sql);
    $check_tai_khoan_stmt->bindParam(':ten_dang_nhap', $so_dien_thoai, PDO::PARAM_STR);
    $check_tai_khoan_stmt->execute();
    if ($check_tai_khoan_stmt->fetchColumn() == 0) {
        $mat_khau = bin2hex(random_bytes(4));
        $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);

        $tai_khoan_sql = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, id_khach_thue, email) 
                          VALUES (:ten_dang_nhap, :mat_khau, 'khach_thue', :id_khach_thue, :email)";
        $tai_khoan_stmt = $conn->prepare($tai_khoan_sql);
        $tai_khoan_stmt->bindParam(':ten_dang_nhap', $so_dien_thoai, PDO::PARAM_STR);
        $tai_khoan_stmt->bindParam(':mat_khau', $mat_khau_hash, PDO::PARAM_STR);
        $tai_khoan_stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
        $tai_khoan_stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $tai_khoan_stmt->execute();

        $_SESSION['temp_login'] = [
            'ten_dang_nhap' => $so_dien_thoai,
            'mat_khau' => $mat_khau
        ];
    } else {
        $update_tai_khoan_sql = "UPDATE tai_khoan SET id_khach_thue = :id_khach_thue WHERE ten_dang_nhap = :ten_dang_nhap";
        $update_tai_khoan_stmt = $conn->prepare($update_tai_khoan_sql);
        $update_tai_khoan_stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
        $update_tai_khoan_stmt->bindParam(':ten_dang_nhap', $so_dien_thoai, PDO::PARAM_STR);
        $update_tai_khoan_stmt->execute();
    }

    //thêm temp_login, để đảm bảo thanh_toan.php nhận được thông tin temp_login khi tài khoản mới được tạo.
    echo json_encode(['success' => true, 'temp_login' => $_SESSION['temp_login'] ?? null, 'redirect' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt = null;
$check_stmt = null;
$check_phone_stmt = null;
$check_tai_khoan_stmt = null;
$update_tai_khoan_stmt = null;
$tai_khoan_stmt = null;
$conn = null;
?>