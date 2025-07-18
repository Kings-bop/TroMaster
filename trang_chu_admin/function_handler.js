async function fetchTenants() {
    try {
        const response = await fetch("admin/get_tenants.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" })
        });

        const result = await response.json();
        if (!result.success) throw new Error(result.message);

        displayTenants(result.data);
    } catch (error) {
        console.error("Lỗi khi lấy danh sách khách thuê:", error);
        document.getElementById("tenantList").innerHTML = `<p>Lỗi: ${error.message}</p>`;
    }
}

function displayTenants(tenants) {
    const list = document.getElementById("tenantList");
    list.innerHTML = "";
    tenants.forEach(t => {
        const item = document.createElement("div");
        item.classList.add("tenant-card");
        item.innerHTML = `
            <h4>${t.ho_ten}</h4>
            <p>SĐT: ${t.so_dien_thoai}</p>
            <p>Email: ${t.email || "Không có"}</p>
            <button onclick="editTenant(${t.id_khach_thue})">Chỉnh sửa</button>
            <button onclick="deleteTenant(${t.id_khach_thue})">Xóa</button>
        `;
        list.appendChild(item);
    });
}

async function fetchContracts() {
    try {
        const response = await fetch("admin/get_contracts.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" })
        });

        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        displayContracts(result.data);
    } catch (error) {
        console.error("Lỗi khi lấy hợp đồng:", error);
        document.getElementById("contractList").innerHTML = `<p>${error.message}</p>`;
    }
}

function displayContracts(contracts) {
    const list = document.getElementById("contractList");
    list.innerHTML = "";

    contracts.forEach(c => {
        const item = document.createElement("div");
        item.classList.add("contract-card");
        item.innerHTML = `
            <h4>Hợp đồng ID ${c.id_hop_dong}</h4>
            <p>Khách thuê: ${c.ho_ten}</p>
            <p>Phòng: ${c.so_phong}</p>
            <p>Ngày ký: ${c.ngay_ky}</p>
            <p>Ngày hết hạn: ${c.ngay_het_han}</p>
            <p>Trạng thái: ${c.trang_thai}</p>
        `;
        list.appendChild(item);
    });
}

async function fetchPayments() {
    try {
        const response = await fetch("admin/get_payments.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ page: 1, limit: 100, search: "" })
        });

        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        displayPayments(result.data);
    } catch (error) {
        console.error("Lỗi khi lấy danh sách hóa đơn:", error);
        document.getElementById("paymentList").innerHTML = `<p>${error.message}</p>`;
    }
}

function displayPayments(payments) {
    const list = document.getElementById("paymentList");
    list.innerHTML = "";

    payments.forEach(p => {
        const item = document.createElement("div");
        item.classList.add("payment-card");
        item.innerHTML = `
            <h4>Hóa đơn tháng ${p.thang_ap_dung}</h4>
            <p>Khách thuê: ${p.ho_ten}</p>
            <p>Phòng: ${p.so_phong}</p>
            <p>Tổng tiền: ${p.tong_tien} VNĐ</p>
            <p>Trạng thái: ${p.trang_thai_thanh_toan}</p>
        `;
        list.appendChild(item);
    });
}