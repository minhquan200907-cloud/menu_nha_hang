<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Repositories/OrderRepository.php';
require_once __DIR__ . '/../Repositories/MenuRepository.php';

class OrderController extends Controller {
    private $orderRepo;
    private $menuRepo;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?controller=auth&action=login');
        }
        $this->orderRepo = new OrderRepository();
        $this->menuRepo = new MenuRepository();
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
     * Nhân viên & Admin đều có thể tạo đơn hàng
     */
    public function create() {
        $tableNumber = $_REQUEST['table_number'] ?? 1;
        $customerName = !empty($_REQUEST['customer_name']) ? trim($_REQUEST['customer_name']) : 'Khách tại bàn';
        $selectedItems = $_REQUEST['items'] ?? []; 

        if ($this->orderRepo->isTableOccupied($tableNumber)) {
            echo "<script>
                alert('Bàn " . htmlspecialchars($tableNumber) . " đang có đơn hàng chưa hoàn thành! Vui lòng chọn bàn khác.');
                window.history.back();
            </script>";
            exit();
        }

        $items = [];
        foreach ($selectedItems as $itemId => $qty) {
            $quantity = (int)$qty;
            if ($quantity > 0) {
                $menuItem = $this->menuRepo->getById($itemId);
                if ($menuItem) {
                    $itemData = is_object($menuItem) ? (array)$menuItem : $menuItem;
                    
                    $items[] = [
                        'menu_item_id' => $itemId,
                        'quantity'     => $quantity,
                        'price'        => $itemData['price'] ?? 0
                    ];
                }
            }
        }

        if (empty($items)) {
            echo "<script>alert('Vui lòng chọn số lượng ít nhất 1 món ăn lớn hơn 0!'); window.history.back();</script>";
            exit();
        }

        $this->orderRepo->createOrder($tableNumber, $customerName, $items);

        $this->redirect('index.php?controller=order&action=index');
    }

    /**
     * Chỉ Admin mới được đổi trạng thái đơn hàng (Thanh toán/Hoàn thành)
     */
    public function updateStatus() {
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'admin') {
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
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'admin') {
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