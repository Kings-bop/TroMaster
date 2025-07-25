// Xử lý dữ liệu Dashboard
function handleDashboardData(data, type) {
    const elementId = `total-${type}`;
    const element = document.getElementById(elementId);

    if (!element) {
        console.warn(`Không tìm thấy phần tử có id '${elementId}' trong DOM.`);
        return;
    }

    if (data.success) {
        element.textContent = data[`total${type.charAt(0).toUpperCase() + type.slice(1)}`] || 0;
    } else {
        element.textContent = "0";
    }
}

// Xử lý dữ liệu Quản lý khu trọ
function handleKhuVucData(result) {
    const tableBody = document.getElementById("khu_vuc-table-body");
    tableBody.innerHTML = "";

    if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="5" style="text-align:center;">Không có khu trọ nào được tìm thấy</td>`;
        tableBody.appendChild(row);
        return;
    }

    result.data.forEach(khuVuc => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${khuVuc.id_khu_tro}</td>
            <td>${khuVuc.dia_chi}</td>
            <td>${khuVuc.tong_quan || "Không có"}</td>
            <td>${new Date(khuVuc.ngay_tao).toLocaleDateString('vi-VN')}</td>
            <td>
                <div class="action-buttons">
                    <button class="edit" onclick="editKhuVuc(${khuVuc.id_khu_tro})">Sửa</button>
                    <button class="delete" onclick="deleteKhuVuc(${khuVuc.id_khu_tro})">Xóa</button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
    updatePagination("khu_vuc-pagination", result.totalPages, result.currentPage, loadKhuVuc, currentKhuVucSearch);
}

function handleAddKhuVucResult(result) {
    if (result.success) {
        alert("Thêm khu trọ thành công!");
        closeModal("addKhuVucModal");
        loadKhuVuc(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm khu trọ!");
    }
}

function handleUpdateKhuVucResult(result) {
    if (result.success) {
        alert("Cập nhật khu trọ thành công!");
        closeModal("editKhuVucModal");
        loadKhuVuc(currentKhuVucPage, currentKhuVucSearch);
    } else {
        alert(result.message || "Có lỗi khi cập nhật khu trọ!");
    }
}

function handleDeleteKhuVucResult(result) {
    if (result.success) {
        alert("Xóa khu trọ thành công!");
        loadKhuVuc(currentKhuVucPage, currentKhuVucSearch);
    } else {
        alert(result.message || "Có lỗi khi xóa khu trọ!");
    }
}


// Xử lý dữ liệu Quản lý quy định chung
function handleLuuDienChungData(result, currentLuuDienChungPage) {
    console.log("Starting handleLuuDienChungData, result:", result, "currentLuuDienChungPage:", currentLuuDienChungPage);
    const tableBody = document.getElementById("luu_dien_chung-table-body");
    console.log("tableBody element:", tableBody);
    if (!tableBody) {
        console.error("Phần tử #luu_dien_chung-table-body không tồn tại trong DOM!");
        return;
    }
    tableBody.innerHTML = ""; // Xóa nội dung cũ

    if (result && typeof result.success !== "undefined" && Array.isArray(result.data)) {
        console.log("Processing data:", result.data);
        if (result.data.length === 0) {
            const row = document.createElement("tr");
            row.innerHTML = `<td colspan="4" style="text-align:center;">Không có quy định nào được tìm thấy</td>`;
            tableBody.appendChild(row);
        } else {
            result.data.forEach(quyDinh => {
                const row = document.createElement("tr");

                const linkCell = document.createElement("td");
                const link = document.createElement("a");
                link.href = quyDinh.noi_dung || "#";
                link.target = "_blank";
                link.className = "link-button";
                link.textContent = "Xem quy định";
                linkCell.appendChild(link);

                const startDateCell = document.createElement("td");
                startDateCell.textContent = quyDinh.ngay_tao || "Chưa xác định";

                const endDateCell = document.createElement("td");
                endDateCell.textContent = quyDinh.ngay_cap_nhat || "Chưa xác định";

                const actionCell = document.createElement("td");
                const editButton = document.createElement("button");
                editButton.textContent = "Sửa";
                editButton.onclick = () => editQuyDinh(quyDinh.id_quy_dinh);
                const deleteButton = document.createElement("button1");
                deleteButton.textContent = "Xóa";
                deleteButton.onclick = () => deleteQuyDinh(quyDinh.id_quy_dinh);
                actionCell.appendChild(editButton);
                actionCell.appendChild(deleteButton);

                row.appendChild(linkCell);
                row.appendChild(startDateCell);
                row.appendChild(endDateCell);
                row.appendChild(actionCell);

                tableBody.appendChild(row);
            });

            // Cập nhật phân trang với ID đúng
            if (result.totalPages && typeof updatePagination === "function") {
                updatePagination("luu_dien_chung-pagination", result.totalPages, currentLuuDienChungPage, loadLuuDienChung, currentQuyDinhSearch);
            }
        }
    } else {
        console.error("Invalid data format or missing data:", result);
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="4" style="text-align:center;">Lỗi khi tải dữ liệu</td>`;
        tableBody.appendChild(row);
    }
}

