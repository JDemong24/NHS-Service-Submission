/*
 * Add support for year-based challenges: split the user table in two: user, progress (1:N)
 */

CREATE TABLE `progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `puzzle_states` varchar(500) NOT NULL,
  `completion_time` datetime DEFAULT NULL,
  `genius_ind` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  INDEX `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
;

INSERT INTO progress (user_id, year, puzzle_states, completion_time, genius_ind)
SELECT user_id, 2017, puzzle_states, completion_time, isGenius
  FROM user
;

ALTER TABLE user
DROP COLUMN isGenius,
DROP COLUMN completion_time,
DROP COLUMN puzzle_states
;

/*
 * Update the puzzle_states field to only use states 0 (unsolved) and 1 (solved). This is done in
 * two steps:
 *
 * 1. Replace all the 2's and 3's with zeros. This affects both array values (good) and indices (bad)
 * 2. Fix the array indices 2 and 3. This isn't difficult because we know exactly where in the string
 *    these characters are located.
 */

UPDATE progress
   SET puzzle_states = REPLACE(REPLACE(puzzle_states, 'i:2;', 'i:0;'), 'i:3;', 'i:0;')
;

UPDATE progress
   SET puzzle_states =
       CONCAT(SUBSTRING(puzzle_states, 1, 24), '2', SUBSTRING(puzzle_states, 26, 7), '3', SUBSTRING(puzzle_states, 34))
;

/*
 * Standardize column/index names for the user table
 */

ALTER TABLE user
CHANGE COLUMN user_id      id           INT(11) NOT NULL AUTO_INCREMENT,
CHANGE COLUMN display_name display_name VARCHAR(100) NOT NULL,
CHANGE COLUMN email        email        VARCHAR(100) NOT NULL,
CHANGE COLUMN password     password     VARCHAR(100) NOT NULL
;

ALTER TABLE user
DROP INDEX username_UNIQUE,
ADD UNIQUE INDEX email_UNIQUE (email ASC),
DROP INDEX user_id_UNIQUE,
ADD UNIQUE INDEX id_UNIQUE (id ASC)
;

/*
 * Encrypt all the passwords in the database
 */
UPDATE user
   SET password = PASSWORD(password)
;