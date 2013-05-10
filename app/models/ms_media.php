<?php
/** ---------------------------------------------------------------------
 * app/models/ms_media.php
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

BaseModel::$s_ca_models_definitions['ms_media'] = array(
 	'NAME_SINGULAR' 	=> _t('media file'),
 	'NAME_PLURAL' 		=> _t('media files'),
 	'FIELDS' 			=> array(
 		'media_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media id'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this media')
		),
		'title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Title'), 'DESCRIPTION' => _t('Optional display title for image.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'project_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Project id', 'DESCRIPTION' => 'Project id'
		),
		'user_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Media id', 'DESCRIPTION' => 'User that uploaded media'
		),
		'media' => array(
				"FIELD_TYPE" => FT_MEDIA, "DISPLAY_TYPE" => DT_FIELD, 
				"DISPLAY_WIDTH" => 50, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => false, 
				"DEFAULT" => "",
				
				"MEDIA_PROCESSING_SETTING" => 'media_files',
				
				"LABEL" => "Select media file", 
				"DESCRIPTION" => "Use the button below to select a media file on your harddrive to upload."
		),
		'preview' => array(
				"FIELD_TYPE" => FT_MEDIA, "DISPLAY_TYPE" => DT_FIELD, 
				"DISPLAY_WIDTH" => 50, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => false, 
				"DEFAULT" => "",
				
				"MEDIA_PROCESSING_SETTING" => 'ms_media_previews',
				
				"LABEL" => "Select optional media preview", 
				"DESCRIPTION" => "Use the button below to select a file to use as a preview for the uploaded media. This is optional. If you do not provide a preview image then Morphosource will attempt to extract one automatically from the uploaded media."
		),
		'published' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Publication status'), 'DESCRIPTION' => _t('Release to public search?'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Not published/ Not available in public search" => 0,
					"Published/ available in public search" => 1
				)
		),
		'published_on' => array(
				'FIELD_TYPE' => FT_DATETIME, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Media published on'), 'DESCRIPTION' => _t('Date/time the Media was published.'),
		),
		'specimen_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 10, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => true, "DEFAULT" => "",
				"LABEL" => "Find the specimen this media depicts", "DESCRIPTION" => "Enter the catalog number of the specimen<br /> and select the specimen from the resulting list of possible matches."
		),
		'element' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Element'), 'DESCRIPTION' => _t('Element of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'side' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => "UNKNOWN",
				'LABEL' => _t('Side'), 'DESCRIPTION' => _t('Side of specimen depicted by media'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Unknown" => "UNKNOWN",
					"Left" => "LEFT",
					"Right" => "RIGHT"
				)
		),
		'facility_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_FIELD" => array('ms_facilities.name'), 
				"DISPLAY_ORDERBY" => array('ms_facilities.name'),
				"DISPLAY_WIDTH" => 100, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => false, "DEFAULT" => "",
				"LABEL" => "Find the facility this media file was created at", "DESCRIPTION" => "Enter the name of the facility<br /> and select the facility from the resulting list of possible matches."
		),
		'notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Notes'), 'DESCRIPTION' => _t('Notes about the media file.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'approval_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Approval status'), 'DESCRIPTION' => _t('Approval status'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('New') 	=> 0,
					_t('Approved')	=> 1
				)
		),
		'is_copyrighted' => array(
				"FIELD_TYPE" => FT_BIT, "DISPLAY_TYPE" => DT_CHECKBOXES, 
				"DISPLAY_WIDTH" => 1, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, 
				"DEFAULT" => 0,
				"LABEL" => "Is this media copyrighted?", "DESCRIPTION" => "When checked, indicates this media file has copyright restrictions."
		),
		'copyright_permission' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Copyright permission'), 'DESCRIPTION' => _t('Copyright permission'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Copyright permission not set" => 0,
					"Person loading media owns copyright and grants permission for use of media on MorphoSource" => 1,
					"Permission to use media on MorphoSource granted by copyright holder" => 2,
					"Permission pending" => 3,
					"Copyright expired or work otherwise in public domain" => 4,
					"Copyright permission not yet requested" => 5
				)
		),
		'copyright_license' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Copyright license'), 'DESCRIPTION' => _t('Copyright license'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Media reuse policy not set" => 0,
					"CC0 - relinquish copyright" => 1,
					"Attribution CC BY - reuse with attribution" => 2,
					"Attribution-NonCommercial CC BY-NC - reuse but noncommercial" => 3,
					"Attribution-ShareAlike CC BY-SA - reuse here and applied to future uses " => 4,
					"Attribution- CC BY-NC-SA - reuse here and applied to future uses but noncommercial" => 5,
					"Attribution-NoDerivs CC BY-ND - reuse but no changes" => 6,
					"Attribution-NonCommercial-NoDerivs CC BY-NC-ND - reuse noncommerical no changes" => 7,
					"Media released for onetime use, no reuse without permission" => 8,
					"Unknown - Will set before project publication" => 20
				)
		),
		'copyright_info' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 60, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Copyright Holder'), 'DESCRIPTION' => _t('Name of copyright holder.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'media_metadata' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Metadata from media file'), 'DESCRIPTION' => _t('Metadata from media file.')
		),
		'scanner_type' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Scanner type'), 'DESCRIPTION' => _t('Scanner type'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Scanner 1') 	=> 1,
					_t('Scanner 2')	=> 2
				)
		),
		'scanner_x_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('X res'), 'DESCRIPTION' => _t('X resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_y_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Y res'), 'DESCRIPTION' => _t('Y resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_z_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Z res'), 'DESCRIPTION' => _t('Z resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_voltage' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Voltage'), 'DESCRIPTION' => _t('Scanner voltage.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_amperage' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Amperage'), 'DESCRIPTION' => _t('Scanner amperage.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_watts' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Watts'), 'DESCRIPTION' => _t('Scanner watts.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_projections' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Projections'), 'DESCRIPTION' => _t('Scanner projections.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_frame_averaging' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Frame averaging'), 'DESCRIPTION' => _t('Scanner frame averaging.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_wedge' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Wedge'), 'DESCRIPTION' => _t('Scanner wedge.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_calibration_check' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Calibration check'), 'DESCRIPTION' => _t('Scanner calibration check'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Option 1') 	=> 1,
					_t('Option 2')	=> 2
				)
		),
		'scanner_calibration_description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 65, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Calibration description'), 'DESCRIPTION' => _t('Description of scanner calibration.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'scanner_technicians' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 65, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Technicians'), 'DESCRIPTION' => _t('Scanner technicians.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'created_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media created on'), 'DESCRIPTION' => _t('Date/time the Media was created.'),
		),
		'last_modified_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media last modified on'), 'DESCRIPTION' => _t('Date/time the Media was last modified.'),
		)
 	)
);

class ms_media extends BaseModel {
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
	protected $TABLE = 'ms_media';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'media_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('media_id');

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
	protected $ORDER_BY = array('created_on');

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