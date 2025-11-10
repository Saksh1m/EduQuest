CREATE DATABASE IF NOT EXISTS eduquest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eduquest;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    prompt TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('a','b','c','d') NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password_hash, is_admin)
SELECT 'admin', 'admin@example.com', '$2y$12$u/Z2dxfX62EyhzPUF1RCaOWj5vKTTvrNzaLyZbvkQCQWkU9KWpL7K', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

INSERT INTO quizzes (title, description, created_by)
SELECT 'General Knowledge', 'Test your knowledge across multiple domains.', u.id
FROM users u
WHERE u.username = 'admin'
  AND NOT EXISTS (SELECT 1 FROM quizzes WHERE title = 'General Knowledge');

SET @general_quiz_id := (SELECT id FROM quizzes WHERE title = 'General Knowledge' LIMIT 1);

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'Which planet is known as the Red Planet?', 'Earth', 'Mars', 'Jupiter', 'Venus', 'b'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'Which planet is known as the Red Planet?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'Who wrote the play "Romeo and Juliet"?', 'William Shakespeare', 'Jane Austen', 'Charles Dickens', 'Mark Twain', 'a'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'Who wrote the play "Romeo and Juliet"?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'What is the capital city of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'c'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'What is the capital city of Australia?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'Which element has the chemical symbol "O"?', 'Oxygen', 'Gold', 'Silver', 'Hydrogen', 'a'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'Which element has the chemical symbol "O"?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'How many continents are there on Earth?', 'Five', 'Six', 'Seven', 'Eight', 'c'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'How many continents are there on Earth?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'What is the largest mammal in the world?', 'African Elephant', 'Blue Whale', 'Giraffe', 'Hippopotamus', 'b'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'What is the largest mammal in the world?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'In computing, what does "CPU" stand for?', 'Central Process Unit', 'Computer Processing Utility', 'Central Processing Unit', 'Central Performance Utility', 'c'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'In computing, what does "CPU" stand for?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'Which language is primarily spoken in Brazil?', 'Spanish', 'Portuguese', 'French', 'English', 'b'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'Which language is primarily spoken in Brazil?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'What is the boiling point of water at sea level?', '90°C', '100°C', '110°C', '120°C', 'b'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'What is the boiling point of water at sea level?');

INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option)
SELECT @general_quiz_id, 'Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Claude Monet', 'c'
WHERE @general_quiz_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM questions WHERE prompt = 'Who painted the Mona Lisa?');