# Модели мантиқии базаи додаҳои School Drive

## 1. Муқаддима

Модели мантиқии базаи додаҳои School Drive сохтори маълумотро муайян мекунад. Он барои нигоҳдорӣ, коркард ва идоракунии маълумоти истифодабарандагон, фанҳо, маводи таълимӣ ва супоришҳо истифода мешавад.

Базаи додаҳо аз рӯйи модели релятсионӣ тарҳрезӣ шудааст.

## 2. Ҷадвалҳои асосӣ

### 2.1. Ҷадвали users

Барои нигоҳдории маълумоти умумии истифодабарандагон.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| full_name | VARCHAR | NOT NULL |
| username | VARCHAR | UNIQUE, NOT NULL |
| email | VARCHAR | UNIQUE, NOT NULL |
| password_hash | VARCHAR | NOT NULL |
| role | VARCHAR | NOT NULL |
| created_at | TIMESTAMP | |

### 2.2. Ҷадвали students

Барои нигоҳдории маълумоти иловагии донишҷӯён.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| user_id | BIGINT | FK, UNIQUE |
| student_number | VARCHAR | UNIQUE |
| course | INT | |

### 2.3. Ҷадвали teachers

Барои нигоҳдории маълумоти иловагии омӯзгорон.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| user_id | BIGINT | FK, UNIQUE |
| specialization | VARCHAR | |

### 2.4. Ҷадвали subjects

Барои нигоҳдории фанҳои таълимӣ.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| name | VARCHAR | NOT NULL |
| description | TEXT | |

### 2.5. Ҷадвали categories

Барои гурӯҳбандии маводи таълимӣ.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| name | VARCHAR | NOT NULL |
| description | TEXT | |

### 2.6. Ҷадвали materials

Барои нигоҳдории маълумоти маводи таълимӣ.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| teacher_id | BIGINT | FK |
| subject_id | BIGINT | FK |
| category_id | BIGINT | FK |
| title | VARCHAR | NOT NULL |
| description | TEXT | |
| created_at | TIMESTAMP | |

### 2.7. Ҷадвали material_files

Барои нигоҳдории маълумоти файлҳои ба маводи таълимӣ замшуда.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| material_id | BIGINT | FK |
| file_name | VARCHAR | NOT NULL |
| file_path | VARCHAR | NOT NULL |
| file_type | VARCHAR | |
| file_size | BIGINT | |

### 2.8. Ҷадвали assignments

Барои нигоҳдории супоришҳои таълимӣ.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| teacher_id | BIGINT | FK |
| subject_id | BIGINT | FK |
| title | VARCHAR | NOT NULL |
| description | TEXT | |
| deadline | DATETIME | |

### 2.9. Ҷадвали submissions

Барои нигоҳдории ҷавобҳои донишҷӯён ба супоришҳо.

| Майдон | Навъи дода | Маҳдудият |
|---|---|---|
| id | BIGINT | PK |
| assignment_id | BIGINT | FK |
| student_id | BIGINT | FK |
| file_path | VARCHAR | |
| submitted_at | TIMESTAMP | |
| status | VARCHAR | |

## 3. Робитаи байни ҷадвалҳо

Робитаҳои асосии базаи додаҳо:

| № | Ҷадвали якум | Ҷадвали дуюм | Навъи робита |
|---|---|---|---|
| 1 | users | students | One-to-One |
| 2 | users | teachers | One-to-One |
| 3 | teachers | materials | One-to-Many |
| 4 | subjects | materials | One-to-Many |
| 5 | categories | materials | One-to-Many |
| 6 | materials | material_files | One-to-Many |
| 7 | teachers | assignments | One-to-Many |
| 8 | subjects | assignments | One-to-Many |
| 9 | assignments | submissions | One-to-Many |
| 10 | students | submissions | One-to-Many |

## 4. Калидҳои асосӣ ва хориҷӣ

**Primary Key (PK):**
- users.id
- students.id
- teachers.id
- subjects.id
- categories.id
- materials.id
- material_files.id
- assignments.id
- submissions.id

**Foreign Key (FK):**
- students.user_id → users.id
- teachers.user_id → users.id
- materials.teacher_id → teachers.id
- materials.subject_id → subjects.id
- materials.category_id → categories.id
- material_files.material_id → materials.id
- assignments.teacher_id → teachers.id
- assignments.subject_id → subjects.id
- submissions.assignment_id → assignments.id
- submissions.student_id → students.id

## 5. Қоидаҳои асосии базаи додаҳо

1. Ҳар як истифодабаранда бояд ID-и нодир дошта бошад.
2. Username ва email набояд такрор шаванд.
3. Ҳар як донишҷӯ ба як ҳисоби истифодабаранда пайваст мешавад.
4. Ҳар як омӯзгор ба як ҳисоби истифодабаранда пайваст мешавад.
5. Маводи таълимӣ бояд ба омӯзгор ва фан вобаста бошад.
6. Файл бояд ба маводи таълимии муайян тааллуқ дошта бошад.
7. Ҳар як супориш аз ҷониби омӯзгор сохта мешавад.
8. Ҷавоби донишҷӯ бояд ба супориши муайян вобаста бошад.
9. Ҳангоми сабти маълумот робитаи калидҳои хориҷӣ бояд риоя карда шавад.
10. Паролҳо бояд дар шакли хешшуда нигоҳ дошта шаванд.

## 6. Хулоса

Модели мантиқии базаи додаҳои School Drive сохтори асосии маълумотро муайян мекунад. Он барои марҳилаи минбаъда — сохтани модели физикӣ ва файли SQL Schema асос мегардад.
