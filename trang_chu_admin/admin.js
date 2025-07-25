// Chuyển đổi giữa các section
function showSection(sectionId) {
    console.log("showSection called:", sectionId);
    document.querySelectorAll(".admin-section").forEach(section => {
        section.classList.remove("active");
    });
    document.querySelectorAll(".admin-sidebar li").forEach(item => {
        item.classList.remove("active");
    });
    document.getElementById(sectionId).classList.add("active");
    if (sectionId === 'luu_dien_chung-table-body') {
        loadLuuDienChung(1, '');
    }
    document.querySelector(`.admin-sidebar li[onclick="showSection('${sectionId}')"]`).classList.add("active");
    if (sectionId === "dashboard") loadDashboard();
    else if (sectionId === "khu_vuc") loadKhuVuc();
    else if (sectionId === "luu_dien_chung-table-body"){ loadLuuDienChung(1);} // Gọi với page và search mặc định
    else if (sectionId === "tai_khoan") loadTaiKhoan();
    else if (sectionId === "phong_tro") loadRooms();
    else if (sectionId === "khach_thue") loadTenants();
    else if (sectionId === "hoa_don") loadPayments();
    else if (sectionId === "hop_dong") loadContracts();
}

// Mở modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "flex";
    }
}

// Đóng modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none'; // Ẩn modal
        // Không xóa modalContent.innerHTML để giữ cấu trúc
        
    }
}

// Kiểm tra URL
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Load dữ liệu cho Dashboard
async function loadDashboard() {
    console.log("loadDashboard called");

    try {
        // Lấy dữ liệu khu vực
        const khuVucResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_theaters.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!khuVucResponse.ok) throw new Error("Không thể kết nối đến get_theaters.php");
        const khuVucData = await khuVucResponse.json();
        handleDashboardData(khuVucData, "khu_vuc");

        // Lấy dữ liệu quy định
        const quyDinhResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!quyDinhResponse.ok) throw new Error("Không thể kết nối đến get_quy_dinh.php");
        const quyDinhData = await quyDinhResponse.json();
        handleDashboardData(quyDinhData, "luu_dien_chung");

        // Lấy dữ liệu tài khoản
        const taiKhoanResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!taiKhoanResponse.ok) throw new Error("Không thể kết nối đến get_tai_khoan.php");
        const taiKhoanData = await taiKhoanResponse.json();
        handleDashboardData(taiKhoanData, "tai_khoan");

        // Lấy dữ liệu phòng trọ
        const roomResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!roomResponse.ok) throw new Error("Không thể kết nối đến get_rooms.php");
        const roomData = await roomResponse.json();
        handleDashboardData(roomData, "phong_tro");

        // Lấy dữ liệu khách thuê
        const tenantResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!tenantResponse.ok) throw new Error("Không thể kết nối đến get_tenants.php");
        const tenantData = await tenantResponse.json();
        handleDashboardData(tenantData, "khach_thue");

        // Lấy dữ liệu hóa đơn
        const paymentResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_payments.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!paymentResponse.ok) throw new Error("Không thể kết nối đến get_payments.php");
        const paymentData = await paymentResponse.json();
        handleDashboardData(paymentData, "hoa_don");

        // Lấy dữ liệu hợp đồng
        const contractResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_contracts.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 1, search: "" }),
        });
        if (!contractResponse.ok) throw new Error("Không thể kết nối đến get_contracts.php");
        const contractData = await contractResponse.json();
        handleDashboardData(contractData, "hop_dong");
    } catch (error) {
        console.error("Error loading dashboard:", error.message);
    }
}

// Hàm xử lý dữ liệu và cập nhật DOM
function handleDashboardData(data, type) {
    // nhận dữ liệu từ API (data) và loại (type) để cập nhật số liệu vào phần tử DOM tương ứng (total-${type})
    const totalElement = document.getElementById(`total-${type}`);
    if (totalElement) {
        const total = data.total ? data.total : (data.data ? data.data.length : 0); // Giả định cấu trúc dữ liệu, không có mặc định là 0
        totalElement.textContent = total;
        console.log(`Updated ${type} total to: ${total}`);
    } else {
        console.error(`Element total-${type} not found`);
    }
}

// Gọi hàm khi trang tải
window.onload = function() {
    loadDashboard();
};

// Load dữ liệu cho Quản lý khu trọ
let currentKhuVucPage = 1;
let currentKhuVucSearch = "";

async function loadKhuVuc(page = 1, search = "") {
    console.log("loadKhuVuc called", { page, search });
    currentKhuVucPage = page;
    currentKhuVucSearch = search;

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_theaters.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const result = await response.json();
        console.log("Response from get_theaters.php:", result); // Log toàn bộ phản hồi
        handleKhuVucData(result);
    } catch (error) {
        console.error("Error loading khu vuc:", error);
        alert("Lỗi khi tải danh sách khu trọ: " + error.message);
    }
}

function searchKhuVuc() {
    const search = document.getElementById("search-khu_vuc").value.trim();
    loadKhuVuc(1, search);
}

function refreshKhuVuc() {
    document.getElementById("search-khu_vuc").value = "";
    loadKhuVuc(1, "");
}

async function addKhuVuc() {
    console.log("addKhuVuc called");
    document.getElementById("addKhuVucName").value = "";
    document.getElementById("addKhuVucAddress").value = "";
    openModal("addKhuVucModal");
}

async function saveNewKhuVuc() {
    console.log("saveNewKhuVuc called");
    const address = document.getElementById("addKhuVucAddress").value.trim();
    const overview = document.getElementById("addKhuVucName").value.trim();

    if (!address) {
        alert("Địa chỉ không được để trống!");
        return;
    }

    const khuVucData = { address, overview };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_theater.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(khuVucData),
        });
        const result = await response.json();
        handleAddKhuVucResult(result);
    } catch (error) {
        console.error("Error adding khu vuc:", error);
    }
}

