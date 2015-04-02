DROP TABLE IF EXISTS `ms_media_movement_requests` ;

CREATE  TABLE IF NOT EXISTS `ms_media_movement_requests` (
  `request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `to_project_id` INT UNSIGNED NOT NULL ,
  `requested_on` INT UNSIGNED NULL ,
  `request` TEXT NOT NULL,
  `response` TEXT NOT NULL,
  `status` TINYINT UNSIGNED NOT NULL ,
  `type` TINYINT UNSIGNED NOT NULL ,
  
  PRIMARY KEY (`request_id`),
  
  INDEX `fk_ms_media_movement_requests_media_idx` (`media_id` ASC) ,
  INDEX `fk_ms_media_movement_requests_user_idx` (`user_id` ASC) ,
  INDEX `fk_ms_media_movement_requests_project_idx` (`to_project_id` ASC) ,
  
  CONSTRAINT `fk_ms_media_movement_requests_media_idx`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_movement_requests_user_idx`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_movement_requests_project_idx`
    FOREIGN KEY (`to_project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;


/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (30, unix_timestamp());