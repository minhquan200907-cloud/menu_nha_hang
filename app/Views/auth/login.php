<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Ẩn hoàn toàn thanh Header khi chưa đăng nhập */
    .app-header {
        display: none !important;
    }

    :root {
        --primary-color: #0d6efd;
        --primary-hover: #0b5ed7;
        --danger-color: #dc3545;
        --bg-light: #f4f6f9;
        --border-color: #ced4da;
        --text-color: #212529;
        --shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background-color: var(--bg-light);
        color: var(--text-color);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-wrapper {
        width: 100%;
        max-width: 400px;
        padding: 20px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 32px 28px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .login-title {
        font-size: 1.5rem;
        font-weight: 700;
        text-align: center;
        color: #1a1d20;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        font-size: 0.95rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-bottom: 20px;
        border: 1px solid #f5c2c7;
        text-align: center;
    }

    .btn-login {
        width: 100%;
        background-color: var(--primary-color);
        color: #ffffff;
        border: none;
        padding: 11px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        margin-top: 6px;
    }

    .btn-login:hover {
        background-color: var(--primary-hover);
    }

    .btn-login:active {
        transform: scale(0.99);
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <h3 class="login-title">Đăng Nhập Hệ Thống</h3>
        
        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=login" method="POST">
            <div class="form-group">
                <label for="username">Tài khoản:</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên tài khoản..." required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>