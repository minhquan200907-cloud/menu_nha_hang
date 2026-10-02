CREATE DATABASE IF NOT EXISTS `menu_nha_hang` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `menu_nha_hang`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'staff') DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('available', 'out_of_stock') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_number` INT NOT NULL,
  `customer_name` VARCHAR(100) DEFAULT NULL,
  `total_amount` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `menu_item_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm tài khoản mẫu (Mật khẩu: 123456)
INSERT INTO `users` (`username`, `password`, `fullname`, `role`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe188942b0p.6G3E8iR.N0E9034sC5k1y', 'Quản trị viên', 'admin'),
('staff1', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe188942b0p.6G3E8iR.N0E9034sC5k1y', 'Nhân viên Phục vụ', 'staff');

-- Thêm món ăn mẫu
INSERT INTO `menu_items` (`name`, `category`, `price`, `status`) VALUES
('Lẩu Thái Hải Sản', 'Lẩu', 250000.00, 'available'),
('Cơm Chiên Dương Châu', 'Món Chính', 65000.00, 'available'),
('Bò Lúc Lắc', 'Món Chính', 120000.00, 'available'),
('Trà Đá', 'Đồ Uống', 5000.00, 'available');