-- Update database untuk fitur kader dan pemeriksaan
USE posyandu_lansia;

-- Tambah kolom role dan location_id ke tabel users
ALTER TABLE users ADD COLUMN role ENUM('user', 'kader') DEFAULT 'user';
ALTER TABLE users ADD COLUMN location_id INT NULL;
ALTER TABLE users ADD CONSTRAINT fk_users_location FOREIGN KEY (location_id) REFERENCES locations(id);

-- Tambah kolom golongan darah ke elderly_data
ALTER TABLE elderly_data ADD COLUMN blood_type ENUM('A', 'B', 'AB', 'O') NULL;

-- Tabel untuk data pemeriksaan
CREATE TABLE medical_examinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_id INT NOT NULL,
    kader_id INT NOT NULL,
    blood_sugar DECIMAL(5,2) NULL COMMENT 'mg/dL',
    blood_pressure_systolic INT NULL COMMENT 'mmHg',
    blood_pressure_diastolic INT NULL COMMENT 'mmHg',
    cholesterol DECIMAL(5,2) NULL COMMENT 'mg/dL',
    weight DECIMAL(5,2) NULL COMMENT 'kg',
    height DECIMAL(5,2) NULL COMMENT 'cm',
    uric_acid DECIMAL(5,2) NULL COMMENT 'mg/dL',
    notes TEXT NULL,
    examination_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (kader_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index untuk optimasi
CREATE INDEX idx_medical_examinations_registration ON medical_examinations(registration_id);
CREATE INDEX idx_medical_examinations_kader ON medical_examinations(kader_id);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_location ON users(location_id);

-- Insert sample kader users
INSERT INTO users (name, email, password, role, location_id) VALUES
('Dr. Sari Kader', 'kader1@posyandu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kader', 1),
('Ns. Budi Kader', 'kader2@posyandu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kader', 2),
('Dr. Ani Kader', 'kader3@posyandu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kader', 3);
