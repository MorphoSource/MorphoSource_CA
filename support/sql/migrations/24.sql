ALTER TABLE ms_media_files
	ADD COLUMN published tinyint(3) unsigned NOT NULL,
	ADD COLUMN published_on int(10) unsigned NULL,
	ADD COLUMN title varchar(255) NOT NULL,
	ADD COLUMN side varchar(20) NULL,
	ADD COLUMN element varchar(255) NULL;
	
ALTER TABLE ms_media_x_bibliography
	ADD COLUMN media_file_id int unsigned NULL;
	
ALTER TABLE ms_media_download_stats
	ADD COLUMN media_file_id int unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (23, unix_timestamp());