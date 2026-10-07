<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    :root {
        --primary-color: #0d6efd;
        --border-color: #dee2e6;
        --bg-light: #f8f9fa;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 20px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border-color);
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .btn-back {
        text-decoration: none;
        color: #495057;
        background: #e9ecef;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        transition: background 0.2s;
    }

    .btn-back:hover {
        background: #ced4da;
    }

    table.detail-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    table.detail-table th, 
    table.detail-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    table.detail-table th {
        background-color: #f1f3f5;
        font-weight: 600;
        color: #495057;
    }

    .total-row {
        font-size: 1.1rem;
        font-weight: bold;
        background-color: #f8f9fa;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Chi Tiết Đơn Hàng #<?= htmlspecialchars($orderId ?? $_GET['id'] ?? '') ?></h2>
            <a href="index.php?controller=order&action=index" class="btn-back">&larr; Quay lại danh sách</a>
        </div>

        <table class="detail-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Món Ăn</th>
                    <th>Số Lượng</th>
                    <th>Đơn Giá (VNĐ)</th>
                    <th>Thành Tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                if (!empty($orderDetails)): 
                    foreach ($orderDetails as $index => $item): 
                        $itemArr = is_object($item) ? (array)$item : $item;
                        $price = (float)($itemArr['price'] ?? 0);
                        $qty = (int)($itemArr['quantity'] ?? 0);
                        $subtotal = $price * $qty;
                        $grandTotal += $subtotal;
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= htmlspecialchars($itemArr['name'] ?? $itemArr['item_name'] ?? 'Món ăn') ?></strong></td>
                        <td><?= $qty ?></td>
                        <td><?= number_format($price) ?></td>
                        <td><?= number_format($subtotal) ?></td>
                    </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6c757d; padding: 20px;">Không tìm thấy chi tiết cho đơn hàng này.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($orderDetails)): ?>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Tổng cộng:</td>
                    <td style="color: #0d6efd;"><?= number_format($grandTotal) ?> đ</td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>