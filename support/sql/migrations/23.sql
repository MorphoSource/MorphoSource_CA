DROP TABLE IF EXISTS `ms_media_set_items` ;
CREATE  TABLE IF NOT EXISTS `ms_media_set_items` (
  `item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `media_file_id` INT UNSIGNED NOT NULL,
  `set_id` INT UNSIGNED NOT NULL,
  `created_on` INT UNSIGNED NULL,
  
  PRIMARY KEY (`item_id`),
  
  INDEX `fk_ms_media_set_items_media_file_id` (`media_file_id` ASC) ,
   INDEX `fk_ms_media_set_items_set_id` (`set_id` ASC) ,
  
  CONSTRAINT `fk_ms_media_set_items_media_file_id`
    FOREIGN KEY (`media_file_id` )
    REFERENCES `ms_media_files` (`media_file_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_set_items_set_id`
    FOREIGN KEY (`set_id` )
    REFERENCES `ms_media_sets` (`set_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (23, unix_timestamp());