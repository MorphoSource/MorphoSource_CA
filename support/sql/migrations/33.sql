ALTER TABLE ms_media_download_stats ADD COLUMN intended_use text NULL, ADD COLUMN intended_use_other varchar(255) NULL, ADD COLUMN 3d_print tinyint(3) unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (33, unix_timestamp());