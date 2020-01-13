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
require_once(__CA_MODELS_DIR__."/ms_specimen_view_stats.php");
require_once(__CA_MODELS_DIR__."/ms_taxonomy.php");
require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
require_once(__CA_LIB_DIR__."/ca/Search/TaxonomyNamesSearch.php");
 	#require_once(__CA_APP_DIR__.'/lib/vendor/autoload.php');
use GuzzleHttp\Client;
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
				'LABEL' => 'User id', 'DESCRIPTION' => 'User id'
		),
		'reference_source' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Reference source'), 'DESCRIPTION' => _t('Reference source of specimen.'),
				"BOUNDS_CHOICE_LIST"=> array(
					'Vouchered' => 0,
					'Unvouchered' => 1
				)
		),
		'institution_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Inst. code prefix'), 'DESCRIPTION' => _t('Mandatory component typically equivalent to the repository institution\'s acronym in the full specimen identifier (=catalog number).'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'collection_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Coll. code modifier'), 'DESCRIPTION' => _t('Typically designates a sub-collection within a repository. Not a universal element because not all repositories have sub-collections.  However, this field is critical when applicable to avoid confusing specimens from different sub-collections. Please make sure you do not incorrectly omit a collection code from the specimen identifier (=catalog number).'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'catalog_number' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Alphanumeric suffix'), 'DESCRIPTION' => _t('Mandatory alphanumeric string that is unique within a repository and within a subcollection.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'url' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 65, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('URL to specimen record in home repository'), 'DESCRIPTION' => _t('External link to specimen record in home repository'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'occurrence_id' => array(
				"FIELD_TYPE" => FT_TEXT, "DISPLAY_TYPE" => DT_FIELD, 
				"DISPLAY_WIDTH" => 65, "DISPLAY_HEIGHT" =>1,
				"IS_NULL" => 0, 
				"DEFAULT" => "",
				"LABEL" => "Occurrence ID", "DESCRIPTION" => "Unique Institutional identifier for the specimen",
				"BOUNDS_LENGTH" => array(0,255)
		),
		'uuid' => array(
				"FIELD_TYPE" => FT_TEXT, "DISPLAY_TYPE" => DT_HIDDEN, 
				"DISPLAY_WIDTH" => 40, "DISPLAY_HEIGHT" =>1,
				"IS_NULL" => 0, 
				"DEFAULT" => "",
				"LABEL" => "UUID", "DESCRIPTION" => "iDigBio UUID",
				"BOUNDS_LENGTH" => array(0,255)
		),
		// 'element' => array(
// 				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
// 				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
// 				'IS_NULL' => TRUE, 
// 				'DEFAULT' => '',
// 				'LABEL' => _t('Element'), 'DESCRIPTION' => _t('Element of specimen.'),
// 				'BOUNDS_LENGTH' => array(0,255)
// 		),
// 		'side' => array(
// 				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
// 				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
// 				'IS_NULL' => TRUE, 
// 				'DEFAULT' => '',
// 				'LABEL' => _t('Side'), 'DESCRIPTION' => _t('Side of specimen.'),
// 				'BOUNDS_LENGTH' => array(0,255)
// 		),
		'collector' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Collector'), 'DESCRIPTION' => _t('Who collected the specimen?'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'collected_on' => array(
				'FIELD_TYPE' => FT_DATETIME, 'DISPLAY_TYPE' => DT_FIELD,
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Specimen collection date'), 'DESCRIPTION' => _t('Date specimen was collected.'),
		),
		'description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 62, 'DISPLAY_HEIGHT' => 6,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Description'), 'DESCRIPTION' => _t('Description of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'type' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => 0,
				'LABEL' => _t('Type'), 'DESCRIPTION' => _t('Holotype'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Yes') 	=> 0,
					_t('No')	=> 1
				)
		),
		'sex' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => 0,
				'LABEL' => _t('Sex'), 'DESCRIPTION' => _t('Sex of specimen.'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Male') 	=> 'M',
					_t('Female')	=> 'F'
				)
		),
		'institution_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => TRUE, "DEFAULT" => "",
				"LABEL" => "Find the institution for this specimen", "DESCRIPTION" => "Enter words from the name or location of the institution<br /> and select institution from resulting list of possible matches."
		),
		'notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 62, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Notes'), 'DESCRIPTION' => _t('Notes about the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'relative_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Geologic Age'), 'DESCRIPTION' => _t('Specimen\'s geologic age.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'absolute_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Absolute age'), 'DESCRIPTION' => _t('Specimen\'s absolute age.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'body_mass' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Body mass'), 'DESCRIPTION' => _t('Specimen\'s body mass'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'body_mass_comments' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Body mass comments'), 'DESCRIPTION' => _t('Comments about the specimen\'s body mass.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'body_mass_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => TRUE, "DEFAULT" => "",
				"LABEL" => "Find the body mass bibliographic reference", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
		),
		'locality_description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 64, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Verbatim locality'), 'DESCRIPTION' => _t('Description of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_coordinates' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality coordinate type'), 'DESCRIPTION' => _t('Type of coordinates of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_datum_zone' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality datum/zone'), 'DESCRIPTION' => _t('Datum/zone of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'locality_northing_coordinate' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality northing coordinate'), 'DESCRIPTION' => _t('Northing coordinate of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'locality_easting_coordinate' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality easting coordinate'), 'DESCRIPTION' => _t('Easting coordinate of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'locality_absolute_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality absolute age'), 'DESCRIPTION' => _t('Absolute age of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_relative_age' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality geologic age'), 'DESCRIPTION' => _t('Geologic age of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_absolute_age_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => TRUE, "DEFAULT" => "",
				"LABEL" => "Find bibliographic reference for the absolute age", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
		),
		'locality_relative_age_bibref_id' => array(
				"FIELD_TYPE" => FT_NUMBER, "DISPLAY_TYPE" => DT_HIDDEN,
				"DISPLAY_WIDTH" => 4, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => TRUE, "DEFAULT" => "",
				"LABEL" => "Find bibliographic reference for the relative age", "DESCRIPTION" => "Enter words from the title, publisher, authors or editor<br /> and select reference from resulting list of possible matches."
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
		),
		'batch_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Batch Import Status'), 'DESCRIPTION' => _t('used to find newly imported records for review'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Batch Uploaded') => 1
				)
		),
		'recordset' => array(
				"FIELD_TYPE" => FT_TEXT, "DISPLAY_TYPE" => DT_HIDDEN, 
				"DISPLAY_WIDTH" => 40, "DISPLAY_HEIGHT" =>1,
				"IS_NULL" => true, 
				"DEFAULT" => '',
				"LABEL" => "Recordset", "DESCRIPTION" => "iDigBio recordset ID",
				"BOUNDS_LENGTH" => array(0,255)
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
	protected $LIST_DELIMITER = '/';


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
	# Search
	# ------------------------------------------------------
	protected $SEARCH_CLASSNAME = 'SpecimenSearch';
	protected $SEARCH_RESULT_CLASSNAME = 'SpecimenSearchResult';
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ----------------------------------------
	public function __construct($pn_id=null) {
		parent::__construct($pn_id);
	}
	# ----------------------------------------
	function getSpecimenNumber($pn_specimen_id=null) {
		$va_name = array();
		if(!$pn_specimen_id) { 
			if (!($pn_specimen_id = $this->get("specimen_id"))) { return null; }
			
			$va_name = array(
				'specimen_id' => $pn_specimen_id,
				'institution_code' => $this->get("institution_code"),
				'collection_code' => $this->get("collection_code"),
				'catalog_number' => $this->get("catalog_number")
			);
		} else {
			$o_db = new Db();
			$q_specimen = $o_db->query("SELECT * FROM ms_specimens WHERE specimen_id = ?", array($pn_specimen_id));
			$va_taxonomic_names = array();
			if($q_specimen->numRows()){
				if($q_specimen->nextRow()){
					$va_name = array(
						'specimen_id' => $pn_specimen_id,
						'institution_code' => $q_specimen->get("institution_code"),
						'collection_code' => $q_specimen->get("collection_code"),
						'catalog_number' => $q_specimen->get("catalog_number")
					);
				}
			}
			
		}
		
		if($pn_specimen_id){
			return $this->formatSpecimenNumber($va_name);
		}else{
			return null;
		}
	}
	
	# ----------------------------------------
	function getSpecimenName($pn_specimen_id=null, $pa_options=null) {
		$va_name = array();
		if(!$pn_specimen_id) { 
			if (!($pn_specimen_id = $this->get("specimen_id"))) { return null; }
			
			$va_name = array(
				'specimen_id' => $pn_specimen_id,
				'institution_code' => $this->get("institution_code"),
				'collection_code' => $this->get("collection_code"),
				'catalog_number' => $this->get("catalog_number")
			);
		} else {
			$o_db = new Db();
			$q_specimen = $o_db->query("SELECT * FROM ms_specimens WHERE specimen_id = ?", array($pn_specimen_id));
			$va_taxonomic_names = array();
			if($q_specimen->numRows()){
				if($q_specimen->nextRow()){
					$va_name = array(
						'specimen_id' => $pn_specimen_id,
						'institution_code' => $q_specimen->get("institution_code"),
						'collection_code' => $q_specimen->get("collection_code"),
						'catalog_number' => $q_specimen->get("catalog_number")
					);
				}
			}
			
		}
		
		if (!caGetOption('omitTaxonomy', $pa_options, false)) {
			$va_name['taxa'] = $this->getSpecimenTaxonomy($pn_specimen_id);
		}
		
		if($pn_specimen_id){
			return $this->formatSpecimenName($va_name, $pa_options);
		}else{
			return null;
		}
	}
	
	# ----------------------------------------
	function formatSpecimenName($pa_specimen, $pa_options=null) {
		$va_specimen_parts = array();
		if($pa_specimen["institution_code"]){
			$va_specimen_parts[] = $pa_specimen["institution_code"];
		}
		if($pa_specimen["collection_code"]){
			$va_specimen_parts[] = $pa_specimen["collection_code"];
		}
		if($pa_specimen["catalog_number"]){
			$va_specimen_parts[] = $pa_specimen["catalog_number"];
		}
		$vs_num =  join(":", $va_specimen_parts);
		
		if (!caGetOption('omitTaxonomy', $pa_options, false)) {
			if(is_array($pa_specimen["taxa"]) && (sizeof($pa_specimen["taxa"]) > 0)) {
				$vs_num .= ", <em>".join("; ", $pa_specimen["taxa"])."</em>";
			}
		}
		
		return $vs_num;
	}
	# ----------------------------------------
	function formatSpecimenNumber($pa_specimen) {
		$va_specimen_parts = array();
		if($pa_specimen["institution_code"]){
			$va_specimen_parts[] = $pa_specimen["institution_code"];
		}
		if($pa_specimen["collection_code"]){
			$va_specimen_parts[] = $pa_specimen["collection_code"];
		}
		if($pa_specimen["catalog_number"]){
			$va_specimen_parts[] = $pa_specimen["catalog_number"];
		}
		$vs_num =  join(":", $va_specimen_parts);
		
		return $vs_num;
	}
	# ----------------------------------------
	function getSpecimenTaxonomy($pn_specimen_id=null) {
		if(!$pn_specimen_id) { $pn_specimen_id = $this->get("specimen_id"); }
		if($pn_specimen_id){
			$o_db = new Db();
			
			$va_taxonomic_names = array();
			if (is_array($pn_specimen_id) && sizeof($pn_specimen_id)) {
				$pn_specimen_id = array_map(function($v) { return (int)$v; }, $pn_specimen_id);
				
				$q_specimen_taxonomy = $o_db->query("SELECT tn.alt_id, tn.genus, tn.species, tn.subspecies, sxt.specimen_id FROM ms_specimens_x_taxonomy sxt INNER JOIN ms_taxonomy_names AS tn on tn.alt_id = sxt.alt_id WHERE sxt.specimen_id IN (?) AND tn.is_primary = 1", array($pn_specimen_id));
				
			
				if($q_specimen_taxonomy->numRows()){
					while($q_specimen_taxonomy->nextRow()){
						$va_taxonomic_names[$q_specimen_taxonomy->get("specimen_id")] = trim($q_specimen_taxonomy->get("genus")." ".$q_specimen_taxonomy->get("species")." ".$q_specimen_taxonomy->get("subspecies"));
					}
				}
			} else {
				$q_specimen_taxonomy = $o_db->query("SELECT tn.alt_id, tn.genus, tn.species, tn.subspecies FROM ms_specimens_x_taxonomy sxt INNER JOIN ms_taxonomy_names AS tn on tn.alt_id = sxt.alt_id WHERE sxt.specimen_id = ? AND tn.is_primary = 1", array($pn_specimen_id));
							
				if($q_specimen_taxonomy->numRows()){
					while($q_specimen_taxonomy->nextRow()){
						$va_taxonomic_names[$q_specimen_taxonomy->get("alt_id")] = trim($q_specimen_taxonomy->get("genus")." ".$q_specimen_taxonomy->get("species")." ".$q_specimen_taxonomy->get("subspecies"));
					}
				}
			}
			
			return $va_taxonomic_names;
		}else{
			return false;
		}
	}
	# ----------------------------------------
	/**
	 *
	 */
	function getSpecimenMedia($pn_specimen_id=null, $pa_options=null) {
		if(!$pn_specimen_id) { $pn_specimen_id = $this->get("specimen_id"); }
		
		$va_versions = (isset($pa_options['versions']) && is_array($pa_options['versions']) && sizeof($pa_options['versions'])) ? $pa_options['versions'] : array('thumbnail', 'small', 'preview190');
		$vs_published_where = "";
		
		if($pn_specimen_id){
			$o_db = new Db();
			if($pa_options['user_id']){
				if($pa_options['published']){
					$vs_published_where = "(m.published > 0) OR";
				}
				$q_media = $o_db->query("
					SELECT m.*
					FROM ms_media m
					INNER JOIN ms_projects AS p ON m.project_id = p.project_id
					LEFT JOIN ms_project_users AS pu ON p.project_id = pu.project_id
					LEFT JOIN ms_media_x_projects AS mp ON mp.media_id = m.media_id
					LEFT JOIN ms_project_users AS puReadOnly ON mp.project_id = puReadOnly.project_id
					WHERE (m.specimen_id = ?) AND (p.deleted = 0) AND (".$vs_published_where." (pu.user_id = ?) OR (puReadOnly.user_id = ?))",
					array($pn_specimen_id, $pa_options['user_id'], $pa_options['user_id']));
			}else{
				if($pa_options['published']){
					$vs_published_where = " AND m.published > 0";
				}
				$q_media = $o_db->query("
					SELECT m.*
					FROM ms_media m 
					INNER JOIN ms_projects AS p ON m.project_id = p.project_id
					WHERE m.specimen_id = ?".$vs_published_where." AND (p.deleted = 0)",
					array($pn_specimen_id));
			}
			$va_media = array();
			$t_project = new ms_projects();
			if($q_media->numRows()){
				$t_media = new ms_media();
				while($q_media->nextRow()){
					$vb_published = null;
					if($pa_options['published']){
						$vb_published = 1;
					}
					if($pa_options["user_id"]){
						# --- if a user id is passed, show media they have access to
						if($t_project->isMember($pa_options["user_id"], $q_media->get("project_id")) || $t_media->userHasReadOnlyAccessToMedia($pa_options["user_id"], $q_media->get("media_id"))){
							$vb_published = null;
						}
					}
					$va_media_preview_info = $t_media->getPreviewMediaFile($q_media->get("media_id"), $va_versions, $vb_published);
					$va_media_row = $q_media->getRow();
					unset($va_media_row["media"]);
					$va_media_info = array_merge($va_media_preview_info, $va_media_row);
					$va_media[$q_media->get("media_id")] = $va_media_info;
				}
// 				while($q_media->nextRow()){
// 					$va_media_info = $q_media->getRow();
// 					unset($va_media_info['media']);
// 					$va_media_info['mimetype'] = $q_media->getMediaInfo('media', 'original', 'MIMETYPE');
// 					$va_props = $q_media->getMediaInfo('media', 'original', 'PROPERTIES');
// 					$va_media_info['filesize'] = $va_props['filesize'];
// 					
// 					foreach($va_versions as $vs_version) {
// 						$va_media_info['tags'][$vs_version] = $q_media->getMediaTag('media', $vs_version);
// 						$va_media_info['urls'][$vs_version] = $q_media->getMediaUrl('media', $vs_version);
// 					}
// 				
// 					$va_media[$q_media->get("media_id")] = $va_media_info;
// 				}
			}
			return $va_media;
		}else{
			return false;
		}
	}
	# ------------------------------------------------------
	function getSpecimenMediaIDs($pn_specimen_id=null, $pa_options=null) {
		if(!$pn_specimen_id) { $pn_specimen_id = $this->get("specimen_id"); }
		
		$vs_published_where = "";
		$va_media_ids = array();
		if($pn_specimen_id){
			$o_db = new Db();
			if($pa_options['user_id']){
				if($pa_options['published']){
					$vs_published_where = " m.published > 0 OR";
				}
				$q_media = $o_db->query("
					SELECT media_id
					FROM ms_media m
					INNER JOIN ms_projects AS p ON m.project_id = p.project_id
					LEFT JOIN ms_project_users AS pu ON p.project_id = pu.project_id
					WHERE m.specimen_id = ? AND (".$vs_published_where." pu.user_id = ?) AND (p.deleted = 0)",
					array($pn_specimen_id, $pa_options['user_id']));
			}else{
				if($pa_options['published']){
					$vs_published_where = " AND m.published > 0";
				}
				$q_media = $o_db->query("
					SELECT m.media_id
					FROM ms_media m 
					INNER JOIN ms_projects as p ON m.project_id = p.project_id
					WHERE m.specimen_id = ?".$vs_published_where." AND (p.deleted = 0)",
					array($pn_specimen_id));
			}
			if($q_media->numRows()){
				while($q_media->nextRow()){
					$va_media_ids[] = $q_media->get("media_id");
				}
			}
			return $va_media_ids;
		}else{
			return array();
		}
	}
	# ------------------------------------------------------
	/** 
	 *
	 */
	public function recordView($pn_user_id, $pn_specimen_id=null){
		if(!($vn_specimen_id = $pn_specimen_id)) { 
 			if (!($vn_specimen_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
		
		if ($vn_specimen_id == $this->getPrimaryKey()) {
			$t_specimen = $this;
		} else {
			$t_specimen = new ms_specimens($vn_specimen_id);
		}
		
		$t_stat = new ms_specimen_view_stats();
 		$t_stat->setMode(ACCESS_WRITE);
 		$t_stat->set('specimen_id', $vn_specimen_id);
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
	function numViews($pn_specimen_id=null) {
		if(!$pn_specimen_id){
			$pn_specimen_id = $this->getPrimaryKey();
		}
		if (!$pn_specimen_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_specimen_view_stats
			WHERE specimen_id = ?
		", $pn_specimen_id);
		
		$vn_num_views = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_views = $qr->get("c");
		}
		return $vn_num_views;
	}
	# ------------------------------------------------------
	# iDigBio - load specimen data from idigbio.org 
	# institutioncode, collectioncode, catalognumber, genus, specificepithet, uuid, occurrenceid
	# ----------------------------------------
	public function getIDBSpecimenInfo($va_lookup_values, $vn_limit = 500) {
		$client = new GuzzleHttp\Client();
		if(is_array($va_lookup_values) && sizeof($va_lookup_values)){
			$va_tmp = array();
			foreach($va_lookup_values as $vs_field => $vs_search_term){
				if(($vs_field == "catalognumber") && $vs_coll_code = trim($va_lookup_values["collectioncode"])){
					$va_tmp[] = '"'.$vs_field.'":["'.trim($vs_search_term).'", "'.$vs_coll_code.'-'.trim($vs_search_term).'"]';
				}else{
					if(trim($vs_search_term)){
						$va_tmp[] = '"'.$vs_field.'":"'.trim($vs_search_term).'"';
					}
				}
			}
			if(sizeof($va_tmp)){
				$vs_search = "{".join(",", $va_tmp)."}";
			}
		}
		#$vs_search = '{"institutioncode":"AMNH","genus": "Lavia","specificepithet":"frons"}';
		if($vs_search){
			$vn_specimen_found = 0;
			try{
				$response = $client->get("https://search.idigbio.org/v2/search/records/?rq=".urlencode($vs_search)."&limit=".$vn_limit);
				$data = $response->json();
			}catch(Exception $e){
				return array("success" => false, "error" => "could not connect to idigbio.org: ".$e->getMessage(), "retry" => true);
			}
			if(is_array($data["items"]) && (sizeof($data["items"]) > 0)){
				return array("success" => true, "error" => "", "data" => $data);
			}else{
				return array("success" => false, "error" => "No results found on idigbio.org");
			}
		}else{
			return array("success" => false, "error" => "search not defined");
		}
	}
	# -------------------------------------------------------
		public function importIDBSpecimen($o_request, $vn_project_id, $vs_uuid) {
			if($vs_uuid){
				# build array of lookup terms to send to iDigBio
				$va_lookup_values = array("uuid" => $vs_uuid);
				$t_specimen = new ms_specimens();
				$va_results = $t_specimen->getIDBSpecimenInfo($va_lookup_values);
				if($va_results["success"]){
					if(is_array($va_results["data"]["items"][0]) && sizeof($va_results["data"]["items"][0])){
						$va_specimen_info = $va_results["data"]["items"][0];

						#print "<pre>";
						#print_r($va_specimen_info);
						#print "</pre>";
						#exit;
						# --- set the fields for the specimen
						$t_specimen->set("reference_source", 0);
						$t_specimen->set("notes", "imported from iDigBio. uuid:".$vs_uuid." Occurrence ID:".$va_specimen_info["data"]["dwc:occurrenceID"]);
						$t_specimen->set("institution_code", strtolower($va_specimen_info["data"]["dwc:institutionCode"]));
						$t_specimen->set("collection_code", strtolower($va_specimen_info["data"]["dwc:collectionCode"]));
						$t_specimen->set("catalog_number", strtolower($va_specimen_info["data"]["dwc:catalogNumber"]));
						$t_specimen->set("uuid", $vs_uuid);
						$t_specimen->set("occurrence_id", $va_specimen_info["data"]["dwc:occurrenceID"]);
						$t_specimen->set('project_id', $vn_project_id);
						$t_specimen->set('user_id', $o_request->getUserID());
						$t_specimen->set('url', $va_specimen_info["data"]["dcterms:references"]);
						$t_specimen->set('collector', strtolower($va_specimen_info["data"]["dwc:recordedBy"]));
						$t_specimen->set('collected_on', $va_specimen_info["indexTerms"]["datecollected"]);
						$t_specimen->set('recordset', $va_specimen_info["indexTerms"]["recordset"]);
						if($va_specimen_info["data"]["dwc:sex"]){
							if(strpos(strtolower($va_specimen_info["data"]["dwc:sex"]), "female") !== false){
								$t_specimen->set('sex', 'F');
							}else{
								$t_specimen->set('sex', 'M');
							}
						}
						if($va_specimen_info["data"]["dwc:locality"]){
							$t_specimen->set('locality_description', strtolower($va_specimen_info["data"]["dwc:locality"]));
						}elseif($va_specimen_info["data"]["dwc:verbatimLocality"]){
							$t_specimen->set('locality_description', strtolower($va_specimen_info["data"]["dwc:verbatimLocality"]));
						}elseif($va_specimen_info["data"]["dwc:country"]){
							$t_specimen->set('locality_description', strtolower($va_specimen_info["data"]["dwc:country"]));
						}
						
						# --- check if there is a taxonomy record in MorphoSource to link to
						$vb_taxon_linked = false;
						$vb_taxon_created = false;
						$o_search = new TaxonomyNamesSearch();
						$q_taxon_hits = $o_search->search(
							trim(strtolower($va_specimen_info["data"]["dwc:genus"])." ".
								strtolower($va_specimen_info["data"]["dwc:specificEpithet"])." ".
								strtolower($va_specimen_info["data"]["dwc:infraspecificEpithet"]))."*", 
							array('sort' => 'ms_taxonomy_names.genus')
						);
					
						if($q_taxon_hits->numHits() > 0){
							while($q_taxon_hits->nextHit()){
								if((strtolower($q_taxon_hits->get("genus")) == strtolower($va_specimen_info["data"]["dwc:genus"])) 
									&& (strtolower($q_taxon_hits->get("species")) == strtolower($va_specimen_info["data"]["dwc:specificEpithet"])) 
									&& (strtolower($q_taxon_hits->get("subspecies")) == strtolower($va_specimen_info["data"]["dwc:infraspecificEpithet"]))
								){
									$vn_taxon_id = $q_taxon_hits->get("taxon_id");
									$vn_alt_id = $q_taxon_hits->get("alt_id");
									$vb_taxon_linked = true;
									break;
								}
							}
						}
						if(!$vb_taxon_linked){
							# --- add the ms_taxonomy record
							$t_taxonomy = new ms_taxonomy();
							$t_taxonomy->set('project_id', $vn_project_id);
							$t_taxonomy->set('user_id', $o_request->getUserID());
							$t_taxonomy->set("common_name", $va_specimen_info["data"]["dwc:vernacularName"]);
							$t_taxonomy->set("notes", "imported from iDigBio");
							
							# --- add the taxonomy_names record
							$t_taxonomy_names = new ms_taxonomy_names();
							$t_taxonomy_names->set("genus", ucfirst(strtolower($va_specimen_info["data"]["dwc:genus"])));
							$t_taxonomy_names->set("species", strtolower($va_specimen_info["data"]["dwc:specificEpithet"]));
							$t_taxonomy_names->set("subspecies", strtolower($va_specimen_info["data"]["dwc:infraspecificEpithet"]));
							$t_taxonomy_names->set("source_info", "imported from iDigBio");
							$t_taxonomy_names->set("notes", "imported from iDigBio");
							$t_taxonomy_names->set("is_primary", 1);
							$t_taxonomy_names->set('project_id', $vn_project_id);
							$t_taxonomy_names->set('user_id', $o_request->getUserID());
							
							// Taxa must have at least one field entered
							if (!$t_taxonomy_names->get("genus") && !$t_taxonomy_names->get("species") && !!$t_taxonomy_names->get("subspecies")) {
								$va_errors['general'] = 'Specimen taxon could not be saved: At least one taxonomic field must be set.';
							}
							if (sizeof($va_errors) == 0) {
								# do insert for ms_taxonomy
								$t_taxonomy->setMode(ACCESS_WRITE);
								$t_taxonomy->insert();
								
								if ($t_taxonomy->numErrors()) {
									foreach ($t_taxonomy->getErrors() as $vs_e) {  
										$va_errors["general"] = $vs_e;
									}
								}else{
									# do insert for ms_taxonomy_names
									$t_taxonomy_names->set('taxon_id', $t_taxonomy->get("taxon_id"));
									$t_taxonomy_names->setMode(ACCESS_WRITE);
									$t_taxonomy_names->insert();

									if ($t_taxonomy_names->numErrors()) {
										foreach ($t_taxonomy_names->getErrors() as $vs_e) {  
											$va_errors["general"] = $vs_e;
										}
									}else{
										$vb_taxon_created = true;
										$vn_taxon_id = $t_taxonomy->get("taxon_id");
										$vn_alt_id = $t_taxonomy_names->get("alt_id");
									}
								}
							}
							if(sizeof($va_errors) > 0){
								return array("success" => false, "errors" => $va_errors);
							}
						}
						# --- link the taxa to the specimen
						if(sizeof($va_errors) == 0){
							# do specimen insert or update
							$t_specimen->setMode(ACCESS_WRITE);
							$t_specimen->insert();

							if ($t_specimen->numErrors()) {
								foreach ($t_specimen->getErrors() as $vs_e) {  
									$va_errors["general"] = $vs_e;
									$this->view->setVar("errors", $va_errors);
									return array("success" => false, "errors" => $va_errors);
								}
							}else{
								# --- link taxonomy to specimen
								$t_specimens_x_taxonomy = new ms_specimens_x_taxonomy();
								$t_specimens_x_taxonomy->set("specimen_id",$t_specimen->get("specimen_id"));
								$t_specimens_x_taxonomy->set("alt_id",$vn_alt_id);
								$t_specimens_x_taxonomy->set("user_id",$o_request->user->get("user_id"));
								$t_specimens_x_taxonomy->set("taxon_id",$vn_taxon_id);

								# do insert
								$t_specimens_x_taxonomy->setMode(ACCESS_WRITE);
								$t_specimens_x_taxonomy->insert();
	
								if ($t_specimens_x_taxonomy->numErrors()) {
									foreach ($t_specimens_x_taxonomy->getErrors() as $vs_e) {  
										return array("success" => false, "errors" => $va_errors);
									}
								}else{
									$vs_message = "";
									if($vb_taxon_linked){
										$vs_message = "Linked existing project Taxon to Specimen<br/>";
									}elseif($vb_taxon_created){
										$vs_message = "Created Taxon for Specimen<br/>";
									}
									$vs_message .= "Saved Specimen";								
									return array("success" => true, "message" => $vs_message, "specimen" => $t_specimen);
								}
							}
						}
					}
				}else{
					return array("success" => false, "errors" => array($va_results["error"]));
				}				
			}else{
				return array("success" => false, "errors" => array("Could not import specimen; no uuid passed"));
			}
		}
		# -------------------------------------------------------
}
?>
