-- phpMyAdmin SQL Dump
-- version: 5.x
-- Host: localhost
-- Database: movie_ticket_system
-- --------------------------------------------------------


-- Admin Table
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100),
    address VARCHAR(255),
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Movie Table
CREATE TABLE movie (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    genre VARCHAR(255),
    duration INT,
    language VARCHAR(255),
    description TEXT,
    poster VARCHAR(255)
);

-- Showtime Table
CREATE TABLE showtime (
    showtime_id INT AUTO_INCREMENT PRIMARY KEY,
    show_time TIME NOT NULL,
    movie_id INT NOT NULL,
    total_seats INT NOT NULL DEFAULT 50,
    FOREIGN KEY (movie_id) REFERENCES movie(movie_id) ON DELETE CASCADE
);

-- Ticket Table
CREATE TABLE ticket (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    date DATE NOT NULL,
    no_of_seats INT NOT NULL,
    payment_status ENUM('cash', 'online') DEFAULT 'cash',
    total_amount INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtime(showtime_id) ON DELETE CASCADE
);

-- Insert into admin
INSERT INTO admin (name, password) VALUES
('Anmol', '123'),
('AK', '123');

-- Insert into users
INSERT INTO users (name, phone, email, address, username, password) VALUES
('Anmol', '1234567890', 'bYFkI@example.com', '123 Main St', 'anmol', '123'),
('AK', '123456789', 'bYFkI@example.com', '123 Main St', 'ak', '123');

