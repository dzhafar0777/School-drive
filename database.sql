CREATE DATABASE IF NOT EXISTS driving_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE driving_school;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS exams, driving_sessions, lessons, students, instructors, cars, `groups`, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users(
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','instructor','student') NOT NULL,
 phone VARCHAR(30),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE `groups`(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(60) NOT NULL,
 category ENUM('B','C') NOT NULL,
 start_date DATE NOT NULL,
 exam_date DATE NOT NULL,
 required_hours DECIMAL(5,1) NOT NULL DEFAULT 30
);
CREATE TABLE students(
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL UNIQUE,
 group_id INT NOT NULL,
 birth_date DATE,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(group_id) REFERENCES `groups`(id) ON DELETE CASCADE
);
CREATE TABLE instructors(
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL UNIQUE,
 experience_years INT DEFAULT 0,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE cars(
 id INT AUTO_INCREMENT PRIMARY KEY,
 plate VARCHAR(20) NOT NULL UNIQUE,
 model VARCHAR(60) NOT NULL,
 category ENUM('B','C') NOT NULL
);
CREATE TABLE lessons(
 id INT AUTO_INCREMENT PRIMARY KEY,
 group_id INT NOT NULL,
 topic VARCHAR(160) NOT NULL,
 lesson_date DATE NOT NULL,
 start_time TIME NOT NULL,
 end_time TIME NOT NULL,
 room VARCHAR(30),
 teacher VARCHAR(80),
 FOREIGN KEY(group_id) REFERENCES `groups`(id) ON DELETE CASCADE
);
CREATE TABLE driving_sessions(
 id INT AUTO_INCREMENT PRIMARY KEY,
 instructor_id INT NOT NULL,
 car_id INT NOT NULL,
 student_id INT NULL,
 start_time DATETIME NOT NULL,
 end_time DATETIME NOT NULL,
 status ENUM('free','booked','done','cancelled') NOT NULL DEFAULT 'free',
 comment TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX(instructor_id,start_time), INDEX(car_id,start_time),
 FOREIGN KEY(instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
 FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE CASCADE,
 FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE SET NULL
);
CREATE TABLE exams(
 id INT AUTO_INCREMENT PRIMARY KEY,
 student_id INT NOT NULL,
 exam_type ENUM('theory','practice') NOT NULL,
 exam_date DATE NOT NULL,
 score INT NOT NULL,
 passed TINYINT(1) NOT NULL DEFAULT 0,
 notes VARCHAR(255),
 FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Маълумоти намуна. Парол барои ҳама: 123456 (дар аввалин вуруд ҳеш мешавад)
INSERT INTO users(full_name,email,password,role,phone) VALUES
('Маъмур Раҳимов','admin@drive.tj','RESET','admin','900000001'),
('Фаррух Саидов','ins1@drive.tj','RESET','instructor','900000002'),
('Ҷамшед Нуров','ins2@drive.tj','RESET','instructor','900000003'),
('Ҷаъфар Авезов','st1@drive.tj','RESET','student','900000004'),
('Зарина Холова','st2@drive.tj','RESET','student','900000005'),
('Икром Шоев','st3@drive.tj','RESET','student','900000006');
INSERT INTO `groups`(name,category,start_date,exam_date,required_hours) VALUES
('B-01','B',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 10 DAY),30),
('C-01','C',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 60 DAY),40);
INSERT INTO instructors(user_id,experience_years) VALUES(2,8),(3,5);
INSERT INTO students(user_id,group_id,birth_date) VALUES(4,1,'2004-03-12'),(5,1,'2003-07-21'),(6,2,'2000-01-05');
INSERT INTO cars(plate,model,category) VALUES('0101 AA','Toyota Corolla','B'),('0202 BB','Hyundai Accent','B'),('0303 CC','KamAZ 5320','C');
INSERT INTO lessons(group_id,topic,lesson_date,start_time,end_time,room,teacher) VALUES
(1,'Қоидаҳои ҳаракат: аломатҳои роҳ',CURDATE(),'09:00','10:30','101','Ш. Каримов'),
(1,'Нишонаҳои хат ва самти ҳаракат',DATE_ADD(CURDATE(),INTERVAL 2 DAY),'09:00','10:30','101','Ш. Каримов'),
(2,'Сохтори мошини боркаш',DATE_ADD(CURDATE(),INTERVAL 1 DAY),'14:00','15:30','102','А. Раҳмонов');
INSERT INTO driving_sessions(instructor_id,car_id,student_id,start_time,end_time,status,comment) VALUES
(1,1,1,DATE_SUB(NOW(),INTERVAL 3 DAY),DATE_ADD(DATE_SUB(NOW(),INTERVAL 3 DAY),INTERVAL 60 MINUTE),'done','Бозёбии шаҳрӣ хуб гузашт.'),
(1,1,NULL,DATE_ADD(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 1450 MINUTE),'free',NULL),
(2,2,NULL,DATE_ADD(NOW(),INTERVAL 2 DAY),DATE_ADD(NOW(),INTERVAL 2890 MINUTE),'free',NULL);
