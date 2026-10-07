<?php
class Order {
    public $id;
    public $table_number;
    public $customer_name;
    public $total_amount;
    public $status;
    public $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->table_number = $data['table_number'] ?? 0;
        $this->customer_name = $data['customer_name'] ?? '';
        $this->total_amount = $data['total_amount'] ?? 0;
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? null;
    }
}