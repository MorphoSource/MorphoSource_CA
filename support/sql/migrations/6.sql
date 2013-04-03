ALTER TABLE ms_taxonomy_names ADD COLUMN genus varchar(255) NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (6, unix_timestamp());
