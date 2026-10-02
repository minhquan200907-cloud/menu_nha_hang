<?php
class Router {
    public function handleRequest() {
        $controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'MenuController';
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';

        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    die("Không tìm thấy action: " . $action);
                }
            } else {
                die("Không tìm thấy class: " . $controllerName);
            }
        } else {
            die("Không tìm thấy Controller: " . $controllerName);
        }
    }
}
?>