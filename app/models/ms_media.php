<?php
/** ---------------------------------------------------------------------
 * app/models/ms_media.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
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
require_once(__CA_MODELS_DIR__."/ms_projects.php");
require_once(__CA_MODELS_DIR__."/ms_media_download_requests.php");
require_once(__CA_MODELS_DIR__."/ms_media_multifiles.php");
require_once(__CA_MODELS_DIR__."/ms_media_download_stats.php");

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
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Title'), 'DESCRIPTION' => _t('Optional display title for image.'),
				'BOUNDS_LENGTH' => array(1,255)
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
		'published' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Publication status'), 'DESCRIPTION' => _t('Release to public search?'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Not published / Not available in public search" => 0,
					"Published / available in public search and for download" => 1,
					"Published / available in public search / users must request download permission" => 2
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
				"LABEL" => "Enter the catalog number of the specimen this media depicts (leave out the institution and collection code)", "DESCRIPTION" => "Enter the catalog number of the specimen<br /> and select the specimen from the resulting list of possible matches."
		),
		'element' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 2,
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
					"Not Applicable" => "NA",
					"Unknown" => "UNKNOWN",
					"Left" => "LEFT",
					"Right" => "RIGHT",
					"Ventril" => "Ventril",
					"Dorsal" => "Dorsal",
					"Sagital" => "Sagital"
				)
		),
		'notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Notes'), 'DESCRIPTION' => _t('Notes about the media file.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'grant_support' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 6,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Grant support'), 'DESCRIPTION' => _t('List any grant support used in the creation of your media here.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'media_citation_instructions' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 6,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media citation instructions'), 'DESCRIPTION' => _t('Describes how to cite this media.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'media_citation_instruction1' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media citation instructions'), 'DESCRIPTION' => _t('Enter your name, publication and funding information to customize the media citation instructions that will appear along side your published media.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'media_citation_instruction2' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 'originally appearing in',
				'LABEL' => _t('Media citation instructions part 2'), 'DESCRIPTION' => _t('Describes how to cite this media.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'media_citation_instruction3' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media citation instructions part 3'), 'DESCRIPTION' => _t('Describes how to cite this media.'),
				'BOUNDS_LENGTH' => array(0,255)
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
		'facility_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_SELECT,
				"DISPLAY_FIELD" => array('ms_facilities.name'), 
				"DISPLAY_ORDERBY" => array('ms_facilities.name'),
				"DISPLAY_WIDTH" => 100, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => true, "DEFAULT" => "",
				"LABEL" => "Find the facility this media file was created at", "DESCRIPTION" => "Enter the name of the facility<br /> and select the facility from the resulting list of possible matches."
		),
		'scanner_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_SELECT,
				"BOUNDS_CHOICE_LIST" => array(),
				"DISPLAY_WIDTH" => 100, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => true, "DEFAULT" => "",
				"LABEL" => "Choose scanner used", "DESCRIPTION" => "Choose the scanner at the selected facility used to create this media."
		),
		'scanner_x_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "mm",
				'LABEL' => _t('X res'), 'DESCRIPTION' => _t('X resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_y_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "mm",
				'LABEL' => _t('Y res'), 'DESCRIPTION' => _t('Y resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_z_resolution' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "mm",
				'LABEL' => _t('Z res'), 'DESCRIPTION' => _t('Z resolution of scanner.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_voltage' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "kv",
				'LABEL' => _t('Voltage'), 'DESCRIPTION' => _t('Scanner voltage.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_amperage' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "µa",
				'LABEL' => _t('Amperage'), 'DESCRIPTION' => _t('Scanner amperage.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'scanner_watts' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'SUFFIX' => "W",
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
		'scanner_calibration_shading_correction' => array(
				"FIELD_TYPE" => FT_BIT, "DISPLAY_TYPE" => DT_CHECKBOXES, 
				"DISPLAY_WIDTH" => 1, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, 
				"DEFAULT" => 0,
				"LABEL" => "Shading Correction", "DESCRIPTION" => "When checked, indicates the scanner's shading correction was calibrated."
		),
		'scanner_calibration_flux_normalization' => array(
				"FIELD_TYPE" => FT_BIT, "DISPLAY_TYPE" => DT_CHECKBOXES, 
				"DISPLAY_WIDTH" => 1, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, 
				"DEFAULT" => 0,
				"LABEL" => "Flux Normalization", "DESCRIPTION" => "When checked, indicates the scanner's flux normalization was calibrated."
		),
		'scanner_calibration_geometric_calibration' => array(
				"FIELD_TYPE" => FT_BIT, "DISPLAY_TYPE" => DT_CHECKBOXES, 
				"DISPLAY_WIDTH" => 1, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => 0, 
				"DEFAULT" => 0,
				"LABEL" => "Geometric Calibration", "DESCRIPTION" => "When checked, indicates the scanner's geometric calibration was calibrated."
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
	public function insert ($pa_options=null) {
		if ($vn_rc = parent::insert($pa_options)) {
			if (is_array($va_versions = $this->getMediaVersions("media"))) {
		
				$vn_alloc = 0;
				foreach($va_versions as $vs_version) {
					$va_info = $this->getMediaInfo("media", $vs_version);
					$vn_alloc += $va_info['PROPERTIES']['filesize'];
				}
				$t_project = new ms_projects($this->get('project_id'));
				$t_project->setMode(ACCESS_WRITE);
				$t_project->set('total_storage_allocation', (int)$t_project->get('total_storage_allocation') + (int)$vn_alloc);
				$t_project->update();
			}
		}
		return $vn_rc;
	}
	# ----------------------------------------
	public function update ($pa_options=null) {
		$vn_old_alloc = 0;
		if (is_array($va_versions = $this->getMediaVersions("media"))) {
			foreach($va_versions as $vs_version) {
				$va_info = $this->getMediaInfo("media", $vs_version);
				$vn_old_alloc += $va_info['PROPERTIES']['filesize'];
			}
		}
		if ($vn_rc = parent::update($pa_options)) {
			if (is_array($va_versions = $this->getMediaVersions("media"))) {
		
				$vn_alloc = 0;
				foreach($va_versions as $vs_version) {
					$va_info = $this->getMediaInfo("media", $vs_version);
					$vn_alloc += $va_info['PROPERTIES']['filesize'];
				}
				
				$t_project = new ms_projects($this->get('project_id'));
				
				if (($vn_new_alloc = (int)$t_project->get('total_storage_allocation') + (int)$vn_alloc - (int)$vn_old_alloc) < 0) {
					$vn_new_alloc = 0;
				}
				
				$t_project->setMode(ACCESS_WRITE);
				$t_project->set('total_storage_allocation', $vn_new_alloc);
				$t_project->update();
			}
		}
		return $vn_rc;
	}
	# ----------------------------------------
	public function delete ($pb_delete_related=false, $pa_options=null, $pa_fields=null, $pa_table_list=null) {
		$vn_project_id = $this->get('project_id');
		$vn_old_alloc = 0;
		if (is_array($va_versions = $this->getMediaVersions("media"))) {
			foreach($va_versions as $vs_version) {
				$va_info = $this->getMediaInfo("media", $vs_version);
				$vn_old_alloc += $va_info['PROPERTIES']['filesize'];
			}
		}
		if ($vn_rc = parent::delete($pb_delete_related, $pa_options, $pa_fields, $pa_table_list)) {			
			
			$t_project = new ms_projects($vn_project_id);
			
			if (($vn_new_alloc = (int)$t_project->get('total_storage_allocation') - (int)$vn_old_alloc) < 0) {
				$vn_new_alloc = 0;
			}
			
			$t_project->setMode(ACCESS_WRITE);
			$t_project->set('total_storage_allocation', $vn_new_alloc);
			$t_project->update();
		}
		
		return $vn_rc;
	}
	# ----------------------------------------
	public function htmlFormElement($ps_field, $ps_format=null, $pa_options=null) {
		switch($ps_field){ 
			case 'scanner_id':
				$va_choice_list = array();
				if ($vn_facility_id = $this->get('facility_id')) {
					$va_scanners = ms_facilities::scannerList($vn_facility_id);
					foreach($va_scanners as $vn_scanner_id => $va_scanner) {
						$va_choice_list[$va_scanner['name']] = $vn_scanner_id;
					}
				}
				BaseModel::$s_ca_models_definitions['ms_media']['FIELDS']['scanner_id']['BOUNDS_CHOICE_LIST'] = $va_choice_list; 
				break;
		}
		
		return parent::htmlFormElement($ps_field, $ps_format, $pa_options);
	}
	# ------------------------------------------------------
 	# Multifiles
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function addFile($ps_filepath, $ps_resource_path='/', $pb_allow_duplicates=true) {
 		if(!$this->getPrimaryKey()) { return null; }
 		if (!trim($ps_resource_path)) { $ps_resource_path = '/'; }
 		
 		$t_multifile = new ms_media_multifiles();
 		if (!$pb_allow_duplicates) {
 			if ($t_multifile->load(array('resource_path' => $ps_resource_path, 'media_id' => $this->getPrimaryKey()))) {
 				return null;
 			}
 		}
 		$t_multifile->setMode(ACCESS_WRITE);
 		$t_multifile->set('media_id', $this->getPrimaryKey());
 		$t_multifile->set('media', $ps_filepath);
 		$t_multifile->set('resource_path', $ps_resource_path);
 		
 		$t_multifile->insert();
 		
 		if ($t_multifile->numErrors()) {
 			$this->errors = array_merge($this->errors, $t_multifile->errors);
 			return false;
 		}
 		
 		return $t_multifile;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function removeFile($pn_multifile_id) {
 		if(!$this->getPrimaryKey()) { return null; }
 		
 		$t_multifile = new ms_media_multifiles($pn_multifile_id);
 		
 		if ($t_multifile->get('media_id') == $this->getPrimaryKey()) {
 			$t_multifile->setMode(ACCESS_WRITE);
 			$t_multifile->delete();
 			
			if ($t_multifile->numErrors()) {
				$this->errors = array_merge($this->errors, $t_multifile->errors);
				return false;
			}
		} else {
			$this->postError(2720, _t('File is not part of this media'), 'ms_media->removeFile()');
			return false;
		}
		return true;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function removeAllFiles() {
 		if(!$this->getPrimaryKey()) { return null; }
 		
 		$va_file_ids = array_keys($this->getFileList());
 		
 		foreach($va_file_ids as $vn_id) {
 			$this->removeFile($vn_id);
 			
 			if($this->numErrors()) {
 				return false;
 			}
 		}
 		
 		return true;
 	}
 	# ------------------------------------------------------
 	/**
 	 * Returns list of additional files (page or frame previews for documents or videos, typically) attached to a media item
 	 * The return value is an array key'ed on the multifile_id (a unique identifier for each attached file); array values are arrays
 	 * with keys set to values for each file version returned. They keys are:
 	 *		<version name>_path = The absolute file path to the file
 	 *		<version name>_tag = An HTML tag that will display the file
 	 *		<version name>_url = The URL for the file
 	 *		<version name>_width = The pixel width of the file when displayed
 	 *		<version name>_height = The pixel height of the file when displayed
 	 * The available versions are set in media_processing.conf
 	 *
 	 * @param int $pn_media_id The media_id of the media to return files for. If omitted the currently loaded media is used. If no media_id is specified and no row is loaded null will be returned.
 	 * @param int $pn_start The index of the first file to return. Files are numbered from zero. If omitted the first file found is returned.
 	 * @param int $pn_num_files The maximum number of files to return. If omitted all files are returned.
 	 * @param array $pa_versions A list of file versions to return. If omitted only the "preview" version is returned.
 	 * @return array A list of files attached to the media. If no files are associated an empty array is returned.
 	 */
 	public function getFileList($pn_media_id=null, $pn_start=null, $pn_num_files=null, $pa_versions=null) {
 		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
 		
 		if (!is_array($pa_versions)) {
 			$pa_versions = array('preview');
 		}
 		
 		$vs_limit_sql = '';
 		if (!is_null($pn_start) && !is_null($pn_num_files)) {
 			if (($pn_start >= 0) && ($pn_num_files >= 1)) {
 				$vs_limit_sql = "LIMIT {$pn_start}, {$pn_num_files}";
 			}
 		}
 		
 		$o_db= $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT *
 			FROM ms_media_multifiles
 			WHERE
 				media_id = ?
 			{$vs_limit_sql}
 		", (int)$vn_media_id);
 		
 		$va_files = array();
 		while($qr_res->nextRow()) {
 			$vn_multifile_id = $qr_res->get('multifile_id');
 			$va_files[$vn_multifile_id] = $qr_res->getRow();
 			unset($va_files[$vn_multifile_id]['media']);
 			
 			foreach($pa_versions as $vn_i => $vs_version) {
 				$va_files[$vn_multifile_id][$vs_version.'_path'] = $qr_res->getMediaPath('media', $vs_version);
 				$va_files[$vn_multifile_id][$vs_version.'_tag'] = $qr_res->getMediaTag('media', $vs_version);
 				$va_files[$vn_multifile_id][$vs_version.'_url'] = $qr_res->getMediaUrl('media', $vs_version);
 				
 				$va_info = $qr_res->getMediaInfo('media', $vs_version);
 				$va_files[$vn_multifile_id][$vs_version.'_width'] = $va_info['WIDTH'];
 				$va_files[$vn_multifile_id][$vs_version.'_height'] = $va_info['HEIGHT'];
 				$va_files[$vn_multifile_id][$vs_version.'_mimetype'] = $va_info['MIMETYPE'];
 			}
 		}
 		return $va_files;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function getFileInstance($pn_multifile_id) {
 		if(!$this->getPrimaryKey()) { return null; }
 	
 		$t_multifile = new ms_media_multifiles($pn_multifile_id);
 		
 		if ($t_multifile->get('media_id') == $this->getPrimaryKey()) {
 			return $t_multifile;
 		}
 		return null;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function numFiles($pn_media_id=null) { 		
 		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
 		
 		$o_db= $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT count(*) c
 			FROM ms_media_multifiles
 			WHERE
 				media_id = ?
 		", (int)$vn_media_id);
 		
 		if($qr_res->nextRow()) {
 			return intval($qr_res->get('c'));
 		}
 		return 0;
 	}
	# ------------------------------------------------------
	# Download requests
	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function requestDownload($pn_user_id, $ps_request_text=null, $pn_media_id=null, $pa_options=null) {
 		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
 		
 		if ($this->userAccessToMediaIsPending($pn_user_id, $vn_media_id)) { return false; }
 	
 		$t_req = new ms_media_download_requests();
 		$t_req->setMode(ACCESS_WRITE);
 		$t_req->set('media_id', $vn_media_id);
 		$t_req->set('user_id', $pn_user_id);
 		$t_req->set('request', $ps_request_text);
 		$t_req->set('status', 0);
 		$t_req->insert();
 		
 		if ($t_req->numErrors()) {
 			$this->errors = $t_req->errors;
 			return false;
 		}
 		
 		// Send email to author
 		if ($this->getPrimaryKey() != $vn_media_id) {
 			$t_media = new ms_media($vn_media_id);
 		} else {
 			$t_media = $this;
 		}
 		# --- get the email address of any project members with the role "downloads" so they can be notified in addition to the owner of the media
 		$va_send_to = array();
 		$t_project = new ms_projects($t_media->get("project_id"));
 		$va_members = $t_project->getMembers();
 		$t_member = new ca_users();
 		foreach($va_members as $va_member){
 			$t_member->load($va_member["user_id"]);
 			if($t_member->hasRole("downloads")){
 				$va_send_to[$va_member["email"]] = $va_member["fname"]." ".$va_member["lname"];
 			}
 		}
 		$t_author = new ca_users($t_media->get('user_id'));
 		$t_user = new ca_users($pn_user_id);
 		if ($vs_email = $t_author->get('email')) {
 			$va_send_to[$t_author->get('email')] = $t_author->get('fname')." ".$t_author->get('lname');
 		}
 		if(sizeof($va_send_to) > 0){
 			caSendMessageUsingView($pa_options['request'], $va_send_to, 'do-not-reply@morphosource.org', "[Morphosource] ".$t_user->get('fname').' '.$t_user->get('lname').' has requested download of media', 'author_download_request_notification.tpl', array(
 				'user' => $t_user,
 				'author' => $t_author,
 				'media' => $t_media,
 				'project' => $t_project,
 				'downloadRequest' => $t_req
 			));
 		}
 		
 		return true;
 	}
	# ------------------------------------------------------
 	/**
 	 * @param int $pn_media_id
 	 * @param array $pa_options Options are:
 	 *		status = limits returned requests to a given status. Possible values are these constants (not strings!): __MS_DOWNLOAD_REQUEST_NEW__, __MS_DOWNLOAD_REQUEST_APPROVED__, __MS_DOWNLOAD_REQUEST_DENIED__, __MS_DOWNLOAD_REQUEST_ALL__
 	 *		user_id = restrict to a specific user_id
 	 */
 	public function getDownloadRequests($pn_media_id=null, $pa_options=null) {
 		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		$vs_user_sql = '';
		if (isset($pa_options['user_id'])) {
			$vs_user_sql = " AND (user_id = ".(int)$pa_options['user_id'].")";
		}
		
		$vs_status_sql = '';
 		if (isset($pa_options['status'])) {
 			switch((int)$pa_options['status']) {
 				case __MS_DOWNLOAD_REQUEST_NEW__:
 					$vs_status_sql = " AND (status = 0)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_APPROVED__:
 					$vs_status_sql = " AND (status = 1)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_DENIED__:
 					$vs_status_sql = " AND (status = 2)";
 					break;
 			}
 		}
 		
 		$o_db = $this->getDb();
 		
 		$qr_res = $o_db->query("
 			SELECT * 
 			FROM ms_media_download_requests
 			WHERE 
 				media_id = ? {$vs_status_sql} {$vs_user_sql}
 		", array((int)$vn_media_id));
 		return $qr_res->getAllRows();
 	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function userCanDownloadMedia($pn_user_id, $pn_media_id=null) {
		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		if ($vn_media_id == $this->getPrimaryKey()) {
			$t_media = $this;
		} else {
			$t_media = new ms_media($vn_media_id);
		}
		
		if ($t_media->get('published') == 1) { return true; }
 		
 		$o_db = $this->getDb();
 		
 		# --- check if user has access to the project that made the media
 		$t_project = new ms_projects($t_media->get('project_id'));
 		if($t_project->isMember($pn_user_id)){
 			return true;
 		}
 		$qr_res = $o_db->query("
 			SELECT * 
 			FROM ms_media_download_requests
 			WHERE 
 				media_id = ? AND status = ? AND user_id = ? 
 		", array((int)$vn_media_id, __MS_DOWNLOAD_REQUEST_APPROVED__, $pn_user_id));
 		
 		if ($qr_res->numRows() > 0) {
 			return true;
 		}
 		
 		return false;
	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function userAccessToMediaIsPending($pn_user_id, $pn_media_id=null) {
		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		if ($vn_media_id == $this->getPrimaryKey()) {
			$t_media = $this;
		} else {
			$t_media = new ms_media($vn_media_id);
		}
		
		if ($t_media->get('published') == 1) { return false; }
 		
 		$o_db = $this->getDb();
 		
 		$qr_res = $o_db->query("
 			SELECT * 
 			FROM ms_media_download_requests
 			WHERE 
 				media_id = ? AND status = ? AND user_id = ? 
 		", array((int)$vn_media_id, __MS_DOWNLOAD_REQUEST_NEW__, $pn_user_id));
 		
 		if ($qr_res->numRows() > 0) {
 			return true;
 		}
 		
 		return false;
	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function recordDownload($pn_user_id, $pn_media_id=null){
		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		if ($vn_media_id == $this->getPrimaryKey()) {
			$t_media = $this;
		} else {
			$t_media = new ms_media($vn_media_id);
		}
		
		$t_stat = new ms_media_download_stats();
 		$t_stat->setMode(ACCESS_WRITE);
 		$t_stat->set('media_id', $vn_media_id);
 		$t_stat->set('user_id', $pn_user_id);
 		$t_stat->insert();
 		
 		if ($t_stat->numErrors()) {
 			$this->errors = $t_stat->errors;
 			return false;
 		}else{
 			return true;
 		}
		
	}
	# ----------------------------------------
	function numDownloads($pn_media_id=null) {
		if(!$pn_media_id){
			$pn_media_id = $this->getPrimaryKey();
		}
		if (!$pn_media_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media_download_stats
			WHERE media_id = ?
		", $pn_media_id);
		
		$vn_num_downloads = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_downloads = $qr->get("c");
		}
		return $vn_num_downloads;
	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function formatPublishedText($pn_published=null) {
		if (!$pn_published) {
			$pn_published = $this->get("published");
		}
		$vs_publish_text = "";
		switch($pn_published){
			case 0:
				$vs_publish_text = "Unpublished";
			break;
			# ------------------------------
			case 1:
				$vs_publish_text = "Published with unrestricted download";
			break;
			# ------------------------------
			case 2:
				$vs_publish_text = "Published with restricted download";
			break;
			# ------------------------------
		}
 		
 		return $vs_publish_text;
	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function getMediaCitationInstructions($pn_media_id=null) {
		if(!($vn_media_id = $pn_media_id)) { 
 			if (!($vn_media_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		if ($vn_media_id == $this->getPrimaryKey()) {
			$t_media = $this;
		} else {
			$t_media = new ms_media($vn_media_id);
		}
		$vs_citation_text = "";
		if($t_media->get("media_citation_instruction1")){
			$vs_citation_text = $t_media->get("media_citation_instruction1")." provided access to these data".(($t_media->get("media_citation_instruction2")) ? " " : "").$t_media->get("media_citation_instruction2").", the collection of which was funded by ".$t_media->get("media_citation_instruction3").". The files were downloaded from www.MorphoSource.org, Duke University.";
 		}
 		return $vs_citation_text;
	}
	# ------------------------------------------------------
}
?>