async function editKhuVuc(id) {
    console.log("editKhuVuc called:", id);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_theaters.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const result = await response.json();
        if (result.success) {
            const khuVuc = result.data.find(k => k.id_khu_tro === id);
            if (khuVuc) {
                document.getElementById("editKhuVucId").value = khuVuc.id_khu_tro;
                document.getElementById("editKhuVucAddress").value = khuVuc.dia_chi;
                document.getElementById("editKhuVucOverview").value = khuVuc.tong_quan || "";
                openModal("editKhuVucModal");
            } else {
                alert("Không tìm thấy khu trọ!");
            }
        } else {
            alert(result.message || "Có lỗi khi lấy thông tin khu trọ!");
        }
    } catch (error) {
        console.error("Error fetching khu vuc:", error);
    }
}

async function updateKhuVuc() {
    console.log("updateKhuVuc called");
    const id = document.getElementById("editKhuVucId").value;
    const address = document.getElementById("editKhuVucAddress").value.trim();
    const overview = document.getElementById("editKhuVucOverview").value.trim();

    if (!id || !address) {
        alert("Vui lòng điền đầy đủ thông tin!");
        return;
    }

    const khuVucData = { id, address, overview };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/edit_khu_vuc.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(khuVucData),
        });
        const result = await response.json();
        handleUpdateKhuVucResult(result);
    } catch (error) {
        console.error("Error updating khu vuc:", error);
    }
}

async function deleteKhuVuc(id) {
    console.log("deleteKhuVuc called:", id);
    if (!confirm(`Bạn có chắc chắn muốn xóa khu trọ với ID ${id}?`)) {
        return;
    }
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_khu_vuc.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id }),
        });
        const result = await response.json();
        handleDeleteKhuVucResult(result);
    } catch (error) {
        console.error("Error deleting khu vuc:", error);
    }
}

// Khai báo biến toàn cục
let currentLuuDienChungPage = 1;
let currentQuyDinhSearch = "";

async function loadLuuDienChung(page = 1, search = "") {
    currentLuuDienChungPage = page;
    currentQuyDinhSearch = search;

    console.log("Loading luu dien chung with page:", page, "search:", search);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();
        console.log("API response:", result); // Kiểm tra dữ liệu trả về
        handleLuuDienChungData(result, currentLuuDienChungPage);
    } catch (error) {
        console.error("Error loading quy dinh:", error);
    }
}

function searchLuuDienChung() {
    const search = document.getElementById("search-luu_dien_chung").value.trim(); /*trim loại bỏ khoảng trắng thừa */
    loadLuuDienChung(1, search); // Bắt đầu lại từ trang 1 khi tìm kiếm
}

function refreshLuuDienChung() {
    document.getElementById("search-luu_dien_chung").value = "";
    loadLuuDienChung(1, ""); // Làm mới từ trang 1
}

async function addLuuDienChung() {
    console.log("addLuuDienChung called");
    const khuTroElement = document.getElementById("addLuuDienChungKhuTro");
    const linkElement = document.getElementById("addLuuDienChungLink");
    if (khuTroElement) khuTroElement.value = "";
    if (linkElement) linkElement.value = "";
    if (document.getElementById("addLuuDienChungModal")) {
        openModal("addLuuDienChungModal");
    } else {
        console.error("Modal addLuuDienChungModal not found!");
    }
}

async function saveNewLuuDienChung() {
    console.log("saveNewLuuDienChung called");
    const idKhuTro = document.getElementById("addLuuDienChungKhuTro").value;
    const link = document.getElementById("addLuuDienChungLink").value.trim();

    if (!idKhuTro || !link) {
        alert("Vui lòng điền đầy đủ thông tin!");
        return;
    }

    const quyDinhData = { idKhuTro, link };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(quyDinhData),
        });
        const result = await response.json();
        handleAddLuuDienChungResult(result);
    } catch (error) {
        console.error("Error adding quy dinh:", error);
    }
}

async function editQuyDinh(id) {
    console.log("editQuyDinh called:", id);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const result = await response.json();
        if (result.success) {
            const quyDinh = result.data.find(q => q.id_quy_dinh === id);
            if (quyDinh) {
                document.getElementById("editQuyDinhId").value = quyDinh.id_quy_dinh;
                document.getElementById("editQuyDinhKhuTro").value = quyDinh.id_khu_tro;
                document.getElementById("editQuyDinhLink").value = quyDinh.noi_dung;
                openModal("editQuyDinhModal");
            } else {
                alert("Không tìm thấy quy định!");
            }
        } else {
            alert(result.message || "Có lỗi khi lấy thông tin quy định!");
        }
    } catch (error) {
        console.error("Error fetching quy dinh:", error);
    }
}

async function updateQuyDinh() {
    console.log("updateQuyDinh called");
    const id = document.getElementById("editQuyDinhId").value;
    const idKhuTro = document.getElementById("editQuyDinhKhuTro").value;
    const link = document.getElementById("editQuyDinhLink").value.trim();

    if (!id || !idKhuTro || !link) {
        alert("Vui lòng điền đầy đủ thông tin!");
        return;
    }

    const quyDinhData = { id, idKhuTro, link };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/edit_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(quyDinhData),
        });
        const result = await response.json();
        handleUpdateQuyDinhResult(result);
    } catch (error) {
        console.error("Error updating quy dinh:", error);
    }
}

async function deleteQuyDinh(id) {
    console.log("deleteQuyDinh called:", id);
    if (!confirm(`Bạn có chắc chắn muốn xóa quy định với ID ${id}?`)) {
        return;
    }
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_quy_dinh.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id }),
        });
        const result = await response.json();
        handleDeleteQuyDinhResult(result);
    } catch (error) {
        console.error("Error deleting quy dinh:", error);
    }
}

// Load dữ liệu cho Quản lý tài khoản
let currentTaiKhoanPage = 1;
let currentTaiKhoanSearch = "";

async function loadTaiKhoan(page = 1, search = "") {
    console.log("loadTaiKhoan called", { page, search });
    currentTaiKhoanPage = page;
    currentTaiKhoanSearch = search;

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        const result = await response.json();
        handleTaiKhoanData(result);
    } catch (error) {
        console.error("Error loading tai khoan:", error);
    }
}

function searchTaiKhoan() {
    const search = document.getElementById("search-tai_khoan").value.trim();
    loadTaiKhoan(1, search);
}

function refreshTaiKhoan() {
    document.getElementById("search-tai_khoan").value = "";
    loadTaiKhoan(1, "");
}

