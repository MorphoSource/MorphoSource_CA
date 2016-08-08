DROP TABLE IF EXISTS `ms_specimens_x_projects`;

CREATE  TABLE IF NOT EXISTS `ms_specimens_x_projects` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `specimen_id` INT UNSIGNED NOT NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  
  PRIMARY KEY (`link_id`)
)
ENGINE = InnoDB;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (35, unix_timestamp());