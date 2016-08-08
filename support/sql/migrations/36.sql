ALTER TABLE ms_facilities MODIFY name VARCHAR(255) NOT NULL;
/* -------------------------------------------------------------------------------- */

/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (36, unix_timestamp());