async function addTaiKhoan() {
    console.log("addTaiKhoan called");
    document.getElementById("addTaiKhoanTenDangNhap").value = "";
    document.getElementById("addTaiKhoanMatKhau").value = "";
    document.getElementById("addTaiKhoanVaiTro").value = "khach_thue";
    document.getElementById("addTaiKhoanIdKhachThue").value = "";
    document.getElementById("addTaiKhoanEmail").value = "";
    openModal("addTaiKhoanModal");
}

async function saveNewTaiKhoan() {
    console.log("saveNewTaiKhoan called");
    const tenDangNhap = document.getElementById("addTaiKhoanTenDangNhap").value.trim();
    const matKhau = document.getElementById("addTaiKhoanMatKhau").value.trim();
    const vaiTro = document.getElementById("addTaiKhoanVaiTro").value;
    const idKhachThue = document.getElementById("addTaiKhoanIdKhachThue").value.trim() || null;
    const email = document.getElementById("addTaiKhoanEmail").value.trim() || null;

    if (!tenDangNhap || !matKhau) {
        alert("Vui lòng điền đầy đủ tên đăng nhập và mật khẩu!");
        return;
    }

    const taiKhoanData = { tenDangNhap, matKhau, vaiTro, idKhachThue, email };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(taiKhoanData),
        });
        const result = await response.json();
        handleAddTaiKhoanResult(result);
    } catch (error) {
        console.error("Error adding tai khoan:", error);
    }
}

async function editTaiKhoan(id) {
    console.log("editTaiKhoan called:", id);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const result = await response.json();
        if (result.success) {
            const taiKhoan = result.data.find(t => t.id_tai_khoan === id);
            if (taiKhoan) {
                document.getElementById("editTaiKhoanId").value = taiKhoan.id_tai_khoan;
                document.getElementById("editTaiKhoanTenDangNhap").value = taiKhoan.ten_dang_nhap;
                document.getElementById("editTaiKhoanMatKhau").value = ""; // Không hiển thị mật khẩu cũ
                document.getElementById("editTaiKhoanVaiTro").value = taiKhoan.vai_tro;
                document.getElementById("editTaiKhoanIdKhachThue").value = taiKhoan.id_khach_thue || "";
                document.getElementById("editTaiKhoanEmail").value = taiKhoan.email || "";
                openModal("editTaiKhoanModal");
            } else {
                alert("Không tìm thấy tài khoản!");
            }
        } else {
            alert(result.message || "Có lỗi khi lấy thông tin tài khoản!");
        }
    } catch (error) {
        console.error("Error fetching tai khoan:", error);
    }
}

async function updateTaiKhoan() {
    console.log("updateTaiKhoan called");
    const id = document.getElementById("editTaiKhoanId").value;
    const tenDangNhap = document.getElementById("editTaiKhoanTenDangNhap").value.trim();
    const matKhau = document.getElementById("editTaiKhoanMatKhau").value.trim();
    const vaiTro = document.getElementById("editTaiKhoanVaiTro").value;
    const idKhachThue = document.getElementById("editTaiKhoanIdKhachThue").value.trim() || null;
    const email = document.getElementById("editTaiKhoanEmail").value.trim() || null;

    if (!id || !tenDangNhap) {
        alert("Vui lòng điền đầy đủ ID và tên đăng nhập!");
        return;
    }

    const taiKhoanData = { id, tenDangNhap, matKhau, vaiTro, idKhachThue, email };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/edit_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(taiKhoanData),
        });
        const result = await response.json();
        handleUpdateTaiKhoanResult(result);
    } catch (error) {
        console.error("Error updating tai khoan:", error);
    }
}

async function deleteTaiKhoan(id) {
    console.log("deleteTaiKhoan called:", id);
    if (!confirm(`Bạn có chắc chắn muốn xóa tài khoản với ID ${id}?`)) {
        return;
    }
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_tai_khoan.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id }),
        });
        const result = await response.json();
        handleDeleteTaiKhoanResult(result);
    } catch (error) {
        console.error("Error deleting tai khoan:", error);
    }
}

// Load dữ liệu cho Quản lý phòng trọ
let currentRoomPage = 1;
let currentRoomSearch = "";

async function loadRooms(page = 1, search = "") {
    console.log("loadRooms called with page:", page, "search:", search);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        if (!response.ok) throw new Error("Không thể kết nối đến get_rooms.php");
        const result = await response.json();
        handleRoomsData(result); // Giả sử handleRoomsData đã được định nghĩa
    } catch (error) {
        console.error("Error loading rooms:", error);
        alert("Lỗi tải danh sách phòng: " + error.message);
    }
}

function searchPhongTro() {
    const search = document.getElementById("search-phong_tro").value.trim();
    loadRooms(1, search);
}

function refreshPhongTro() {
    document.getElementById("search-phong_tro").value = "";
    loadRooms(1, "");
}

