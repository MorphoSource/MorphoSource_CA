ALTER TABLE ms_media ADD COLUMN scanner_exposure_time text NOT NULL;
ALTER TABLE ms_media ADD COLUMN scanner_filter text NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (39, unix_timestamp());