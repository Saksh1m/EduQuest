CREATE DATABASE IF NOT EXISTS eduquest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eduquest;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prompt TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('a','b','c','d') NOT NULL
);

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO questions (prompt, option_a, option_b, option_c, option_d, correct_option) VALUES
('Which planet is known as the Red Planet?', 'Earth', 'Mars', 'Jupiter', 'Venus', 'b'),
('Who wrote the play "Romeo and Juliet"?', 'William Shakespeare', 'Jane Austen', 'Charles Dickens', 'Mark Twain', 'a'),
('What is the capital city of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'c'),
('Which element has the chemical symbol "O"?', 'Oxygen', 'Gold', 'Silver', 'Hydrogen', 'a'),
('How many continents are there on Earth?', 'Five', 'Six', 'Seven', 'Eight', 'c'),
('What is the largest mammal in the world?', 'African Elephant', 'Blue Whale', 'Giraffe', 'Hippopotamus', 'b'),
('In computing, what does "CPU" stand for?', 'Central Process Unit', 'Computer Processing Utility', 'Central Processing Unit', 'Central Performance Utility', 'c'),
('Which language is primarily spoken in Brazil?', 'Spanish', 'Portuguese', 'French', 'English', 'b'),
('What is the boiling point of water at sea level?', '90°C', '100°C', '110°C', '120°C', 'b'),
('Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Claude Monet', 'c');