// Sửa hàm editRoom
async function editRoom(id) {
    console.log("editRoom called:", id);
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        if (!response.ok) throw new Error("Không thể kết nối đến get_rooms.php");
        const result = await response.json();
        if (result.success) {
            const room = result.data.find(r => r.id_phong === id); // Sửa id_phong_tro thành id_phong
            if (room) {
                handleEditRoomData(room);
                openModal("editRoomModal");
            } else {
                alert("Không tìm thấy phòng!");
            }
        } else {
            alert(result.message || "Có lỗi khi lấy thông tin phòng!");
        }
    } catch (error) {
        console.error("Error fetching room:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

// Sửa hàm updateRoom
async function updateRoom() {
    console.log("updateRoom called");
    const id_phong = document.getElementById("editRoomId").value;
    const so_phong = document.getElementById("editRoomSoPhong").value.trim();
    const id_khu_tro = document.getElementById("editRoomKhuTro").value;
    const gia_thue = parseFloat(document.getElementById("editRoomPrice").value);
    const trang_thai = document.getElementById("editRoomStatus").value;

    if (!id_phong || !so_phong || isNaN(gia_thue)) {
        alert("Vui lòng điền đầy đủ thông tin (Số phòng, Giá)!");
        return;
    }

    const roomData = { id_phong, so_phong, id_khu_tro, gia_thue, trang_thai };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/edit_room.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(roomData),
        });
        const result = await response.json();
        if (result.success) {
            alert("Cập nhật phòng thành công!");
            closeModal("editRoomModal");
            loadRooms(1, ""); // Reload danh sách sau khi cập nhật
        } else {
            alert(result.message || "Có lỗi khi cập nhật phòng!");
        }
    } catch (error) {
        console.error("Error updating room:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

async function addPhongTro() {
    console.log("addPhongTro called");
    document.getElementById("addPhongTroName").value = "";
    document.getElementById("addPhongTroKhuVuc").value = "";
    document.getElementById("addPhongTroPrice").value = "";
    document.getElementById("addPhongTroStatus").value = "trong";
    openModal("addPhongTroModal");
}

// Sửa hàm saveNewPhongTro
async function saveNewPhongTro() {
    console.log("saveNewPhongTro called");
    const so_phong = document.getElementById("addPhongTroName").value.trim();
    const id_khu_tro = document.getElementById("addPhongTroKhuVuc").value;
    const gia_thue = parseFloat(document.getElementById("addPhongTroPrice").value);
    const trang_thai = document.getElementById("addPhongTroStatus").value;

    if (!so_phong || !id_khu_tro || isNaN(gia_thue)) {
        alert("Vui lòng điền đầy đủ thông tin!");
        return;
    }

    const roomData = { so_phong, id_khu_tro, gia_thue, trang_thai };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_room.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(roomData),
        });
        const result = await response.json();
        handleAddRoomResult(result);
    } catch (error) {
        console.error("Error adding room:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

// Sửa hàm deleteRoom
async function deleteRoom(id_phong) {
    console.log("deleteRoom called:", id_phong);
    if (!confirm(`Bạn có chắc chắn muốn xóa phòng với ID ${id_phong}?`)) {
        return;
    }
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_room.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_phong }),
        });
        const result = await response.json();
        handleDeleteRoomResult(result);
    } catch (error) {
        console.error("Error deleting room:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

async function loadKhuTro() {
    console.log("loadKhuTro called");
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_theaters.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "get_khu_tro" }), // Thay bằng tham số phù hợp với API
        });
        if (!response.ok) throw new Error("Không thể kết nối đến get_theaters.php");
        const result = await response.json();
        if (result.success) {
            const select = document.getElementById("addPhongTroKhuVuc");
            select.innerHTML = '<option value="">Chọn khu trọ</option>'; // Xóa các option cũ
            result.data.forEach(khu => {
                const option = document.createElement("option");
                option.value = khu.id_khu_tro;
                option.text = khu.dia_chi; // Hoặc trường khác tùy theo API
                select.appendChild(option);
            });
        } else {
            alert(result.message || "Có lỗi khi tải danh sách khu trọ!");
        }
    } catch (error) {
        console.error("Error loading khu tro:", error);
        alert("Lỗi tải danh sách khu trọ: " + error.message);
    }
}

// Load dữ liệu cho Quản lý khách thuê
let currentTenantPage = 1;
let currentTenantSearch = "";

async function loadTenants(page = 1, search = "") {
    console.log("loadTenants called", { page, search });
    currentTenantPage = page;
    currentTenantSearch = search;

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        handleTenantsData(result);
    } catch (error) {
        console.error("Error loading tenants:", error);
        const tbody = document.getElementById("khach_thue-table-body");
        if (tbody) {
            tbody.innerHTML = "<tr><td colspan='9'>Không thể tải dữ liệu khách thuê.</td></tr>";
        }
    }
}


function searchKhachThue() {
    const search = document.getElementById("search-khach_thue").value.trim();
    loadTenants(1, search);
}

function refreshKhachThue() {
    document.getElementById("search-khach_thue").value = "";
    loadTenants(1, "");
}

