ALTER TABLE ms_media 
ADD COLUMN scanner_calibration_shading_correction TINYINT UNSIGNED NOT NULL,
ADD COLUMN scanner_calibration_flux_normalization TINYINT UNSIGNED NOT NULL,
ADD COLUMN scanner_calibration_geometric_calibration TINYINT UNSIGNED NOT NULL,
DROP COLUMN scanner_calibration_check;


/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (17, unix_timestamp());