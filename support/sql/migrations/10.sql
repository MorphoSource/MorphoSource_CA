ALTER TABLE ms_projects ADD COLUMN total_storage_allocation int unsigned not null;

CREATE  TABLE IF NOT EXISTS `ms_scanners` (
  `scanner_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `facility_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TEXT NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`scanner_id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC, `facility_id` ASC) ,
  INDEX `fk_ms_scanners_ms_faciltities1x_idx` (`facility_id` ASC) ,
  INDEX `fk_ms_scanners_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_scanners_ms_faciltities1x_idx`
    FOREIGN KEY (`facility_id` )
    REFERENCES `ms_facilities` (`facility_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_scanners_ms_users1_idx`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (10, unix_timestamp());
