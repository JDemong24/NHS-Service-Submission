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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