-- Insert into movie
INSERT INTO movie (name, genre, duration, language, description, poster) VALUES
('Daagdi Chaawl', 'Action', 140, 'Marathi', 'A gripping crime drama set in Mumbai.', 'img/Daagdi_Chaawl.webp'),
('Elizabeth Ekadashi', 'Drama', 90, 'Marathi', 'A heartwarming story of a boy and his cycle.', 'img/elizabet_ekadashi.webp'),
('Navra Maza Navsach', 'Comedy', 120, 'Marathi', 'A hilarious Marathi comedy film.', 'img/navra_maza_navsach.webp'),
('De Dhakka', 'Drama', 130, 'Marathi', 'A story of resilience and determination.', 'img/De_dhakka.webp'),
('Pavan Khind', 'Historical', 150, 'Marathi', 'A historical war drama.', 'img/PavanKhind.webp'),
('Sairat', 'Romance', 170, 'Marathi', 'A romantic drama about love and caste issues.', 'img/sairat.webp'),
('Zapatlela', 'Horror-Comedy', 125, 'Marathi', 'A horror comedy featuring a possessed doll.', 'img/Zapatlela.webp'),
('Kaakan', 'Drama', 115, 'Marathi', 'A love story set in the past.', 'img/Kaakan.webp'),
('Lai Bhaari', 'Action', 140, 'Marathi', 'An action-packed thriller.', 'img/lay bhari.webp'),
('Naal', 'Drama', 120, 'Marathi', 'An emotional journey of a young boy.', 'img/nal.webp'),
('Poster Girl', 'Comedy', 110, 'Marathi', 'A fun film about social issues.', 'img/poster_girl.webp'),
('Timepass', 'Romance', 130, 'Marathi', 'A teenage love story.', 'img/timepass.webp'),
('Sholay', 'Action', 162, 'Hindi', 'A cult classic action film.', 'img/Sholay.webp'),
('Kabir Singh', 'Romance', 173, 'Hindi', 'A passionate love story.', 'img/kabir_sing.webp'),
('Bhool Bhulaiyaa', 'Horror-Comedy', 150, 'Hindi', 'A psychological horror comedy.', 'img/bhulbhulaya.webp'),
('Munna Bhai MBBS', 'Comedy', 156, 'Hindi', 'A heartwarming comedy-drama.', 'img/munna_bhai_MBBS.webp'),
('Bajrangi Bhaijaan', 'Drama', 163, 'Hindi', 'A story of a man helping a lost child.', 'img/BB.webp'),
('God Tussi Great Ho', 'Comedy', 152, 'Hindi', 'A fantasy comedy about God.', 'img/god-tussi-great-ho.webp'),
('Mission Mangal', 'Drama', 153, 'Hindi', 'A film about India’s Mars mission.', 'img/Mission_mangal.webp'),
('Pathan', 'Action', 146, 'Hindi', 'A high-octane spy thriller.', 'img/Pathan.webp'),
('Uri: The Surgical Strike', 'Action', 138, 'Hindi', 'A film based on real military events.', 'img/Uri.webp'),
('Kick', 'Action', 146, 'Hindi', 'An action-packed thriller.', 'img/Kick.webp'),
('Dangal', 'Sports', 161, 'Hindi', 'A story of female wrestlers in India.', 'img/dangal.webp'),
('PK', 'Comedy', 153, 'Hindi', 'A thought-provoking comedy-drama.', 'img/PK.webp'),
('One Piece', 'Adventure', 120, 'English', 'A movie from the famous anime.', 'img/onepice.webp'),
('Mission Impossible', 'Action', 147, 'English', 'A spy-action thriller.', 'img/Mission_Imposible.webp'),
('Avatar', 'Sci-Fi', 162, 'English', 'A visually stunning sci-fi epic.', 'img/avatar_xlg.webp'),
('Lucifer', 'Crime', 140, 'English', 'A crime thriller based on the TV series.', 'img/Lusifer.webp'),
('Interstellar', 'Sci-Fi', 169, 'English', 'A space travel epic.', 'img/space.webp'),
('Avengers', 'Superhero', 143, 'English', 'A superhero blockbuster.', 'img/Avengers.webp'),
('Deadpool', 'Action-Comedy', 108, 'English', 'A hilarious anti-hero movie.', 'img/deadpool.jpg'),
('Dune', 'Sci-Fi', 155, 'English', 'A futuristic sci-fi adventure.', 'img/Dune.webp'),
('Guardians of the Galaxy', 'Superhero', 136, 'English', 'A space-faring superhero film.', 'img/Gardians_of_galaxy.webp'),
('Joker', 'Thriller', 122, 'English', 'A deep psychological thriller.', 'img/joker.webp'),
('John Wick', 'Action', 131, 'English', 'A revenge-driven action film.', 'img/JW.webp'),
('Spiderman', 'Superhero', 150, 'English', 'A popular superhero franchise.', 'img/spiderman.webp');

-- Insert into showtime
INSERT INTO showtime (movie_id, show_time, total_seats) VALUES
(1, '12:00:00', 50),
(2, '14:00:00', 50),
(3, '16:00:00', 50),
(4, '18:00:00', 50),
(5, '20:00:00', 50);

-- Sample Insert into ticket
INSERT INTO ticket (user_id, showtime_id, date, no_of_seats, payment_status, total_amount) VALUES
(1, 1, '2025-01-20', 9, 'online', 100),
(2, 2, '2025-01-20', 5, 'cash', 50);

-- Random 50 showtimes
INSERT INTO showtime (movie_id, show_time, total_seats)
SELECT 
    movie_id,
    SEC_TO_TIME(FLOOR(RAND() * 43200) + 28800),
    50
FROM movie
LIMIT 50;

-- Random 50 tickets
INSERT INTO ticket (user_id, showtime_id, date, no_of_seats, payment_status, total_amount)
SELECT 
    (SELECT user_id FROM users ORDER BY RAND() LIMIT 1),
    (SELECT showtime_id FROM showtime ORDER BY RAND() LIMIT 1),
    DATE_ADD('2024-01-01', INTERVAL FLOOR(RAND() * 365) DAY),
    FLOOR(RAND() * 5) + 1,
    IF(RAND() > 0.5, 'cash', 'online'),
    FLOOR(RAND() * 500) + 100
FROM movie
LIMIT 50;
