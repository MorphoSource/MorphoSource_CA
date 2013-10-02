ALTER TABLE ms_bibliography ADD COLUMN pp VARCHAR(255) NOT NULL;

ALTER TABLE ms_media
	ADD COLUMN media_citation_instruction1 VARCHAR(255) NOT NULL,
	ADD COLUMN media_citation_instruction2 VARCHAR(255) NOT NULL,
	ADD COLUMN media_citation_instruction3 VARCHAR(255) NOT NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (18, unix_timestamp());