async function editTenant(tenantId) {
    try {
        const response = await fetch(`http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php?id=${tenantId}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();
        handleEditTenantData(result, tenantId); // Gọi hàm xử lý
    } catch (error) {
        console.error('Error fetching tenant:', error);
    }
}

async function updateTenant() {
    const tenantId = document.getElementById('editTenantId').value;
    const ho_ten = document.getElementById('editTenantName').value;
    const so_dien_thoai = document.getElementById('editTenantPhone').value;
    const email = document.getElementById('editTenantEmail').value;
    const so_cccd = document.getElementById('editTenantCCCD').value;
    const phong_tro = document.getElementById('editTenantPhongTro').value;
    const ngay_sinh = document.getElementById('editTenantNgaySinh').value; // Thêm trường ngay_sinh
    const gioi_tinh = document.getElementById('editTenantGioiTinh').value; // Thêm trường gioi_tinh

    console.log('Sending data:', { id: tenantId, ho_ten, so_dien_thoai, email, so_cccd, phong_tro, ngay_sinh, gioi_tinh });

    if (!tenantId || !ho_ten || !so_dien_thoai || !so_cccd || !phong_tro) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }

    try {
        const response = await fetch('http://localhost/quan_ly_phong_tro/trang_chu_admin/update_tenant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: tenantId, ho_ten, so_dien_thoai, email, so_cccd, phong_tro, ngay_sinh, gioi_tinh })
        });
        const result = await response.json();
        handleUpdateTenantResult(result);
    } catch (error) {
        console.error('Error updating tenant:', error);
    }
}

async function addKhachThue() {
    console.log("addKhachThue called");
    const nameInput = document.getElementById("addKhachThueName");
    const emailInput = document.getElementById("addKhachThueEmail");
    const phoneInput = document.getElementById("addKhachThuePhone");
    const roomSelect = document.getElementById("addKhachThuePhongTro");

    if (!nameInput || !emailInput || !phoneInput || !roomSelect) {
        console.error("Một hoặc nhiều phần tử không tồn tại:", {
            nameInput, emailInput, phoneInput, roomSelect
        });
        alert("Có lỗi khi tải modal, vui lòng kiểm tra lại!");
        return;
    }

    nameInput.value = "";
    emailInput.value = "";
    phoneInput.value = "";
    roomSelect.value = "";
    openModal("addKhachThueModal");
}

async function saveNewKhachThue() {
    const name = document.getElementById("addKhachThueName").value.trim();
    const email = document.getElementById("addKhachThueEmail").value.trim();
    const phone = document.getElementById("addKhachThuePhone").value.trim();
    const cccd = document.getElementById("addKhachThueCCCD").value.trim();
    const roomId = document.getElementById("addKhachThuePhongTro").value;
    const ngay_sinh = document.getElementById("addKhachThueNgaySinh").value; // Thêm trường này
    const gioi_tinh = document.getElementById("addKhachThueGioiTinh").value; // Thêm trường này

    if (!name || !phone || !cccd || !roomId || roomId === "") {
        alert("Vui lòng điền đầy đủ thông tin (Họ tên, Số điện thoại, Số CCCD, Phòng trọ)!");
        return;
    }

    const tenantData = { name, email, phone, roomId, so_cccd: cccd, ngay_sinh, gioi_tinh };
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_tenant.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(tenantData),
        });
        const result = await response.json();
        if (result.success) {
            alert("Thêm khách thuê thành công!");
            closeModal("addKhachThueModal");
            loadTenants(1, "");
        } else {
            alert(result.message || "Có lỗi khi thêm khách thuê!");
        }
    } catch (error) {
        console.error("Error adding tenant:", error);
        if (error instanceof SyntaxError) {
            alert("Lỗi server: Dữ liệu trả về không phải JSON hợp lệ. Vui lòng kiểm tra file add_tenant.php!");
        } else {
            alert("Lỗi kết nối server: " + error.message);
        }
    }
}

async function deleteTenant(id) {
    if (confirm('Bạn có chắc chắn muốn xóa khách thuê này?')) {
        try {
            const response = await fetch('http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_tenant.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const result = await response.json();
            if (result.success) {
                alert('Xóa khách thuê thành công!');
                loadTenants(1, ''); // Tải lại danh sách
            } else {
                alert(result.message || 'Có lỗi khi xóa khách thuê!');
            }
        } catch (error) {
            console.error('Error deleting tenant:', error);
            alert('Lỗi kết nối server: ' + error.message);
        }
    }
}

async function loadPhongTro() {
    console.log("loadPhongTro called");
    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }), // Lấy tất cả phòng
        });
        if (!response.ok) throw new Error("Không thể kết nối đến get_rooms.php");
        const result = await response.json();
        if (result.success) {
            const select = document.getElementById("addKhachThuePhongTro");
            select.innerHTML = '<option value="">Chọn phòng trọ</option>'; // Xóa các option cũ
            result.data.forEach(phong => {
                const option = document.createElement("option");
                option.value = phong.id_phong;
                option.text = `${phong.so_phong} - ${phong.dia_chi || 'Chưa xác định'}`; // Hiển thị số phòng và địa chỉ
                select.appendChild(option);
            });
        } else {
            alert(result.message || "Có lỗi khi tải danh sách phòng trọ!");
        }
    } catch (error) {
        console.error("Error loading phong tro:", error);
        alert("Lỗi tải danh sách phòng trọ: " + error.message);
    }
}

// Load dữ liệu cho Quản lý hóa đơn
let currentHoaDonPage = 1;
let currentHoaDonSearch = "";

async function loadPayments(page = 1, search = "") {
    console.log("loadPayments called", { page, search });
    currentHoaDonPage = page;
    currentHoaDonSearch = search;

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_payments.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        const result = await response.json();
        console.log("Data received from server:", result); // Thêm log để debug dữ liệu

        if (result.success) {
            if (typeof handlePaymentsData === "function") {
                handlePaymentsData(result);
            } else {
                console.error("handlePaymentsData is not defined!");
                alert("Có lỗi khi xử lý dữ liệu hóa đơn!");
            }
        } else {
            console.error("Server error:", result.message);
            alert("Lỗi khi tải hóa đơn: " + result.message);
        }
    } catch (error) {
        console.error("Error loading payments:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

function searchHoaDon() {
    const search = document.getElementById("search-hoa_don").value.trim();
    loadPayments(1, search);
}

function refreshHoaDon() {
    document.getElementById("search-hoa_don").value = "";
    loadPayments(1, "");
}

async function addHoaDon() {
    console.log("addHoaDon called");
    try {
        const tenantResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const tenantData = await tenantResponse.json();
        console.log("Tenant Data:", tenantData.data); // Kiểm tra dữ liệu

        const roomResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const roomData = await roomResponse.json();
        console.log("Room Data:", roomData); // Kiểm tra dữ liệu

        openModal("addHoaDonModal");
        handleAddPaymentData(tenantData, roomData);
    } catch (error) {
        console.error("Error loading data for addHoaDon:", error);
        alert("Lỗi tải dữ liệu khách thuê hoặc phòng!");
    }
}

async function saveNewHoaDon() {
    console.log("saveNewHoaDon called");
   
    const tenantSelect = document.getElementById("addHoaDonTenant");
    const roomSelect = document.getElementById("addHoaDonRoom");
    if (!tenantSelect || !roomSelect) {
        console.error("Select elements not found!");
        alert("Lỗi: Không tìm thấy dropdown khách thuê hoặc phòng!");
        return;
    }
    const tenantId = tenantSelect.value;
    const roomId = roomSelect.value;
    console.log("TenantId:", tenantId, "RoomId:", roomId); // Log để kiểm tra
    if (!tenantId || tenantId === "") {
        alert("Vui lòng chọn khách thuê!");
        return;
    }
    const thangApDung = document.getElementById("addHoaDonThangApDung").value;
    const tienPhong = parseFloat(document.getElementById("addHoaDonTienPhong").value) || 0;
    const tienDien = parseFloat(document.getElementById("addHoaDonTienDien").value) || 0;
    const tienNuoc = parseFloat(document.getElementById("addHoaDonTienNuoc").value) || 0;
    const tienInternet = parseFloat(document.getElementById("addHoaDonTienInternet").value) || 0;
    const phiKhac = parseFloat(document.getElementById("addHoaDonPhiKhac").value) || 0;
    const ngayThanhToan = document.getElementById("addHoaDonDate").value;
    const trangThai = document.getElementById("addHoaDonStatus").value;

    if (!tenantId || !roomId || !thangApDung || !tienPhong) {
        alert("Vui lòng điền đầy đủ thông tin bắt buộc (Khách thuê, Phòng, Tháng áp dụng, Tiền phòng)!");
        return;
    }

    const hoaDonData = {
        tenantId,
        roomId,
        thangApDung,
        tienPhong,
        tienDien,
        tienNuoc,
        tienInternet,
        phiKhac,
        ngayThanhToan: ngayThanhToan || null,
        trangThai
    };

     console.log("Sending hoaDonData:", hoaDonData); // Đặt sau khi khai báo hoaDonData

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(hoaDonData),
        });
        const result = await response.json();
        console.log("Response from server:", result);
        handleAddPaymentResult(result);
    } catch (error) {
        console.error("Error adding hoa don:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

async function editPayment(id) {
    console.log("Editing payment with id:", id); // Debug id
    if (!id || id === 0) {
        alert("Không tìm thấy ID hóa đơn hợp lệ!");
        return;
    }

    try {
        // Tải thông tin hóa đơn
        const response = await fetch(`http://localhost/quan_ly_phong_tro/trang_chu_admin/get_payments.php?id=${id}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });
        const result = await response.json();
        console.log("Payment data:", result); // Debug dữ liệu trả về

        if (result.success) {
            const payment = result.data[0]; // Lấy bản ghi đầu tiên (giả sử chỉ có 1 bản ghi với id cụ thể)
            if (!payment) {
                alert("Không tìm thấy hóa đơn!");
                return;
            }

            // Điền thông tin vào modal
            document.getElementById("editHoaDonId").value = payment.id_hoa_don || '';
            document.getElementById("editHoaDonThangApDung").value = payment.thang_ap_dung || '';
            document.getElementById("editHoaDonTienPhong").value = payment.tien_phong || 0;
            document.getElementById("editHoaDonTienDien").value = payment.tien_dien || 0;
            document.getElementById("editHoaDonTienNuoc").value = payment.tien_nuoc || 0;
            document.getElementById("editHoaDonTienInternet").value = payment.tien_internet || 0;
            document.getElementById("editHoaDonTienRac").value = payment.tien_rac || 0;
            document.getElementById("editHoaDonPhiKhac").value = payment.phi_khac || 0;
            document.getElementById("editHoaDonTongTien").value = (payment.tong_tien || 0).toLocaleString() + " VNĐ";
            document.getElementById("editHoaDonDate").value = payment.ngay_thanh_toan || '';
            document.getElementById("editHoaDonStatus").value = payment.trang_thai_thanh_toan || 'chua_thanh_toan';

            // Tải danh sách khách thuê và phòng trọ
            const tenantResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ page: 1, limit: 100, search: "" }),
            });
            const tenantData = await tenantResponse.json();
            const tenantSelect = document.getElementById("editHoaDonTenant");
            tenantSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
            if (tenantData.success) {
                tenantData.data.forEach(tenant => {
                    tenantSelect.innerHTML += `<option value="${tenant.id_khach_thue}" ${tenant.id_khach_thue === payment.id_khach_thue ? 'selected' : ''}>${tenant.ho_ten}</option>`;
                });
            }

            const roomResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ page: 1, limit: 100, search: "" }),
            });
            const roomData = await roomResponse.json();
            const roomSelect = document.getElementById("editHoaDonRoom");
            roomSelect.innerHTML = '<option value="">Chọn phòng trọ</option>';
            if (roomData.success) {
                roomData.data.forEach(room => {
                    roomSelect.innerHTML += `<option value="${room.id_phong}" ${room.id_phong === payment.id_phong ? 'selected' : ''}>${room.so_phong}</option>`;
                });
            }

            // Gắn sự kiện tính tổng tiền
            const inputs = ["editHoaDonTienPhong", "editHoaDonTienDien", "editHoaDonTienNuoc", "editHoaDonTienInternet", "editHoaDonPhiKhac" , "editHoaDonTienRac"];
            inputs.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", calculateTotalAmountEdit);
                }
            });

            openModal("editHoaDonModal");
        } else {
            alert("Không tìm thấy hóa đơn: " + result.message);
        }
    } catch (error) {
        console.error("Error editing payment:", error);
        alert("Lỗi khi tải thông tin hóa đơn: " + error.message);
    }
}

