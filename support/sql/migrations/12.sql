ALTER TABLE ms_media ADD COLUMN grant_support longtext not null;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (12, unix_timestamp());