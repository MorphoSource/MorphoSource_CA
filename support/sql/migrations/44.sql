ALTER TABLE ms_media_files DROP COLUMN distance_units, DROP COLUMN max_distance_x, DROP COLUMN max_distance_3d;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (44, unix_timestamp());