// Hàm tính tổng tiền cho modal sửa
function calculateTotalAmountEdit() {
    const tienPhong = parseFloat(document.getElementById("editHoaDonTienPhong").value) || 0;
    const tienDien = parseFloat(document.getElementById("editHoaDonTienDien").value) || 0;
    const tienNuoc = parseFloat(document.getElementById("editHoaDonTienNuoc").value) || 0;
    const tienInternet = parseFloat(document.getElementById("editHoaDonTienInternet").value) || 0;
    const phiKhac = parseFloat(document.getElementById("editHoaDonPhiKhac").value) || 0;
    const tienRac = parseFloat(document.getElementById("editHoaDonTienRac").value) || 0;
    const tongTien = tienPhong + tienDien + tienNuoc + tienInternet + phiKhac + tienRac;
    document.getElementById("editHoaDonTongTien").value = tongTien.toLocaleString() + " VNĐ";
}

async function updatePayment() {
    console.log("updatePayment called");
    const id = document.getElementById("editHoaDonId").value;
    const tenantId = document.getElementById("editHoaDonTenant").value;
    const roomId = document.getElementById("editHoaDonRoom").value;
    const thangApDung = document.getElementById("editHoaDonThangApDung").value;
    const tienPhong = parseFloat(document.getElementById("editHoaDonTienPhong").value) || 0;
    const tienDien = parseFloat(document.getElementById("editHoaDonTienDien").value) || 0;
    const tienNuoc = parseFloat(document.getElementById("editHoaDonTienNuoc").value) || 0;
    const tienInternet = parseFloat(document.getElementById("editHoaDonTienInternet").value) || 0;
    const phiKhac = parseFloat(document.getElementById("editHoaDonPhiKhac").value) || 0;
    const ngayThanhToan = document.getElementById("editHoaDonDate").value || null;
    const trangThai = document.getElementById("editHoaDonStatus").value;

    if (!id || !tenantId || !roomId || !thangApDung || !tienPhong) {
        alert("Vui lòng điền đầy đủ thông tin bắt buộc!");
        return;
    }

    const paymentData = {
        id,
        tenantId,
        roomId,
        thangApDung,
        tienPhong,
        tienDien,
        tienNuoc,
        tienInternet,
        phiKhac,
        ngayThanhToan,
        trangThai,
        tienRac,
        tongTien: tienPhong + tienDien + tienNuoc + tienInternet + phiKhac + tienRac// Tính tổng tiền
    };

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/update_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(paymentData),
        });
        const result = await response.json();
        if (result.success) {
            alert("Cập nhật hóa đơn thành công!");
            closeModal("editHoaDonModal");
            loadPayments(currentHoaDonPage, currentHoaDonSearch);
        } else {
            alert(result.message || "Có lỗi khi cập nhật hóa đơn!");
        }
    } catch (error) {
        console.error("Error updating payment:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

async function deletePayment(id) {
    if (!id || confirm("Bạn có chắc chắn muốn xóa hóa đơn này?")) {
        try {
            const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_payment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id }),
            });
            const result = await response.json();
            if (result.success) {
                alert("Xóa hóa đơn thành công!");
                loadPayments(currentHoaDonPage, currentHoaDonSearch);
            } else {
                alert(result.message || "Có lỗi khi xóa hóa đơn!");
            }
        } catch (error) {
            console.error("Error deleting payment:", error);
            alert("Lỗi kết nối server: " + error.message);
        }
    }
}

