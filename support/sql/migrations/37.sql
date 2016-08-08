ALTER TABLE ms_media ADD COLUMN derived_from_media_id INT unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (37, unix_timestamp());