SET NAMES utf8mb4;
-- Weekly Plan table
CREATE TABLE IF NOT EXISTS weekly_rows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day VARCHAR(20) NOT NULL DEFAULT 'Senin',
    time VARCHAR(5) NOT NULL DEFAULT '09:00',
    activity VARCHAR(255) NOT NULL DEFAULT '',
    status ENUM('none', 'progress', 'done') NOT NULL DEFAULT 'none',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- To-do list
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL DEFAULT '',
    done BOOLEAN NOT NULL DEFAULT FALSE,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings (key-value: quote, notes)
CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT
);

-- Seed default data
INSERT INTO weekly_rows (day, time, activity, status, sort_order) VALUES
    ('Senin', '09:00', 'Morning review + cek email', 'done', 1),
    ('Selasa', '10:00', 'Deep work — project utama', 'progress', 2),
    ('Rabu', '14:00', 'Meeting tim & planning sprint', 'none', 3),
    ('Kamis', '15:00', 'Belajar skill baru / reading', 'none', 4),
    ('Jumat', '16:00', 'Weekly review & rencana minggu depan', 'none', 5);

INSERT INTO todos (text, done, sort_order) VALUES
    ('Review laporan Q2', FALSE, 1),
    ('Kirim email follow-up klien', TRUE, 2),
    ('Update dokumentasi proyek', FALSE, 3);

INSERT INTO settings (`key`, `value`) VALUES
    ('quote', '"The secret of getting ahead is getting started." — Mark Twain'),
    ('notes', '');
