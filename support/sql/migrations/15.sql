ALTER TABLE ms_media ADD COLUMN media_citation_instruction longtext not null;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (15, unix_timestamp());
