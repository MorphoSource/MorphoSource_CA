ALTER TABLE ms_specimens ADD COLUMN url text NOT NULL;

/* change copyright for all media in P116  "Lucas & Copes MCZ scans" */
/* copyright license to 'CC-BY-NC-ND' copyright permission to 'granted by copyright holder'? */

update ms_media set is_copyrighted = 1, copyright_permission = 2, copyright_license = 7 where project_id = 116;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (32, unix_timestamp());