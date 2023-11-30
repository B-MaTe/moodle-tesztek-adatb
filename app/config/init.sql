CREATE TABLE `users` (
     `id` int(11) NOT NULL,
     `email` varchar(255) NOT NULL,
     `password` varchar(255) NOT NULL,
     `name` varchar(255) NOT NULL,
     `role` varchar(255) NOT NULL DEFAULT 'USER',
     `created_at` datetime NOT NULL,
     `created_by` int(11) DEFAULT NULL,
     FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE tests (
   id INT PRIMARY KEY AUTO_INCREMENT,
   name VARCHAR(255) NOT NULL,
   min_points INT NOT NULL,
   created_at DATETIME NOT NULL,
   created_by INT NOT NULL,
   FOREIGN KEY (created_by) REFERENCES users(id)
);