function handleAddLuuDienChungResult(result) {
    if (result.success) {
        alert("Thêm quy định thành công!");
        closeModal("addLuuDienChungModal");
        loadLuuDienChung(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm quy định!");
    }
}

function handleUpdateQuyDinhResult(result) {
    if (result.success) {
        alert("Cập nhật quy định thành công!");
        closeModal("editQuyDinhModal");
        loadLuuDienChung(currentLuuDienChungPage, currentQuyDinhSearch); // Sửa typo
    } else {
        alert(result.message || "Có lỗi xảy ra khi cập nhật quy định. Vui lòng thử lại!");
    }
}

function handleDeleteQuyDinhResult(result) {
    if (result.success) {
        alert("Xóa quy định thành công!");
        loadLuuDienChung(currentLuuDienChungPage, currentQuyDinhSearch);
    } else {
        alert(result.message || "Có lỗi khi xóa quy định!");
    }
}

// Xử lý dữ liệu Quản lý tài khoản
function handleTaiKhoanData(result) {
    const tableBody = document.getElementById("tai_khoan-table-body");
    tableBody.innerHTML = "";

    if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="6" style="text-align:center;">Không có tài khoản nào được tìm thấy</td>`;
        tableBody.appendChild(row);
        return;
    }

    result.data.forEach(taiKhoan => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${taiKhoan.id_tai_khoan}</td>
            <td>${taiKhoan.ten_dang_nhap}</td>
            <td>${taiKhoan.email || "Không có"}</td>
            <td>${taiKhoan.vai_tro}</td>
            <td>${new Date(taiKhoan.ngay_tao).toLocaleDateString('vi-VN')}</td>
            <td>
                <div class="action-buttons">
                    <button class="edit" onclick="editTaiKhoan(${taiKhoan.id_tai_khoan})">Sửa</button>
                    <button class="delete" onclick="deleteTaiKhoan(${taiKhoan.id_tai_khoan})">Xóa</button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
    updatePagination("tai_khoan-pagination", result.totalPages, result.currentPage, loadTaiKhoan, currentTaiKhoanSearch);
}

function handleAddTaiKhoanResult(result) {
    if (result.success) {
        alert("Thêm tài khoản thành công!");
        closeModal("addTaiKhoanModal");
        loadTaiKhoan(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm tài khoản!");
    }
}

function handleUpdateTaiKhoanResult(result) {
    if (result.success) {
        alert("Cập nhật tài khoản thành công!");
        closeModal("editTaiKhoanModal");
        loadTaiKhoan(currentTaiKhoanPage, currentTaiKhoanSearch);
    } else {
        alert(result.message || "Có lỗi khi cập nhật tài khoản!");
    }
}

function handleDeleteTaiKhoanResult(result) {
    if (result.success) {
        alert("Xóa tài khoản thành công!");
        loadTaiKhoan(currentTaiKhoanPage, currentTaiKhoanSearch);
    } else {
        alert(result.message || "Có lỗi khi xóa tài khoản!");
    }
}

// Xử lý dữ liệu Quản lý phòng trọ
function handleRoomsData(result) {
    const tableBody = document.getElementById("roomTableBody");
    tableBody.innerHTML = "";

    if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="6" style="text-align:center;">Không có phòng trọ nào được tìm thấy</td>`;
        tableBody.appendChild(row);
        return;
    }

    result.data.forEach(room => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${room.id_phong}</td>
            <td>${room.so_phong}</td>
            <td>${room.dia_chi || "Chưa xác định"}</td>
            <td>${room.gia_thue ? Number(room.gia_thue).toLocaleString('vi-VN') : "0"} VNĐ</td>
            <td>${room.trang_thai === "trong" ? "Trống" : "Đã thuê"}</td>
            <td>
                <div class="action-buttons">
                    <button class="edit" onclick="editRoom(${room.id_phong})">Sửa</button>
                    <button class="delete" onclick="deleteRoom(${room.id_phong})">Xóa</button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
    updatePagination("phong_tro-pagination", Math.ceil(result.total / result.limit), result.page, loadRooms, document.getElementById("search-phong_tro").value);
}

// Sửa hàm handleEditRoomData
function handleEditRoomData(room) {
    const editRoomId = document.getElementById("editRoomId");
    const editRoomSoPhong = document.getElementById("editRoomSoPhong");
    const editRoomKhuTro = document.getElementById("editRoomKhuTro");
    const editRoomPrice = document.getElementById("editRoomPrice");
    const editRoomStatus = document.getElementById("editRoomStatus");

    if (!editRoomId || !editRoomSoPhong || !editRoomKhuTro || !editRoomPrice || !editRoomStatus) {
        console.error("Một hoặc nhiều phần tử modal không tồn tại!");
        return;
    }

    editRoomId.value = room.id_phong || "";
    editRoomSoPhong.value = room.so_phong || "";
    editRoomKhuTro.value = room.id_khu_tro || "";
    editRoomPrice.value = room.gia_thue || "";
    editRoomStatus.value = room.trang_thai || "trong";
}

// Sửa hàm handleUpdateRoomResult
function handleUpdateRoomResult(result) {
    if (result.success) {
        alert("Cập nhật phòng thành công!");
        closeModal("editRoomModal");
        loadRooms(currentRoomPage, currentRoomSearch); // Sửa từ loadPhongTro thành loadRooms
    } else {
        alert(result.message || "Có lỗi khi cập nhật phòng!");
    }
}

// Sửa hàm handleAddRoomResult
function handleAddRoomResult(result) {
    if (result.success) {
        alert("Thêm phòng thành công!");
        closeModal("addPhongTroModal"); // Sửa từ addRoomModal thành addPhongTroModal
        loadRooms(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm phòng!");
    }
}

function handleDeleteRoomResult(result) {
    if (result.success) {
        alert("Xóa phòng thành công!");
        loadRooms();
    } else {
        alert(result.message || "Có lỗi khi xóa phòng!");
    }
}

// Xử lý dữ liệu Quản lý khách thuê
function handleTenantsData(result) {
    const tbody = document.getElementById("khach_thue-table-body");
    tbody.innerHTML = "";

    if (result.success && Array.isArray(result.data)) {
        result.data.forEach(tenant => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${tenant.ho_ten || ''}</td>
                <td>${tenant.email || ''}</td>
                <td>${tenant.so_dien_thoai || ''}</td>
                <td>${tenant.so_cccd || ''}</td>
                <td>${tenant.ngay_sinh || ''}</td>
                <td>${tenant.gioi_tinh || ''}</td>
                <td>${tenant.ngay_bat_dau_thue || ''}</td>
                <td>${tenant.phong_tro || 'Chưa có phòng'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit" onclick="editTenant(${tenant.id_khach_thue})">Sửa</button>
                        <button class="delete" onclick="deleteTenant(${tenant.id_khach_thue})">Xóa</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        updatePagination("khach_thue-pagination", result.totalPages, result.currentPage, loadTenants, currentTenantSearch);
    } else {
        tbody.innerHTML = "<tr><td colspan='9'>Không có dữ liệu khách thuê.</td></tr>";
    }
}

function handleEditTenantData(result, tenantId) {
    if (result.success) {
        const tenant = result.data[0]; // Lấy phần tử đầu tiên từ mảng data
        if (tenant && tenant.id_khach_thue === parseInt(tenantId)) { // Chuyển tenantId thành số để so sánh
            document.getElementById('editTenantId').value = tenant.id_khach_thue || '';
            document.getElementById('editTenantName').value = tenant.ho_ten || '';
            document.getElementById('editTenantPhone').value = tenant.so_dien_thoai || '';
            document.getElementById('editTenantEmail').value = tenant.email || '';
            document.getElementById('editTenantCCCD').value = tenant.so_cccd || '';
            document.getElementById('editTenantPhongTro').value = tenant.id_phong || '';
            document.getElementById('editTenantNgaySinh').value = tenant.ngay_sinh || ''; // Thêm ngày sinh
            document.getElementById('editTenantGioiTinh').value = tenant.gioi_tinh || ''; // Thêm giới tính
            openModal('editTenantModal');
        } else {
            alert('Không tìm thấy thông tin khách thuê!');
        }
    } else {
        alert(result.message || 'Có lỗi khi lấy thông tin khách thuê!');
    }
}

function handleUpdateTenantResult(result) {
    if (result.success) {
        alert("Cập nhật khách thuê thành công!");
        closeModal("editTenantModal");
        loadTenants(currentTenantPage, currentTenantSearch);
    } else {
        alert(result.message || "Có lỗi khi cập nhật khách thuê!");
    }
}

function handleAddTenantResult(result) {
    if (result.success) {
        alert("Thêm khách thuê thành công!");
        closeModal("addKhachThueModal");
        loadTenants(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm khách thuê!");
    }
}

function handleDeleteTenantResult(result) {
    if (result.success) {
        alert("Xóa khách thuê thành công!");
        loadTenants();
    } else {
        alert(result.message || "Có lỗi khi xóa khách thuê!");
    }
}

// Xử lý dữ liệu Quản lý hóa đơn
function handleAddPaymentData(tenantsData, roomsData) {
    if (tenantsData.success && roomsData.success) {
        const tenantSelect = document.getElementById("addHoaDonTenant");
        if (!tenantSelect) {
            console.error("Element addHoaDonTenant not found!");
            return;
        }
        tenantSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
        tenantsData.data.forEach(tenant => {
            tenantSelect.innerHTML += `<option value="${tenant.id}">${tenant.ho_ten}</option>`;
        });

        const roomSelect = document.getElementById("addHoaDonRoom");
        if (!roomSelect) {
            console.error("Element addHoaDonRoom not found!");
            return;
        }
        roomSelect.innerHTML = '<option value="">Chọn phòng</option>';
        roomsData.data.forEach(room => {
            roomSelect.innerHTML += `<option value="${room.id_phong}">${room.so_phong}</option>`;
        });

        const dateInput = document.getElementById("addHoaDonDate");
        if (dateInput) dateInput.value = "";
        const statusSelect = document.getElementById("addHoaDonStatus");
        if (statusSelect) statusSelect.value = "chua_thanh_toan";

        // Đặt giá trị mặc định cho các trường tiền
        const tienPhong = document.getElementById("addHoaDonTienPhong");
        if (tienPhong) tienPhong.value = "";
        const tienDien = document.getElementById("addHoaDonTienDien");
        if (tienDien) tienDien.value = "0";
        const tienNuoc = document.getElementById("addHoaDonTienNuoc");
        if (tienNuoc) tienNuoc.value = "0";
        const tienInternet = document.getElementById("addHoaDonTienInternet");
        if (tienInternet) tienInternet.value = "0";
        const phiKhac = document.getElementById("addHoaDonPhiKhac");
        if (phiKhac) phiKhac.value = "0";
        const tongTien = document.getElementById("addHoaDonTongTien");
        if (tongTien) tongTien.value = "0";
        const tienRac = document.getElementById("addHoaDonTienRac");
        if (tienRac) phiKhac.value = "0";

        openModal("addHoaDonModal");
    } else {
        alert("Có lỗi khi lấy dữ liệu!");
    }
}

function handleEditPaymentData(result, paymentId) {
    if (result.success) {
        const payment = result.data.find(p => p.id === paymentId);
        if (payment) {
            document.getElementById("editPaymentId").value = payment.id;
            document.getElementById("editPaymentTenant").value = payment.tenant_id;
            document.getElementById("editPaymentAmount").value = payment.so_tien;
            document.getElementById("editPaymentDate").value = payment.ngay_thanh_toan;
            openModal("editPaymentModal");
        } else {
            alert("Không tìm thấy hóa đơn!");
        }
    } else {
        alert(result.message || "Có lỗi khi lấy thông tin hóa đơn!");
    }
}

function handleUpdatePaymentResult(result) {
    if (result.success) {
        alert("Cập nhật hóa đơn thành công!");
        closeModal("editPaymentModal");
        loadPayments(currentHoaDonPage, currentHoaDonSearch);
    } else {
        alert(result.message || "Có lỗi khi cập nhật hóa đơn!");
    }
}

function handleAddPaymentData(tenantsData, roomsData) {
    console.log("Tenants Data:", tenantsData);
    if (tenantsData.success && roomsData.success) {
        const tenantSelect = document.getElementById("addHoaDonTenant");
        if (!tenantSelect) {
            console.error("Element addHoaDonTenant not found!");
            return;
        }
        tenantSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
        tenantsData.data.forEach(tenant => {
            tenantSelect.innerHTML += `<option value="${tenant.id_khach_thue}">${tenant.ho_ten}</option>`; // Sửa từ tenant.id thành tenant.id_khach_thue
        });

        const roomSelect = document.getElementById("addHoaDonRoom");
        if (!roomSelect) {
            console.error("Element addHoaDonRoom not found!");
            return;
        }
        roomSelect.innerHTML = '<option value="">Chọn phòng</option>';
        roomsData.data.forEach(room => {
            roomSelect.innerHTML += `<option value="${room.id_phong}">${room.so_phong}</option>`;
        });

        const dateInput = document.getElementById("addHoaDonDate");
        if (dateInput) dateInput.value = "";
        const statusSelect = document.getElementById("addHoaDonStatus");
        if (statusSelect) statusSelect.value = "chua_thanh_toan";

        const tienPhong = document.getElementById("addHoaDonTienPhong");
        if (tienPhong) tienPhong.value = "";
        const tienDien = document.getElementById("addHoaDonTienDien");
        if (tienDien) tienDien.value = "0";
        const tienNuoc = document.getElementById("addHoaDonTienNuoc");
        if (tienNuoc) tienNuoc.value = "0";
        const tienInternet = document.getElementById("addHoaDonTienInternet");
        if (tienInternet) tienInternet.value = "0";
        const phiKhac = document.getElementById("addHoaDonPhiKhac");
        if (phiKhac) phiKhac.value = "0";
        const tongTien = document.getElementById("addHoaDonTongTien");
        if (tongTien) tongTien.value = "0";

        openModal("addHoaDonModal");
    } else {
        alert("Có lỗi khi lấy dữ liệu!");
    }
}

function handlePaymentsData(result) {
    const tableBody = document.getElementById("hoa_don-table-body");
    tableBody.innerHTML = "";

    if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="7" style="text-align:center;">Không có hóa đơn nào được tìm thấy</td>`;
        tableBody.appendChild(row);
        return;
    }

    result.data.forEach(payment => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${payment.id_hoa_don || ''}</td>
            <td>${payment.ho_ten || ''}</td>
            <td>${payment.so_phong || ''}</td>
            <td>${Number(payment.tong_tien || 0).toLocaleString()} VNĐ</td>
            <td>${payment.ngay_thanh_toan || ''}</td>
            <td>${payment.trang_thai_thanh_toan === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán'}</td>
            <td>
                <div class="action-buttons">
                    <button class="edit" onclick="editPayment(${payment.id_hoa_don || 0})">Sửa</button>
                    <button class="delete" onclick="deletePayment(${payment.id_hoa_don || 0})">Xóa</button>
                    <button class="send-email" onclick="sendInvoiceEmail(${payment.id_hoa_don || 0})">Gửi Email</button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });

    updatePagination("hoa_don-pagination", result.totalPages, result.currentPage, loadPayments, currentHoaDonSearch);
}

function handleAddPaymentResult(result) {
    if (result.success) {
        alert("Thêm hóa đơn thành công!");
        loadPayments();
        closeModal("addHoaDonModal");
        loadPayments(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm hóa đơn!");
    }
}

function handleDeletePaymentResult(result) {
    if (result.success) {
        alert("Xóa hóa đơn thành công!");
        loadPayments();
    } else {
        alert(result.message || "Có lỗi khi xóa hóa đơn!");
    }
}

//Gửi hóa đơn tới email
async function sendInvoiceEmail(id) {
    if (!id || !confirm("Gửi hóa đơn qua email cho khách thuê?")) {
        return;
    }

    try {
        const response = await fetch(`http://localhost/quan_ly_phong_tro/trang_chu_admin/send_invoice_email.php?id=${id}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });
        const result = await response.json(); // Có thể ném lỗi nếu JSON không hợp lệ
        if (result.success) {
            alert("Gửi email thành công!");
        } else {
            alert(result.message || "Gửi email thất bại!");
        }
    } catch (error) {
        console.error("Error sending email:", error);
        alert("Lỗi xử lý phản hồi: " + error.message);
    }
}

// Xử lý dữ liệu Quản lý hợp đồng
function handleContractsData(result) {
    const tableBody = document.getElementById("hop_dong-table-body");
    if (!tableBody) {
        console.error("Element hop_dong-table-body not found!");
        return;
    }
    tableBody.innerHTML = "";

    if (!result || !result.success || !Array.isArray(result.data) || result.data.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Không có hợp đồng nào được tìm thấy</td></tr>`;
        updatePagination("hop_dong-pagination", result.totalPages, result.currentPage, loadContracts, currentHopDongSearch);
        return;
    }

    result.data.forEach(contract => {
        const row = `
            <tr>
                <td>${contract.so_hop_dong || 'N/A'}</td>
                <td>${contract.ho_ten || 'N/A'}</td>
                <td>${contract.so_phong || 'N/A'}</td>
                <td>${contract.ngay_bat_dau || 'N/A'}</td>
                <td>${contract.ngay_ket_thuc || 'N/A'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit" onclick="editContract(${contract.so_hop_dong || 0})">Sửa</button>
                        <button class="delete" onclick="deleteContract(${contract.so_hop_dong || 0})">Xóa</button>
                    </div>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    updatePagination("hop_dong-pagination", result.totalPages || 1, result.currentPage || 1, loadContracts, currentHopDongSearch);
}

function handleEditContractData(contract, contractId) {
    console.log("Rendering edit form for contract:", contract, "ID:", contractId); // Debug 1
    if (!contract || !contractId) {
        console.error("Invalid contract or contractId:", contract, contractId);
        alert("Dữ liệu hợp đồng không hợp lệ!");
        return;
    }

    // Lấy modal và content
    const modal = document.getElementById('editContractModal');
    const modalContent = modal.querySelector('.modal-content');

    // Tạo form HTML
    modalContent.innerHTML = `
        <h3>Chỉnh sửa hợp đồng ID: ${contractId}</h3>
        <form id="editContractForm">
            <input type="hidden" name="id_hop_dong" value="${contractId}">
            <label>Họ tên: <input type="text" name="ho_ten" value="${contract.ho_ten || ''}" readonly></label><br>
            <label>Số phòng: <input type="text" name="so_phong" value="${contract.so_phong || ''}" readonly></label><br>
            <label>Ngày bắt đầu: <input type="date" name="ngay_bat_dau" value="${contract.ngay_bat_dau || ''}" required></label><br>
            <label>Ngày kết thúc: <input type="date" name="ngay_ket_thuc" value="${contract.ngay_ket_thuc || ''}" required></label><br>
            <label>Trạng thái: <select name="trang_thai" required>
                <option value="con_hieu_luc" ${contract.trang_thai === 'con_hieu_luc' ? 'selected' : ''}>Còn hiệu lực</option>
                <option value="het_hieu_luc" ${contract.trang_thai === 'het_hieu_luc' ? 'selected' : ''}>Hết hiệu lực</option>
            </select></label><br>
            <label>Nội dung: <textarea name="noi_dung">${contract.noi_dung || ''}</textarea></label><br>
            <button type="submit">Lưu thay đổi</button>
            <button type="button" onclick="closeModal('editContractModal')">Hủy</button>
        </form>
    `;

    // Mở modal
    modal.classList.add('active');
    

    // Gỡ bỏ sự kiện cũ từ form hiện tại
    const form = document.getElementById('editContractForm');
    if (form) {
        const oldSubmit = form.querySelector('button[type="submit"]');
        if (oldSubmit && oldSubmit._submitListener) {
            form.removeEventListener('submit', oldSubmit._submitListener);
        }
    }

    // Gắn sự kiện mới
    const submitHandler = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        console.log("Form data sent:", data);

        try {
            const response = await fetch("http://localhost/quan_ly_phong_tro/trang_chu_admin/update_contract.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                alert("Cập nhật hợp đồng thành công!");
                closeModal('editContractModal');
                loadContracts(); // Tải lại danh sách hợp đồng
            } else {
                alert("Cập nhật thất bại: " + result.message);
            }
        } catch (error) {
            console.error("Error updating contract:", error);
            alert("Lỗi khi cập nhật hợp đồng: " + error.message);
        }
    };

    if (form) {
        form.addEventListener('submit', submitHandler);
        const newSubmit = form.querySelector('button[type="submit"]');
        if (newSubmit) {
            newSubmit._submitListener = submitHandler;
        }
    }
}

function handleUpdateContractResult(result) {
    if (result.success) {
        alert("Cập nhật hợp đồng thành công!");
        closeModal("editContractModal");
        loadContracts(currentHopDongPage, currentHopDongSearch);
    } else {
        alert(result.message || "Có lỗi khi cập nhật hợp đồng!");
    }
}

function handleAddContractData(tenantsData, roomsData) {
    if (tenantsData.success && roomsData.success) {
        const tenantSelect = document.getElementById("addHopDongKhachThue");
        tenantSelect.innerHTML = '<option value="">Chọn khách thuê</option>';
        tenantsData.data.forEach(tenant => {
            tenantSelect.innerHTML += `<option value="${tenant.id_khach_thue}">${tenant.ho_ten}</option>`;
        });

        const roomSelect = document.getElementById("addHopDongPhongTro");
        roomSelect.innerHTML = '<option value="">Chọn phòng trọ</option>';
        roomsData.data.forEach(room => {
            roomSelect.innerHTML += `<option value="${room.id_phong}">${room.so_phong}</option>`;
        });

        document.getElementById("addHopDongStartDate").value = "";
        document.getElementById("addHopDongEndDate").value = "";
        openModal("addHopDongModal");
    } else {
        alert("Có lỗi khi lấy dữ liệu khách thuê hoặc phòng trọ!");
    }
}

function handleAddContractResult(result) {
    if (result.success) {
        alert("Thêm hợp đồng thành công!");
        closeModal("addHopDongModal");
        loadContracts(1, "");
    } else {
        alert(result.message || "Có lỗi khi thêm hợp đồng!");
    }
}

function handleDeleteContractResult(result) {
    if (result.success) {
        alert("Xóa hợp đồng thành công!");
        loadContracts();
    } else {
        alert(result.message || "Có lỗi khi xóa hợp đồng!");
    }
}

// Xử lý đăng xuất
function handleLogoutResult(result) {
    if (result.success) {
        alert("Đăng xuất thành công!");
        window.location.href = "http://localhost/quan_ly_phong_tro/trang_chu_khach/login.php";
    } else {
        alert(result.message || "Đăng xuất thất bại, vui lòng thử lại!");
    }
}

//Truyền trực tiếp hàm tải dữ liệu (updatePagination trả về chuỗi giá trị input ko phải hàm nên lỗi) 
// Hàm cập nhật phân trang
function updatePagination(paginationId, totalPages, currentPage, loadFunction, search = "") {
    console.log("loadFunction:", loadFunction, "totalPages:", totalPages, "currentPage:", currentPage, "search:", search);
    const pagination = document.getElementById(paginationId);
    if (!pagination) {
        console.error(`Phần tử với ID '${paginationId}' không tồn tại trong DOM.`);
        alert(`Không thể tải phân trang cho '${paginationId}'. Vui lòng kiểm tra lại!`);
        return;
    }

    // Kiểm tra loadFunction, hỗ trợ cả chuỗi tên hàm
    let funcToCall;
    if (typeof loadFunction === 'function') {
        funcToCall = loadFunction;
    } else if (typeof loadFunction === 'string' && window[loadFunction]) {
        funcToCall = window[loadFunction];
    } else {
        console.error(`loadFunction không phải là hàm hợp lệ: ${loadFunction}`);
        alert('Lỗi: Hàm tải dữ liệu không hợp lệ!');
        return;
    }

    pagination.innerHTML = "";

    search = search !== undefined && search !== null ? String(search) : "";

    const maxPagesToShow = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

    if (endPage - startPage < maxPagesToShow - 1) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }

    if (currentPage > 1) {
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Trước';
        prevButton.addEventListener('click', () => funcToCall(currentPage - 1, search));
        pagination.appendChild(prevButton);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        if (i === currentPage) pageButton.classList.add('active');
        pageButton.addEventListener('click', () => funcToCall(i, search));
        pagination.appendChild(pageButton);
    }

    if (currentPage < totalPages) {
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Sau';
        nextButton.addEventListener('click', () => funcToCall(currentPage + 1, search));
        pagination.appendChild(nextButton);
    }
}