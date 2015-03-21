ALTER TABLE ms_projects
	ADD COLUMN deleted tinyint(3) unsigned NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (28, unix_timestamp());