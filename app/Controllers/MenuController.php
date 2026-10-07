<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Repositories/MenuRepository.php';

class MenuController extends Controller {
    private $menuRepo;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?controller=auth&action=login');
        }

        // Chặn nhân viên truy cập vào Quản Lý Thực Đơn
        $role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'staff';
        if ($role !== 'admin') {
            echo "<script>
                alert('Bạn không có quyền truy cập vào Quản Lý Thực Đơn!');
                window.location.href = 'index.php?controller=order&action=index';
            </script>";
            exit();
        }

        $this->menuRepo = new MenuRepository();
    }

    public function index() {
        if (isset($_GET['sub_action'])) {
            $subAction = $_GET['sub_action'];

            if ($subAction === 'save') {
                $data = [
                    'id'       => $_GET['id'] ?? null,
                    'name'     => $_GET['name'] ?? '',
                    'category' => $_GET['category'] ?? '',
                    'price'    => $_GET['price'] ?? 0,
                    'status'   => $_GET['status'] ?? 'available'
                ];
                $this->menuRepo->save($data);
                $this->redirect('index.php?controller=menu&action=index');
            } elseif ($subAction === 'delete' && isset($_GET['id'])) {
                $this->menuRepo->delete($_GET['id']);
                $this->redirect('index.php?controller=menu&action=index');
            }
        }

        $editItem = null;
        if (isset($_GET['edit_id'])) {
            $editItem = $this->menuRepo->getById($_GET['edit_id']);
        }

        $items = $this->menuRepo->getAll();
        $this->view('menu/index', ['items' => $items, 'editItem' => $editItem]);
    }
}