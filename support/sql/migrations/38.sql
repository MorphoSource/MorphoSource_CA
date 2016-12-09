DROP TABLE IF EXISTS `ms_media_shares`;

CREATE  TABLE IF NOT EXISTS `ms_media_shares` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `shared_by_user_id` INT UNSIGNED NOT NULL ,
  `use_restrictions` TEXT NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  
  PRIMARY KEY (`link_id`)
)
ENGINE = InnoDB;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (38, unix_timestamp());