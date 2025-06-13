-- Sample data untuk testing sistem Posyandu Lansia
USE posyandu_lansia;

-- Insert sample users
INSERT INTO users (name, email, password) VALUES
('John Doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ahmad Wijaya', 'ahmad.wijaya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample elderly data
INSERT INTO elderly_data (user_id, name, nik, birth_date, bpjs_number, address) VALUES
(1, 'Siti Aminah', '3404012345678901', '1957-03-15', '0001234567890', 'Jl. Mawar No. 123, Condongcatur, Depok, Sleman'),
(2, 'Budi Santoso', '3404012345678902', '1952-08-20', '0001234567891', 'Jl. Melati No. 456, Caturtunggal, Depok, Sleman'),
(3, 'Mariam Sari', '3404012345678903', '1959-12-10', '0001234567892', 'Jl. Anggrek No. 789, Maguwoharjo, Depok, Sleman');

-- Insert sample locations
INSERT INTO locations (name, address, description, image_url, total_patients, next_schedule) VALUES
('Posyandu Condongcatur', 
 'Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman, DIY 55283',
 'Posyandu Condongcatur merupakan fasilitas kesehatan terpadu yang melayani masyarakat lansia dengan tenaga medis berpengalaman dan fasilitas modern. Kami berkomitmen memberikan pelayanan kesehatan terbaik untuk meningkatkan kualitas hidup lansia.',
 'https://via.placeholder.com/600x300/4F46E5/FFFFFF?text=Posyandu+Condongcatur',
 45,
 '2024-01-15'),

('Posyandu Caturtunggal',
 'Jl. Babarsari, Caturtunggal, Depok, Sleman, DIY 55281',
 'Posyandu Caturtunggal adalah fasilitas kesehatan modern dengan layanan kesehatan terpadu untuk lansia. Dilengkapi dengan peralatan medis terkini dan tenaga kesehatan profesional.',
 'https://via.placeholder.com/600x300/059669/FFFFFF?text=Posyandu+Caturtunggal',
 38,
 '2024-01-16'),

('Posyandu Maguwoharjo',
 'Jl. Raya Maguwoharjo, Maguwoharjo, Depok, Sleman, DIY 55282',
 'Posyandu Maguwoharjo menyediakan layanan kesehatan komprehensif untuk lansia dengan akses mudah dan lingkungan yang nyaman. Fokus pada pelayanan preventif dan promotif.',
 'https://via.placeholder.com/600x300/DC2626/FFFFFF?text=Posyandu+Maguwoharjo',
 52,
 '2024-01-17');

-- Insert sample schedules
INSERT INTO schedules (location_id, schedule_date, start_time, end_time, max_capacity, registered_count) VALUES
-- Schedules for Posyandu Condongcatur
(1, '2024-01-15', '08:00:00', '12:00:00', 50, 12),
(1, '2024-01-22', '08:00:00', '12:00:00', 50, 8),
(1, '2024-01-29', '08:00:00', '12:00:00', 50, 15),
(1, '2024-02-05', '08:00:00', '12:00:00', 50, 5),

-- Schedules for Posyandu Caturtunggal
(2, '2024-01-16', '08:00:00', '12:00:00', 45, 10),
(2, '2024-01-23', '08:00:00', '12:00:00', 45, 6),
(2, '2024-01-30', '08:00:00', '12:00:00', 45, 12),
(2, '2024-02-06', '08:00:00', '12:00:00', 45, 3),

-- Schedules for Posyandu Maguwoharjo
(3, '2024-01-17', '08:00:00', '12:00:00', 55, 18),
(3, '2024-01-24', '08:00:00', '12:00:00', 55, 14),
(3, '2024-01-31', '08:00:00', '12:00:00', 55, 20),
(3, '2024-02-07', '08:00:00', '12:00:00', 55, 7);

-- Insert sample registrations
INSERT INTO registrations (user_id, schedule_id, elderly_name, elderly_nik, elderly_birth_date, elderly_bpjs, elderly_address, queue_number) VALUES
(1, 1, 'Siti Aminah', '3404012345678901', '1957-03-15', '0001234567890', 'Jl. Mawar No. 123, Condongcatur, Depok, Sleman', 1),
(2, 2, 'Budi Santoso', '3404012345678902', '1952-08-20', '0001234567891', 'Jl. Melati No. 456, Caturtunggal, Depok, Sleman', 2),
(3, 3, 'Mariam Sari', '3404012345678903', '1959-12-10', '0001234567892', 'Jl. Anggrek No. 789, Maguwoharjo, Depok, Sleman', 3);

-- Insert health statistics
INSERT INTO health_statistics (title, percentage, description, icon, color, display_order) VALUES
('Diabetes', 35.00, 'Lansia dengan diabetes di wilayah Depok', 'fas fa-heartbeat', '#EF4444', 1),
('Hipertensi', 42.00, 'Lansia dengan tekanan darah tinggi', 'fas fa-heart', '#F59E0B', 2),
('Stroke', 18.00, 'Kasus stroke pada lansia', 'fas fa-brain', '#8B5CF6', 3),
('Demensia', 12.00, 'Lansia dengan gangguan kognitif', 'fas fa-head-side-virus', '#06B6D4', 4);

-- Insert health articles
INSERT INTO health_articles (title, source, url, image_url, summary, is_featured) VALUES
('Tips Menjaga Kesehatan Jantung di Usia Lanjut',
 'Halodoc',
 'https://halodoc.com/artikel/tips-jantung-sehat-lansia',
 'https://via.placeholder.com/300x200/EF4444/FFFFFF?text=Kesehatan+Jantung',
 'Panduan lengkap untuk menjaga kesehatan jantung pada lansia dengan tips diet, olahraga, dan gaya hidup sehat.',
 TRUE),

('Pentingnya Olahraga Ringan untuk Lansia',
 'Kompas Health',
 'https://health.kompas.com/read/2023/olahraga-lansia',
 'https://via.placeholder.com/300x200/059669/FFFFFF?text=Olahraga+Lansia',
 'Manfaat olahraga ringan untuk lansia dan jenis-jenis aktivitas fisik yang aman dan bermanfaat.',
 TRUE),

('Nutrisi Seimbang untuk Mencegah Diabetes',
 'Detik Health',
 'https://health.detik.com/berita-detikhealth/nutrisi-diabetes-lansia',
 'https://via.placeholder.com/300x200/F59E0B/FFFFFF?text=Nutrisi+Diabetes',
 'Panduan nutrisi dan pola makan sehat untuk mencegah dan mengelola diabetes pada lansia.',
 TRUE),

('Mengenal Tanda-tanda Demensia pada Lansia',
 'SehatQ',
 'https://sehatq.com/artikel/demensia-lansia-gejala',
 'https://via.placeholder.com/300x200/8B5CF6/FFFFFF?text=Demensia+Lansia',
 'Informasi tentang gejala awal demensia dan cara pencegahan yang dapat dilakukan.',
 FALSE),

('Cara Mencegah Stroke pada Usia Lanjut',
 'Alodokter',
 'https://alodokter.com/stroke-lansia-pencegahan',
 'https://via.placeholder.com/300x200/06B6D4/FFFFFF?text=Pencegahan+Stroke',
 'Tips dan strategi untuk mencegah stroke pada lansia melalui gaya hidup sehat.',
 FALSE);

-- Update registered_count in schedules based on registrations
UPDATE schedules s SET registered_count = (
    SELECT COUNT(*) FROM registrations r 
    WHERE r.schedule_id = s.id AND r.status = 'registered'
);

-- Update total_patients in locations based on registrations
UPDATE locations l SET total_patients = (
    SELECT COUNT(DISTINCT r.user_id) FROM registrations r 
    JOIN schedules s ON r.schedule_id = s.id 
    WHERE s.location_id = l.id
);
