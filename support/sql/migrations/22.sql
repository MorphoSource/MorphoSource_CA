DROP TABLE IF EXISTS `ms_media_files` ;

CREATE  TABLE IF NOT EXISTS `ms_media_files` (
  `media_file_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `media` LONGBLOB NOT NULL ,
  `notes` TEXT NOT NULL ,
  `media_metadata` LONGTEXT NOT NULL ,
  `use_for_preview` TINYINT UNSIGNED NULL ,
  `created_on` INT NOT NULL ,
  `last_modified_on` INT NOT NULL ,
  PRIMARY KEY (`media_file_id`) ,
  INDEX `fk_ms_media_files_ms_media1_idx` (`media_id` ASC) ,
  INDEX `fk_ms_media_files_ms_users1_idx` (`user_id` ASC) ,
 CONSTRAINT `fk_ms_media_files_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_files_ms_media1`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

DROP TABLE IF EXISTS `ms_media_files_multifiles` ;

create table ms_media_files_multifiles (
	multifile_id		int unsigned not null auto_increment,
	media_file_id		int unsigned not null references ms_media_files(media_file_id),
	resource_path		text not null,
	media				longblob not null,
	media_metadata		longblob not null,
	media_content		longtext not null,
	rank				int unsigned not null default 0,	
	primary key (multifile_id),
	INDEX `fk_ms_media_files_multifiles_ms_media_file_id` (`media_file_id` ASC) ,
  	CONSTRAINT `fk_ms_media_files_multifiles_ms_media_file_id_x`
  	 FOREIGN KEY (`media_file_id` )
   	 REFERENCES `ms_media_files` (`media_file_id` )
   	 ON DELETE RESTRICT
   	 ON UPDATE RESTRICT
	
) engine=innodb CHARACTER SET utf8 COLLATE utf8_general_ci;


/* --- ONCE data migration has been done, we will need to drop the original multifiles table and media, media_metadata from ms_media;

ALTER TABLE ms_media
	DROP COLUMN media,
	DROP COLUMN media_metadata;
	
DROP TABLE IF EXISTS `ms_media_multifiles`;
*/


/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (22, unix_timestamp());