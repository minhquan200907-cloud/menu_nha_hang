<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Kiểm tra vai trò của người dùng hiện tại
$userRole = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'staff';
$isAdmin = ($userRole === 'admin');
?>

<style>
    :root {
        --primary-color: #0d6efd;
        --primary-hover: #0b5ed7;
        --danger-color: #dc3545;
        --success-color: #198754;
        --warning-color: #ffc107;
        --bg-light: #f8f9fa;
        --border-color: #dee2e6;
        --text-color: #212529;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: var(--bg-light);
        color: var(--text-color);
        margin: 0;
        padding: 20px;
        line-height: 1.5;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1a1d20;
    }

    /* Form Card Styles */
    fieldset.order-card {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px 24px;
        margin: 0 0 24px 0;
        background: #ffffff;
        box-shadow: var(--shadow);
    }

    legend.order-legend {
        font-weight: 600;
        color: var(--primary-color);
        padding: 0 8px;
        font-size: 1.1rem;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .form-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group label {
        font-size: 0.9rem;
        font-weight: 500;
        color: #495057;
    }

    input[type="text"],
    input[type="number"] {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    /* Section danh sách món ăn */
    .menu-selection-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 12px;
        color: #343a40;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
        background: var(--bg-light);
        padding: 16px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .menu-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }

    .menu-item-info {
        font-size: 0.9rem;
    }

    .menu-item-info strong {
        color: #212529;
    }

    .menu-item-price {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .input-qty {
        width: 60px;
        text-align: center;
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
    }

    /* Table Styles */
    .table-container {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    table.custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    table.custom-table th, 
    table.custom-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
    }

    table.custom-table th {
        background-color: #f1f3f5;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    table.custom-table tr:last-child td {
        border-bottom: none;
    }

    table.custom-table tr:hover {
        background-color: #f8f9fa;
    }

    /* Status Badges */
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
    }

    .badge-completed {
        background-color: #e6f4ea;
        color: var(--success-color);
    }

    .badge-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    /* Action Links */
    .action-links a {
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        margin-right: 8px;
    }

    .action-view {
        color: var(--primary-color);
    }

    .action-pay {
        color: var(--success-color);
    }

    .action-delete {
        color: var(--danger-color);
    }

    .action-links a:hover {
        text-decoration: underline;
    }
</style>

<div class="container">
    <h2 class="page-title">Quản Lý Đặt Món & Đơn Hàng</h2>

    <!-- Form Tạo Đơn Hàng Mới -->
    <fieldset class="order-card">
        <legend class="order-legend">Tạo Đơn Hàng Mới</legend>
        <form action="index.php?controller=order&action=create" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Số bàn:</label>
                    <input type="number" name="table_number" value="1" min="1" required style="width: 80px;">
                </div>
                <div class="form-group">
                    <label>Tên khách hàng:</label>
                    <input type="text" name="customer_name" placeholder="Tùy chọn">
                </div>
            </div>

            <div class="menu-selection-title">Chọn món ăn:</div>
            <div class="menu-grid">
                <?php if (!empty($menuItems)): ?>
                    <?php foreach ($menuItems as $item): ?>
                        <?php $itemArr = is_object($item) ? (array)$item : $item; ?>
                        <div class="menu-item-row">
                            <div class="menu-item-info">
                                <strong><?= htmlspecialchars($itemArr['name'] ?? '') ?></strong>
                                <div class="menu-item-price"><?= number_format($itemArr['price'] ?? 0) ?> đ</div>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 0.8rem;">SL:</label>
                                <input type="number" name="items[<?= $itemArr['id'] ?>]" value="0" min="0" class="input-qty">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #6c757d;">Không có món ăn nào trong thực đơn!</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Tạo Đơn Hàng</button>
        </form>
    </fieldset>

    <!-- Bảng Danh Sách Đơn Hàng -->
    <div class="table-container">
        <table class="custom-table">
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
                        ?>
                        <tr>
                            <td><strong>#<?= $orderArr['id'] ?? '' ?></strong></td>
                            <td>Bàn <?= htmlspecialchars($orderArr['table_number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($orderArr['customer_name'] ?? 'Khách lẻ') ?></td>
                            <td><strong><?= number_format($orderArr['total_amount'] ?? 0) ?> đ</strong></td>
                            <td>
                                <span class="badge-status <?= $isCompleted ? 'badge-completed' : 'badge-pending' ?>">
                                    <?= $isCompleted ? 'Hoàn thành' : 'Chờ xử lý' ?>
                                </span>
                            </td>
                            <td><?= $orderArr['created_at'] ?? $orderArr['order_date'] ?? '' ?></td>
                            <td class="action-links">
                                <!-- Ai cũng xem được chi tiết đơn hàng -->
                                <a href="index.php?controller=order&action=detail&id=<?= $orderArr['id'] ?>" class="action-view">Xem Chi Tiết</a>
                                
                                <!-- Chỉ ADMIN mới có quyền Thanh Toán và Xóa -->
                                <?php if ($isAdmin): ?>
                                    <?php if (!$isCompleted): ?>
                                        <a href="index.php?controller=order&action=updateStatus&id=<?= $orderArr['id'] ?>&status=completed" class="action-pay">Đã Thanh Toán</a>
                                    <?php endif; ?>
                                    <a href="index.php?controller=order&action=delete&id=<?= $orderArr['id'] ?>" class="action-delete" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">Xóa</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6c757d; padding: 24px;">Chưa có đơn hàng nào!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>