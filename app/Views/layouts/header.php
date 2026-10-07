<?php
$userSession = $_SESSION['user'] ?? $_SESSION ?? [];
$userName = $userSession['username'] ?? $userSession['name'] ?? 'Người dùng';
$userRole = $userSession['role'] ?? 'staff';
$isAdmin = ($userRole === 'admin');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhà Hàng</title>
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-hover: #0b5ed7;
            --danger-color: #dc3545;
            --danger-hover: #bb2d3b;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.5;
        }

        /* Top Header / Navigation Bar */
        .app-header {
            background-color: #ffffff;
            box-shadow: var(--shadow);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            margin-bottom: 30px;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background-color: #f1f3f5;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .user-name {
            color: var(--text-dark);
            font-weight: 700;
        }

        .btn-logout {
            text-decoration: none;
            color: var(--danger-color);
            font-weight: 600;
            padding: 6px 14px;
            border: 1px solid #f8d7da;
            background-color: #fce8e6;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background-color: var(--danger-color);
            color: #ffffff;
            border-color: var(--danger-color);
        }
    </style>
</head>
<body>

<header class="app-header">
    <div class="header-container">
        <!-- Bên trái: Điều hướng (Ẩn Quản Lý Thực Đơn đối với nhân viên) -->
        <nav class="nav-links">
            <?php if ($isAdmin): ?>
                <a href="index.php?controller=menu&action=index" class="nav-link">Quản Lý Thực Đơn</a>
            <?php endif; ?>
            <a href="index.php?controller=order&action=index" class="nav-link">Quản Lý Đơn Hàng</a>
        </nav>

        <!-- Bên phải: Thông tin người dùng & Đăng xuất -->
        <div class="user-nav">
            <span>Xin chào, <strong class="user-name"><?= htmlspecialchars($userName) ?></strong></span>
            <a href="index.php?controller=auth&action=logout" class="btn-logout">Đăng Xuất</a>
        </div>
    </div>
</header>