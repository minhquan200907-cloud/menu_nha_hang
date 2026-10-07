<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Router.php';

// Nếu người dùng đã đăng nhập và không truyền controller trên URL
if (isset($_SESSION['user']) && !isset($_GET['controller'])) {
    $role = $_SESSION['user']['role'] ?? 'staff';
    
    // Admin mặc định vào Quản Lý Thực Đơn, Nhân viên mặc định vào Quản Lý Đơn Hàng
    if ($role === 'admin') {
        $_GET['controller'] = 'menu';
    } else {
        $_GET['controller'] = 'order';
    }
    
    if (!isset($_GET['action'])) {
        $_GET['action'] = 'index';
    }
}

$router = new Router();
$router->handleRequest();