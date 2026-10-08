<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Repositories/OrderRepository.php';
require_once __DIR__ . '/../Repositories/MenuRepository.php';

class OrderController extends Controller {
    private $orderRepo;
    private $menuRepo;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?controller=auth&action=login');
            exit();
        }
        $this->orderRepo = new OrderRepository();
        $this->menuRepo = new MenuRepository();
    }

    /**
     * Lấy role người dùng hiện tại an toàn
     */
    private function getUserRole() {
        return $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'staff';
    }

    public function index() {
        $subAction = $_REQUEST['sub_action'] ?? '';
        if ($subAction === 'create') {
            $this->create();
            return;
        } elseif ($subAction === 'update_status') {
            $this->updateStatus();
            return;
        } elseif ($subAction === 'delete') {
            $this->delete();
            return;
        }

        $orders = $this->orderRepo->getAll();
        $menuItems = $this->menuRepo->getAll();

        $this->view('orders/index', [
            'orders'    => $orders,
            'menuItems' => $menuItems
        ]);
    }

    public function detail() {
        $id = $_GET['id'] ?? $_GET['view_id'] ?? null;

        if (!$id) {
            $this->redirect('index.php?controller=order&action=index');
            return;
        }

        $orderDetails = $this->orderRepo->getOrderDetails($id);

        $this->view('orders/detail', [
            'orderId'      => $id,
            'orderDetails' => $orderDetails
        ]);
    }

    /**
     * Nhân viên & Admin tạo đơn mới hoặc gọi thêm món vào bàn đang mở
     */
    public function create() {
        $tableNumber = (int)($_REQUEST['table_number'] ?? 1);
        $customerName = !empty($_REQUEST['customer_name']) ? trim($_REQUEST['customer_name']) : 'Khách lẻ';
        $selectedItems = $_REQUEST['items'] ?? []; 

        $items = [];
        foreach ($selectedItems as $itemId => $qty) {
            $quantity = (int)$qty;
            if ($quantity > 0) {
                $menuItem = $this->menuRepo->getById($itemId);
                if ($menuItem) {
                    $itemData = is_object($menuItem) ? (array)$menuItem : $menuItem;
                    
                    // Kiểm tra trạng thái món ăn
                    $status = $itemData['status'] ?? 'Sẵn sàng';
                    $isAvailable = ($status === 'Sẵn sàng' || $status === 'available' || $status == 1);
                    if (!$isAvailable) {
                        continue; // Bỏ qua món đã hết hàng
                    }

                    $items[] = [
                        'menu_item_id' => $itemId,
                        'quantity'     => $quantity,
                        'price'        => $itemData['price'] ?? 0
                    ];
                }
            }
        }

        if (empty($items)) {
            echo "<script>alert('Vui lòng chọn số lượng ít nhất 1 món ăn còn hàng!'); window.history.back();</script>";
            exit();
        }

        // Kiểm tra bàn có đơn chưa hoàn thành
        $isOccupied = $this->orderRepo->isTableOccupied($tableNumber);

        if ($isOccupied) {
            if (method_exists($this->orderRepo, 'addItemsToExistingOrder')) {
                // Tự động gộp món vào đơn chưa hoàn thành của bàn
                $this->orderRepo->addItemsToExistingOrder($tableNumber, $items);
            } else {
                echo "<script>alert('Bàn " . htmlspecialchars((string)$tableNumber) . " đang có đơn chưa hoàn thành!'); window.history.back();</script>";
                exit();
            }
        } else {
            // Tạo đơn hàng mới nếu bàn trống
            $this->orderRepo->createOrder($tableNumber, $customerName, $items);
        }

        $this->redirect('index.php?controller=order&action=index');
    }

    /**
     * Chỉ Admin mới được đổi trạng thái đơn hàng (Thanh toán/Hoàn thành)
     */
    public function updateStatus() {
        if ($this->getUserRole() !== 'admin') {
            echo "<script>alert('Bạn không có quyền thực hiện thao tác này!'); window.history.back();</script>";
            exit();
        }

        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'completed';

        if ($id) {
            $this->orderRepo->updateStatus($id, $status);
        }

        $this->redirect('index.php?controller=order&action=index');
    }

    /**
     * Chỉ Admin mới được xóa đơn hàng
     */
    public function delete() {
        if ($this->getUserRole() !== 'admin') {
            echo "<script>alert('Bạn không có quyền xóa đơn hàng!'); window.history.back();</script>";
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->orderRepo->delete($id);
        }

        $this->redirect('index.php?controller=order&action=index');
    }
}