ALTER TABLE ms_specimens ADD COLUMN recordset VARCHAR(255) NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (45, unix_timestamp());