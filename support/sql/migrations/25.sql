DROP TABLE IF EXISTS `ms_media_x_projects` ;
CREATE  TABLE IF NOT EXISTS `ms_media_x_projects` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `media_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  
  PRIMARY KEY (`link_id`),
  
  INDEX `fk_ms_media_x_projects_media_id` (`media_id` ASC) ,
   INDEX `fk_ms_media_x_projects_project_id` (`project_id` ASC) ,
  
  CONSTRAINT `fk_ms_media_x_projects_media_id`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_projects_project_id`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (25, unix_timestamp());