ALTER TABLE ms_specimens ADD COLUMN description longtext not null;
ALTER TABLE ca_sql_search_word_index MODIFY COLUMN field_num varchar(20) not null;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (14, unix_timestamp());