// Tính tổng tiền khi nhập chi phí
function calculateTotalAmount() {
    const tienPhong = parseFloat(document.getElementById("addHoaDonTienPhong").value) || 0;
    const tienDien = parseFloat(document.getElementById("addHoaDonTienDien").value) || 0;
    const tienNuoc = parseFloat(document.getElementById("addHoaDonTienNuoc").value) || 0;
    const tienInternet = parseFloat(document.getElementById("addHoaDonTienInternet").value) || 0;
    const phiKhac = parseFloat(document.getElementById("addHoaDonPhiKhac").value) || 0;
    const tienRac = parseFloat(document.getElementById("addHoaDonTienRac").value) || 0;
    const tongTien = tienPhong + tienDien + tienNuoc + tienInternet + phiKhac + tienRac;
    document.getElementById("addHoaDonTongTien").value = tongTien;
}

// Gắn sự kiện input để tính tổng tiền
document.addEventListener("DOMContentLoaded", function() {
    const inputs = ["addHoaDonTienPhong", "addHoaDonTienDien", "addHoaDonTienNuoc", "addHoaDonTienInternet", "addHoaDonPhiKhac"];
    inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", calculateTotalAmount);
        }
    });
});

// Load dữ liệu cho Quản lý hợp đồng
let currentHopDongPage = 1;
let currentHopDongSearch = "";

async function loadContracts(page = 1, search = "") {
    console.log("loadContracts called", { page, search });
    currentHopDongPage = page;
    currentHopDongSearch = search;

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_contracts.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page, limit: 10, search }),
        });
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const result = await response.json();
        console.log("API response for contracts:", result); // Log để kiểm tra dữ liệu
        handleContractsData(result);
    } catch (error) {
        console.error("Error loading contracts:", error);
        const tableBody = document.getElementById("hop_dong-table-body");
        if (tableBody) {
            tableBody.innerHTML = `<tr><td colspan="6">Lỗi tải danh sách hợp đồng: ${error.message}</td></tr>`;
        }
    }
}

function searchHopDong() {
    const search = document.getElementById("search-hop_dong").value.trim();
    loadContracts(1, search);
}

function refreshHopDong() {
    document.getElementById("search-hop_dong").value = "";
    loadContracts(1, "");
}

async function addHopDong() {
    console.log("addHopDong called");
    document.getElementById("addHopDongKhachThue").value = "";
    document.getElementById("addHopDongPhongTro").value = "";
    document.getElementById("addHopDongStartDate").value = "";
    document.getElementById("addHopDongEndDate").value = "";

    try {
        const tenantResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const tenantData = await tenantResponse.json();
        console.log("Tenant Data:", tenantData); // Kiểm tra dữ liệu

        const tenantSelect = document.getElementById("addHopDongKhachThue");
        tenantSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
        if (tenantData.success) {
            tenantData.data.forEach(tenant => {
                tenantSelect.innerHTML += `<option value="${tenant.id_khach_thue}">${tenant.ho_ten}</option>`;
            });
        }

        const roomResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" }),
        });
        const roomData = await roomResponse.json();
        console.log("Room Data:", roomData); // Kiểm tra dữ liệu

        const roomSelect = document.getElementById("addHopDongPhongTro");
        roomSelect.innerHTML = '<option value="">Chọn phòng trọ</option>';
        if (roomData.success) {
            roomData.data.forEach(room => {
                roomSelect.innerHTML += `<option value="${room.id_phong}">${room.so_phong}</option>`;
            });
        }

        openModal("addHopDongModal");
    } catch (error) {
        console.error("Error loading data for addHopDong:", error);
        alert("Lỗi tải danh sách khách thuê hoặc phòng trọ!");
    }
}

async function saveNewHopDong() {
    console.log("saveNewHopDong called");
    const id_khach_thue = document.getElementById("addHopDongKhachThue").value;
    const id_phong = document.getElementById("addHopDongPhongTro").value;
    const start_date = document.getElementById("addHopDongStartDate").value;
    const end_date = document.getElementById("addHopDongEndDate").value;

    console.log({ id_khach_thue, id_phong, start_date, end_date }); // Kiểm tra giá trị

    if (!id_khach_thue || !id_phong || !start_date || !end_date) {
        alert("Vui lòng điền đầy đủ thông tin! Kiểm tra: " + 
              `id_khach_thue=${id_khach_thue}, id_phong=${id_phong}, start_date=${start_date}, end_date=${end_date}`);
        return;
    }

    const hopDongData = { id_khach_thue, id_phong, start_date, end_date };

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/add_contract.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(hopDongData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        if (result.success) {
            alert("Thêm hợp đồng thành công!");
            handleAddContractResult(result);
            closeModal("addHopDongModal");
        } else {
            alert(result.message || "Có lỗi khi thêm hợp đồng!");
        }
    } catch (error) {
        console.error("Error adding hop dong:", error);
        alert("Lỗi kết nối server: " + error.message);
    }
}

