/*
 * Add ability to track information for an individual puzzle
 */

CREATE TABLE `puzzle_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `progress_id` int(11) NOT NULL,
  `puzzle_id` int(3) NOT NULL,
  `parameters` varchar(100) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE INDEX `natural_key` (`progress_id`, `puzzle_id`),
  KEY `progress_id` (`progress_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
