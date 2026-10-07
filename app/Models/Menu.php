<?php
class Menu {
    public $id;
    public $name;
    public $category;
    public $price;
    public $status;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->status = $data['status'] ?? 'available';
    }
}
