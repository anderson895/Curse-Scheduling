-- =============================================================
-- Migration: Auto-Scheduling support
-- Run this once on your existing `cursescheduling` database.
-- It adds a `faculty_meta` table that stores each faculty's
-- weekly availability and the subjects they specialize in.
-- =============================================================

USE `cursescheduling`;

CREATE TABLE IF NOT EXISTS `faculty_meta` (
  `fm_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `availability` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
      COMMENT '{"Monday":[{"from":"07:00","to":"12:00"}], ...}'
      CHECK (availability IS NULL OR JSON_VALID(availability)),
  `specializations` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
      COMMENT '["CHEM 103","ENG 100", ...]'
      CHECK (specializations IS NULL OR JSON_VALID(specializations)),
  PRIMARY KEY (`fm_id`),
  UNIQUE KEY `unique_user` (`user_id`),
  CONSTRAINT `fk_faculty_meta_user`
      FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
