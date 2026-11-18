CREATE DATABASE IF NOT EXISTS event_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_management;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE venues (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address VARCHAR(255) NOT NULL,
  capacity INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  venue_id INT NOT NULL,
  created_by INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  event_date DATETIME NOT NULL,
  quota INT NOT NULL DEFAULT 0,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE RESTRICT,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  user_id INT NOT NULL,
  status ENUM('registered','cancelled') NOT NULL DEFAULT 'registered',
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_event_user (event_id, user_id),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Akun admin: admin@event.test / password
INSERT INTO users (name,email,password,role) VALUES
('Administrator','admin@event.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin');

INSERT INTO categories (name) VALUES ('Seminar'),('Workshop'),('Lomba'),('Konser');
INSERT INTO venues (name,address,capacity) VALUES
('Aula Utama','Jl. Setiabudi No. 229, Bandung',300),
('Gedung Serbaguna','Jl. Dipatiukur No. 35, Bandung',150);

INSERT INTO events (category_id,venue_id,created_by,title,description,event_date,quota,price,status) VALUES
(1,1,1,'Seminar Nasional Teknologi Pendidikan','Seminar tentang tren teknologi dalam dunia pendidikan.','2026-12-10 09:00:00',100,50000,'published'),
(2,2,1,'Workshop Web Development PHP','Belajar membuat aplikasi web dengan PHP native dan MySQL.','2026-12-20 13:00:00',30,0,'published'),
(3,1,1,'Lomba Coding Antar Kampus','Kompetisi pemrograman tingkat mahasiswa.','2027-01-15 08:00:00',50,25000,'draft');
