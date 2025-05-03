CREATE TABLE USERS (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(30) NOT NULL,
  hash_password VARCHAR(65) NOT NULL,
  email VARCHAR(30) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user'
);

INSERT INTO USERS (id, full_name, hash_password, email, role) VALUES
(1, 'Thuc Khue', '9712fcb48082d4c5b186b95831111afff01c5ecbe8f403ae7b9270d82e11b4aa', 'thuckhue@gmail.com', 'user'),
(2, 'Admin 1', '8d95192faa9fb16da0b722aa37e3da57db26499e2290a413969288628d2f1ebc', 'admin1@gmail.com', 'admin'),
(3, 'Nguyen Thi A', '3538b3aee447215b5cb724430fbad88fa78e2b4e1e61ae4d7371e7199c4922be', 'anguyen@gmail.com', 'user');

CREATE TABLE CATEGORY (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

INSERT INTO CATEGORY (id, name) VALUES
(1, 'Furniture'),
(2, 'Stationery'),
(3, 'Traveling items');

CREATE TABLE PRODUCTS (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price INT(11) NOT NULL,
  image VARCHAR(255) NOT NULL,
  category_id INT(11) NOT NULL,
  quantity INT(11) DEFAULT 0,
  sold INT(11) DEFAULT 0,
  FOREIGN KEY (category_id) REFERENCES CATEGORY(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO PRODUCTS (id, name, description, price, image, category_id, quantity, sold) VALUES
(1, 'Fabric Storage Box Thin', '35x37x16cm', 343000, 'C01001.jpg', 1, 98, 30),
(2, 'Wooden Dining Bench', 'The compact couch can be used for both the living room and dining room.', 2936000, 'wooden_bench_dining.png', 1, 50, 121),
(3, 'Pine - Folding Table', 'Pine wood retains the shape of the button, the more it is used, the more aesthetically rich it becomes...', 1963000, 'C01003.jpg', 1, 199, 12),
(4, 'Wooden Chest 4 Drawers', 'The product is made from natural rubber wood...', 4899000, 'C01004.jpg', 1, 79, 77),
(5, 'Fregrance Candle', 'Fragile items made of glass...', 399000, 'C01005.jpg', 1, 149, 35),
(6, 'Nylon Mesh Pen Case With Gusset', 'Made of lightweight and durable mesh material...', 136000, 'C02001.jpg', 2, 497, 20),
(7, 'Always-on Laptop Case', 'Size A4, approximately 340×255×40mm', 637000, 'C02002.jpg', 2, 300, 36),
(8, 'Highlighter', 'Blue highlighter.', 150000, 'C02003.jpg', 2, 997, 79),
(9, 'Stainless Steel Left-handed Scissors', 'Compact, left-handed scissors...', 38000, 'C02004.jpg', 2, 249, 98),
(10, 'Paper - Notebook - 7mm', 'Simple plain cover...', 38000, 'C02005.jpg', 2, 500, 29),
(11, 'Nylon Pouch Book', 'Convenient pouch for organizing...', 274000, 'C03001.jpg', 3, 100, 45),
(12, 'Hard Shell Suitcase (75L)', 'TSA locks, recycled interior...', 5890000, 'C03002.jpg', 3, 20, 67),
(13, 'Nylon Pouch with handle', 'Approx. 20.5*10.5*12.5 cm.', 264000, 'C03003.jpg', 3, 500, 88),
(14, 'PET Refill bottle Foam type', 'Foam spray bottle, 400ml', 97000, 'C03004.jpg', 3, 200, 152),
(15, 'PET Petit Spray Bottle', '50ml spray bottle', 68000, 'C03005.jpg', 1, 300, 6),
(40, 'PP File Partition Type', 'Accordion-style file organizer', 146000, 'file-partition.png', 2, 49, 0);

CREATE TABLE CARTS (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT(11),
  num_of_products INT(11),
  price INT(11),
  status ENUM('Finished','Unfinished') NOT NULL DEFAULT 'Unfinished',
  FOREIGN KEY (user_id) REFERENCES USERS(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO CARTS (id, user_id, num_of_products, price, status) VALUES
(2, 1, 3, 5993000, 'Finished'),
(3, 1, 2, 479000, 'Unfinished'),
(4, 3, 3, 887000, 'Finished'),
(5, 3, 1, 1963000, 'Finished');

CREATE TABLE CART_PRODUCTS (
  cart_id INT(11) NOT NULL,
  products_id INT(11) NOT NULL,
  quantity INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (cart_id, products_id),
  FOREIGN KEY (cart_id) REFERENCES CARTS(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (products_id) REFERENCES PRODUCTS(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO CART_PRODUCTS (cart_id, products_id, quantity) VALUES
(2, 1, 2),
(2, 4, 1),
(2, 6, 3),
(3, 1, 1),
(3, 6, 1),
(4, 5, 1),
(4, 8, 3),
(4, 9, 1),
(5, 3, 1);

CREATE TABLE ORDERS (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT(11),
  cart_id INT(11),
  receiver_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  status ENUM('Waiting','Packing','Delivery','Finished') NOT NULL DEFAULT 'Waiting',
  price INT(11) NOT NULL,
  phone_number VARCHAR(15) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES USERS(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (cart_id) REFERENCES CARTS(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO ORDERS (id, user_id, cart_id, receiver_name, address, status, price, phone_number) VALUES
(1, 1, 2, 'Thuc Khue', '268 Ly Thuong Kiet, 14, Ho Chi Minh City', 'Waiting', 5993000, '0123456789'),
(2, 3, 4, 'Nguyen Thi A', '72 Le Thanh Ton, Ben Nghe, Ho Chi Minh', 'Waiting', 887000, '0123456789'),
(3, 3, 5, 'Trinh Tran Phuong Tuan', '200 Ly Thuong Kiet, 14, Ho Chi Minh', 'Waiting', 1963000, '0627384919');
