<?php
require_once __DIR__ . '/../../core/Database.php';

class OrderRepository {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        if (method_exists($dbInstance, 'getConnection')) {
            $this->db = $dbInstance->getConnection();
        } else {
            $this->db = $dbInstance;
        }
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra xem bàn có đang có đơn hàng chưa hoàn thành (chờ xử lý) hay không
     */
    public function isTableOccupied($tableNumber) {
        $sql = "SELECT COUNT(*) as count FROM orders 
                WHERE table_number = :table_number 
                AND status NOT IN ('completed', 'Đã thanh toán', 'Hoàn Thành')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':table_number' => $tableNumber]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result['count'] ?? 0) > 0;
    }

    public function getOrderDetails($orderId) {
        try {
            $stmt = $this->db->prepare("
                SELECT od.*, COALESCE(m.name, 'Món đã xóa') as item_name 
                FROM order_details od
                LEFT JOIN menu m ON od.menu_item_id = m.id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $stmt = $this->db->prepare("
                SELECT od.*, COALESCE(m.name, 'Món đã xóa') as item_name 
                FROM order_details od
                LEFT JOIN menu_items m ON od.menu_item_id = m.id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Cộng dồn món ăn mới vào đơn hàng đang mở của bàn
     */
    public function addItemsToExistingOrder($tableNumber, $items) {
        try {
            // 1. Lấy đơn hàng chưa hoàn thành của bàn này
            $stmt = $this->db->prepare("
                SELECT id FROM orders 
                WHERE table_number = ? 
                AND status NOT IN ('completed', 'Đã thanh toán', 'Hoàn Thành') 
                ORDER BY id DESC LIMIT 1
            ");
            $stmt->execute([$tableNumber]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return false;
            }

            $orderId = $order['id'];
            $addedTotal = 0;

            foreach ($items as $item) {
                $menuItemId = $item['menu_item_id'];
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                $itemTotal = $price * $quantity;

                // Kiểm tra xem món này đã có trong đơn hàng chưa
                $stmtCheck = $this->db->prepare("
                    SELECT id, quantity FROM order_details 
                    WHERE order_id = ? AND menu_item_id = ?
                ");
                $stmtCheck->execute([$orderId, $menuItemId]);
                $existingDetail = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existingDetail) {
                    // Món đã có -> Tăng số lượng
                    $newQty = (int)$existingDetail['quantity'] + $quantity;
                    try {
                        $stmtUpdate = $this->db->prepare("
                            UPDATE order_details 
                            SET quantity = ?, total_price = total_price + ? 
                            WHERE id = ?
                        ");
                        $stmtUpdate->execute([$newQty, $itemTotal, $existingDetail['id']]);
                    } catch (PDOException $ex) {
                        // Trường hợp bảng order_details không có cột total_price
                        $stmtUpdate = $this->db->prepare("
                            UPDATE order_details 
                            SET quantity = ? 
                            WHERE id = ?
                        ");
                        $stmtUpdate->execute([$newQty, $existingDetail['id']]);
                    }
                } else {
                    // Món chưa có -> Thêm dòng mới
                    try {
                        $stmtInsert = $this->db->prepare("
                            INSERT INTO order_details (order_id, menu_item_id, quantity, price, total_price) 
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmtInsert->execute([$orderId, $menuItemId, $quantity, $price, $itemTotal]);
                    } catch (PDOException $ex) {
                        // Trường hợp bảng order_details không có cột total_price
                        $stmtInsert = $this->db->prepare("
                            INSERT INTO order_details (order_id, menu_item_id, quantity, price) 
                            VALUES (?, ?, ?, ?)
                        ");
                        $stmtInsert->execute([$orderId, $menuItemId, $quantity, $price]);
                    }
                }

                $addedTotal += $itemTotal;
            }

            // 2. Cập nhật lại tổng tiền đơn hàng
            if ($addedTotal > 0) {
                $stmtUpdateOrder = $this->db->prepare("
                    UPDATE orders 
                    SET total_amount = total_amount + ? 
                    WHERE id = ?
                ");
                $stmtUpdateOrder->execute([$addedTotal, $orderId]);
            }

            return true;
        } catch (PDOException $e) {
            echo "Lỗi CSDL khi gọi thêm món: " . htmlspecialchars($e->getMessage());
            exit();
        }
    }

    public function createOrder($tableNumber, $customerName, $items) {
        try {
            // Lấy user_id từ Session nếu có
            $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;

            // Nếu không tìm thấy trong session, lấy ID người dùng đầu tiên từ bảng users
            if (!$userId) {
                try {
                    $stmtUser = $this->db->query("SELECT id FROM users LIMIT 1");
                    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
                    $userId = $user['id'] ?? null;
                } catch (PDOException $e) {
                    $userId = null;
                }
            }

            // 1. Tính tổng tiền
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += (float)$item['price'] * (int)$item['quantity'];
            }

            // Lấy thời gian hiện tại từ PHP
            $currentTime = date('Y-m-d H:i:s');

            // 2. Thêm đơn hàng
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO orders (user_id, table_number, customer_name, total_amount, status, created_at) 
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $stmt->execute([$userId, $tableNumber, $customerName, $totalAmount, $currentTime]);
            } catch (PDOException $ex) {
                try {
                    // Trường hợp cột tên là order_date thay vì created_at
                    $stmt = $this->db->prepare("
                        INSERT INTO orders (user_id, table_number, customer_name, total_amount, status, order_date) 
                        VALUES (?, ?, ?, ?, 'pending', ?)
                    ");
                    $stmt->execute([$userId, $tableNumber, $customerName, $totalAmount, $currentTime]);
                } catch (PDOException $ex2) {
                    // Trường hợp bảng orders không có cột user_id
                    $stmt = $this->db->prepare("
                        INSERT INTO orders (table_number, customer_name, total_amount, status, created_at) 
                        VALUES (?, ?, ?, 'pending', ?)
                    ");
                    $stmt->execute([$tableNumber, $customerName, $totalAmount, $currentTime]);
                }
            }

            // 3. Lấy ID đơn hàng vừa tạo
            $orderId = $this->db->lastInsertId();

            // 4. Lưu danh sách món chi tiết
            foreach ($items as $item) {
                $price = (float)$item['price'];
                $quantity = (int)$item['quantity'];
                $itemTotal = $price * $quantity;

                try {
                    $stmtDetail = $this->db->prepare("
                        INSERT INTO order_details (order_id, menu_item_id, quantity, price, total_price) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmtDetail->execute([$orderId, $item['menu_item_id'], $quantity, $price, $itemTotal]);
                } catch (PDOException $ex) {
                    $stmtDetail = $this->db->prepare("
                        INSERT INTO order_details (order_id, menu_item_id, quantity, price) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmtDetail->execute([$orderId, $item['menu_item_id'], $quantity, $price]);
                }
            }

            return true;
        } catch (PDOException $e) {
            echo "Lỗi CSDL khi tạo đơn hàng: " . htmlspecialchars($e->getMessage());
            exit();
        }
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmtDetail = $this->db->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmtDetail->execute([$id]);

        $stmtOrder = $this->db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmtOrder->execute([$id]);
    }
}