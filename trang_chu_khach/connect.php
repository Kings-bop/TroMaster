<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quan_ly_phong_tro";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");
} catch(PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
    exit();
}
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ +07
?>