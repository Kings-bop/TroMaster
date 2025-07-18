<?php
session_start();

// Xóa tất cả dữ liệu session
session_unset();
session_destroy();

// Trả về JSON
header("Content-Type: application/json");
echo json_encode(["success" => true, "message" => "Đăng xuất thành công"]);
exit();
?>
