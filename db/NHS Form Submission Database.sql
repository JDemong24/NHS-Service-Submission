DROP DATABASE IF EXISTS `nhs`;

CREATE DATABASE `nhs` DEFAULT CHARSET=latin1;

USE `nhs`;

CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `sso_google_id` varchar(100) DEFAULT NULL,
  `display_name` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `verified` int NOT NULL DEFAULT '0',
  `verification_hash` varchar(100) DEFAULT NULL,
  `verification_expiration` datetime DEFAULT NULL,
  `isAdmin` boolean DEFAULT FALSE,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `nhs`.`submissions` (
  `sub_id` INT NOT NULL AUTO_INCREMENT,
  `sub_user_id` INT NOT NULL,
  `sub_date` DATETIME NOT NULL,
  `sub_supervisor_name` VARCHAR(100) NOT NULL,
  `sub_supervisor_phone_number` VARCHAR(100) NOT NULL,
  `sub_service_title` VARCHAR(100) NOT NULL,
  `sub_service_description` VARCHAR(1000) NOT NULL,
  `sub_supervisor_email` VARCHAR(200) NOT NULL,
  `sub_grade_level` INT NOT NULL,
  `sub_hours` INT NOT NULL,
  `sub_status` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`sub_id`),
  UNIQUE INDEX `ul_id_UNIQUE` (`sub_id` ASC) VISIBLE);
  
  CREATE TABLE `nhs`.`config` (
  `cfg_id` INT NOT NULL AUTO_INCREMENT,
  `cfg_school_start_date` date NOT NULL ,
  PRIMARY KEY (`cfg_id`),
  UNIQUE INDEX `ul_id_UNIQUE` (`cfg_id` ASC) VISIBLE);
  
  
  insert into config(cfg_school_start_date)
  values('2022-09-01');
  
-- DROP EVENT IF EXISTS `hours_reset`;
-- SET GLOBAL event_scheduler = ON;
-- CREATE EVENT hours_reset
-- ON SCHEDULE EVERY 1 YEAR
-- STARTS '2021-05-31 12:14:00'
-- DO
-- UPDATE submissions SET sub_hours=0;