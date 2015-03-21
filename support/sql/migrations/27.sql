ALTER TABLE ms_media
	ADD COLUMN merged_media text NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (27, unix_timestamp());