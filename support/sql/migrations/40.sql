ALTER TABLE ms_specimens ADD COLUMN uuid VARCHAR(255) NOT NULL;
ALTER TABLE ms_specimens ADD COLUMN occurrence_id VARCHAR(255) NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (40, unix_timestamp());