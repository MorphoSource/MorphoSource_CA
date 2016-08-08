ALTER TABLE ms_media_files ADD COLUMN file_type tinyint(3) unsigned NULL, ADD COLUMN distance_units tinyint(3) unsigned NULL, ADD COLUMN max_distance_x varchar(255) NOT NULL, ADD COLUMN max_distance_3d varchar(255) NOT NULL;
ALTER TABLE ms_specimens ADD COLUMN type tinyint(3) unsigned NULL;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (34, unix_timestamp());