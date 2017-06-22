ALTER TABLE ms_specimens ADD COLUMN batch_status TINYINT UNSIGNED NULL;
ALTER TABLE ms_media ADD COLUMN batch_status TINYINT UNSIGNED NULL;
ALTER TABLE ms_media_files ADD COLUMN batch_status TINYINT UNSIGNED NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (41, unix_timestamp());