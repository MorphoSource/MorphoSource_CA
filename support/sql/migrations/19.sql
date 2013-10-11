DROP TABLE IF EXISTS `ms_media_download_stats` ;

CREATE  TABLE IF NOT EXISTS `ms_media_download_stats` (
  `download_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `downloaded_on` INT UNSIGNED NULL,
  
  PRIMARY KEY (`download_id`),
  
  INDEX `fk_ms_media_download_stats_media_id_idx` (`media_id` ASC) ,
  INDEX `fk_ms_media_download_stats_user_id_idx` (`user_id` ASC) ,
  
  CONSTRAINT `fk_ms_media_download_stats_ms_media_idx`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_download_stats_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (19, unix_timestamp());