async function editContract(contractId) {
    console.log("Editing contract with ID:", contractId, "Type:", typeof contractId);
    if (!contractId || isNaN(contractId)) {
        console.error("Invalid contract ID:", contractId);
        alert("ID hợp đồng không hợp lệ!");
        return;
    }

    const requestBody = { action: "get_by_id", id: contractId, limit: 1, page: 1, search: "" };
    console.log("Sending request with body:", JSON.stringify(requestBody));

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_contracts.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(requestBody)
        });
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const result = await response.json();
        console.log("API response for edit contract:", result);
        if (result.success && Array.isArray(result.data) && result.data.length > 0) {
            handleEditContractData(result.data[0], contractId);
        } else {
            alert("Không tìm thấy hợp đồng để chỉnh sửa! Response: " + JSON.stringify(result));
        }
    } catch (error) {
        console.error("Error fetching contract:", error);
        alert("Lỗi khi tải thông tin hợp đồng: " + error.message);
    }
}

// Hàm phụ để tải danh sách (tùy chọn)
async function fetchTenantsAndRoomsForEdit(contract) {
    console.log("Fetching tenants and rooms for contract:", contract);
    try {
        const tenantResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" })
        });
        const tenantData = await tenantResponse.json();
        console.log("Tenant data:", tenantData);

        const editContractTenant = document.getElementById("editContractTenant");
        if (editContractTenant) {
            editContractTenant.innerHTML = '<option value="">Chọn khách thuê</option>';
            if (tenantData.success) {
                tenantData.data.forEach(tenant => {
                    editContractTenant.innerHTML += `<option value="${tenant.id_khach_thue}" ${tenant.id_khach_thue === contract.id_khach_thue ? 'selected' : ''}>${tenant.ho_ten}</option>`;
                });
            }
        } else {
            console.error("editContractTenant not found!");
        }

        const roomResponse = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/get_rooms.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" })
        });
        const roomData = await roomResponse.json();
        console.log("Room data:", roomData);

        const editContractRoom = document.getElementById("editContractRoom");
        if (editContractRoom) {
            editContractRoom.innerHTML = '<option value="">Chọn phòng trọ</option>';
            if (roomData.success) {
                roomData.data.forEach(room => {
                    editContractRoom.innerHTML += `<option value="${room.id_phong}" ${room.id_phong === contract.id_phong ? 'selected' : ''}>${room.so_phong}</option>`;
                });
            }
        } else {
            console.error("editContractRoom not found!");
        }
    } catch (error) {
        console.error("Error fetching tenants and rooms:", error);
    }
}

async function updateContract() {
    const contractId = document.getElementById("editContractId").value;
    const tenantId = document.getElementById("editContractTenant").value;
    const roomId = document.getElementById("editContractRoom").value;
    const startDate = document.getElementById("editContractStartDate").value;
    const endDate = document.getElementById("editContractEndDate").value;

    console.log("Data to send:", { contractId, tenantId, roomId, startDate, endDate });

    const data = {
        contractId,
        tenantId,
        roomId,
        startDate,
        endDate
    };

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/update_contract.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        console.log("Server response:", result);
        if (result.success) {
            alert(result.message);
            closeModal("editContractModal");
            refreshHopDong();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error updating contract:", error);
        alert("Có lỗi xảy ra khi cập nhật hợp đồng!");
    }
}

async function deleteContract(contractId) {
    console.log("Deleting contract with ID:", contractId);
    if (!contractId || isNaN(contractId)) {
        alert("ID hợp đồng không hợp lệ!");
        return;
    }

    if (!confirm("Bạn có chắc chắn muốn xóa hợp đồng này?")) {
        return;
    }

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/delete_contract.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_hop_dong: contractId })
        });
        const result = await response.json();
        console.log("API response for delete contract:", result);
        if (result.success) {
            alert(result.message);
            loadContracts(); // Tải lại danh sách sau khi xóa
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error deleting contract:", error);
        alert("Lỗi khi xóa hợp đồng: " + error.message);
    }
}

// Đăng xuất Admin
async function logoutAdmin() {
    console.log("logoutAdmin called");
    if (!confirm("Bạn có chắc chắn muốn đăng xuất không?")) {
        return;
    }

    try {
        const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/admin-logout.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
        });
        const result = await response.json();
        console.log("Logout response:", result);

        if (result.success) {
            alert("Đăng xuất thành công!");
            window.location.href = "http://localhost/quan_ly_phong_tro/trang_chu_khach/login.php";
        } else {
            alert(result.message || "Đăng xuất thất bại, vui lòng thử lại!");
        }
    } catch (error) {
        console.error("Lỗi khi đăng xuất:", error);
        alert("Có lỗi xảy ra khi đăng xuất, vui lòng thử lại sau!");
    }
}

function showEditContractModal(contractId) {
    fetch(`/api/contract/${contractId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Dữ liệu nhận được:', data); // Kiểm tra dữ liệu
            document.getElementById('editContractId').value = data.id || '';
            document.getElementById('editContractTenant').value = data.tenantId || '';
            document.getElementById('editContractRoom').value = data.roomId || '';
            document.getElementById('editContractStartDate').value = data.startDate || '';
            document.getElementById('editContractEndDate').value = data.endDate || '';
            document.getElementById('editContractModal').style.display = 'flex';
        })
        .catch(error => console.error('Error fetching contract:', error));
}

// Thêm vào cuối file admin.js hoặc trong một block DOMContentLoaded
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search-khu_vuc");
    if (searchInput) {
        searchInput.addEventListener("input", function() {
            const search = this.value.trim();
            loadKhuVuc(1, search); // Tải lại trang 1 với từ khóa tìm kiếm
        });
    } else {
        console.error("Không tìm thấy phần tử #search-khu_vuc trong DOM!");
    }
});

// Khởi tạo khi trang được tải
window.onload = function() {
    showSection("dashboard");
};