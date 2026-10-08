<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Kiểm tra vai trò của người dùng hiện tại
$userRole = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'staff';
$isAdmin = ($userRole === 'admin');
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    :root {
        --primary-color: #3b82f6;
        --primary-hover: #2563eb;
        --primary-light: #eff6ff;
        --danger-color: #ef4444;
        --danger-bg: #fef2f2;
        --success-color: #10b981;
        --success-bg: #ecfdf5;
        --warning-color: #f59e0b;
        --warning-bg: #fffbeb;
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: var(--bg-main);
        color: var(--text-primary);
        margin: 0;
        padding: 32px 20px;
        line-height: 1.5;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 24px;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    /* Form Card Styles */
    fieldset.order-card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 28px;
        margin: 0 0 32px 0;
        background: var(--card-bg);
        box-shadow: var(--shadow-md);
    }

    legend.order-legend {
        font-weight: 700;
        color: var(--primary-color);
        padding: 0 12px;
        font-size: 1.1rem;
        letter-spacing: -0.01em;
    }

    .form-row {
        display: flex;
        gap: 24px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .form-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    input[type="text"],
    input[type="number"] {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 0.95rem;
        outline: none;
        background-color: #fff;
        color: var(--text-primary);
        transition: all 0.2s ease;
    }

    input[type="text"]:focus,
    input[type="number"]:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    /* Section danh sách món ăn */
    .menu-selection-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 14px;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
        background: #f1f5f9;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .menu-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .menu-item-row:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Style cho món đã hết hàng */
    .menu-item-row.out-of-stock {
        background-color: #fafafa;
        border-color: #fee2e2;
        opacity: 0.75;
    }

    .menu-item-info {
        font-size: 0.925rem;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .menu-item-info strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .badge-out-stock {
        display: inline-block;
        color: var(--danger-color);
        font-size: 0.7rem;
        font-weight: 700;
        background: var(--danger-bg);
        padding: 2px 8px;
        border-radius: 6px;
        margin-left: 6px;
        text-transform: uppercase;
    }

    .menu-item-price {
        color: var(--text-secondary);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .input-qty {
        width: 60px;
        text-align: center;
        font-weight: 600;
    }

    .input-qty:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: #ffffff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
    }

    /* Thanh Tìm Kiếm Đơn Hàng */
    .search-box-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-input-group {
        position: relative;
        flex: 1;
        max-width: 360px;
    }

    .search-input-group input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        box-sizing: border-box;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
        display: flex;
        align-items: center;
    }

    /* Table Styles */
    .table-container {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    table.custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    table.custom-table th, 
    table.custom-table td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    table.custom-table th {
        background-color: #f8fafc;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    table.custom-table tr:last-child td {
        border-bottom: none;
    }

    table.custom-table tr:hover {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        font-size: 0.775rem;
        font-weight: 700;
        border-radius: 9999px;
    }

    .badge-completed {
        background-color: var(--success-bg);
        color: var(--success-color);
        border: 1px solid #a7f3d0;
    }

    .badge-pending {
        background-color: var(--warning-bg);
        color: var(--warning-color);
        border: 1px solid #fde68a;
    }

    /* Thao Tác thành dạng Button/Thẻ Khối */
    .action-links {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        line-height: 1.2;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .action-view {
        background-color: #f0f7ff;
        color: #0284c7;
        border-color: #bae6fd !important;
    }

    .action-view:hover {
        background-color: #0284c7;
        color: #ffffff;
        border-color: #0284c7 !important;
        text-decoration: none !important;
    }

    .action-add {
        background-color: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0 !important;
    }

    .action-add:hover {
        background-color: #059669;
        color: #ffffff;
        border-color: #059669 !important;
        text-decoration: none !important;
    }

    .action-pay {
        background-color: #fffbeb;
        color: #d97706;
        border-color: #fde68a !important;
    }

    .action-pay:hover {
        background-color: #d97706;
        color: #ffffff;
        border-color: #d97706 !important;
        text-decoration: none !important;
    }

    .action-delete {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca !important;
    }

    .action-delete:hover {
        background-color: #dc2626;
        color: #ffffff;
        border-color: #dc2626 !important;
        text-decoration: none !important;
    }
</style>

<div class="container">
    <h2 class="page-title">Quản Lý Đặt Món & Đơn Hàng</h2>

    <!-- Form Tạo Đơn Hàng / Gọi Thêm Món -->
    <fieldset class="order-card">
        <legend class="order-legend">Tạo Đơn Hàng / Gọi Thêm Món</legend>
        <form action="index.php?controller=order&sub_action=create" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Số bàn:</label>
                    <input type="number" id="table_number" name="table_number" value="1" min="1" required style="width: 80px;">
                </div>
                <div class="form-group">
                    <label>Tên khách hàng:</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="Tùy chọn">
                </div>
            </div>

            <div class="menu-selection-title">Chọn món ăn:</div>
            <div class="menu-grid">
                <?php if (!empty($menuItems)): ?>
                    <?php foreach ($menuItems as $item): ?>
                        <?php 
                            $itemArr = is_object($item) ? (array)$item : $item; 
                            $status = $itemArr['status'] ?? 'Sẵn sàng';
                            $isAvailable = ($status === 'Sẵn sàng' || $status === 'available' || $status == 1);
                        ?>
                        <div class="menu-item-row <?= !$isAvailable ? 'out-of-stock' : '' ?>">
                            <div class="menu-item-info">
                                <strong><?= htmlspecialchars($itemArr['name'] ?? '') ?></strong>
                                <?php if (!$isAvailable): ?>
                                    <span class="badge-out-stock">Hết hàng</span>
                                <?php endif; ?>
                                <div class="menu-item-price"><?= number_format($itemArr['price'] ?? 0) ?> đ</div>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 0.8rem;">SL:</label>
                                <input type="number" 
                                       name="items[<?= $itemArr['id'] ?>]" 
                                       value="0" 
                                       min="0" 
                                       class="input-qty" 
                                       <?= !$isAvailable ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #6c757d;">Không có món ăn nào trong thực đơn!</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Xác Nhận Đặt Món</button>
        </form>
    </fieldset>

    <!-- Thanh Tìm Kiếm Đơn Hàng -->
    <div class="search-box-wrapper">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-primary);">Danh Sách Đơn Hàng</h3>
        <div class="search-input-group">
            <span class="search-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" id="search_table_input" onkeyup="filterOrders()" placeholder="Tìm bàn (vd: Bàn 1), tên khách...">
        </div>
    </div>

    <!-- Bảng Danh Sách Đơn Hàng -->
    <div class="table-container">
        <table class="custom-table" id="orders_table">
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Bàn</th>
                    <th>Khách Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Thời Gian</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php 
                            $orderArr = is_object($order) ? (array)$order : $order;
                            $status = $orderArr['status'] ?? 'pending';
                            $isCompleted = ($status === 'completed' || $status === 'Đã thanh toán' || $status === 'Hoàn Thành');
                            $tableNo = $orderArr['table_number'] ?? 1;
                            $customerName = htmlspecialchars($orderArr['customer_name'] ?? 'Khách lẻ', ENT_QUOTES);
                        ?>
                        <tr>
                            <td><strong>#<?= $orderArr['id'] ?? '' ?></strong></td>
                            <td>Bàn <?= htmlspecialchars($tableNo) ?></td>
                            <td><?= $customerName ?></td>
                            <td><strong><?= number_format($orderArr['total_amount'] ?? 0) ?> đ</strong></td>
                            <td>
                                <span class="badge-status <?= $isCompleted ? 'badge-completed' : 'badge-pending' ?>">
                                    <?= $isCompleted ? 'Hoàn thành' : 'Chờ xử lý' ?>
                                </span>
                            </td>
                            <td><?= $orderArr['created_at'] ?? $orderArr['order_date'] ?? '' ?></td>
                            <td class="action-links">
                                <a href="index.php?controller=order&action=detail&id=<?= $orderArr['id'] ?>" class="action-view">Xem Chi Tiết</a>
                                
                                <?php if (!$isCompleted): ?>
                                    <a href="javascript:void(0);" onclick="setTableFocus(<?= $tableNo ?>, '<?= $customerName ?>')" class="action-add">+ Gọi Thêm</a>
                                <?php endif; ?>

                                <?php if ($isAdmin): ?>
                                    <?php if (!$isCompleted): ?>
                                        <a href="index.php?controller=order&sub_action=update_status&id=<?= $orderArr['id'] ?>&status=completed" class="action-pay">Đã Thanh Toán</a>
                                    <?php endif; ?>
                                    <a href="index.php?controller=order&sub_action=delete&id=<?= $orderArr['id'] ?>" class="action-delete" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">Xóa</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="no_orders_row">
                        <td colspan="7" style="text-align: center; color: #6c757d; padding: 24px;">Chưa có đơn hàng nào!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function setTableFocus(tableNumber, customerName) {
    var tableInput = document.getElementById('table_number');
    var customerInput = document.getElementById('customer_name');
    
    if (tableInput) {
        tableInput.value = tableNumber;
    }
    
    if (customerInput) {
        customerInput.value = (customerName && customerName !== 'Khách lẻ') ? customerName : '';
    }
    
    if (tableInput) {
        tableInput.focus();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Hàm lọc bàn / tên khách / mã đơn theo thời gian thực
function filterOrders() {
    var input = document.getElementById("search_table_input");
    var filter = input.value.toLowerCase().trim();
    var table = document.getElementById("orders_table");
    var trs = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    for (var i = 0; i < trs.length; i++) {
        // Bỏ qua dòng thông báo trống gốc nếu có
        if (trs[i].id === "no_orders_row") continue;

        var orderId = trs[i].getElementsByTagName("td")[0]?.textContent || "";
        var tableCol = trs[i].getElementsByTagName("td")[1]?.textContent || "";
        var customerCol = trs[i].getElementsByTagName("td")[2]?.textContent || "";

        var textValue = (orderId + " " + tableCol + " " + customerCol).toLowerCase();

        if (textValue.indexOf(filter) > -1) {
            trs[i].style.display = "";
        } else {
            trs[i].style.display = "none";
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>