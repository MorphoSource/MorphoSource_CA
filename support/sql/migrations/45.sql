DROP TABLE IF EXISTS `ms_scanner_modes`;

CREATE  TABLE IF NOT EXISTS `ms_scanner_modes` (
  `scanner_mode_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `scanner_id` INT UNSIGNED NOT NULL ,
  `modality` TINYINT UNSIGNED NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  
  PRIMARY KEY (`scanner_mode_id`)
)
ENGINE = InnoDB;

ALTER TABLE ms_media ADD COLUMN scanner_mode_id INT unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (45, unix_timestamp());