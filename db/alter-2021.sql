/*
 * Add support for accounts to authenticate with Google
 */

ALTER TABLE user
  ADD COLUMN sso_google_id VARCHAR(100) DEFAULT NULL AFTER password
;