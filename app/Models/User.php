<?php
class User {
    public $id;
    public $username;
    public $password;
    public $fullname;
    public $role;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->fullname = $data['fullname'] ?? '';
        $this->role = $data['role'] ?? 'staff';
    }
}