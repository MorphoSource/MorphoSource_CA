ALTER TABLE ms_media ADD COLUMN scanner_id int unsigned null references ms_scanners(scanner_id);
ALTER TABLE ms_media DROP COLUMN scanner_type;
ALTER TABLE ms_media DROP COLUMN preview;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (11, unix_timestamp());
