-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th4 21, 2025 lúc 07:50 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `mini_muji`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `CARTS`
--

CREATE TABLE `CARTS` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `num_of_products` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` enum('Finished','Unfinished') NOT NULL DEFAULT 'Unfinished'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `CARTS`
--

INSERT INTO `CARTS` (`id`, `user_id`, `num_of_products`, `price`, `status`) VALUES
(1, 1, 2, 1621222, 'Unfinished');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `CART_PRODUCTS`
--

CREATE TABLE `CART_PRODUCTS` (
  `cart_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `CART_PRODUCTS`
--

INSERT INTO `CART_PRODUCTS` (`cart_id`, `products_id`, `quantity`) VALUES
(1, 5, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `CATEGORY`
--

CREATE TABLE `CATEGORY` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `CATEGORY`
--

INSERT INTO `CATEGORY` (`id`, `name`) VALUES
(1, 'Furniture'),
(2, 'Stationery'),
(3, 'Traveling items');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ORDERS`
--

CREATE TABLE `ORDERS` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `status` enum('Waiting','Packing','Delivery','Finished') NOT NULL DEFAULT 'Waiting',
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `PRODUCTS`
--

CREATE TABLE `PRODUCTS` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `PRODUCTS`
--

INSERT INTO `PRODUCTS` (`id`, `name`, `description`, `price`, `image`, `category_id`, `quantity`, `sold`) VALUES
(1, 'Fabric Storage Box Thin', '35x37x16cm', 343000, 'C01001.jpg', 1, 100, 0),
(2, 'Wooden Dining Bench', 'The compact couch can be used for both the living room and dining room.', 2936000, 'C01002.jpg', 1, 50, 0),
(3, 'Pine - Folding Table', 'Pine wood retains the shape of the button, the more it is used, the more aesthetically rich it becomes. Easy to assemble and fold, you can take it out when needed and store it neatly when not in use.', 1963000, 'C01003.jpg', 1, 200, 0),
(4, 'Wooden Chest 4 Drawers', 'The product is made from natural rubber wood. The product is highly applicable, suitable for every bedroom space.', 4899000, 'C01004.jpg', 1, 80, 0),
(5, 'Fregrance Candle', 'Fragile items made of glass, ceramics, and mica', 399000, 'C01005.jpg', 1, 150, 0),
(6, 'Nylon Mesh Pen Case With Gusset', 'Made of lightweight and durable mesh material, allowing easy management and visibility of the contents.', 136000, 'C02001.jpg', 2, 500, 0),
(7, 'Always-on Laptop Case', 'Size A4, approximately 340×255×40mm', 637000, 'C02002.jpg', 2, 300, 0),
(8, 'Highlighter', 'Blue highlighter.', 150000, 'C02003.jpg', 2, 1000, 0),
(9, 'Stainless Steel Left-handed Scissors', 'These are compact, left-handed scissors designed for easy portability and come with a cap for safe storage.', 38000, 'C02004.jpg', 2, 250, 0),
(10, 'Paper - Notebook - 7mm', 'The product has had unnecessary processing and decoration removed. With a simple plain cover and paper size with no distinction between top, bottom, left and right, so you can freely use it as you like..', 38000, 'C02005.jpg', 2, 500, 0),
(11, 'Nylon Pouch Book', 'This is a convenient pouch for organizing small makeup items. It has a book-style design that can be opened.', 274000, 'C03001.jpg', 3, 100, 0),
(12, 'Freely Adjustable Handle Hard Shell Suitcase (75L)', 'The carrying case has a blocking function that allows you to freely adjust the height of the carrying bar. It uses two-wheeled wheels that run smoothly and are easy to use, and use TS (TSA) locks for keys. Recycled materials are used for the interior.', 5890000, 'C03002.jpg', 3, 20, 0),
(13, 'Nylon Pouch with handle', 'Approx. 20.5*10.5*12.5 cm.', 264000, 'C03003.jpg', 3, 500, 0),
(14, 'PET Refill bottle Foam type', 'This is the type of bottle used to create foam, clear 400ml.', 97000, 'C03004.jpg', 3, 200, 0),
(15, 'PET Petit Spray Bottle', 'This is a convenient bottle for portioning out lotions and other liquids. Please press it empty several times until the contents come out. Heat resistant: 60°C, cold resistant: -20°C. Capacity: 50ml.', 68000, 'C03005.jpg', 3, 300, 0),
(17, 'fwf', 'hhhh', 800, 'Ảnh màn hình 2025-04-21 lúc 09.28.49.png', 2, 123, 0),
(18, 'ừergr', 'ưgrg', 233, 'Ảnh màn hình 2025-04-21 lúc 09.33.15.png', 2, 23, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `USERS`
--

CREATE TABLE `USERS` (
  `id` int(11) NOT NULL,
  `full_name` varchar(30) NOT NULL,
  `hash_password` varchar(65) NOT NULL,
  `email` varchar(30) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `USERS`
--

INSERT INTO `USERS` (`id`, `full_name`, `hash_password`, `email`, `role`) VALUES
(1, 'Thuc Khue', '9712fcb48082d4c5b186b95831111afff01c5ecbe8f403ae7b9270d82e11b4aa', 'thuckhue@gmail.com', 'user'),
(2, 'Admin 1', '8d95192faa9fb16da0b722aa37e3da57db26499e2290a413969288628d2f1ebc', 'admin1@gmail.com', 'admin');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `CARTS`
--
ALTER TABLE `CARTS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `CART_PRODUCTS`
--
ALTER TABLE `CART_PRODUCTS`
  ADD PRIMARY KEY (`cart_id`,`products_id`),
  ADD KEY `products_id` (`products_id`);

--
-- Chỉ mục cho bảng `CATEGORY`
--
ALTER TABLE `CATEGORY`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ORDERS`
--
ALTER TABLE `ORDERS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Chỉ mục cho bảng `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `CARTS`
--
ALTER TABLE `CARTS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `CATEGORY`
--
ALTER TABLE `CATEGORY`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `ORDERS`
--
ALTER TABLE `ORDERS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `USERS`
--
ALTER TABLE `USERS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `CARTS`
--
ALTER TABLE `CARTS`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `CART_PRODUCTS`
--
ALTER TABLE `CART_PRODUCTS`
  ADD CONSTRAINT `cart_products_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `CARTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_products_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `PRODUCTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `ORDERS`
--
ALTER TABLE `ORDERS`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `CARTS` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `CATEGORY` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
