ALTER TABLE ms_media_files CHANGE COLUMN published published tinyint(3) unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (31, unix_timestamp());