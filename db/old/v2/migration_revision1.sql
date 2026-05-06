-- =============================================================
-- Revision 1 migration
-- 1. `rooms` table — managed list of room numbers (replaces the
--    hard-coded 301..305 used previously).
-- 2. curriculum.course_tier — gen_ed | gen_eng | major  (drives the
--    plotting prioritization workflow described in revision1.txt).
-- 3. curriculum.pairing — preferred meeting-day pairing for
--    paired/face-to-face courses (TTH, MWF, WS, or NONE).
-- =============================================================

USE `cursescheduling`;

-- ---------- ROOMS ----------
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `room_name`  VARCHAR(50)  NOT NULL,
  `room_type`  VARCHAR(40)  NOT NULL DEFAULT 'lecture',
  `capacity`   INT(11)      NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `uniq_room_name` (`room_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `rooms` (`room_name`, `room_type`, `capacity`)
VALUES
  ('301','lecture',40),
  ('302','lecture',40),
  ('303','lecture',40),
  ('304','laboratory',30),
  ('305','laboratory',30);

-- ---------- COURSE TIER + PAIRING ----------
-- Some MariaDB versions do not support IF NOT EXISTS for ADD COLUMN,
-- so wrap in dynamic SQL.
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'curriculum'
    AND COLUMN_NAME  = 'course_tier'
);
SET @sql := IF(@col_exists = 0,
  "ALTER TABLE `curriculum` ADD COLUMN `course_tier` ENUM('gen_ed','gen_eng','major') NULL DEFAULT NULL AFTER `prerequisite`",
  "SELECT 'course_tier already present'");
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'curriculum'
    AND COLUMN_NAME  = 'pairing'
);
SET @sql := IF(@col_exists = 0,
  "ALTER TABLE `curriculum` ADD COLUMN `pairing` ENUM('NONE','MWF','TTH','WS') NOT NULL DEFAULT 'NONE' AFTER `course_tier`",
  "SELECT 'pairing already present'");
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- BACKFILL TIERS ----------
-- Gen Ed: FCL, SOC, ENG, FIL, PE, NSTP, PSY, MUS, ART, GES, GEC
UPDATE `curriculum`
SET `course_tier` = 'gen_ed'
WHERE `course_tier` IS NULL
  AND (
       `subject_code` LIKE 'FCL%'  OR `subject_code` LIKE 'SOC%'
    OR `subject_code` LIKE 'ENG%'  OR `subject_code` LIKE 'FIL%'
    OR `subject_code` LIKE 'PE.%'  OR `subject_code` LIKE 'PE %'
    OR `subject_code` LIKE 'NSTP%' OR `subject_code` LIKE 'PSY%'
    OR `subject_code` LIKE 'MUS%'  OR `subject_code` LIKE 'ART%'
    OR `subject_code` LIKE 'GES%'  OR `subject_code` LIKE 'GEC%'
    OR `subject_code` LIKE 'PHIL%' OR `subject_code` LIKE 'HUM%'
  );

-- Gen Engineering: Chemistry / Physics / Math / Drawing / Engineering Mechanics / basic EE / MECH
UPDATE `curriculum`
SET `course_tier` = 'gen_eng'
WHERE `course_tier` IS NULL
  AND (
       `subject_code` LIKE 'CHEM%' OR `subject_code` LIKE 'PHY%'
    OR `subject_code` LIKE 'MAT%'  OR `subject_code` LIKE 'MAT.%'
    OR `subject_code` LIKE 'DRAW%' OR `subject_code` LIKE 'MECH%'
    OR `subject_code` LIKE 'MEC%'
  );

-- Everything else => major / professional
UPDATE `curriculum`
SET `course_tier` = 'major'
WHERE `course_tier` IS NULL;

-- ---------- BACKFILL PAIRING ----------
-- Lab courses default to TTH (face-to-face pairing per revision1.txt)
UPDATE `curriculum`
SET `pairing` = 'TTH'
WHERE `pairing` = 'NONE'
  AND `lab_hours` > 0;
