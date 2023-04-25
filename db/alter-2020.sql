/*
 * Increasing security around the Genius Board - only users with a verified email will be listed
 */

ALTER TABLE user
 ADD COLUMN created                 DATETIME NOT NULL              AFTER display_name,
 ADD COLUMN verified                INT NOT NULL DEFAULT 0         AFTER created,
 ADD COLUMN verification_hash       VARCHAR(100) NULL DEFAULT NULL AFTER verified,
 ADD COLUMN verification_expiration DATETIME NULL DEFAULT NULL     AFTER verification_hash
;

/*
 * Assume all existing users have verified their email address
 */

UPDATE user
   SET verified = 1
;