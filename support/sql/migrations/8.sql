ALTER TABLE ms_media ADD COLUMN element varchar(255) NULL;
ALTER TABLE ms_media ADD COLUMN preview LONGBLOB NOT NULL;


create table ms_media_multifiles (
	multifile_id		int unsigned not null auto_increment,
	media_id			int unsigned not null references ms_media(media_id),
	resource_path		text not null,
	media				longblob not null,
	media_metadata		longblob not null,
	media_content		longtext not null,
	rank				int unsigned not null default 0,	
	primary key (multifile_id),
	key i_resource_path (resource_path(255)),
	key i_media_id (media_id)
) engine=innodb CHARACTER SET utf8 COLLATE utf8_general_ci;

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (8, unix_timestamp());
