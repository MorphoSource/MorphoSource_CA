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
				'LABEL' => _t('Institution code'), 'DESCRIPTION' => _t('Institution code of specimen.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'collection_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Collection code'), 'DESCRIPTION' => _t('Collection code of specimen.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'catalog_number' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 18, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Catalog number'), 'DESCRIPTION' => _t('Catalog number of specimen.'),
				'BOUNDS_LENGTH' => array(1,255)
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
		'description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 62, 'DISPLAY_HEIGHT' => 6,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Description'), 'DESCRIPTION' => _t('Description of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
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
				'LABEL' => _t('Relative age'), 'DESCRIPTION' => _t('Specimen\'s relative age.'),
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
				'DISPLAY_WIDTH' => 30, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality description'), 'DESCRIPTION' => _t('Description of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'locality_coordinates' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 29, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Locality coordinates'), 'DESCRIPTION' => _t('Coordinates of the locality of the specimen.'),
				'BOUNDS_LENGTH' => array(0,65535)
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
				'LABEL' => _t('Locality relative age'), 'DESCRIPTION' => _t('Relative age of the locality of the specimen.'),
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
	function getSpecimenName($pn_specimen_id=null) {
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
		
		$va_name['taxa'] = $this->getSpecimenTaxonomy($pn_specimen_id);
		
		if($pn_specimen_id){
			return $this->formatSpecimenName($va_name);
		}else{
			return null;
		}
	}
	
	# ----------------------------------------
	function formatSpecimenName($pa_specimen) {
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
		$vs_num =  join("-", $va_specimen_parts);
		
		if(is_array($pa_specimen["taxa"])){
			$vs_num .= ", <em>".join("; ", $pa_specimen["taxa"])."</em>";
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
		$vs_num =  join("-", $va_specimen_parts);
		
		return $vs_num;
	}
	# ----------------------------------------
	function getSpecimenTaxonomy($pn_specimen_id=null) {
		if(!$pn_specimen_id) { $pn_specimen_id = $this->get("specimen_id"); }
		if($pn_specimen_id){
			$o_db = new Db();
			$q_specimen_taxonomy = $o_db->query("SELECT tn.alt_id, tn.genus, tn.species, tn.subspecies FROM ms_specimens_x_taxonomy sxt INNER JOIN ms_taxonomy_names AS tn on tn.alt_id = sxt.alt_id WHERE sxt.specimen_id = ? AND tn.is_primary = 1", array($pn_specimen_id));
			$va_taxonomic_names = array();
			if($q_specimen_taxonomy->numRows()){
				while($q_specimen_taxonomy->nextRow()){
					$va_taxonomic_names[$q_specimen_taxonomy->get("alt_id")] = trim($q_specimen_taxonomy->get("genus")." ".$q_specimen_taxonomy->get("species")." ".$q_specimen_taxonomy->get("subspecies"));
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
					$vs_published_where = " m.published > 0 OR";
				}
				$q_media = $o_db->query("
					SELECT m.*
					FROM ms_media m
					INNER JOIN ms_projects AS p ON m.project_id = p.project_id
					LEFT JOIN ms_project_users AS pu ON p.project_id = pu.project_id
					WHERE m.specimen_id = ? AND (".$vs_published_where." pu.user_id = ?)",
					array($pn_specimen_id, $pa_options['user_id']));
			}else{
				if($pa_options['published']){
					$vs_published_where = " AND m.published > 0";
				}
				$q_media = $o_db->query("
					SELECT *
					FROM ms_media m 
					WHERE m.specimen_id = ?".$vs_published_where,
					array($pn_specimen_id));
			}
			$va_media = array();
			if($q_media->numRows()){
				while($q_media->nextRow()){
					$va_media_info = $q_media->getRow();
					unset($va_media_info['media']);
					$va_media_info['mimetype'] = $q_media->getMediaInfo('media', 'original', 'MIMETYPE');
					$va_props = $q_media->getMediaInfo('media', 'original', 'PROPERTIES');
					$va_media_info['filesize'] = $va_props['filesize'];
					
					foreach($va_versions as $vs_version) {
						$va_media_info['tags'][$vs_version] = $q_media->getMediaTag('media', $vs_version);
						$va_media_info['urls'][$vs_version] = $q_media->getMediaUrl('media', $vs_version);
					}
				
					$va_media[$q_media->get("media_id")] = $va_media_info;
				}
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
					WHERE m.specimen_id = ? AND (".$vs_published_where." pu.user_id = ?)",
					array($pn_specimen_id, $pa_options['user_id']));
			}else{
				if($pa_options['published']){
					$vs_published_where = " AND m.published > 0";
				}
				$q_media = $o_db->query("
					SELECT media_id
					FROM ms_media m 
					WHERE m.specimen_id = ?".$vs_published_where,
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
	# ----------------------------------------
}
?>