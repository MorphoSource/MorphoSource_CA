ALTER TABLE ms_media_files ADD COLUMN ark VARCHAR(255) NULL;
ALTER TABLE ms_media_files ADD COLUMN ark_reserved INT unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (43, unix_timestamp());

/* -------------------------------------------------------------------------------- */
/* The following only alters non-NULL values in doi column of ms_media_files */
UPDATE ms_media_files
SET ark = if(doi IS NOT NULL, substr(doi, instr(doi, ' | ') + 3, length(doi) - instr(doi, ' | ') ), NULL);
UPDATE ms_media_files
SET doi = if(doi IS NOT NULL, substr(doi, 1, instr(doi, ' | ') -1 ), NULL);