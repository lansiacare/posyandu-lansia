-- Database: posyandu_lansia
-- Membuat database dan tabel untuk sistem Posyandu Lansia

-- Membuat database
CREATE DATABASE IF NOT EXISTS posyandu_lansia;
USE posyandu_lansia;

-- Tabel users untuk autentikasi
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel elderly_data untuk data lansia
CREATE TABLE elderly_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    nik VARCHAR(16) UNIQUE NOT NULL,
    birth_date DATE NOT NULL,
    bpjs_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel locations untuk lokasi posyandu
CREATE TABLE locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    total_patients INT DEFAULT 0,
    next_schedule DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel schedules untuk jadwal posyandu
CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_id INT NOT NULL,
    schedule_date DATE NOT NULL,
    start_time TIME DEFAULT '08:00:00',
    end_time TIME DEFAULT '12:00:00',
    max_capacity INT DEFAULT 50,
    registered_count INT DEFAULT 0,
    status ENUM('active', 'full', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);

-- Tabel registrations untuk pendaftaran
CREATE TABLE registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    schedule_id INT NOT NULL,
    elderly_name VARCHAR(100) NOT NULL,
    elderly_nik VARCHAR(16) NOT NULL,
    elderly_birth_date DATE NOT NULL,
    elderly_bpjs VARCHAR(20) NOT NULL,
    elderly_address TEXT NOT NULL,
    queue_number INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);

-- Tabel health_statistics untuk statistik kesehatan
CREATE TABLE health_statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel health_articles untuk artikel kesehatan
CREATE TABLE health_articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    source VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    image_url VARCHAR(500),
    summary TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Index untuk optimasi query
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_elderly_nik ON elderly_data(nik);
CREATE INDEX idx_schedules_date ON schedules(schedule_date);
CREATE INDEX idx_registrations_user ON registrations(user_id);
CREATE INDEX idx_registrations_schedule ON registrations(schedule_id);
