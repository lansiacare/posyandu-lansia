-- Update database untuk menambahkan kolom jenis kelamin
USE posyandu_lansia;

-- Tambah kolom jenis kelamin ke elderly_data
ALTER TABLE elderly_data ADD COLUMN gender ENUM('L', 'P') NULL AFTER birth_date;

-- Update data yang sudah ada dengan nilai default
UPDATE elderly_data SET gender = 'L' WHERE gender IS NULL AND MOD(id, 2) = 0;
UPDATE elderly_data SET gender = 'P' WHERE gender IS NULL AND MOD(id, 2) = 1;

-- Tambahkan kolom jenis kelamin ke registrations
ALTER TABLE registrations ADD COLUMN elderly_gender ENUM('L', 'P') NULL AFTER elderly_name;

-- Update data registrations yang sudah ada
UPDATE registrations r
JOIN elderly_data e ON r.elderly_nik = e.nik
SET r.elderly_gender = e.gender
WHERE r.elderly_gender IS NULL AND e.gender IS NOT NULL;

-- Update data registrations yang masih NULL
UPDATE registrations SET elderly_gender = 'L' WHERE elderly_gender IS NULL AND MOD(id, 2) = 0;
UPDATE registrations SET elderly_gender = 'P' WHERE elderly_gender IS NULL AND MOD(id, 2) = 1;
