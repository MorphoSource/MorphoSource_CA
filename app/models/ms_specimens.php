<?php
/** ---------------------------------------------------------------------
 * app/models/ms_specimens.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage models
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
 
require_once(__CA_LIB_DIR__."/core/BaseModel.php");

BaseModel::$s_ca_models_definitions['ms_specimens'] = array(
 	'NAME_SINGULAR' 	=> _t('specimen'),
 	'NAME_PLURAL' 		=> _t('specimens'),
 	'FIELDS' 			=> array(
 		'specimen_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Specimen reference id'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this specimen.')
		),
		'project_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Project id', 'DESCRIPTION' => 'Project id'
		),
		'user_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'User id', 'DESCRIPTION' => 'User id'
		),
		'reference_source' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Reference source'), 'DESCRIPTION' => _t('Reference source of specimen.'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Reference 1') 	=> 1,
					_t('Reference 2')	=> 2
				)
		),
		'institution_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, "DEFAULT" => "",
				"LABEL" => "Find the institution for this specimen", "DESCRIPTION" => "Enter words from the name or location of the institution<br /> and select institution from resulting list of possible matches."
		),
		'institution_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Institution ode'), 'DESCRIPTION' => _t('Institution code of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'collection_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Collection code'), 'DESCRIPTION' => _t('Collection code of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'catalog_number' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Catalog number'), 'DESCRIPTION' => _t('Catalog number of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'sex' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Sex'), 'DESCRIPTION' => _t('Sex of specimen.'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Male') 	=> 'M',
					_t('Female')	=> 'F'
				)
		),
		'element' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Element'), 'DESCRIPTION' => _t('Element of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'side' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Side'), 'DESCRIPTION' => _t('Side of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Notes'), 'DESCRIPTION' => _t('Notes about the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'relative_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Relative age'), 'DESCRIPTION' => _t('Specimen\'s relative age.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'absolute_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 45, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Absolute age'), 'DESCRIPTION' => _t('Specimen\'s absolute age.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'body_mass' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 45, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Body mass'), 'DESCRIPTION' => _t('Specimen\'s body mass'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'body_mass_comments' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Body mass comments'), 'DESCRIPTION' => _t('Comments about the specimen\'s body mass.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'body_mass_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, "DEFAULT" => "",
				"LABEL" => "Find body mass bibliographic reference", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
		),
		'locality_description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality description'), 'DESCRIPTION' => _t('Description of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_coordinates' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality coordinates'), 'DESCRIPTION' => _t('Coordinates of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_absolute_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality absolute age'), 'DESCRIPTION' => _t('Absolute age of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_absolute_age_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, "DEFAULT" => "",
				"LABEL" => "Find bibliographic reference for the absolute age", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
		),
		'locality_relative_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality relative age'), 'DESCRIPTION' => _t('Relative age of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_relative_age_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, "DEFAULT" => "",
				"LABEL" => "Find bibliographic reference for the relative age", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
		),
		'approval_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Approval status'), 'DESCRIPTION' => _t('Approval status'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('New') 	=> 0,
					_t('Approved')	=> 1
				)
		),
		'created_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Created on'), 'DESCRIPTION' => _t('Date/time the specimen record was created.'),
		),
		'last_modified_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Last modified on'), 'DESCRIPTION' => _t('Date/time the specimen record was last modified.'),
		)
 	)
);

class ms_specimens extends BaseModel {
	# ---------------------------------
	# --- Object attribute properties
	# ---------------------------------
	# Describe structure of content object's properties - eg. database fields and their
	# associated types, what modes are supported, et al.
	#

	# ------------------------------------------------------
	# --- Basic object parameters
	# ------------------------------------------------------
	# what table does this class represent?
	protected $TABLE = 'ms_specimens';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'specimen_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('institution_code', 'collection_code', 'catalog_number');

	# When the list of "list fields" above contains more than one field,
	# the LIST_DELIMITER text is displayed between fields as a delimiter.
	# This is typically a comma or space, but can be any string you like
	protected $LIST_DELIMITER = ' ';


	# What you'd call a single record from this table (eg. a "person")
	protected $NAME_SINGULAR;

	# What you'd call more than one record from this table (eg. "people")
	protected $NAME_PLURAL;

	# List of fields to sort listing of records by; you can use 
	# SQL 'ASC' and 'DESC' here if you like.
	protected $ORDER_BY = array('institution_code', 'collection_code', 'catalog_number');

	# If you want to order records arbitrarily, add a numeric field to the table and place
	# its name here. The generic list scripts can then use it to order table records.
	protected $RANK = '';
	
	# ------------------------------------------------------
	# Hierarchical table properties
	# ------------------------------------------------------
	protected $HIERARCHY_TYPE				=	null;
	protected $HIERARCHY_LEFT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_RIGHT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_PARENT_ID_FLD		=	null;
	protected $HIERARCHY_DEFINITION_TABLE	=	null;
	protected $HIERARCHY_ID_FLD				=	null;
	protected $HIERARCHY_POLY_TABLE			=	null;
	
	# ------------------------------------------------------
	# Change logging
	# ------------------------------------------------------
	protected $UNIT_ID_FIELD = null;
	protected $LOG_CHANGES_TO_SELF = false;
	protected $LOG_CHANGES_USING_AS_SUBJECT = array(
		"FOREIGN_KEYS" => array(
		
		),
		"RELATED_TABLES" => array(
		
		)
	);	
	
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ----------------------------------------
	public function __construct($pn_id=null) {
		parent::__construct($pn_id);
	}
	# ----------------------------------------
}
?>