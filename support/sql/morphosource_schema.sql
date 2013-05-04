SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `ca_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_users` ;

CREATE  TABLE IF NOT EXISTS `ca_users` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_name` VARCHAR(255) NOT NULL ,
  `userclass` TINYINT UNSIGNED NOT NULL ,
  `password` VARCHAR(100) NOT NULL ,
  `fname` VARCHAR(255) NOT NULL ,
  `lname` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `sms_number` VARCHAR(30) NOT NULL ,
  `vars` LONGTEXT NOT NULL ,
  `volatile_vars` TEXT NOT NULL ,
  `active` TINYINT UNSIGNED NOT NULL ,
  `confirmed_on` INT UNSIGNED NULL ,
  `confirmation_key` VARCHAR(32) NULL ,
  `registered_on` INT UNSIGNED NOT NULL ,
  `entity_id` INT UNSIGNED NULL ,
  `icon` LONGBLOB NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_projects` ;

CREATE  TABLE IF NOT EXISTS `ms_projects` (
  `project_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `abstract` TEXT NOT NULL ,
  `published_on` INT UNSIGNED NULL ,
  `publication_status` TINYINT UNSIGNED NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`project_id`) ,
  INDEX `fk_ms_projects_ms_users1_idx` (`user_id` ASC) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  CONSTRAINT `fk_ms_projects_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_bibliography`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_bibliography` ;

CREATE  TABLE IF NOT EXISTS `ms_bibliography` (
  `bibref_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `reference_type` TINYINT UNSIGNED NOT NULL ,
  `article_title` TEXT NOT NULL ,
  `article_secondary_title` TEXT NOT NULL ,
  `journal_title` TEXT NOT NULL ,
  `monograph_title` TEXT NOT NULL ,
  `authors` TEXT NOT NULL ,
  `secondary_authors` TEXT NOT NULL ,
  `editors` TEXT NOT NULL ,
  `vol` VARCHAR(45) NOT NULL ,
  `num` VARCHAR(45) NOT NULL ,
  `publisher` TEXT NOT NULL ,
  `pubyear` SMALLINT UNSIGNED NOT NULL ,
  `place_of_publication` TEXT NOT NULL ,
  `abstract` TEXT NOT NULL ,
  `description` TEXT NOT NULL ,
  `collation` VARCHAR(255) NOT NULL ,
  `external_identifier` TEXT NOT NULL ,
  `url` TEXT NOT NULL ,
  `worktype` VARCHAR(100) NOT NULL ,
  `edition` VARCHAR(100) NOT NULL ,
  `sect` VARCHAR(100) NOT NULL ,
  `isbn` VARCHAR(100) NOT NULL ,
  `keywords` TEXT NOT NULL ,
  `language` VARCHAR(100) NOT NULL ,
  `electronic_resource_num` VARCHAR(255) NOT NULL ,
  `author_address` TEXT NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`bibref_id`) ,
  INDEX `fk_ms_bibliography_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_bibliography_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_bibliography_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_bibliography_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_institutions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_institutions` ;

CREATE  TABLE IF NOT EXISTS `ms_institutions` (
  `institution_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `location_city` VARCHAR(255) NOT NULL ,
  `location_state` VARCHAR(255) NOT NULL ,
  `location_country` VARCHAR(255) NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`institution_id`) ,
  INDEX `fk_ms_institutions_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_institutions_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_specimens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_specimens` ;

CREATE  TABLE IF NOT EXISTS `ms_specimens` (
  `specimen_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `notes` TEXT NOT NULL ,
  `reference_source` TINYINT UNSIGNED NULL ,
  `institution_code` VARCHAR(255) NOT NULL ,
  `collection_code` VARCHAR(255) NOT NULL ,
  `catalog_number` VARCHAR(255) NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `sex` CHAR(1) NULL ,
  `element` VARCHAR(255) NULL ,
  `side` VARCHAR(255) NULL ,
  `relative_age` VARCHAR(255) NULL ,
  `absolute_age` VARCHAR(45) NULL ,
  `body_mass` VARCHAR(45) NULL ,
  `body_mass_comments` TEXT NULL ,
  `body_mass_bibref_id` INT UNSIGNED NULL ,
  `locality_description` TEXT NULL ,
  `locality_coordinates` TEXT NULL ,
  `locality_absolute_age` TEXT NULL ,
  `locality_absolute_age_bibref_id` INT UNSIGNED NULL ,
  `locality_relative_age` TEXT NULL ,
  `locality_relative_age_bibref_id` INT UNSIGNED NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  `institution_id` INT NULL ,
  PRIMARY KEY (`specimen_id`) ,
  INDEX `fk_ms_specimens_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_specimens_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_specimens_ms_bibliography1_idx` (`locality_relative_age_bibref_id` ASC) ,
  INDEX `fk_ms_specimens_ms_bibliography2_idx` (`locality_absolute_age_bibref_id` ASC) ,
  INDEX `fk_ms_specimens_ms_bibliography3_idx` (`body_mass_bibref_id` ASC) ,
  INDEX `fk_ms_specimens_ms_institutions1_idx` (`institution_id` ASC) ,
  CONSTRAINT `fk_ms_specimens_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_ms_bibliography1`
    FOREIGN KEY (`locality_relative_age_bibref_id` )
    REFERENCES `ms_bibliography` (`bibref_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_ms_bibliography2`
    FOREIGN KEY (`locality_absolute_age_bibref_id` )
    REFERENCES `ms_bibliography` (`bibref_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_ms_bibliography3`
    FOREIGN KEY (`body_mass_bibref_id` )
    REFERENCES `ms_bibliography` (`bibref_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_ms_institutions1`
    FOREIGN KEY (`institution_id` )
    REFERENCES `ms_institutions` (`institution_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_taxonomy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_taxonomy` ;

CREATE  TABLE IF NOT EXISTS `ms_taxonomy` (
  `taxon_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL ,
  `notes` TEXT NOT NULL ,
  `is_extinct` TINYINT UNSIGNED NOT NULL ,
  `common_name` VARCHAR(255) NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`taxon_id`) ,
  INDEX `fk_ms_taxonomy_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_taxonomy_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_taxonomy_ms_projects10`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_taxonomy_ms_users10`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_taxonomy_names`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_taxonomy_names` ;

CREATE  TABLE IF NOT EXISTS `ms_taxonomy_names` (
  `alt_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `taxon_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `notes` TEXT NOT NULL ,
  `genus` VARCHAR(255) NOT NULL ,
  `species` VARCHAR(255) NOT NULL ,
  `subspecies` VARCHAR(255) NOT NULL ,
  `variety` VARCHAR(255) NOT NULL ,
  `author` VARCHAR(255) NOT NULL ,
  `year` VARCHAR(10) NOT NULL ,
  `ht_supraspecific_clade` VARCHAR(255) NOT NULL ,
  `ht_kingdom` VARCHAR(255) NOT NULL ,
  `ht_phylum` VARCHAR(255) NOT NULL ,
  `ht_class` VARCHAR(255) NOT NULL ,
  `ht_subclass` VARCHAR(255) NOT NULL ,
  `ht_superorder` VARCHAR(255) NOT NULL ,
  `ht_order` VARCHAR(255) NOT NULL ,
  `ht_suborder` VARCHAR(255) NOT NULL ,
  `ht_superfamily` VARCHAR(255) NOT NULL ,
  `ht_family` VARCHAR(255) NOT NULL ,
  `ht_subfamily` VARCHAR(255) NOT NULL ,
  `source_info` TEXT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `justification` TEXT NOT NULL ,
  `review_status` TINYINT UNSIGNED NOT NULL ,
  `review_notes` TEXT NOT NULL ,
  `reviewed_on` INT UNSIGNED NULL ,
  `reviewed_by_id` INT UNSIGNED NULL ,
  `is_primary` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`alt_id`) ,
  INDEX `fk_ms_taxonomy_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_taxonomy_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_taxonomy_alternate_names_ms_taxonomy1_idx` (`taxon_id` ASC) ,
  INDEX `fk_ms_taxonomy_alternate_names_ms_users1_idx` (`reviewed_by_id` ASC) ,
  UNIQUE INDEX `u_primary` (`taxon_id` ASC, `is_primary` ASC) ,
  CONSTRAINT `fk_ms_taxonomy_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_taxonomy_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_taxonomy_alternate_names_ms_taxonomy1`
    FOREIGN KEY (`taxon_id` )
    REFERENCES `ms_taxonomy` (`taxon_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_taxonomy_alternate_names_ms_users1`
    FOREIGN KEY (`reviewed_by_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_facilities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_facilities` ;

CREATE  TABLE IF NOT EXISTS `ms_facilities` (
  `facility_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TEXT NOT NULL ,
  `institution` VARCHAR(45) NOT NULL ,
  `address1` VARCHAR(45) NOT NULL ,
  `address2` VARCHAR(45) NOT NULL ,
  `city` VARCHAR(45) NOT NULL ,
  `stateprov` VARCHAR(45) NOT NULL ,
  `postalcode` VARCHAR(45) NOT NULL ,
  `country` VARCHAR(45) NOT NULL ,
  `contact` VARCHAR(45) NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`facility_id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC, `project_id` ASC) ,
  INDEX `fk_ms_facilities_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_facilities_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_facilities_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_facilities_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_media`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_media` ;

CREATE  TABLE IF NOT EXISTS `ms_media` (
  `media_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `specimen_id` INT UNSIGNED NOT NULL ,
  `facility_id` INT UNSIGNED NOT NULL ,
  `media` LONGBLOB NOT NULL ,
  `preview` LONGBLOB NOT NULL ,
  `notes` TEXT NOT NULL ,
  `element` VARCHAR(255) NULL ,
  `is_copyrighted` TINYINT UNSIGNED NOT NULL ,
  `copyright_info` VARCHAR(255) NOT NULL ,
  `copyright_permission` TINYINT UNSIGNED NOT NULL ,
  `copyright_license` TINYINT UNSIGNED NULL ,
  `media_metadata` LONGTEXT NOT NULL ,
  `created_on` INT NOT NULL ,
  `last_modified_on` INT NOT NULL ,
  `approval_status` TINYINT UNSIGNED NOT NULL ,
  `scanner_type` TINYINT UNSIGNED NULL ,
  `scanner_x_resolution` VARCHAR(45) NULL ,
  `scanner_y_resolution` VARCHAR(45) NULL ,
  `scanner_z_resolution` VARCHAR(45) NULL ,
  `scanner_voltage` VARCHAR(45) NULL ,
  `scanner_amperage` VARCHAR(45) NULL ,
  `scanner_watts` VARCHAR(45) NULL ,
  `scanner_projections` VARCHAR(45) NULL ,
  `scanner_frame_averaging` VARCHAR(45) NULL ,
  `scanner_acquisition_time` VARCHAR(45) NULL ,
  `scanner_wedge` VARCHAR(45) NULL ,
  `scanner_calibration_check` TINYINT UNSIGNED NULL ,
  `scanner_calibration_description` TEXT NULL ,
  `scanner_technicians` TEXT NULL ,
  PRIMARY KEY (`media_id`) ,
  INDEX `fk_ms_media_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_media_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_media_ms_specimens1_idx` (`specimen_id` ASC) ,
  INDEX `fk_ms_media_ms_facilities1_idx` (`facility_id` ASC) ,
  CONSTRAINT `fk_ms_media_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_ms_specimens1`
    FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_specimens` (`specimen_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_ms_facilities1`
    FOREIGN KEY (`facility_id` )
    REFERENCES `ms_facilities` (`facility_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_media_multifiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_media_multifiles` ;

create table ms_media_multifiles (
	multifile_id		int unsigned not null auto_increment,
	media_id			int unsigned not null references ms_media(media_id),
	resource_path		text not null,
	media				longblob not null,
	media_metadata		longblob not null,
	media_content		longtext not null,
	rank				int unsigned not null default 0,	
	primary key (multifile_id),
	key i_resource_path ca_object_representation_multifiles(resource_path(255)),
	key i_media_id ca_object_representation_multifiles(media_id)
) engine=innodb CHARACTER SET utf8 COLLATE utf8_general_ci;


-- -----------------------------------------------------
-- Table `ms_project_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_project_users` ;

CREATE  TABLE IF NOT EXISTS `ms_project_users` (
  `membership_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `membership_type` TINYINT UNSIGNED NOT NULL ,
  `joined_on` INT UNSIGNED NOT NULL ,
  `last_access_on` INT UNSIGNED NULL ,
  `active` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`membership_id`) ,
  INDEX `fk_ms_project_users_ms_projects_idx` (`project_id` ASC) ,
  INDEX `fk_ms_project_users_ms_users1_idx` (`user_id` ASC) ,
  UNIQUE INDEX `u_all` (`project_id` ASC, `user_id` ASC) ,
  CONSTRAINT `fk_ms_project_users_ms_projects`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_project_users_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_media_x_bibliography`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_media_x_bibliography` ;

CREATE  TABLE IF NOT EXISTS `ms_media_x_bibliography` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `bibref_id` INT UNSIGNED NOT NULL ,
  `pp` VARCHAR(255) NOT NULL ,
  `notes` TEXT NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`link_id`) ,
  INDEX `fk_ms_media_x_bibliography_ms_media1_idx` (`media_id` ASC) ,
  INDEX `fk_ms_media_x_bibliography_ms_bibliography1_idx` (`bibref_id` ASC) ,
  INDEX `fk_ms_media_x_bibliography_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_media1`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_bibliography1`
    FOREIGN KEY (`bibref_id` )
    REFERENCES `ms_bibliography` (`bibref_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_specimens_x_bibliography`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_specimens_x_bibliography` ;

CREATE  TABLE IF NOT EXISTS `ms_specimens_x_bibliography` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `specimen_id` INT UNSIGNED NOT NULL ,
  `bibref_id` INT UNSIGNED NOT NULL ,
  `pp` VARCHAR(255) NOT NULL ,
  `notes` TEXT NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`link_id`) ,
  INDEX `fk_ms_specimens_x_bibliography_ms_specimen_idx` (`specimen_id` ASC) ,
  INDEX `fk_ms_specimens_x_bibliography_ms_bibliography1_idx` (`specimen_id` ASC) ,
  INDEX `fk_ms_specimens_x_bibliography_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_media10`
    FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_bibliography10`
    FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_specimens` (`specimen_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_bibliography_ms_users10`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_ontology`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_ontology` ;

CREATE  TABLE IF NOT EXISTS `ms_ontology` (
  `term_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `common_name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`term_id`) ,
  INDEX `fk_ms_ontology_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_ontology_ms_projects1_idx` (`project_id` ASC) ,
  CONSTRAINT `fk_ms_ontology_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_ontology_hierarchy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_ontology_hierarchy` ;

CREATE  TABLE IF NOT EXISTS `ms_ontology_hierarchy` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT UNSIGNED NOT NULL ,
  `child_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`link_id`, `user_id`) ,
  INDEX `fk_ms_ontology_hierarchy_ms_ontology1_idx` (`parent_id` ASC) ,
  INDEX `fk_ms_ontology_hierarchy_ms_ontology2_idx` (`child_id` ASC) ,
  INDEX `fk_ms_ontology_hierarchy_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_ontology_hierarchy_ms_ontology1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `ms_ontology` (`term_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_hierarchy_ms_ontology2`
    FOREIGN KEY (`child_id` )
    REFERENCES `ms_ontology` (`term_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_hierarchy_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_ontology_terms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_ontology_terms` ;

CREATE  TABLE IF NOT EXISTS `ms_ontology_terms` (
  `alt_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `term_id` INT UNSIGNED NOT NULL ,
  `term` VARCHAR(255) NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `last_modified_on` INT UNSIGNED NOT NULL ,
  `justification` TEXT NOT NULL ,
  `review_status` TINYINT UNSIGNED NOT NULL ,
  `review_notes` TEXT NOT NULL ,
  `reviewed_on` INT UNSIGNED NULL ,
  `reviewed_by_id` INT UNSIGNED NULL ,
  `is_primary` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`alt_id`) ,
  INDEX `fk_ms_ontology_alternate_terms_ms_ontology1_idx` (`term_id` ASC) ,
  INDEX `fk_ms_ontology_alternate_terms_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_ontology_alternate_terms_ms_projects1_idx` (`project_id` ASC) ,
  INDEX `fk_ms_ontology_alternate_terms_ms_users2_idx` (`reviewed_by_id` ASC) ,
  UNIQUE INDEX `u_primary` (`term_id` ASC, `is_primary` ASC) ,
  CONSTRAINT `fk_ms_ontology_alternate_terms_ms_ontology1`
    FOREIGN KEY (`term_id` )
    REFERENCES `ms_ontology` (`term_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_alternate_terms_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_alternate_terms_ms_projects1`
    FOREIGN KEY (`project_id` )
    REFERENCES `ms_projects` (`project_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_ontology_alternate_terms_ms_users2`
    FOREIGN KEY (`reviewed_by_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_media_x_ontology`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_media_x_ontology` ;

CREATE  TABLE IF NOT EXISTS `ms_media_x_ontology` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT UNSIGNED NOT NULL ,
  `term_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `alt_id` INT UNSIGNED NULL ,
  PRIMARY KEY (`link_id`) ,
  INDEX `fk_ms_media_x_ontology_ms_media1_idx` (`media_id` ASC) ,
  INDEX `fk_ms_media_x_ontology_ms_ontology1_idx` (`term_id` ASC) ,
  INDEX `fk_ms_media_x_ontology_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_media_x_ontology_ms_ontology_alternate_terms1_idx` (`alt_id` ASC) ,
  CONSTRAINT `fk_ms_media_x_ontology_ms_media1`
    FOREIGN KEY (`media_id` )
    REFERENCES `ms_media` (`media_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_ontology_ms_ontology1`
    FOREIGN KEY (`term_id` )
    REFERENCES `ms_ontology` (`term_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_ontology_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_media_x_ontology_ms_ontology_alternate_terms1`
    FOREIGN KEY (`alt_id` )
    REFERENCES `ms_ontology_terms` (`alt_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_specimens_x_ontology`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_specimens_x_ontology` ;

CREATE  TABLE IF NOT EXISTS `ms_specimens_x_ontology` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `specimen_id` INT UNSIGNED NOT NULL ,
  `term_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `alt_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`link_id`) ,
  INDEX `fk_ms_specimens_x_ontology_ms_specimens1_idx` (`specimen_id` ASC) ,
  INDEX `fk_ms_specimens_x_ontology_ms_ontology1_idx` (`term_id` ASC) ,
  INDEX `fk_ms_specimens_x_ontology_ms_users1_idx` (`user_id` ASC) ,
  INDEX `fk_ms_specimens_x_ontology_ms_ontology_alternate_terms1_idx` (`alt_id` ASC) ,
  CONSTRAINT `fk_ms_specimens_x_ontology_ms_specimens1`
    FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_specimens` (`specimen_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_ontology_ms_ontology1`
    FOREIGN KEY (`term_id` )
    REFERENCES `ms_ontology` (`term_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_ontology_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_ontology_ms_ontology_alternate_terms1`
    FOREIGN KEY (`alt_id` )
    REFERENCES `ms_ontology_terms` (`alt_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ms_specimens_x_taxonomy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ms_specimens_x_taxonomy` ;

CREATE  TABLE IF NOT EXISTS `ms_specimens_x_taxonomy` (
  `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `specimen_id` INT UNSIGNED NOT NULL ,
  `taxon_id` INT UNSIGNED NOT NULL ,
  `alt_id` INT UNSIGNED NULL ,
  `justification` TEXT NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`link_id`) ,
  INDEX `fk_ms_specimens_x_taxonomy_ms_specimens1_idx` (`specimen_id` ASC) ,
  INDEX `fk_ms_specimens_x_taxonomy_ms_taxonomy1_idx` (`taxon_id` ASC) ,
  INDEX `fk_ms_specimens_x_taxonomy_ms_taxonomy_alternate_names1_idx` (`alt_id` ASC) ,
  INDEX `fk_ms_specimens_x_taxonomy_ms_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_ms_specimens_x_taxonomy_ms_specimens1`
    FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_specimens` (`specimen_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_taxonomy_ms_taxonomy1`
    FOREIGN KEY (`taxon_id` )
    REFERENCES `ms_taxonomy` (`taxon_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_taxonomy_ms_taxonomy_alternate_names1`
    FOREIGN KEY (`alt_id` )
    REFERENCES `ms_taxonomy_names` (`alt_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ms_specimens_x_taxonomy_ms_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ca_ips`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_ips` ;

CREATE  TABLE IF NOT EXISTS `ca_ips` (
  `ip_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `ip1` TINYINT UNSIGNED NOT NULL ,
  `ip2` TINYINT UNSIGNED NULL DEFAULT NULL ,
  `ip3` TINYINT UNSIGNED NULL DEFAULT NULL ,
  `ip4s` TINYINT UNSIGNED NULL DEFAULT NULL ,
  `ip4e` TINYINT UNSIGNED NULL DEFAULT NULL ,
  `notes` TEXT NOT NULL ,
  PRIMARY KEY (`ip_id`) ,
  UNIQUE INDEX `u_ip` (`ip1` ASC, `ip2` ASC, `ip3` ASC, `ip4s` ASC, `ip4e` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_ca_ips_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_sql_search_words`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_sql_search_words` ;

CREATE  TABLE IF NOT EXISTS `ca_sql_search_words` (
  `word_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `word` VARCHAR(255) NOT NULL ,
  `stem` VARCHAR(255) NOT NULL ,
  `locale_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`word_id`) ,
  UNIQUE INDEX `u_word` (`word` ASC) ,
  INDEX `i_stem` (`stem` ASC) ,
  INDEX `i_locale_id` (`locale_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_sql_search_word_index`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_sql_search_word_index` ;

CREATE  TABLE IF NOT EXISTS `ca_sql_search_word_index` (
  `table_num` TINYINT(3) UNSIGNED NOT NULL ,
  `row_id` INT(10) UNSIGNED NOT NULL ,
  `field_table_num` TINYINT(3) UNSIGNED NOT NULL ,
  `field_num` TINYINT(3) UNSIGNED NOT NULL ,
  `field_row_id` INT(10) UNSIGNED NOT NULL ,
  `rel_type_id` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `word_id` INT(10) UNSIGNED NOT NULL ,
  `boost` TINYINT UNSIGNED NOT NULL DEFAULT 1 ,
  `access` TINYINT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `i_row_id` (`row_id` ASC, `table_num` ASC) ,
  INDEX `i_word_id` (`word_id` ASC, `access` ASC) ,
  INDEX `i_field_row_id` (`field_row_id` ASC, `field_table_num` ASC) ,
  INDEX `i_rel_type_id` (`rel_type_id` ASC) ,
  INDEX `fk_words_idx` (`word_id` ASC) ,
  CONSTRAINT `fk_words`
    FOREIGN KEY (`word_id` )
    REFERENCES `ca_sql_search_words` (`word_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_sql_search_ngrams`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_sql_search_ngrams` ;

CREATE  TABLE IF NOT EXISTS `ca_sql_search_ngrams` (
  `word_id` INT(10) UNSIGNED NOT NULL ,
  `ngram` CHAR(4) NOT NULL ,
  `seq` TINYINT(3) UNSIGNED NOT NULL ,
  PRIMARY KEY (`word_id`, `seq`) ,
  INDEX `i_ngram` (`ngram` ASC) ,
  INDEX `fk_words_idx` (`word_id` ASC) ,
  CONSTRAINT `fk_words1`
    FOREIGN KEY (`word_id` )
    REFERENCES `ca_sql_search_words` (`word_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_task_queue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_task_queue` ;

CREATE  TABLE IF NOT EXISTS `ca_task_queue` (
  `task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `row_key` CHAR(32) NULL DEFAULT NULL ,
  `entity_key` CHAR(32) NULL DEFAULT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `started_on` INT UNSIGNED NULL DEFAULT NULL ,
  `completed_on` INT UNSIGNED NULL DEFAULT NULL ,
  `priority` SMALLINT UNSIGNED NOT NULL ,
  `handler` VARCHAR(20) NOT NULL ,
  `parameters` TEXT NOT NULL ,
  `notes` TEXT NOT NULL ,
  `error_code` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`task_id`) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_started_on` (`started_on` ASC) ,
  INDEX `i_completed_on` (`completed_on` ASC) ,
  INDEX `i_entity_key` (`entity_key` ASC) ,
  INDEX `i_row_key` (`row_key` ASC) ,
  INDEX `i_error_code` (`error_code` ASC) ,
  INDEX `fk_user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_user_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_user_groups` ;

CREATE  TABLE IF NOT EXISTS `ca_user_groups` (
  `group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT UNSIGNED NULL DEFAULT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `code` VARCHAR(20) NOT NULL ,
  `description` TEXT NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `vars` TEXT NOT NULL ,
  `hier_left` DECIMAL(30,20) NOT NULL ,
  `hier_right` DECIMAL(30,20) NOT NULL ,
  PRIMARY KEY (`group_id`) ,
  INDEX `i_hier_left` (`hier_left` ASC) ,
  INDEX `i_hier_right` (`hier_right` ASC) ,
  INDEX `i_parent_id` (`parent_id` ASC) ,
  UNIQUE INDEX `u_name` (`name` ASC) ,
  UNIQUE INDEX `u_code` (`code` ASC) ,
  CONSTRAINT `fk_ca_user_groups_parent_id`
    FOREIGN KEY (`parent_id` )
    REFERENCES `ca_user_groups` (`group_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_user_roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_user_roles` ;

CREATE  TABLE IF NOT EXISTS `ca_user_roles` (
  `role_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `code` VARCHAR(20) NOT NULL ,
  `description` TEXT NOT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `vars` TEXT NOT NULL ,
  `field_access` TEXT NOT NULL ,
  PRIMARY KEY (`role_id`) ,
  UNIQUE INDEX `u_name` (`name` ASC) ,
  UNIQUE INDEX `u_code` (`code` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_users_x_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_users_x_groups` ;

CREATE  TABLE IF NOT EXISTS `ca_users_x_groups` (
  `relation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `group_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`relation_id`) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_group_id` (`group_id` ASC) ,
  UNIQUE INDEX `u_all` (`user_id` ASC, `group_id` ASC) ,
  CONSTRAINT `fk_ca_users_x_groups_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ca_users_x_groups_group_id`
    FOREIGN KEY (`group_id` )
    REFERENCES `ca_user_groups` (`group_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_users_x_roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_users_x_roles` ;

CREATE  TABLE IF NOT EXISTS `ca_users_x_roles` (
  `relation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `role_id` SMALLINT UNSIGNED NOT NULL ,
  `rank` INT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`relation_id`) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_role_id` (`role_id` ASC) ,
  UNIQUE INDEX `u_all` (`user_id` ASC, `role_id` ASC) ,
  CONSTRAINT `fk_ca_users_x_roles_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_ca_users_x_roles_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `ca_user_roles` (`role_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_item_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_item_comments` ;

CREATE  TABLE IF NOT EXISTS `ca_item_comments` (
  `comment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `media1` LONGBLOB NOT NULL ,
  `media2` LONGBLOB NOT NULL ,
  `media3` LONGBLOB NOT NULL ,
  `media4` LONGBLOB NOT NULL ,
  `comment` TEXT NULL DEFAULT NULL ,
  `rating` TINYINT NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `access` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `ip_addr` VARCHAR(39) NULL DEFAULT NULL ,
  `moderated_on` INT UNSIGNED NULL DEFAULT NULL ,
  `moderated_by_user_id` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`comment_id`) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_email` (`email` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_created_on` (`created_on` ASC) ,
  INDEX `i_access` (`access` ASC) ,
  INDEX `i_moderated_on` (`moderated_on` ASC) ,
  INDEX `fk_moderator_idx` (`moderated_by_user_id` ASC) ,
  CONSTRAINT `fk_8701C3AD-ED71-41F1-A9AA-EFA01D447C60`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` ),
  CONSTRAINT `fk_moderator`
    FOREIGN KEY (`moderated_by_user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_item_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_item_tags` ;

CREATE  TABLE IF NOT EXISTS `ca_item_tags` (
  `tag_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `tag` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`tag_id`) ,
  INDEX `u_tag` (`tag` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_items_x_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_items_x_tags` ;

CREATE  TABLE IF NOT EXISTS `ca_items_x_tags` (
  `relation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `access` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `ip_addr` CHAR(39) NULL DEFAULT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  `moderated_on` INT UNSIGNED NULL DEFAULT NULL ,
  `moderated_by_user_id` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`relation_id`) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_tag_id` (`tag_id` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_access` (`access` ASC) ,
  INDEX `i_created_on` (`created_on` ASC) ,
  INDEX `i_moderated_on` (`moderated_on` ASC) ,
  INDEX `fk_4124ABD9-1F95-4CA7-96BD-79531FD209DB` (`tag_id` ASC) ,
  INDEX `fk_moderator_idx` (`moderated_by_user_id` ASC) ,
  CONSTRAINT `fk_E6D8A68D-AA1B-4BE5-AA1B-5BEAD89A774E`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` ),
  CONSTRAINT `fk_4124ABD9-1F95-4CA7-96BD-79531FD209DB`
    FOREIGN KEY (`tag_id` )
    REFERENCES `ca_item_tags` (`tag_id` ),
  CONSTRAINT `fk_moderator1`
    FOREIGN KEY (`moderated_by_user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_item_views`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_item_views` ;

CREATE  TABLE IF NOT EXISTS `ca_item_views` (
  `view_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `viewed_on` INT UNSIGNED NOT NULL ,
  `ip_addr` VARCHAR(39) NULL DEFAULT NULL ,
  PRIMARY KEY (`view_id`) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_created_on` (`viewed_on` ASC) ,
  CONSTRAINT `fk_A290F070-DABA-45C7-B539-6711A56A15B8`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_item_view_counts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_item_view_counts` ;

CREATE  TABLE IF NOT EXISTS `ca_item_view_counts` (
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `view_count` INT UNSIGNED NOT NULL ,
  INDEX `u_row` (`row_id` ASC, `table_num` ASC) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_view_count` (`view_count` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_search_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_search_log` ;

CREATE  TABLE IF NOT EXISTS `ca_search_log` (
  `search_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `log_datetime` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `search_expression` VARCHAR(1024) NOT NULL ,
  `num_hits` INT UNSIGNED NOT NULL ,
  `form_id` INT UNSIGNED NULL DEFAULT NULL ,
  `ip_addr` CHAR(15) NULL DEFAULT NULL ,
  `details` TEXT NOT NULL ,
  `execution_time` DECIMAL(7,3) NOT NULL ,
  `search_source` VARCHAR(40) NOT NULL ,
  PRIMARY KEY (`search_id`) ,
  INDEX `i_log_datetime` (`log_datetime` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_form_id` (`form_id` ASC)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_user_notes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_user_notes` ;

CREATE  TABLE IF NOT EXISTS `ca_user_notes` (
  `note_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `bundle_name` VARCHAR(255) NOT NULL ,
  `note` LONGTEXT NOT NULL ,
  `created_on` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`note_id`) ,
  INDEX `i_row_id` (`row_id` ASC, `table_num` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_bundle_name` (`bundle_name` ASC) ,
  CONSTRAINT `fk_ca_user_notes_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_bookmark_folders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_bookmark_folders` ;

CREATE  TABLE IF NOT EXISTS `ca_bookmark_folders` (
  `folder_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`folder_id`) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_8B72B143-3FA9-47CB-A154-987A66720DC5`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_bookmarks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_bookmarks` ;

CREATE  TABLE IF NOT EXISTS `ca_bookmarks` (
  `bookmark_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `folder_id` INT UNSIGNED NOT NULL ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `notes` TEXT NOT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `created_on` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`bookmark_id`) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_folder_id` (`folder_id` ASC) ,
  CONSTRAINT `fk_34EBB120-0314-498E-B993-3BB0347A1981`
    FOREIGN KEY (`folder_id` )
    REFERENCES `ca_bookmark_folders` (`folder_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_change_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_change_log` ;

CREATE  TABLE IF NOT EXISTS `ca_change_log` (
  `log_id` BIGINT NOT NULL AUTO_INCREMENT ,
  `log_datetime` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `changetype` CHAR(1) NOT NULL ,
  `logged_table_num` TINYINT UNSIGNED NOT NULL ,
  `logged_row_id` INT UNSIGNED NOT NULL ,
  `rolledback` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `unit_id` CHAR(32) NULL DEFAULT NULL ,
  `batch_id` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`log_id`) ,
  INDEX `i_datetime` (`log_datetime` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_logged` (`logged_row_id` ASC, `logged_table_num` ASC) ,
  INDEX `i_unit_id` (`unit_id` ASC) ,
  INDEX `i_table_num` (`logged_table_num` ASC) ,
  INDEX `i_batch_id` (`batch_id` ASC) ,
  INDEX `fk_user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_id1`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_change_log_snapshots`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_change_log_snapshots` ;

CREATE  TABLE IF NOT EXISTS `ca_change_log_snapshots` (
  `log_id` BIGINT NOT NULL ,
  `snapshot` LONGBLOB NOT NULL ,
  INDEX `i_log_id` (`log_id` ASC) ,
  CONSTRAINT `fk_ca_change_log_snaphots_log_id`
    FOREIGN KEY (`log_id` )
    REFERENCES `ca_change_log` (`log_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_change_log_subjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_change_log_subjects` ;

CREATE  TABLE IF NOT EXISTS `ca_change_log_subjects` (
  `log_id` BIGINT NOT NULL ,
  `subject_table_num` TINYINT UNSIGNED NOT NULL ,
  `subject_row_id` INT UNSIGNED NOT NULL ,
  INDEX `i_log_id` (`log_id` ASC) ,
  INDEX `i_subject` (`subject_row_id` ASC, `subject_table_num` ASC) ,
  CONSTRAINT `fk_ca_change_log_subjects_log_id`
    FOREIGN KEY (`log_id` )
    REFERENCES `ca_change_log` (`log_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_eventlog`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_eventlog` ;

CREATE  TABLE IF NOT EXISTS `ca_eventlog` (
  `date_time` INT UNSIGNED NOT NULL ,
  `code` CHAR(4) NOT NULL ,
  `message` TEXT NOT NULL ,
  `source` VARCHAR(255) NOT NULL ,
  INDEX `i_when` (`date_time` ASC) ,
  INDEX `i_source` (`source` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_schema_updates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_schema_updates` ;

CREATE  TABLE IF NOT EXISTS `ca_schema_updates` (
  `version_num` INT UNSIGNED NOT NULL ,
  `datetime` INT UNSIGNED NOT NULL ,
  UNIQUE INDEX `u_version_num` (`version_num` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_locales`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_locales` ;

CREATE  TABLE IF NOT EXISTS `ca_locales` (
  `locale_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `language` VARCHAR(3) NOT NULL ,
  `country` CHAR(2) NOT NULL ,
  `dialect` VARCHAR(8) NULL DEFAULT NULL ,
  `dont_use_for_cataloguing` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`locale_id`) ,
  INDEX `u_language_country` (`language` ASC, `country` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_application_vars`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_application_vars` ;

CREATE  TABLE IF NOT EXISTS `ca_application_vars` (
  `vars` LONGTEXT NOT NULL )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_acl`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_acl` ;

CREATE  TABLE IF NOT EXISTS `ca_acl` (
  `acl_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `group_id` INT UNSIGNED NULL DEFAULT NULL ,
  `user_id` INT UNSIGNED NULL DEFAULT NULL ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  `access` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `notes` CHAR(10) NOT NULL ,
  `inherited_from_table_num` TINYINT UNSIGNED NULL DEFAULT NULL ,
  `inherited_from_row_id` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`acl_id`) ,
  INDEX `i_row_id` (`row_id` ASC, `table_num` ASC) ,
  INDEX `i_user_id` (`user_id` ASC) ,
  INDEX `i_group_id` (`group_id` ASC) ,
  INDEX `i_inherited_from_table_num` (`inherited_from_table_num` ASC) ,
  INDEX `i_inherited_from_row_id` (`inherited_from_row_id` ASC) ,
  CONSTRAINT `fk_ca_acl_group_id`
    FOREIGN KEY (`group_id` )
    REFERENCES `ca_user_groups` (`group_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_acl_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_lists`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_lists` ;

CREATE  TABLE IF NOT EXISTS `ca_lists` (
  `list_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `list_code` VARCHAR(100) NOT NULL ,
  `is_system_list` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `is_hierarchical` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `use_as_vocabulary` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `default_sort` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`list_id`) ,
  UNIQUE INDEX `u_code` (`list_code` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_list_labels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_list_labels` ;

CREATE  TABLE IF NOT EXISTS `ca_list_labels` (
  `label_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `list_id` SMALLINT UNSIGNED NOT NULL ,
  `locale_id` SMALLINT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`label_id`) ,
  INDEX `i_list_id` (`list_id` ASC) ,
  INDEX `i_name` (`name` ASC) ,
  UNIQUE INDEX `u_locale_id` (`list_id` ASC, `locale_id` ASC) ,
  INDEX `fk_ca_list_labels_locale_id` (`locale_id` ASC) ,
  CONSTRAINT `fk_ca_list_labels_list_id`
    FOREIGN KEY (`list_id` )
    REFERENCES `ca_lists` (`list_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_labels_locale_id`
    FOREIGN KEY (`locale_id` )
    REFERENCES `ca_locales` (`locale_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_list_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_list_items` ;

CREATE  TABLE IF NOT EXISTS `ca_list_items` (
  `item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` INT UNSIGNED NULL DEFAULT NULL ,
  `list_id` SMALLINT UNSIGNED NOT NULL ,
  `type_id` INT UNSIGNED NULL DEFAULT NULL ,
  `idno` VARCHAR(255) NOT NULL ,
  `idno_sort` VARCHAR(255) NOT NULL ,
  `item_value` VARCHAR(255) NOT NULL ,
  `rank` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `hier_left` DECIMAL(30,20) NOT NULL ,
  `hier_right` DECIMAL(30,20) NOT NULL ,
  `is_enabled` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `is_default` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `validation_format` VARCHAR(255) NOT NULL ,
  `color` CHAR(6) NULL DEFAULT NULL ,
  `icon` LONGBLOB NOT NULL ,
  `access` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`item_id`) ,
  INDEX `i_list_id` (`list_id` ASC) ,
  INDEX `i_parent_id` (`parent_id` ASC) ,
  INDEX `i_idno` (`idno` ASC) ,
  INDEX `i_idno_sort` (`idno_sort` ASC) ,
  INDEX `i_hier_left` (`hier_left` ASC) ,
  INDEX `i_hier_right` (`hier_right` ASC) ,
  INDEX `i_value_text` (`item_value` ASC) ,
  INDEX `i_type_id` (`type_id` ASC) ,
  CONSTRAINT `fk_ca_list_items_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_items_list_id`
    FOREIGN KEY (`list_id` )
    REFERENCES `ca_lists` (`list_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_items_parent_id`
    FOREIGN KEY (`parent_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_list_item_labels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_list_item_labels` ;

CREATE  TABLE IF NOT EXISTS `ca_list_item_labels` (
  `label_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `item_id` INT UNSIGNED NOT NULL ,
  `locale_id` SMALLINT UNSIGNED NOT NULL ,
  `type_id` INT UNSIGNED NULL DEFAULT NULL ,
  `name_singular` VARCHAR(255) NOT NULL ,
  `name_plural` VARCHAR(255) NOT NULL ,
  `name_sort` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `source_info` LONGTEXT NOT NULL ,
  `is_preferred` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`label_id`) ,
  INDEX `i_name_singular` (`item_id` ASC, `name_singular` ASC) ,
  INDEX `i_name` (`item_id` ASC, `name_plural` ASC) ,
  INDEX `i_item_id` (`item_id` ASC) ,
  UNIQUE INDEX `u_all` (`item_id` ASC, `name_singular` ASC, `name_plural` ASC, `type_id` ASC, `locale_id` ASC) ,
  INDEX `i_name_sort` (`name_sort` ASC) ,
  INDEX `i_type_id` (`type_id` ASC) ,
  INDEX `fk_ca_list_item_labels_locale_id` (`locale_id` ASC) ,
  CONSTRAINT `fk_ca_list_item_labels_item_id`
    FOREIGN KEY (`item_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_item_labels_locale_id`
    FOREIGN KEY (`locale_id` )
    REFERENCES `ca_locales` (`locale_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_item_labels_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_metadata_elements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_metadata_elements` ;

CREATE  TABLE IF NOT EXISTS `ca_metadata_elements` (
  `element_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `list_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `element_code` VARCHAR(30) NOT NULL ,
  `documentation_url` VARCHAR(255) NOT NULL ,
  `datatype` TINYINT UNSIGNED NOT NULL ,
  `settings` LONGTEXT NOT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `hier_left` DECIMAL(30,20) NOT NULL ,
  `hier_right` DECIMAL(30,20) NOT NULL ,
  `hier_element_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`element_id`) ,
  INDEX `i_hier_element_id` (`hier_element_id` ASC) ,
  UNIQUE INDEX `u_name_short` (`element_code` ASC) ,
  INDEX `i_parent_id` (`parent_id` ASC) ,
  INDEX `i_hier_left` (`hier_left` ASC) ,
  INDEX `i_hier_right` (`hier_right` ASC) ,
  INDEX `i_list_id` (`list_id` ASC) ,
  CONSTRAINT `fk_ca_metadata_elements_list_id`
    FOREIGN KEY (`list_id` )
    REFERENCES `ca_lists` (`list_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_metadata_elements_parent_id`
    FOREIGN KEY (`parent_id` )
    REFERENCES `ca_metadata_elements` (`element_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_metadata_element_labels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_metadata_element_labels` ;

CREATE  TABLE IF NOT EXISTS `ca_metadata_element_labels` (
  `label_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `element_id` SMALLINT UNSIGNED NOT NULL ,
  `locale_id` SMALLINT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  PRIMARY KEY (`label_id`) ,
  INDEX `i_element_id` (`element_id` ASC) ,
  INDEX `i_name` (`name` ASC) ,
  INDEX `i_locale_id` (`locale_id` ASC) ,
  CONSTRAINT `fk_ca_metadata_element_labels_element_id`
    FOREIGN KEY (`element_id` )
    REFERENCES `ca_metadata_elements` (`element_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_metadata_element_labels_locale_id`
    FOREIGN KEY (`locale_id` )
    REFERENCES `ca_locales` (`locale_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_metadata_type_restrictions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_metadata_type_restrictions` ;

CREATE  TABLE IF NOT EXISTS `ca_metadata_type_restrictions` (
  `restriction_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `type_id` INT UNSIGNED NULL DEFAULT NULL ,
  `element_id` SMALLINT UNSIGNED NOT NULL ,
  `settings` LONGTEXT NOT NULL ,
  `include_subtypes` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`restriction_id`) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_type_id` (`type_id` ASC) ,
  INDEX `i_element_id` (`element_id` ASC) ,
  INDEX `i_include_subtypes` (`include_subtypes` ASC) ,
  CONSTRAINT `fk_ca_metadata_type_restrictions_element_id`
    FOREIGN KEY (`element_id` )
    REFERENCES `ca_metadata_elements` (`element_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_attributes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_attributes` ;

CREATE  TABLE IF NOT EXISTS `ca_attributes` (
  `attribute_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `element_id` SMALLINT UNSIGNED NOT NULL ,
  `locale_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `row_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`attribute_id`) ,
  INDEX `i_locale_id` (`locale_id` ASC) ,
  INDEX `i_row_id` (`row_id` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_element_id` (`element_id` ASC) ,
  CONSTRAINT `fk_ca_attributes_element_id`
    FOREIGN KEY (`element_id` )
    REFERENCES `ca_metadata_elements` (`element_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_attributes_locale_id`
    FOREIGN KEY (`locale_id` )
    REFERENCES `ca_locales` (`locale_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_attribute_values`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_attribute_values` ;

CREATE  TABLE IF NOT EXISTS `ca_attribute_values` (
  `value_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `element_id` SMALLINT UNSIGNED NOT NULL ,
  `attribute_id` INT UNSIGNED NOT NULL ,
  `item_id` INT UNSIGNED NULL DEFAULT NULL ,
  `value_longtext1` LONGTEXT NULL DEFAULT NULL ,
  `value_longtext2` LONGTEXT NULL DEFAULT NULL ,
  `value_blob` LONGBLOB NULL DEFAULT NULL ,
  `value_decimal1` DECIMAL(40,20) NULL DEFAULT NULL ,
  `value_decimal2` DECIMAL(40,20) NULL DEFAULT NULL ,
  `value_integer1` INT UNSIGNED NULL DEFAULT NULL ,
  `source_info` LONGTEXT NOT NULL ,
  PRIMARY KEY (`value_id`) ,
  INDEX `i_element_id` (`element_id` ASC) ,
  INDEX `i_attribute_id` (`attribute_id` ASC) ,
  INDEX `i_value_integer1` (`value_integer1` ASC) ,
  INDEX `i_value_decimal1` (`value_decimal1` ASC) ,
  INDEX `i_value_decimal2` (`value_decimal2` ASC) ,
  INDEX `i_item_id` (`item_id` ASC) ,
  INDEX `i_value_longtext1` (`value_longtext1`(500) ASC) ,
  INDEX `i_value_longtext2` (`value_longtext2`(500) ASC) ,
  CONSTRAINT `fk_ca_attribute_values_attribute_id`
    FOREIGN KEY (`attribute_id` )
    REFERENCES `ca_attributes` (`attribute_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_attribute_values_element_id`
    FOREIGN KEY (`element_id` )
    REFERENCES `ca_metadata_elements` (`element_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_attribute_values_item_id`
    FOREIGN KEY (`item_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_relationship_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_relationship_types` ;

CREATE  TABLE IF NOT EXISTS `ca_relationship_types` (
  `type_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `sub_type_left_id` INT UNSIGNED NULL DEFAULT NULL ,
  `sub_type_right_id` INT UNSIGNED NULL DEFAULT NULL ,
  `hier_left` DECIMAL(30,20) UNSIGNED NOT NULL ,
  `hier_right` DECIMAL(30,20) UNSIGNED NOT NULL ,
  `hier_type_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `table_num` TINYINT UNSIGNED NOT NULL ,
  `type_code` VARCHAR(30) NOT NULL ,
  `rank` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `is_default` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`type_id`) ,
  UNIQUE INDEX `u_type_code` (`type_code` ASC, `table_num` ASC) ,
  INDEX `i_table_num` (`table_num` ASC) ,
  INDEX `i_sub_type_left_id` (`sub_type_left_id` ASC) ,
  INDEX `i_sub_type_right_id` (`sub_type_right_id` ASC) ,
  INDEX `i_parent_id` (`parent_id` ASC) ,
  INDEX `i_hier_type_id` (`hier_type_id` ASC) ,
  INDEX `i_hier_left` (`hier_left` ASC) ,
  INDEX `i_hier_right` (`hier_right` ASC) ,
  CONSTRAINT `fk_ca_relationship_types_parent_id`
    FOREIGN KEY (`parent_id` )
    REFERENCES `ca_relationship_types` (`type_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_relationship_type_labels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_relationship_type_labels` ;

CREATE  TABLE IF NOT EXISTS `ca_relationship_type_labels` (
  `label_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type_id` SMALLINT UNSIGNED NOT NULL ,
  `locale_id` SMALLINT UNSIGNED NOT NULL ,
  `typename` VARCHAR(255) NOT NULL ,
  `typename_reverse` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `description_reverse` TEXT NOT NULL ,
  PRIMARY KEY (`label_id`) ,
  INDEX `i_type_id` (`type_id` ASC) ,
  INDEX `i_locale_id` (`locale_id` ASC) ,
  UNIQUE INDEX `u_typename` (`type_id` ASC, `locale_id` ASC, `typename` ASC) ,
  UNIQUE INDEX `u_typename_reverse` (`typename_reverse` ASC, `type_id` ASC, `locale_id` ASC) ,
  CONSTRAINT `fk_ca_relationship_type_labels_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `ca_relationship_types` (`type_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_relationship_type_labels_locale_id`
    FOREIGN KEY (`locale_id` )
    REFERENCES `ca_locales` (`locale_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ca_list_items_x_list_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ca_list_items_x_list_items` ;

CREATE  TABLE IF NOT EXISTS `ca_list_items_x_list_items` (
  `relation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `term_left_id` INT UNSIGNED NOT NULL ,
  `term_right_id` INT UNSIGNED NOT NULL ,
  `type_id` SMALLINT UNSIGNED NULL DEFAULT NULL ,
  `source_info` LONGTEXT NOT NULL ,
  `sdatetime` DECIMAL(30,20) NULL DEFAULT NULL ,
  `edatetime` DECIMAL(30,20) NULL DEFAULT NULL ,
  `label_left_id` INT UNSIGNED NULL DEFAULT NULL ,
  `label_right_id` INT UNSIGNED NULL DEFAULT NULL ,
  `rank` INT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`relation_id`) ,
  INDEX `i_term_left_id` (`term_left_id` ASC) ,
  INDEX `i_term_right_id` (`term_right_id` ASC) ,
  INDEX `i_type_id` (`type_id` ASC) ,
  UNIQUE INDEX `u_all` (`term_left_id` ASC, `term_right_id` ASC, `type_id` ASC, `sdatetime` ASC, `edatetime` ASC) ,
  INDEX `i_label_left_id` (`label_left_id` ASC) ,
  INDEX `i_label_right_id` (`label_right_id` ASC) ,
  CONSTRAINT `ca_ca_list_items_x_list_items_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `ca_relationship_types` (`type_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `ca_ca_list_items_x_list_items_term_left_id`
    FOREIGN KEY (`term_left_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `ca_ca_list_items_x_list_items_term_right_id`
    FOREIGN KEY (`term_right_id` )
    REFERENCES `ca_list_items` (`item_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_items_x_list_items_label_left_id`
    FOREIGN KEY (`label_left_id` )
    REFERENCES `ca_list_item_labels` (`label_id` )
    ON DELETE restrict
    ON UPDATE restrict,
  CONSTRAINT `fk_ca_list_items_x_list_items_label_right_id`
    FOREIGN KEY (`label_right_id` )
    REFERENCES `ca_list_item_labels` (`label_id` )
    ON DELETE restrict
    ON UPDATE restrict)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


/* Indicate up to what migration this schema definition covers */
/* CURRENT MIGRATION: 1 */
INSERT IGNORE INTO ca_schema_updates (version_num, datetime) VALUES (1, unix_timestamp());
