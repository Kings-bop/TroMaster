<?php
session_start(); // Khởi tạo session
require_once 'connect.php'; // Kết nối cơ sở dữ liệu
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Phòng Trọ - Khách Thuê</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Giữ nguyên các style hiện tại của bạn */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            transform: translate(0, 0);
        }

        .modal.active {
            display: flex !important;
        }

        .modal-content h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .modal-content table {
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .modal-content th,
        .modal-content td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .modal-content th {
            background-color: #4CAF50;
            color: white;
        }

        .modal-content .form-group {
            margin-bottom: 15px;
            display: none;
        }

        .modal-content .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .modal-content .form-group input,
        .modal-content .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .modal-content .buttons {
            text-align: right;
        }

        .modal-content .buttons button {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content .buttons .edit-btn,
        .modal-content .buttons .save-btn {
            background-color: #9E9E9E;
            color: white;
        }

        .modal-content .buttons .change-password-btn {
            background-color: #9E9E9E;
            color: white;
        }

        .modal-content .buttons .history-btn {
            background-color: #9E9E9E;
            color: white;
        }

        .modal-content .buttons .close-btn {
            background-color: #757575;
            color: white;
        }

        .invoice-modal {
            display: none !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .invoice-modal.active {
            display: flex !important;
        }

        .invoice-modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
            transform: translateY(0);
        }

        .invoice-modal-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-modal-content th,
        .invoice-modal-content td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .invoice-modal-content th {
            background-color: #4CAF50;
            color: white;
        }

        .invoice-modal-content .close-btn {
            background-color: #757575;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-row label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .form-row input,
        .form-row select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-row input:focus,
        .form-row select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        #changePassword #mainButtons {
            display: none;
        }

        #changePassword .buttons {
            margin-top: 20px;
            text-align: right;
        }

        #mainButtons {
            margin-top: 20px;
            text-align: right;
        }

        .room-actions {
            margin-top: 10px;
        }

        .btn-rent,
        .btn-detail {
            display: inline-block;
            padding: 8px 15px;
            margin-right: 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }

        .btn-rent {
            background-color: #4CAF50;
        }

        .btn-detail {
            background-color: #2196F3;
        }

        .btn-rent:hover,
        .btn-detail:hover {
            opacity: 0.8;
        }

        .status-rented {
            color: #ff0000;
            font-weight: bold;
        }

        .status-available {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">Quản Lý Phòng Trọ Công ty TroMaster</div>
        <div class="nav">
            <a href="#" onclick="openPersonalModal(event)">Thông tin cá nhân</a>
            <a href="#" onclick="openRoomsModal(event)">Xem phòng</a>
           <a href="../trang_chu_admin/quy_dinh.html" target="_blank">Xem quy định chung</a>
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $sql = "SELECT tk.ten_dang_nhap, kt.ho_ten 
                    FROM tai_khoan tk 
                    LEFT JOIN khach_thue kt ON tk.id_khach_thue = kt.id_khach_thue 
                    WHERE tk.id_tai_khoan = :id_tai_khoan";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $displayName = htmlspecialchars(trim($user['ho_ten'] ?? $user['ten_dang_nhap'] ?? 'Khách'));
                echo '<li>Xin chào, ' . $displayName . ' ';
                echo '<a href="#" onclick="confirmLogout(event)" class="logout">Đăng xuất</a></li>';
            } else {
                echo '<li><a href="login.php" class="login">Đăng nhập</a></li>';
                echo '<li><a href="register.php" class="login">Đăng ký</a></li>';
            }
            ?>
        </div>
    </div>

            <!-- Modal Quy định chung -->
    <div id="rulesModal" class="modal">
        <div class="modal-content">
            <h2>Quy định chung</h2>
            <?php
            try {
                $sql = "SELECT q.noi_dung, k.dia_chi 
                        FROM quy_dinh q 
                        JOIN khu_tro k ON q.id_khu_tro = k.id_khu_tro";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($rules) {
                    echo '<table class="rules-table">
                            <thead>
                                <tr>
                                    <th>Địa chỉ</th>
                                    <th>Nội dung</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $index = 0;
                    foreach ($rules as $rule) {
                        echo '<tr onclick="openDetailModal(' . $index . ', \'' . htmlspecialchars(addslashes($rule['noi_dung']), ENT_QUOTES) . '\', \'' . htmlspecialchars($rule['dia_chi'], ENT_QUOTES) . '\')" style="cursor: pointer;">
                                <td>' . htmlspecialchars($rule['dia_chi']) . '</td>
                                <td>' . htmlspecialchars(substr($rule['noi_dung'], 0, 50)) . (strlen($rule['noi_dung']) > 50 ? '...' : '') . '</td>
                            </tr>';
                        $index++;
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<p>Không có quy định nào được tìm thấy.</p>';
                }
            } catch (PDOException $e) {
                echo '<p>Lỗi khi tải quy định: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
            <div class="buttons">
                <button class="close-btn" onclick="closeRulesModal()">Đóng</button>
            </div>
        </div>
    </div>


    <script>
        // Định nghĩa $rules ở đầu file PHP
        <?php
        try {
            $sql = "SELECT q.noi_dung, k.dia_chi 
                    FROM quy_dinh q 
                    JOIN khu_tro k ON q.id_khu_tro = k.id_khu_tro";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $rules = [];
        }
        ?>
        let rulesData = <?php echo json_encode($rules); ?> || [];

        function openDetailModal(index, noiDung, diaChi) {
            const detailContent = document.getElementById('detailContent');
            if (noiDung && diaChi) {
                detailContent.textContent = diaChi + ': ' + noiDung;
                document.getElementById('detailModal').style.display = 'flex';
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        function closeRulesModal() {
            document.getElementById('rulesModal').style.display = 'none';
        }

        function openRulesModal(event) {
            event.preventDefault();
            const modal = document.getElementById('rulesModal');
            modal.style.display = 'flex';
        }
    </script>

    <div class="carousel">
    <div class="slides">
        <div class="slide"><img src="images/download (1).jpg" alt="Banner 1"></div>
        <div class="slide"><img src="images/keo.jpg" alt="Banner 2"></div>
        <div class="slide"><img src="images/download.jpg" alt="Banner 3"></div>
    </div>
    <button class="prev">❮</button>
    <button class="next">❯</button>
</div>

    <div class="search-bar">
        <select id="statusFilter">
            <option value="tat-ca">Tình trạng: Tất cả</option>
            <option value="trong">Phòng trống</option>
            <option value="da-thue">Phòng đã thuê</option>
        </select>
        <select>
            <option value="">Khoảng giá</option>
            <option value="0-2000000">Dưới 2 triệu</option>
            <option value="2000000-5000000">2 - 5 triệu</option>
            <option value="5000000-10000000">5 - 10 triệu</option>
        </select>
        <select>
            <option value="0">Diện tích</option>
            <option value="0-20">Dưới 20m²</option>
            <option value="20-40">20 - 40m²</option>
            <option value="40-100">40 - 100m²</option>
        </select>
        <input type="text" placeholder="Tìm kiếm theo tên phòng hoặc địa chỉ...">
        <button onclick="filterRooms()">Tìm kiếm</button>
    </div>
    <div class="content">
        <div class="city-highlights">
            <h2>Khu vực nổi bật theo thành phố</h2>
            <div class="city-list">
                <div class="city-card">
                    <h3>Hà Nội</h3>
                    <ul>
                        <li><a href="#ha-dong" onclick="scrollToSection('ha-dong')">Hà Đông</a></li>
                        <li><a href="#cau-giay" onclick="scrollToSection('cau-giay')">Cầu Giấy</a></li>
                        <li><a href="#dong-da" onclick="scrollToSection('dong-da')">Đống Đa</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="hot-rooms">
            <h2>Phòng Hot - Nổi Bật</h2>
            <div class="alo">
                <?php
                try {
                    $sql = "SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, k.dia_chi 
                        FROM phong_tro p 
                        JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro 
                        WHERE p.trang_thai IN ('trong', 'da-thue') 
                        ORDER BY p.gia_thue ASC 
                        LIMIT 3";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $hot_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($hot_rooms) {
                        foreach ($hot_rooms as $room) {
                            if (!empty($room['id_phong']) && is_numeric($room['id_phong'])) {
                                $image = !empty($room['hinh_anh']) ? htmlspecialchars($room['hinh_anh']) : 'https://via.placeholder.com/150x100?text=Phòng+' . htmlspecialchars($room['so_phong']);
                                $price = number_format($room['gia_thue'], 0, ',', '.');
                                $area = number_format($room['dien_tich'], 1);
                                $address = htmlspecialchars($room['dia_chi']);
                                $posted_time = date('Y-m-d H:i:s', strtotime('-1 day'));
                                $status_class = $room['trang_thai'] === 'trong' ? 'status-available' : 'status-rented';
                                $status_text = $room['trang_thai'] === 'trong' ? 'Còn trống' : 'Đã thuê';

                                echo '<div class="hot-room-card">';
                                echo '<a href="chi_tiet_phong.php?id_phong=' . htmlspecialchars($room['id_phong']) . '">';
                                echo '<img src="' . $image . '" alt="Phòng ' . htmlspecialchars($room['so_phong']) . '">';
                                echo '</a>';
                                echo '<div class="hot-room-info">';
                                echo '<div class="rating">★★★★★ <span>CHO Ở GHÉP ĐÔI NAM NỮ</span></div>';
                                echo '<div class="status"><span class="' . $status_class . '">' . $status_text . '</span></div>';
                                echo '<div class="room-details">';
                                echo '<span class="area">Diện tích: ' . $area . 'm²</span><br>';
                                echo '<span class="address">Địa chỉ: ' . $address . '</span><br>';
                                echo '</div>';
                                echo '<div class="posted-time">Cập nhật: ' . $posted_time . '</div>';
                                echo '<div class="room-actions">';
                                if ($room['trang_thai'] === 'trong') {
                                    echo '<a href="dang_ky_thue.php?id_phong=' . htmlspecialchars($room['id_phong']) . '" class="btn-rent">Đăng ký thuê</a>';
                                }
                                echo '<a href="chi_tiet_phong.php?id_phong=' . htmlspecialchars($room['id_phong']) . '" class="btn-detail">Xem chi tiết</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p>Phòng không hợp lệ: ID không tồn tại.</p>';
                            }
                        }
                    } else {
                        echo '<p>Không có phòng hot nào hiện tại.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p>Lỗi khi tải phòng hot: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>
            </div>
        </div>

        <div class="house">
            <div id="roomListSection">

            </div>
        </div>

        <div class="about-us">
            <h2>Giới Thiệu Về Chúng Tôi</h2>
            <p class="intro">
                TroMaster là nền tảng quản lý nhà trọ tiên tiến, được phát triển với mục tiêu mang lại trải nghiệm tiện
                lợi,
                minh bạch và hiện đại cho cả chủ nhà lẫn người thuê trọ. Với sứ mệnh số hóa ngành bất động sản cho thuê,
                chúng tôi không chỉ cung cấp một công cụ quản lý mạnh mẽ mà còn xây dựng một cộng đồng thuê trọ văn minh
                và
                đáng tin cậy.
            </p>

            <div class="reasons">
                <h2>Tại sao nên chọn TroMaster?</h2>
                <ul>
                    <li><strong>🔍 Tìm kiếm dễ dàng:</strong> Bộ lọc thông minh giúp bạn tìm được nhà trọ theo nhu cầu
                        cụ
                        thể như khu vực, giá cả, tiện nghi, và diện tích.</li>
                    <li><strong>📲 Quản lý hiện đại:</strong> Chủ trọ có thể dễ dàng quản lý danh sách phòng, thông tin
                        khách thuê, hợp đồng, và tình trạng thanh toán ngay trên điện thoại hoặc máy tính.</li>
                    <li><strong>🔔 Thông báo tự động:</strong> Hệ thống tự động nhắc nhở hạn thanh toán, cập nhật tình
                        trạng
                        phòng trống và thông tin mới cho người dùng.</li>
                    <li><strong>🔐 Bảo mật và minh bạch:</strong> Dữ liệu người dùng được mã hóa và lưu trữ an toàn. Mọi
                        giao dịch đều được ghi nhận rõ ràng, minh bạch.</li>
                    <li><strong>💬 Hỗ trợ 24/7:</strong> Đội ngũ hỗ trợ luôn sẵn sàng giải đáp mọi thắc mắc, hỗ trợ xử
                        lý sự
                        cố kỹ thuật nhanh chóng.</li>
                    <li><strong>🌐 Giao diện thân thiện:</strong> Thiết kế đơn giản, hiện đại, dễ sử dụng với mọi đối
                        tượng,
                        từ người trẻ đến người lớn tuổi.</li>
                </ul>
            </div>

            <div class="reasons">
                <h2>Sứ mệnh của chúng tôi</h2>
                <p>
                    TroMaster không chỉ là một công cụ quản lý nhà trọ, mà còn là cầu nối giúp hình thành mối quan hệ
                    tốt
                    đẹp giữa chủ nhà và người thuê. Chúng tôi tin rằng sự minh bạch, tiện lợi và công bằng sẽ tạo nên
                    một
                    thị trường cho thuê lành mạnh, văn minh và phát triển bền vững trong tương lai.
                </p>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>THÔNG TIN</h3>
                    <p><a href="#">Câu hỏi thường gặp</a></p>
                    <p><a href="#">Điều khoản sử dụng</a></p>
                    <p><a href="#">Chính sách bảo mật</a></p>
                </div>
                <div class="footer-section">
                    <h3>THÔNG TIN CHĂM SÓC KHÁCH HÀNG</h3>
                    <p>Hotline: 1900 5678</p>
                    <p>Email: support@TroMaster.vn</p>
                    <p>Giờ làm việc: 8:00 - 22:00</p>
                </div>
                <div class="footer-section">
                    <h3>KẾT NỐI VỚI CHÚNG TÔI</h3>
                    <p><a href="">Facebook</a></p>
                    <p><a href="">Instagram</a></p>
                    <p><a href="">YouTube</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© TroMaster Website Quản Lý Phòng Trọ Hàng Đầu Việt Nam.</p>
            </div>
        </div>

        <div id="personalInfoModal" class="modal">
            <div class="modal-content">
                <h2>Thông tin cá nhân</h2>
                <div id="viewInfo">
                    <?php
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id_tai_khoan'])) {
                        try {
                            $sql_tk = "SELECT id_khach_thue FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
                            $stmt_tk = $conn->prepare($sql_tk);
                            $stmt_tk->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
                            $stmt_tk->execute();
                            $tk = $stmt_tk->fetch(PDO::FETCH_ASSOC);
                            $id_khach_thue = $tk['id_khach_thue'];

                            if ($id_khach_thue) {
                                $sql = "SELECT ho_ten, ngay_sinh, so_dien_thoai, so_cccd, gioi_tinh, email 
                                    FROM khach_thue WHERE id_khach_thue = :id_khach_thue";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
                                $stmt->execute();
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($user) {
                                    echo '<table>';
                                    echo '<tr><th>Họ và tên:</th><td>' . htmlspecialchars($user['ho_ten'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '<tr><th>Ngày sinh:</th><td>' . htmlspecialchars($user['ngay_sinh'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '<tr><th>Số điện thoại:</th><td>' . htmlspecialchars($user['so_dien_thoai'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '<tr><th>Số CCCD:</th><td>' . htmlspecialchars($user['so_cccd'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '<tr><th>Giới tính:</th><td>' . htmlspecialchars($user['gioi_tinh'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '<tr><th>Email:</th><td>' . htmlspecialchars($user['email'] ?? 'Chưa cập nhật') . '</td></tr>';
                                    echo '</table>';
                                } else {
                                    echo '<p class="no-data">Không tìm thấy thông tin người dùng.</p>';
                                }
                            } else {
                                echo '<p class="no-data">Tài khoản không liên kết với khách thuê.</p>';
                            }
                        } catch (PDOException $e) {
                            echo '<p class="no-data">Lỗi khi tải thông tin cá nhân: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        }
                    } else {
                        echo '<p class="no-data">Vui lòng đăng nhập để xem thông tin cá nhân.</p>';
                    }
                    ?>
                </div>
                <div id="editInfo" class="form-group" style="display: none;">
                    <?php
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id_tai_khoan'])) {
                        try {
                            $sql_tk = "SELECT id_khach_thue FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
                            $stmt_tk = $conn->prepare($sql_tk);
                            $stmt_tk->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
                            $stmt_tk->execute();
                            $tk = $stmt_tk->fetch(PDO::FETCH_ASSOC);
                            $id_khach_thue = $tk['id_khach_thue'];

                            if ($id_khach_thue) {
                                $sql = "SELECT ho_ten, ngay_sinh, so_dien_thoai, so_cccd, gioi_tinh, email 
                                    FROM khach_thue WHERE id_khach_thue = :id_khach_thue";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
                                $stmt->execute();
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($user) {
                                    echo '<div class="form-row"><label for="ho_ten">Họ và tên:</label><input type="text" id="ho_ten" value="' . htmlspecialchars($user['ho_ten'] ?? '') . '"></div>';
                                    echo '<div class="form-row"><label for="ngay_sinh">Ngày sinh:</label><input type="date" id="ngay_sinh" value="' . htmlspecialchars($user['ngay_sinh'] ?? '') . '"></div>';
                                    echo '<div class="form-row"><label for="so_dien_thoai">Số điện thoại:</label><input type="text" id="so_dien_thoai" value="' . htmlspecialchars($user['so_dien_thoai'] ?? '') . '"></div>';
                                    echo '<div class="form-row"><label for="so_cccd">Số CCCD:</label><input type="text" id="so_cccd" value="' . htmlspecialchars($user['so_cccd'] ?? '') . '"></div>';
                                    echo '<div class="form-row"><label for="gioi_tinh">Giới tính:</label><select id="gioi_tinh"><option value="nam" ' . ($user['gioi_tinh'] == 'nam' ? 'selected' : '') . '>Nam</option><option value="nu" ' . ($user['gioi_tinh'] == 'nu' ? 'selected' : '') . '>Nữ</option><option value="khac" ' . ($user['gioi_tinh'] == 'khac' ? 'selected' : '') . '>Khác</option></select></div>';
                                    echo '<div class="form-row"><label for="email">Email:</label><input type="email" id="email" value="' . htmlspecialchars($user['email'] ?? '') . '"></div>';
                                }
                            }
                        } catch (PDOException $e) {
                            echo '<p class="no-data">Lỗi khi tải thông tin để chỉnh sửa: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        }
                    }
                    ?>
                </div>
                <div id="changePassword" style="display: none;">
                    <h2>Đổi mật khẩu</h2>
                    <div class="form-row"><label for="oldPassword">Mật khẩu cũ:</label><input type="password"
                            id="oldPassword"></div>
                    <div class="form-row"><label for="newPassword">Mật khẩu mới:</label><input type="password"
                            id="newPassword"></div>
                    <div class="form-row"><label for="confirmPassword">Xác nhận mật khẩu mới:</label><input
                            type="password" id="confirmPassword"></div>
                    <div class="buttons">
                        <button class="save-btn" onclick="changePassword()">Lưu mật khẩu</button>
                        <button class="close-btn" onclick="showViewInfo()">Quay lại</button>
                    </div>
                </div>
                <div class="buttons" id="mainButtons">
                    <button class="edit-btn" id="editBtn" onclick="toggleEditMode(true)">Chỉnh sửa thông tin</button>
                    <button class="save-btn" id="saveBtn" style="display: none;" onclick="saveChanges()">Lưu thay
                        đổi</button>
                    <button class="change-password-btn" onclick="showChangePassword()">Đổi mật khẩu</button>
                    <button class="history-btn" onclick="openInvoiceModal(event)">Xem hóa đơn</button>
                    <button class="close-btn" onclick="closePersonalModal()">Đóng</button>
                </div>
            </div>
        </div>

        <!-- Modal Lịch sử giao dịch -->
        <div id="invoiceModal" class="invoice-modal">
            <div class="invoice-modal-content">
                <h2>Lịch sử giao dịch</h2>
                <button class="close-btn" onclick="closeInvoiceModal()">Đóng</button>
                <?php
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id_tai_khoan'])) {
                    try {
                        $sql_tk = "SELECT id_khach_thue FROM tai_khoan WHERE id_tai_khoan = :id_tai_khoan";
                        $stmt_tk = $conn->prepare($sql_tk);
                        $stmt_tk->bindParam(':id_tai_khoan', $_SESSION['id_tai_khoan'], PDO::PARAM_INT);
                        $stmt_tk->execute();
                        $tk = $stmt_tk->fetch(PDO::FETCH_ASSOC);
                        $id_khach_thue = $tk['id_khach_thue'];

                        if ($id_khach_thue) {
                            $sql = "SELECT id_hoa_don, thang_ap_dung, tien_phong, tien_dien, tien_nuoc, tien_internet, phi_khac, tong_tien, trang_thai_thanh_toan, ngay_thanh_toan 
                                FROM hoa_don WHERE id_khach_thue = :id_khach_thue ORDER BY ngay_tao DESC";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':id_khach_thue', $id_khach_thue, PDO::PARAM_INT);
                            $stmt->execute();
                            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($invoices) {
                                echo '<table>';
                                echo '<tr><th>Mã hóa đơn</th><th>Tháng áp dụng</th><th>Phòng</th><th>Điện</th><th>Nước</th><th>Internet</th><th>Phí khác</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày thanh toán</th></tr>';
                                foreach ($invoices as $invoice) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($invoice['id_hoa_don']) . '</td>';
                                    echo '<td>' . htmlspecialchars($invoice['thang_ap_dung']) . '</td>';
                                    echo '<td>' . number_format($invoice['tien_phong'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . number_format($invoice['tien_dien'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . number_format($invoice['tien_nuoc'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . number_format($invoice['tien_internet'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . number_format($invoice['phi_khac'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . number_format($invoice['tong_tien'], 0, ',', '.') . ' đ</td>';
                                    echo '<td>' . htmlspecialchars($invoice['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán') . '</td>';
                                    echo '<td>' . htmlspecialchars($invoice['ngay_thanh_toan'] ?? 'Chưa thanh toán') . '</td>';
                                    echo '</tr>';
                                }
                                echo '</table>';
                            } else {
                                echo '<p class="no-data">Không có hóa đơn nào.</p>';
                            }
                        } else {
                            echo '<p class="no-data">Tài khoản không liên kết với khách thuê.</p>';
                        }
                    } catch (PDOException $e) {
                        echo '<p class="no-data">Lỗi khi tải lịch sử giao dịch: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }
                } else {
                    echo '<p class="no-data">Vui lòng đăng nhập để xem lịch sử giao dịch.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Modal Xem phòng -->
        <div id="roomsModal" class="modal">
            <div class="modal-content">
                <h2>Danh sách phòng</h2>
                <div id="viewRooms">
                    <?php
                    try {
                        $sql = "SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, k.dia_chi 
                            FROM phong_tro p 
                            JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro 
                            WHERE p.trang_thai = 'trong'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($rooms) {
                            echo '<table>';
                            echo '<tr><th>Số phòng</th><th>Diện tích</th><th>Giá thuê</th><th>Trạng thái</th><th>Địa chỉ</th><th>Hành động</th></tr>';
                            foreach ($rooms as $room) {
                                $price = number_format($room['gia_thue'], 0, ',', '.');
                                $area = number_format($room['dien_tich'], 1);
                                $status_text = $room['trang_thai'] === 'trong' ? 'Còn trống' : 'Đã thuê';
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($room['so_phong']) . '</td>';
                                echo '<td>' . $area . ' m²</td>';
                                echo '<td>' . $price . ' đ</td>';
                                echo '<td>' . $status_text . '</td>';
                                echo '<td>' . htmlspecialchars($room['dia_chi']) . '</td>';
                                echo '<td><a href="chi_tiet_phong.php?id_phong=' . htmlspecialchars($room['id_phong']) . '" class="btn-detail">Xem chi tiết</a></td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        } else {
                            echo '<p class="no-data">Không có phòng trống nào.</p>';
                        }
                    } catch (PDOException $e) {
                        echo '<p class="no-data">Lỗi khi tải danh sách phòng: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }
                    ?>
                </div>
                <div class="buttons">
                    <button class="close-btn" onclick="closeRoomsModal()">Đóng</button>
                </div>
            </div>
        </div>

        <script>
            const slides1 = document.querySelector('.carousel .slides');
            const slide1 = document.querySelectorAll('.carousel .slide');
            const prev1 = document.querySelector('.carousel .prev');
            const next1 = document.querySelector('.carousel .next');
            let currentIndex1 = 0;

            function showSlide1(index) {
                if (index >= slide1.length) currentIndex1 = 0;
                else if (index < 0) currentIndex1 = slide1.length - 1;
                else currentIndex1 = index;
                slides1.style.transform = `translateX(-${currentIndex1 * 100}%)`;
            }

            prev1.addEventListener('click', () => showSlide1(currentIndex1 - 1));
            next1.addEventListener('click', () => showSlide1(currentIndex1 + 1));
            setInterval(() => showSlide1(currentIndex1 + 1), 5000);

            function confirmLogout(event) {
                event.preventDefault();
                if (confirm("Bạn có muốn đăng xuất không?")) {
                    window.location.href = "logout.php";
                }
            }

            function openPersonalModal(event) {
                event.preventDefault();
                const modal = document.getElementById('personalInfoModal');
                modal.style.display = 'flex';
                document.getElementById('viewInfo').style.display = 'block';
                document.getElementById('editInfo').style.display = 'none';
                document.getElementById('changePassword').style.display = 'none';
                document.getElementById('editBtn').style.display = 'inline-block';
                document.getElementById('saveBtn').style.display = 'none';
                document.getElementById('mainButtons').style.display = 'block';
                loadPersonalInfo();
            }

            function editPersonalInfo() {
                document.getElementById('viewInfo').style.display = 'none';
                document.getElementById('editInfo').style.display = 'block';
                document.getElementById('editBtn').style.display = 'none';
                document.getElementById('saveBtn').style.display = 'inline-block';
            }

            function savePersonalInfo() {
                alert('Thông tin đã được lưu!');
                document.getElementById('viewInfo').style.display = 'block';
                document.getElementById('editInfo').style.display = 'none';
                document.getElementById('editBtn').style.display = 'inline-block';
                document.getElementById('saveBtn').style.display = 'none';
                loadPersonalInfo();
            }

            function closePersonalModal() {
                const editInfo = document.getElementById('editInfo');
                const changePassword = document.getElementById('changePassword');
                if (editInfo.style.display === 'block' || changePassword.style.display === 'block') {
                    document.getElementById('viewInfo').style.display = 'block';
                    editInfo.style.display = 'none';
                    changePassword.style.display = 'none';
                    document.getElementById('editBtn').style.display = 'inline-block';
                    document.getElementById('saveBtn').style.display = 'none';
                    document.getElementById('mainButtons').style.display = 'block';
                    loadPersonalInfo();
                } else {
                    document.getElementById('personalInfoModal').style.display = 'none';
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('personalInfoModal').style.display = 'none';
            });



            function openInvoiceModal(event) {
                event.preventDefault();
                const modal = document.getElementById('invoiceModal');
                modal.classList.add('active');
            }

            function closeInvoiceModal() {
                const modal = document.getElementById('invoiceModal');
                modal.classList.remove('active');
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('invoiceModal').classList.remove('active');
                document.getElementById('personalInfoModal').style.display = 'none';
            });

            function toggleEditMode(isEdit) {
                const viewInfo = document.getElementById('viewInfo');
                const editInfo = document.getElementById('editInfo');
                const editBtn = document.getElementById('editBtn');
                const saveBtn = document.getElementById('saveBtn');

                if (isEdit) {
                    viewInfo.style.display = 'none';
                    editInfo.style.display = 'block';
                    editBtn.style.display = 'none';
                    saveBtn.style.display = 'inline-block';
                } else {
                    viewInfo.style.display = 'block';
                    editInfo.style.display = 'none';
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    loadPersonalInfo();
                }
            }

            function saveChanges() {
                const id_khach_thue = <?php echo json_encode($id_khach_thue ?? 0); ?>;
                const ho_ten = document.getElementById('ho_ten').value;
                const ngay_sinh = document.getElementById('ngay_sinh').value;
                const so_dien_thoai = document.getElementById('so_dien_thoai').value;
                const so_cccd = document.getElementById('so_cccd').value;
                const gioi_tinh = document.getElementById('gioi_tinh').value;
                const email = document.getElementById('email').value;

                fetch('update_khach_thue.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_khach_thue=${id_khach_thue}&ho_ten=${encodeURIComponent(ho_ten)}&ngay_sinh=${encodeURIComponent(ngay_sinh)}&so_dien_thoai=${encodeURIComponent(so_dien_thoai)}&so_cccd=${encodeURIComponent(so_cccd)}&gioi_tinh=${encodeURIComponent(gioi_tinh)}&email=${encodeURIComponent(email)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Cập nhật thông tin thành công!');
                            toggleEditMode(false);
                        } else {
                            alert('Cập nhật thất bại: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Lỗi khi cập nhật: ' + error.message);
                    });
            }

            function loadPersonalInfo() {
                if (<?php echo json_encode(isset($_SESSION['id_tai_khoan']) && $_SESSION['loggedin']); ?>) {
                    fetch(`get_khach_thue.php?id_khach_thue=<?php echo $id_khach_thue ?? 0; ?>`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const user = data.data;
                                document.getElementById('viewInfo').innerHTML = `
                            <table>
                                <tr><th>Họ và tên:</th><td>${user.ho_ten || 'Chưa cập nhật'}</td></tr>
                                <tr><th>Ngày sinh:</th><td>${user.ngay_sinh || 'Chưa cập nhật'}</td></tr>
                                <tr><th>Số điện thoại:</th><td>${user.so_dien_thoai || 'Chưa cập nhật'}</td></tr>
                                <tr><th>Số CCCD:</th><td>${user.so_cccd || 'Chưa cập nhật'}</td></tr>
                                <tr><th>Giới tính:</th><td>${user.gioi_tinh || 'Chưa cập nhật'}</td></tr>
                                <tr><th>Email:</th><td>${user.email || 'Chưa cập nhật'}</td></tr>
                            </table>
                        `;
                            } else {
                                document.getElementById('viewInfo').innerHTML = '<p class="no-data">' + (data.message || 'Không tải được thông tin.') + '</p>';
                            }
                        })
                        .catch(error => {
                            document.getElementById('viewInfo').innerHTML = '<p class="no-data">Lỗi khi tải thông tin: ' + error.message + '</p>';
                        });
                } else {
                    document.getElementById('viewInfo').innerHTML = '<p class="no-data">Vui lòng đăng nhập để xem thông tin cá nhân.</p>';
                }
            }

            function showChangePassword() {
                document.getElementById('viewInfo').style.display = 'none';
                document.getElementById('editInfo').style.display = 'none';
                document.getElementById('changePassword').style.display = 'block';
                document.getElementById('mainButtons').style.display = 'none';
            }

            function showViewInfo() {
                document.getElementById('viewInfo').style.display = 'block';
                document.getElementById('editInfo').style.display = 'none';
                document.getElementById('changePassword').style.display = 'none';
                document.getElementById('mainButtons').style.display = 'block';
                loadPersonalInfo();
            }

            function changePassword() {
                const oldPassword = document.getElementById('oldPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (!oldPassword || !newPassword || !confirmPassword) {
                    alert('Vui lòng điền đầy đủ thông tin!');
                    return;
                }
                if (newPassword !== confirmPassword) {
                    alert('Mật khẩu mới và xác nhận mật khẩu không khớp!');
                    return;
                }
                if (newPassword.length < 6) {
                    alert('Mật khẩu mới phải có ít nhất 6 ký tự!');
                    return;
                }

                const sessionId = <?php echo json_encode($_SESSION['id_tai_khoan'] ?? 'Not set'); ?>;
                fetch('change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_tai_khoan=${encodeURIComponent(sessionId)}&oldPassword=${encodeURIComponent(oldPassword)}&newPassword=${encodeURIComponent(newPassword)}&confirmPassword=${encodeURIComponent(confirmPassword)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.success) {
                            alert('Đổi mật khẩu thành công!');
                            showViewInfo();
                        } else {
                            alert('Đổi mật khẩu thất bại: ' + (data ? data.message : 'Không nhận được phản hồi từ server'));
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi đổi mật khẩu:', error);
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    });
            }

            window.onclick = function (event) {
                const personalModal = document.getElementById('personalInfoModal');
                const invoiceModal = document.getElementById('invoiceModal');
                if (event.target == personalModal) {
                    closePersonalModal();
                }
                if (event.target == invoiceModal) {
                    invoiceModal.style.display = 'none';
                }
            }

            function scrollToSection(sectionId) {
                event.preventDefault();
                const section = document.getElementById(sectionId);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            }

            function filterRooms() {
                const status = document.getElementById('statusFilter').value;
                const priceRange = document.querySelector('.search-bar select:nth-child(2)').value;
                const areaRange = document.querySelector('.search-bar select:nth-child(3)').value;
                const searchTerm = document.querySelector('.search-bar input[type="text"]').value.toLowerCase();

                fetch('filter_rooms.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `status=${encodeURIComponent(status)}&priceRange=${encodeURIComponent(priceRange)}&areaRange=${encodeURIComponent(areaRange)}&searchTerm=${encodeURIComponent(searchTerm)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        const roomListSection = document.getElementById('roomListSection');
                        roomListSection.innerHTML = data.html; // Cập nhật nội dung
                        // Cuộn đến section cụ thể dựa trên từ khóa
                        if (searchTerm) {
                            const areaMap = {
                                'hà đông': 'ha-dong',
                                'cầu giấy': 'cau-giay',
                                'đống đa': 'dong-da'
                            };
                            const normalizedSearch = searchTerm.toLowerCase();
                            for (let key in areaMap) {
                                if (normalizedSearch.includes(key)) {
                                    const section = document.getElementById(areaMap[key]);
                                    if (section) {
                                        section.scrollIntoView({ behavior: 'smooth' });
                                        break;
                                    }
                                }
                            }
                        } else {
                            roomListSection.scrollIntoView({ behavior: 'smooth' }); // Cuộn đến section chung nếu không có từ khóa
                        }
                        if (!data.success) {
                            roomListSection.innerHTML = '<p>Không tìm thấy phòng nào.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi lọc phòng:', error);
                        document.getElementById('roomListSection').innerHTML = '<p>Có lỗi xảy ra, vui lòng thử lại.</p>';
                        document.getElementById('roomListSection').scrollIntoView({ behavior: 'smooth' });
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                const searchButton = document.querySelector('.search-bar button');
                searchButton.addEventListener('click', filterRooms);

                // Xóa sự kiện input để tránh cuộn khi nhập từng ký tự
                const searchInput = document.querySelector('.search-bar input[type="text"]');
                searchInput.removeEventListener('input', filterRooms); // Đảm bảo xóa nếu đã thêm trước đó

                // Khởi tạo lần đầu để hiển thị tất cả phòng
                filterRooms();
            });

            function openRulesModal(event) {
                event.preventDefault();
                const modal = document.getElementById('rulesModal');
                modal.style.display = 'flex';
            }

            function closeRulesModal() {
                const modal = document.getElementById('rulesModal');
                modal.style.display = 'none';
            }

            function openRoomsModal(event) {
                event.preventDefault();
                const modal = document.getElementById('roomsModal');
                modal.style.display = 'flex';
            }

            function closeRoomsModal() {
                const modal = document.getElementById('roomsModal');
                modal.style.display = 'none';
            }

            window.onclick = function (event) {
                const personalModal = document.getElementById('personalInfoModal');
                const invoiceModal = document.getElementById('invoiceModal');
                const rulesModal = document.getElementById('rulesModal');
                const roomsModal = document.getElementById('roomsModal');
                if (event.target == personalModal) {
                    closePersonalModal();
                }
                if (event.target == invoiceModal) {
                    invoiceModal.style.display = 'none';
                }
                if (event.target == rulesModal) {
                    closeRulesModal();
                }
                if (event.target == roomsModal) {
                    closeRoomsModal();
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('rulesModal').style.display = 'none';
                document.getElementById('roomsModal').style.display = 'none';
            });
        </script>

    </div>
</body>

</html>