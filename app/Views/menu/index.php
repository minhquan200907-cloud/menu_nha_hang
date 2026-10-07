<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$userRole = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'staff';
$isAdmin = ($userRole === 'admin');
?>

<style>
    :root {
        --primary-color: #0d6efd;
        --primary-hover: #0b5ed7;
        --danger-color: #dc3545;
        --success-color: #198754;
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
    fieldset.menu-card {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px 24px;
        margin: 0 0 24px 0;
        background: #ffffff;
        box-shadow: var(--shadow);
    }

    legend.menu-legend {
        font-weight: 600;
        color: var(--primary-color);
        padding: 0 8px;
        font-size: 1.1rem;
    }

    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
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
    input[type="number"],
    select {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
    }

    .btn-cancel {
        color: #6c757d;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .btn-cancel:hover {
        background-color: #e9ecef;
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
        font-size: 0.825rem;
        font-weight: 600;
        border-radius: 50px;
    }

    .badge-available {
        background-color: #e6f4ea;
        color: var(--success-color);
    }

    .badge-unavailable {
        background-color: #fce8e6;
        color: var(--danger-color);
    }

    /* Action Links */
    .action-links a {
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        margin-right: 8px;
    }

    .action-edit {
        color: var(--primary-color);
    }

    .action-edit:hover {
        text-decoration: underline;
    }

    .action-delete {
        color: var(--danger-color);
    }

    .action-delete:hover {
        text-decoration: underline;
    }
</style>

<div class="container">
    <h2 class="page-title">Quản Lý Thực Đơn</h2>

    <!-- Chỉ Admin mới nhìn thấy Form Thêm / Sửa món ăn -->
    <?php if ($isAdmin): ?>
    <fieldset class="menu-card">
        <legend class="menu-legend"><?= isset($editItem) ? 'Sửa Món Ăn' : 'Thêm Món Ăn Mới' ?></legend>
        <form action="index.php" method="GET" class="form-grid">
            <input type="hidden" name="controller" value="menu">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="sub_action" value="save">
            <?php if (isset($editItem)): ?>
                <?php $editArr = is_object($editItem) ? (array)$editItem : $editItem; ?>
                <input type="hidden" name="id" value="<?= $editArr['id'] ?? '' ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Tên món:</label>
                <input type="text" name="name" value="<?= isset($editArr) ? htmlspecialchars($editArr['name'] ?? '') : '' ?>" required placeholder="Nhập tên món">
            </div>

            <div class="form-group">
                <label>Danh mục:</label>
                <input type="text" name="category" value="<?= isset($editArr) ? htmlspecialchars($editArr['category'] ?? '') : '' ?>" required placeholder="Nhập danh mục">
            </div>

            <div class="form-group">
                <label>Giá:</label>
                <input type="number" name="price" value="<?= isset($editArr) ? $editArr['price'] ?? '' : '' ?>" required placeholder="0">
            </div>
<div class="form-group">
                <label>Trạng thái:</label>
                <select name="status">
                    <?php $currentStatus = $editArr['status'] ?? 'available'; ?>
                    <option value="available" <?= ($currentStatus === 'available' || $currentStatus === 'Sẵn sàng') ? 'selected' : '' ?>>Sẵn sàng</option>
                    <option value="unavailable" <?= ($currentStatus === 'unavailable' || $currentStatus === 'Hết hàng') ? 'selected' : '' ?>>Hết hàng</option>
                </select>
            </div>

            <button type="submit" class="btn-submit"><?= isset($editItem) ? 'Cập Nhật' : 'Thêm Mới' ?></button>
            <?php if (isset($editItem)): ?>
                <a href="index.php?controller=menu&action=index" class="btn-cancel">Hủy</a>
            <?php endif; ?>
        </form>
    </fieldset>
    <?php endif; ?>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tên Món</th>
                    <th>Danh Mục</th>
                    <th>Giá (VNĐ)</th>
                    <th>Trạng Thái</th>
                    <?php if ($isAdmin): ?>
                        <th>Thao Tác</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <?php 
                            $row = is_object($item) ? (array)$item : $item;
                            $status = $row['status'] ?? 'available';
                            $isAvailable = ($status === 'available' || $status === 'Sẵn sàng' || $status == 1);
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['name'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($row['category'] ?? '') ?></td>
                            <td><?= number_format($row['price'] ?? 0) ?></td>
                            <td>
                                <span class="badge-status <?= $isAvailable ? 'badge-available' : 'badge-unavailable' ?>">
                                    <?= $isAvailable ? 'Sẵn sàng' : 'Hết hàng' ?>
                                </span>
                            </td>
                            <?php if ($isAdmin): ?>
                            <td class="action-links">
                                <a href="index.php?controller=menu&action=index&edit_id=<?= $row['id'] ?>" class="action-edit">Sửa</a>
                                <a href="index.php?controller=menu&action=index&sub_action=delete&id=<?= $row['id'] ?>" class="action-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
<?php else: ?>
                    <tr>
                        <td colspan="<?= $isAdmin ? 5 : 4 ?>" style="text-align: center; color: #6c757d; padding: 24px;">Chưa có món ăn nào!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>