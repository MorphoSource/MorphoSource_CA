ALTER TABLE ms_specimens
	ADD COLUMN collector varchar(255) NOT NULL,
	ADD COLUMN collected_on int(10) unsigned NULL,
	ADD COLUMN locality_northing_coordinate varchar(255) NULL,
	ADD COLUMN locality_easting_coordinate varchar(255) NULL,
	ADD COLUMN locality_datum_zone varchar(255) NULL;

ALTER TABLE ms_media
	ADD COLUMN reviewer_id INT UNSIGNED NULL;
/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (26, unix_timestamp());