ALTER TABLE ms_media MODIFY COLUMN facility_id int unsigned null;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (13, unix_timestamp());
