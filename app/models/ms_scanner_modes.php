<?php
/** ---------------------------------------------------------------------
 * app/models/ms_scanner_modes.php
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

BaseModel::$s_ca_models_definitions['ms_scanner_modes'] = array(
 	'NAME_SINGULAR' 	=> _t('scanner modality'),
 	'NAME_PLURAL' 		=> _t('scanner modalities'),
 	'FIELDS' 			=> array(
 		'scanner_mode_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Scanner modality'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this scanner/modality combination')
		),
		'scanner_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_FIELD" => array('ms_scanner_modes.scanner_id'), 
				"DISPLAY_ORDERBY" => array('ms_scanner_modes.scanner_id'),
				"DISPLAY_WIDTH" => 100, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => true, "DEFAULT" => "",
				"LABEL" => "Choose scanner used", "DESCRIPTION" => "Choose the scanner at the selected facility used to create this media."
		),
		'modality' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Modality'), 'DESCRIPTION' => _t('Modality of scanner'),
				'BOUNDS_CHOICE_LIST'=> array(
					'Micro/Nano X-Ray Computed Tomography' => 0,
					'Medical X-Ray Computed Tomography' => 1,
					'Magnetic Resonance Imaging' => 2,
					'Positron Emission Tomography' => 3,
					'Synchotron Imaging' => 4,
					'Neutrino Imaging' => 5,
					'Photogrammetry' => 6,
					'Structured Light' => 7,
					'Laser Scan' => 8,
					'Confocal Image Stacking' => 9,
					'Optical Image Stacking' => 10,
					'Infrared' => 11,
					'Reflectance Transformation Imaging' => 12,
					'Photography' => 13,
					'Scanning Electron Microscopy' => 14
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
				'LABEL' => _t('Project last modified on'), 'DESCRIPTION' => _t('Date/time the Project was last modified.'),
		),
		'user_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'User id', 'DESCRIPTION' => 'User id'
		)
 	)
);

class ms_scanner_modes extends BaseModel {
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
	protected $TABLE = 'ms_scanner_modes';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'scanner_mode_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('scanner_id', 'modality', 'user_id');

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
	protected $ORDER_BY = array('scanner_id');

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
	protected $LOG_CHANGES_TO_SELF = true;
	protected $LOG_CHANGES_USING_AS_SUBJECT = array(
		"FOREIGN_KEYS" => array(
			'scanner_id'
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
	/**
	 * Returns list of scanner names combined with modes (optionally from list of scanner IDs)
	 */
	static public function scannerModeList($va_where_scanner_id=null) {
		$o_db = new Db();
		$db_statement = 
			"SELECT sm.*, s.name 
			FROM ms_scanner_modes AS sm 
			INNER JOIN ms_scanners AS s ON s.scanner_id = sm.scanner_id";
		if ($va_where_scanner_id) {
			$qr_res = $o_db->query($db_statement." WHERE sm.scanner_id IN (?)" 
				, [$va_where_scanner_id]);
		} else {
			$qr_res = $o_db->query($db_statement);
		}
		
		$va_rows = array();
		while($qr_res->nextRow()) {
			$va_rows[(int)$qr_res->get('scanner_mode_id')] = $qr_res->getRow();
		}
		return $va_rows;
	}
}
?>