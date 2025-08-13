<?php
session_start();
require_once 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['ten_dang_nhap'])) {
    header("Location: ../login.php");
    exit();
}

$ten_dang_nhap = $_SESSION['ten_dang_nhap'];
$stmt = $conn->prepare("SELECT vai_tro FROM tai_khoan WHERE ten_dang_nhap = ?");
$stmt->execute([$ten_dang_nhap]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !in_array($user['vai_tro'], ['admin', 'super_admin'])) {
    // Nếu không phải admin, chuyển hướng đến trang khách thuê
    if (isset($_SESSION['loggedin'])) {
        header("Location: ../khach_thue.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - TroMaster</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="admin-logo">
            <span>TROMASTER</span>
        </div>
        <button class="admin-logout" onclick="logoutAdmin()">Đăng xuất</button>
    </header>

    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <ul>
                <li class="active" onclick="showSection('dashboard')">Tổng quan</li>
                <li onclick="showSection('khu_vuc')">Quản lý khu trọ</li>
                <li onclick="showSection('luu_dien_chung')">Quản lý quy định chung</li>
                <li onclick="showSection('tai_khoan')">Quản lý tài khoản</li>
                <li onclick="showSection('phong_tro')">Quản lý phòng trọ</li>
                <li onclick="showSection('khach_thue')">Quản lý khách thuê</li>
                <li onclick="showSection('hoa_don')">Quản lý hóa đơn</li>
                <li onclick="showSection('hop_dong')">Quản lý hợp đồng</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="admin-section active">
                <h2>Tổng quan</h2>
                <div class="dashboard-stats">
                    <div class="stat-box">
                        <h3>Khu trọ</h3>
                        <p id="total-khu_vuc">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Quy định chung</h3>
                        <p id="total-luu_dien_chung">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Tài khoản</h3>
                        <p id="total-tai_khoan">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Phòng trọ</h3>
                        <p id="total-phong_tro">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Khách thuê</h3>
                        <p id="total-khach_thue">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Hóa đơn</h3>
                        <p id="total-hoa_don">0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Hợp đồng</h3>
                        <p id="total-hop_dong">0</p>
                    </div>
                </div>
            </section>

            <!-- Quản lý khu trọ -->
            <section id="khu_vuc" class="admin-section">
                <h2>Quản lý khu trọ</h2>
                <div class="table-controls">
                    <input type="text" id="search-khu_vuc" placeholder="Tìm kiếm khu trọ theo địa chỉ..."
                        onkeyup="searchKhuVuc()">
                    <button onclick="refreshKhuVuc()">Làm mới</button>
                    <button onclick="addKhuVuc()">Thêm khu trọ</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Địa chỉ</th>
                            <th>Tổng quan</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="khu_vuc-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="khu_vuc-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Quản lý quy định chung -->
            <section id="luu_dien_chung" class="admin-section">
                <h2>Quản lý quy định chung</h2>
                <div class="table-controls">
                    <input type="text" id="search-luu_dien_chung" placeholder="Tìm kiếm quy định..."
                        onkeyup="searchLuuDienChung()">
                    <button onclick="refreshLuuDienChung()">Làm mới</button>
                    <button onclick="addLuuDienChung()">Thêm quy định</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Link quy định</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày cập nhật</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="luu_dien_chung-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="luu_dien_chung-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Quản lý tài khoản -->
            <section id="tai_khoan" class="admin-section">
                <h2>Quản lý tài khoản</h2>
                <div class="table-controls">
                    <input type="text" id="search-tai_khoan" placeholder="Tìm kiếm tài khoản..."
                        onkeyup="searchTaiKhoan()">
                    <button onclick="refreshTaiKhoan()">Làm mới</button>
                    <button onclick="addTaiKhoan()">Thêm tài khoản</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="tai_khoan-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="tai_khoan-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Modal thêm tài khoản -->
            <div class="modal" id="addTaiKhoanModal">
                <div class="modal-content">
                    <h3>Thêm tài khoản mới</h3>
                    <label for="addTaiKhoanTenDangNhap">Tên đăng nhập:</label>
                    <input type="text" id="addTaiKhoanTenDangNhap" placeholder="Tên đăng nhập">
                    <label for="addTaiKhoanMatKhau">Mật khẩu:</label>
                    <input type="password" id="addTaiKhoanMatKhau" placeholder="Mật khẩu">
                    <label for="addTaiKhoanVaiTro">Vai trò:</label>
                    <select id="addTaiKhoanVaiTro">
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="khach_thue" selected>Khách thuê</option>
                    </select>
                    <label for="addTaiKhoanIdKhachThue">ID Khách thuê:</label>
                    <input type="number" id="addTaiKhoanIdKhachThue" placeholder="ID khách thuê (tùy chọn)">
                    <label for="addTaiKhoanEmail">Email:</label>
                    <input type="email" id="addTaiKhoanEmail" placeholder="Email (tùy chọn)">
                    <button onclick="saveNewTaiKhoan()">Lưu</button>
                    <button onclick="closeModal('addTaiKhoanModal')">Hủy</button>
                </div>
            </div>

            <!-- Modal sửa tài khoản -->
            <div class="modal" id="editTaiKhoanModal">
                <div class="modal-content">
                    <h3>Sửa tài khoản</h3>
                    <input type="hidden" id="editTaiKhoanId">
                    <label for="editTaiKhoanTenDangNhap">Tên đăng nhập:</label>
                    <input type="text" id="editTaiKhoanTenDangNhap" placeholder="Tên đăng nhập">
                    <label for="editTaiKhoanMatKhau">Mật khẩu (để trống nếu không đổi):</label>
                    <input type="password" id="editTaiKhoanMatKhau" placeholder="Mật khẩu">
                    <label for="editTaiKhoanVaiTro">Vai trò:</label>
                    <select id="editTaiKhoanVaiTro">
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="khach_thue">Khách thuê</option>
                    </select>
                    <label for="editTaiKhoanIdKhachThue">ID Khách thuê:</label>
                    <input type="number" id="editTaiKhoanIdKhachThue" placeholder="ID khách thuê (tùy chọn)">
                    <label for="editTaiKhoanEmail">Email:</label>
                    <input type="email" id="editTaiKhoanEmail" placeholder="Email (tùy chọn)">
                    <button onclick="updateTaiKhoan()">Lưu</button>
                    <button onclick="closeModal('editTaiKhoanModal')">Hủy</button>
                </div>
            </div>

            <!-- Sửa modal editRoomModal -->
            <div class="modal" id="editRoomModal">
                <div class="modal-content">
                    <h3>Sửa phòng</h3>
                    <input type="hidden" id="editRoomId">
                    <label for="editRoomSoPhong">Số phòng:</label>
                    <input type="text" id="editRoomSoPhong" placeholder="Số phòng">
                    <label for="editRoomKhuTro">Khu trọ:</label>
                    <select id="editRoomKhuTro">
                        <?php
                        $khuTroStmt = $conn->query("SELECT id_khu_tro, dia_chi FROM khu_tro");
                        while ($khuTro = $khuTroStmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$khuTro['id_khu_tro']}'>{$khuTro['dia_chi']}</option>";
                        }
                        ?>
                    </select>
                    <label for="editRoomPrice">Giá (VND):</label>
                    <input type="number" id="editRoomPrice" placeholder="Giá phòng">
                    <label for="editRoomStatus">Trạng thái:</label>
                    <select id="editRoomStatus">
                        <option value="trong">Trống</option>
                        <option value="da_thue">Đã thuê</option>
                    </select>
                    <button onclick="updateRoom()">Lưu</button>
                    <button onclick="closeModal('editRoomModal')">Hủy</button>
                </div>
            </div>

            <!-- Sửa bảng phòng trọ -->
            <section id="phong_tro" class="admin-section">
                <h2>Quản lý phòng trọ</h2>
                <div class="table-controls">
                    <input type="text" id="search-phong_tro" placeholder="Tìm kiếm phòng trọ..."
                        onkeyup="searchPhongTro()">
                    <button onclick="refreshPhongTro()">Làm mới</button>
                    <button onclick="addPhongTro()">Thêm phòng trọ</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Số phòng</th>
                            <th>Khu trọ</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="roomTableBody">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="phong_tro-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Quản lý khách thuê -->
            <section id="khach_thue" class="admin-section">
                <h2>Quản lý khách thuê</h2>
                <div class="table-controls">
                    <input type="text" id="search-khach_thue" placeholder="Tìm kiếm khách thuê..."
                        onkeyup="searchKhachThue()">
                    <button onclick="refreshKhachThue()">Làm mới</button>
                    <button onclick="addKhachThue()">Thêm khách thuê</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>CCCD</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Ngày bắt đầu thuê</th>
                            <th>Phòng trọ</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="khach_thue-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="khach_thue-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Quản lý hóa đơn -->
            <section id="hoa_don" class="admin-section">
                <h2>Quản lý hóa đơn</h2>
                <div class="table-controls">
                    <input type="text" id="search-hoa_don" placeholder="Tìm kiếm hóa đơn..." onkeyup="searchHoaDon()">
                    <button onclick="loadRooms(1, '')">Làm mới</button>
                    <button onclick="addHoaDon()">Thêm hóa đơn</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Số hóa đơn</th>
                            <th>Khách thuê</th>
                            <th>Phòng trọ</th>
                            <th>Số tiền</th>
                            <th>Ngày thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="hoa_don-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="hoa_don-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>

            <!-- Quản lý hợp đồng -->
            <section id="hop_dong" class="admin-section">
                <h2>Quản lý hợp đồng</h2>
                <div class="table-controls">
                    <input type="text" id="search-hop_dong" placeholder="Tìm kiếm hợp đồng..."
                        onkeyup="searchHopDong()">
                    <button onclick="refreshHopDong()">Làm mới</button>
                    <button onclick="addHopDong()">Thêm hợp đồng</button>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Số hợp đồng</th>
                            <th>Khách thuê</th>
                            <th>Phòng trọ</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="hop_dong-table-body">
                        <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="hop_dong-pagination">
                    <!-- Phân trang sẽ được thêm bằng JavaScript -->
                </div>
            </section>
        </main>
    </div>

    <!-- Modal thêm khu trọ -->
    <div class="modal" id="addKhuVucModal">
        <div class="modal-content">
            <h3>Thêm khu trọ mới</h3>
            <label for="addKhuVucName">Tên khu trọ:</label>
            <input type="text" id="addKhuVucName" placeholder="Tên khu trọ">
            <label for="addKhuVucAddress">Địa chỉ:</label>
            <textarea id="addKhuVucAddress" placeholder="Địa chỉ"></textarea>
            <button onclick="saveNewKhuVuc()">Lưu</button>
            <button onclick="closeModal('addKhuVucModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal chỉnh sửa khu trọ -->
    <div class="modal" id="editKhuVucModal">
        <div class="modal-content">
            <h3>Sửa khu trọ</h3>
            <input type="hidden" id="editKhuVucId">
            <label for="editKhuVucAddress">Địa chỉ:</label>
            <textarea id="editKhuVucAddress" placeholder="Địa chỉ"></textarea>
            <label for="editKhuVucOverview">Tổng quan:</label>
            <textarea id="editKhuVucOverview" placeholder="Tổng quan"></textarea>
            <button onclick="updateKhuVuc()">Lưu</button>
            <button onclick="closeModal('editKhuVucModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm quy định chung -->
    <div class="modal" id="addLuuDienChungModal">
        <div class="modal-content">
            <h3>Thêm quy định chung mới</h3>
            <label for="addLuuDienChungKhuTro">Khu trọ:</label>
            <select id="addLuuDienChungKhuTro">
                <?php
                $khuTroStmt = $conn->query("SELECT id_khu_tro, dia_chi FROM khu_tro");
                while ($khuTro = $khuTroStmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$khuTro['id_khu_tro']}'>{$khuTro['dia_chi']}</option>";
                }
                ?>
            </select>
            <label for="addLuuDienChungLink">Link quy định:</label>
            <input type="url" id="addLuuDienChungLink" placeholder="Nhập URL quy định" required>
            <button onclick="saveNewLuuDienChung()">Lưu</button>
            <button onclick="closeModal('addLuuDienChungModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal chỉnh sửa quy định -->
    <div class="modal" id="editQuyDinhModal">
        <div class="modal-content">
            <h3>Sửa quy định</h3>
            <input type="hidden" id="editQuyDinhId">
            <label for="editQuyDinhKhuTro">Khu trọ:</label>
            <select id="editQuyDinhKhuTro">
                <?php
                $khuTroStmt = $conn->query("SELECT id_khu_tro, dia_chi FROM khu_tro");
                while ($khuTro = $khuTroStmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$khuTro['id_khu_tro']}'>{$khuTro['dia_chi']}</option>";
                }
                ?>
            </select>
            <label for="editQuyDinhLink">Link quy định:</label>
            <input type="url" id="editQuyDinhLink" placeholder="Nhập URL quy định" required>
            <button onclick="updateQuyDinh()">Lưu</button>
            <button onclick="closeModal('editQuyDinhModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm tài khoản -->
    <div class="modal" id="addTaiKhoanModal">
        <div class="modal-content">
            <h3>Thêm tài khoản mới</h3>
            <label for="addTaiKhoanUsername">Tên đăng nhập:</label>
            <input type="text" id="addTaiKhoanUsername" placeholder="Tên đăng nhập">
            <label for="addTaiKhoanEmail">Email:</label>
            <input type="email" id="addTaiKhoanEmail" placeholder="Email">
            <label for="addTaiKhoanRole">Vai trò:</label>
            <select id="addTaiKhoanRole">
                <option value="chu_tro">Chủ trọ</option>
                <option value="khach_thue">Khách thuê</option>
            </select>
            <button onclick="saveNewTaiKhoan()">Lưu</button>
            <button onclick="closeModal('addTaiKhoanModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm phòng trọ -->
    <div class="modal" id="addPhongTroModal">
        <div class="modal-content">
            <h3>Thêm phòng trọ mới</h3>
            <label for="addPhongTroName">Tên phòng:</label>
            <input type="text" id="addPhongTroName" placeholder="Tên phòng">
            <label for="addPhongTroKhuVuc">Khu trọ:</label>
            <select id="addPhongTroKhuVuc">
                <option value="">Chọn khu trọ</option>
                <!-- Danh sách sẽ được tải bằng JavaScript -->
            </select>
            <button onclick="loadKhuTro()">Tải danh sách khu trọ</button>
            <label for="addPhongTroPrice">Giá:</label>
            <input type="number" id="addPhongTroPrice" placeholder="Giá phòng">
            <label for="addPhongTroStatus">Trạng thái:</label>
            <select id="addPhongTroStatus">
                <option value="trong">Trống</option>
                <option value="da_thue">Đã thuê</option>
            </select>
            <button onclick="saveNewPhongTro()">Lưu</button>
            <button onclick="closeModal('addPhongTroModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm khách thuê -->
    <div class="modal" id="addKhachThueModal">
        <div class="modal-content">
            <h3>Thêm khách thuê mới</h3>
            <label for="addKhachThueName">Họ tên:</label>
            <input type="text" id="addKhachThueName" placeholder="Họ tên" required>
            <label for="addKhachThueEmail">Email:</label>
            <input type="email" id="addKhachThueEmail" placeholder="Email">
            <label for="addKhachThuePhone">Số điện thoại:</label>
            <input type="text" id="addKhachThuePhone" placeholder="Số điện thoại" required>
            <label for="addKhachThueCCCD">Số CCCD:</label>
            <input type="text" id="addKhachThueCCCD" placeholder="Số CCCD" required>
            <label for="addKhachThuePhongTro">Phòng trọ:</label>
            <select id="addKhachThuePhongTro">
                <option value="">Chọn phòng trọ</option>
            </select>
            <label for="addKhachThueNgaySinh">Ngày sinh:</label>
            <input type="date" id="addKhachThueNgaySinh" placeholder="Ngày sinh">
            <label for="addKhachThueGioiTinh">Giới tính:</label>
            <select id="addKhachThueGioiTinh">
                <option value="">Chọn giới tính</option>
                <option value="nam">Nam</option>
                <option value="nu">Nữ</option>
                <option value="khac">Khác</option>
            </select>
            <button onclick="loadPhongTro()">Tải danh sách phòng trọ</button>
            <button onclick="saveNewKhachThue()">Lưu</button>
            <button onclick="closeModal('addKhachThueModal')">Hủy</button>
        </div>
    </div>

    <div class="modal" id="editTenantModal">
        <div class="modal-content">
            <h3>Sửa thông tin khách thuê</h3>
            <input type="hidden" id="editTenantId" value="">
            <label for="editTenantName">Họ tên:</label>
            <input type="text" id="editTenantName" placeholder="Họ tên">
            <label for="editTenantPhone">Số điện thoại:</label>
            <input type="text" id="editTenantPhone" placeholder="Số điện thoại">
            <label for="editTenantEmail">Email:</label>
            <input type="email" id="editTenantEmail" placeholder="Email">
            <label for="editTenantCCCD">Số CCCD:</label>
            <input type="text" id="editTenantCCCD" placeholder="Số CCCD">
            <label for="editTenantPhongTro">Phòng trọ:</label>
            <select id="editTenantPhongTro">
                <?php
                $phongTroStmt = $conn->query("SELECT id_phong, so_phong FROM phong_tro");
                while ($phong = $phongTroStmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$phong['id_phong']}'>{$phong['so_phong']}</option>";
                }
                ?>
            </select>
            <label for="editTenantNgaySinh">Ngày sinh:</label>
            <input type="date" id="editTenantNgaySinh" placeholder="Ngày sinh">
            <label for="editTenantGioiTinh">Giới tính:</label>
            <select id="editTenantGioiTinh">
                <option value="">Chọn giới tính</option>
                <option value="nam">Nam</option>
                <option value="nu">Nữ</option>
                <option value="khac">Khác</option>
            </select>
            <button onclick="updateTenant()">Lưu</button>
            <button onclick="closeModal('editTenantModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm hóa đơn -->
    <div class="modal" id="addHoaDonModal">
        <div class="modal-content">
            <h3>Thêm hóa đơn mới</h3>
            <label for="addHoaDonTenant">Khách thuê:</label>
            <select id="addHoaDonTenant" required></select>
            <label for="addHoaDonRoom">Phòng trọ:</label>
            <select id="addHoaDonRoom" required></select>
            <label for="addHoaDonThangApDung">Tháng áp dụng:</label>
            <input type="month" id="addHoaDonThangApDung" required>
            <label for="addHoaDonTienPhong">Tiền phòng (VNĐ):</label>
            <input type="number" id="addHoaDonTienPhong" placeholder="Tiền phòng" min="0" required>
            <label for="addHoaDonTienDien">Tiền điện (VNĐ):</label>
            <input type="number" id="addHoaDonTienDien" placeholder="Tiền điện" min="0" value="0">
            <label for="addHoaDonTienNuoc">Tiền nước (VNĐ):</label>
            <input type="number" id="addHoaDonTienNuoc" placeholder="Tiền nước" min="0" value="0">
            <label for="addHoaDonTienInternet">Tiền internet (VNĐ):</label>
            <input type="number" id="addHoaDonTienInternet" placeholder="Tiền internet" min="0" value="0">
            <label for="addHoaDonTienRac">Tiền rác (VNĐ):</label>
            <input type="number" id="addHoaDonTienRac" placeholder="Tiền rác" min="0" value="0">
            <label for="addHoaDonPhiKhac">Phí khác (VNĐ):</label>
            <input type="number" id="addHoaDonPhiKhac" placeholder="Phí khác" min="0" value="0">
            <label for="addHoaDonTongTien">Tổng tiền (VNĐ):</label>
            <input type="number" id="addHoaDonTongTien" readonly>
            <label for="addHoaDonDate">Ngày thanh toán:</label>
            <input type="date" id="addHoaDonDate">
            <label for="addHoaDonStatus">Trạng thái:</label>
            <select id="addHoaDonStatus">
                <option value="chua_thanh_toan">Chưa thanh toán</option>
                <option value="da_thanh_toan">Đã thanh toán</option>
            </select>
            <button onclick="saveNewHoaDon()">Lưu</button>
            <button onclick="closeModal('addHoaDonModal')">Hủy</button>
        </div>
    </div>

    <div class="modal" id="editHoaDonModal">
        <div class="modal-content">
            <h3>Sửa hóa đơn</h3>
            <input type="hidden" id="editHoaDonId">
            <label for="editHoaDonTenant">Khách thuê:</label>
            <select id="editHoaDonTenant">
                <!-- Populate dynamically with JavaScript -->
            </select>
            <label for="editHoaDonRoom">Phòng trọ:</label>
            <select id="editHoaDonRoom">
                <!-- Populate dynamically with JavaScript -->
            </select>
            <label for="editHoaDonThangApDung">Tháng áp dụng:</label>
            <input type="month" id="editHoaDonThangApDung">
            <label for="editHoaDonTienPhong">Tiền phòng (VNĐ):</label>
            <input type="number" id="editHoaDonTienPhong" min="0">
            <label for="editHoaDonTienDien">Tiền điện (VNĐ):</label>
            <input type="number" id="editHoaDonTienDien" min="0" value="0">
            <label for="editHoaDonTienNuoc">Tiền nước (VNĐ):</label>
            <input type="number" id="editHoaDonTienNuoc" min="0" value="0">
            <label for="editHoaDonTienInternet">Tiền internet (VNĐ):</label>
            <input type="number" id="editHoaDonTienInternet" min="0" value="0">
            <label for="editHoaDonTienRac">Tiền Rác (VNĐ):</label>
            <input type="number" id="editHoaDonTienRac" min="0" value="0">
            <label for="editHoaDonPhiKhac">Phí khác (VNĐ):</label>
            <input type="number" id="editHoaDonPhiKhac" min="0" value="0">
            <label for="editHoaDonTongTien">Tổng tiền (VNĐ):</label>
            <input type="text" id="editHoaDonTongTien" readonly>
            <label for="editHoaDonDate">Ngày thanh toán:</label>
            <input type="date" id="editHoaDonDate">
            <label for="editHoaDonStatus">Trạng thái:</label>
            <select id="editHoaDonStatus">
                <option value="chua_thanh_toan">Chưa thanh toán</option>
                <option value="da_thanh_toan">Đã thanh toán</option>
            </select>
            <button onclick="updatePayment()">Lưu</button>
            <button onclick="closeModal('editHoaDonModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal thêm hợp đồng -->
    <div id="addHopDongModal" class="modal">
        <div class="modal-content">
            <h2>Thêm Hợp Đồng</h2>
            <label>Khách Thuê:</label>
            <select id="addHopDongKhachThue">
                <option value="">Chọn khách thuê</option>
            </select>
            <label>Phòng Trọ:</label>
            <select id="addHopDongPhongTro">
                <option value="">Chọn phòng trọ</option>
            </select>
            <label>Ngày Bắt Đầu:</label>
            <input type="date" id="addHopDongStartDate">
            <label>Ngày Kết Thúc:</label>
            <input type="date" id="addHopDongEndDate">
            <button onclick="saveNewHopDong()">Lưu</button>
            <button onclick="closeModal('addHopDongModal')">Hủy</button>
        </div>
    </div>

    <!-- Modal chỉnh sửa hợp đồng -->
    <div class="modal" id="editContractModal">
        <div class="modal-content">
            <h3>Sửa hợp đồng</h3>
            <input type="hidden" id="editContractId">
            <label for="editContractTenant">Khách thuê:</label>
            <select id="editContractTenant"></select>
            <label for="editContractRoom">Phòng trọ:</label>
            <select id="editContractRoom"></select>
            <label for="editContractStartDate">Ngày bắt đầu:</label>
            <input type="date" id="editContractStartDate">
            <label for="editContractEndDate">Ngày kết thúc:</label>
            <input type="date" id="editContractEndDate">
            <button onclick="updateContract()">Lưu</button>
            <button onclick="closeModal('editContractModal')">Hủy</button>
        </div>
    </div>

    <!-- Script -->
    <script src="admin_handler.js"></script>
    <script src="admin.js"></script>
</body>

</html>