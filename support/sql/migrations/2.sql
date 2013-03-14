/*
 *  6 March 2013 - add missing ca_groups_x_roles table
 */

/*==========================================================================*/
create table ca_groups_x_roles
(
   relation_id                    int unsigned                   not null AUTO_INCREMENT,
   group_id                       int unsigned                   not null,
   role_id                        smallint unsigned              not null,
   rank                           int unsigned                   not null default 0,
   primary key (relation_id),
   constraint fk_ca_groups_x_roles_group_id foreign key (group_id)
      references ca_user_groups (group_id) on delete restrict on update restrict,
   constraint fk_ca_groups_x_roles_role_id foreign key (role_id)
      references ca_user_roles (role_id) on delete restrict on update restrict
) engine=innodb CHARACTER SET utf8 COLLATE utf8_general_ci;

create index i_group_id on ca_groups_x_roles(group_id);
create index i_role_id on ca_groups_x_roles(role_id);
create index u_all on ca_groups_x_roles
(
   group_id,
   role_id
);

/* -------------------------------------------------------------------------------- */
/* Always add the update to ca_schema_updates at the end of the file */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (2, unix_timestamp());
