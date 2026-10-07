<?php
class Controller {
    public function view($viewPath, $data = []) {
        extract($data);
        $file = __DIR__ . '/../app/Views/' . $viewPath . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            die("Không tìm thấy View: " . $viewPath);
        }
    }

    public function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit();
    }
}