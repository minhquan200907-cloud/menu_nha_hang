<?php
require_once __DIR__ . '/../../core/Database.php';

class MenuRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM menu_items ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function save($data) {
        if (!empty($data['id'])) {
            // Cập nhật món ăn
            $stmt = $this->db->prepare("UPDATE menu_items SET name = ?, category = ?, price = ?, status = ? WHERE id = ?");
            return $stmt->execute([
                $data['name'],
                $data['category'],
                $data['price'],
                $data['status'] ?? 'available',
                $data['id']
            ]);
        } else {
            // Thêm món ăn mới
            $stmt = $this->db->prepare("INSERT INTO menu_items (name, category, price, status) VALUES (?, ?, ?, ?)");
            return $stmt->execute([
                $data['name'],
                $data['category'],
                $data['price'],
                $data['status'] ?? 'available'
            ]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
}