<?php
/** ---------------------------------------------------------------------
 * app/models/ms_projects.php
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
	require_once(__CA_MODELS_DIR__."/ms_project_users.php");
	require_once(__CA_MODELS_DIR__."/ms_media.php");
	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
	require_once(__CA_MODELS_DIR__."/ca_users.php");

BaseModel::$s_ca_models_definitions['ms_projects'] = array(
 	'NAME_SINGULAR' 	=> _t('project'),
 	'NAME_PLURAL' 		=> _t('projects'),
 	'FIELDS' 			=> array(
 		'project_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Project id'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this project')
		),
		'user_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => 'Row id', 'DESCRIPTION' => 'Project administrator'
		),
		'name' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 83, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Name'), 'DESCRIPTION' => _t('Project name.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'abstract' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Abstract'), 'DESCRIPTION' => _t('Project abstract.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'url' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 83, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('External link'), 'DESCRIPTION' => _t('External link to more information about the project'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'published_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_HIDDEN, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Published on'), 'DESCRIPTION' => _t('Project publication date.'),
		),
		'total_storage_allocation' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Storage used by project'), 'DESCRIPTION' => _t('Total storage used by project, in bytes.'),
		),
		'publication_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Publish project details?'), 'DESCRIPTION' => _t('Would you like your project name, description and members made publicly available on Morphosource?'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Keep project information private') 	=> 0,
					_t('Publish project details')	=> 1
				)
		),
		'created_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Project created on'), 'DESCRIPTION' => _t('Date/time the Project was created.'),
		),
		'last_modified_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Project last modified on'), 'DESCRIPTION' => _t('Date/time the Project was last modified.'),
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
		'deleted' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Is project deleted?'), 'DESCRIPTION' => _t('Media from deleted projects is not available in the public search/browse.'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Active') 	=> 0,
					_t('Deleted')	=> 1
				)
		)
 	)
);

class ms_projects extends BaseModel {
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
	protected $TABLE = 'ms_projects';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'project_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('name');

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
	protected $ORDER_BY = array('name');

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
	protected $SEARCH_CLASSNAME = 'ProjectSearch';
	protected $SEARCH_RESULT_CLASSNAME = 'ProjectSearchResult';
	
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ----------------------------------------
	public function __construct($pn_id=null) {
		parent::__construct($pn_id);
	}
	# ----------------------------------------
	function getProjectsForMember($pn_user_id) {
		$pn_user_id = intval($pn_user_id);
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT p.*, pu.membership_type 
			FROM ms_projects p
			INNER JOIN ms_project_users AS pu ON pu.project_id = p.project_id
			WHERE
				(pu.user_id = ?) AND (pu.active = 1) AND p.deleted = 0
			ORDER BY p.project_id DESC
		", $pn_user_id);
		
		$va_projects = array();
		while ($qr->nextRow()) {
			$va_projects[] = $qr->getRow();
		} 
		return $va_projects;
	}
	# ----------------------------------------
	function isMember($pn_user_id, $pn_project_id = "") {
		$pn_user_id = intval($pn_user_id);
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if ($pn_project_id && ($pn_user_id > 0)) {
			$t_user = new ca_users($pn_user_id);
			if($t_user->isFullAccessUser()){
				$o_db = $this->getDb();
				$q = $o_db->query("
					SELECT user_id, project_id 
					FROM ms_project_users 
					WHERE
						(project_id = ?) AND (user_id = ?)
				", $pn_project_id, $pn_user_id);
				if ($q->nextRow()) {
					return true;
				} else {
					return false;
				}
			}else{
				return false;
			}
		} else {
			return false;
		}
	}
	# ----------------------------------------
	function getMembers() {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT u.user_id, u.user_name, u.email, u.fname, u.lname, pu.membership_type 
			FROM ca_users u
			INNER JOIN ms_project_users AS pu ON pu.user_id = u.user_id
			WHERE
				(pu.project_id = ?) AND (pu.active = 1)
			ORDER BY u.lname, u.fname
		", $vn_project_id);
		
		$va_members = array();
		while ($qr->nextRow()) {
			$va_members[] = $qr->getRow();
		} 
		return $va_members;
	}
	# ----------------------------------------
	/**
	 * Can contribute info/ access project forms/ edit project info
	 */
	public function isFullAccessMember($pn_user_id, $pn_project_id = "") {
		$pn_user_id = intval($pn_user_id);
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if ($pn_project_id && ($pn_user_id > 0)) {
			$t_user = new ca_users($pn_user_id);
			if($t_user->isFullAccessUser()){
				$o_db = $this->getDb();
				$q = $o_db->query("
					SELECT user_id, project_id 
					FROM ms_project_users 
					WHERE
						(project_id = ?) AND (user_id = ?) AND membership_type = 1
				", $pn_project_id, $pn_user_id);
				if ($q->nextRow()) {
					return true;
				} else {
					return false;
				}
			}else{
				return false;
			}
		} else {
			return false;
		}
	}
	
	# ----------------------------------------
	/**
	 * Sets last_accessed_on timestamp in ms_projects and ms_projects_users for the specified user and currently loaded project to the current time
	 */
	function setUserAccessTime($pn_user_id) {
		if (!($vn_project_id = $this->getPrimaryKey())) { return null; }
		$vn_time = time();

		$t_pu = new ms_project_users();
		if ($t_pu->load(array('user_id' => (int)$pn_user_id, 'project_id' => $vn_project_id))) {
			$t_pu->setMode(ACCESS_WRITE);
			$t_pu->set('last_access_on', $vn_time);
			$t_pu->update();
			
			if ($t_pu->numErrors()) {
				$this->errors = $t_pu->errors;
				return false;
			}

			$this->setMode(ACCESS_WRITE);
			$this->set('last_modified_on', $vn_time);
			$this->update();
			if ($this->numErrors())
				return false;

			return true;
		}
		return null;
	}
	# ----------------------------------------
	function numDownloads($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media_download_stats ms
			INNER JOIN ms_media AS m ON ms.media_id = m.media_id
			WHERE m.project_id = ?
		", $pn_project_id);
		
		$vn_num_downloads = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_downloads = $qr->get("c");
		}
		return $vn_num_downloads;
	}
	# ----------------------------------------
	function numMediaViews($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media_view_stats ms
			INNER JOIN ms_media AS m ON ms.media_id = m.media_id
			WHERE m.project_id = ?
		", $pn_project_id);
		
		$vn_num_views = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_views = $qr->get("c");
		}
		return $vn_num_views;
	}
	# ----------------------------------------
	function numMedia($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media m
			WHERE m.project_id = ?
		", $pn_project_id);
		
		$vn_num_media = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_media = $qr->get("c");
		}
		return $vn_num_media;
	}
	# ----------------------------------------
	function numReadOnlyMedia($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media m
			LEFT JOIN ms_media_x_projects AS mxp ON m.media_id = mxp.media_id
			WHERE mxp.project_id = ? AND m.project_id != ?
		", $pn_project_id, $pn_project_id);
		
		$vn_num_media = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_media = $qr->get("c");
		}
		return $vn_num_media;
	}
	# ----------------------------------------
	function numMediaFiles($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_media_files mf
			INNER JOIN ms_media AS m ON m.media_id = mf.media_id
			WHERE m.project_id = ?
		", $pn_project_id);
		
		$vn_num_media_files = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_media_files = $qr->get("c");
		}
		return $vn_num_media_files;
	}
	# ----------------------------------------
	# --- returns count of all specimens used in project NOT created by project
	function numSpecimens($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();

		
		$qr = $o_db->query("
			SELECT DISTINCT s.specimen_id
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			WHERE s.project_id = ?
			OR m.project_id = ?
		", $pn_project_id, $pn_project_id);
		
		$vn_num_specimens = $qr->numRows();
		return $vn_num_specimens;
	}
	# ----------------------------------------
	# --- returns count of all citations used in project NOT created by project
	function numCitations($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$va_bibref_ids = array();
		# --- get bib refs created by projects in case they are not in use
		$q_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b WHERE b.project_id = ?", $pn_project_id);
		
		# --- get media bib refs - in case they were made in another project
		$q_media_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b INNER JOIN ms_media_x_bibliography AS mxb ON mxb.bibref_id = b.bibref_id INNER JOIN ms_media AS m ON mxb.media_id = m.media_id WHERE m.project_id = ?", $pn_project_id);
		
		# -- get specimen bib refs
		$q_specimen_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b INNER JOIN ms_specimens_x_bibliography AS sxb ON sxb.bibref_id = b.bibref_id INNER JOIN ms_media AS m ON sxb.specimen_id = m.specimen_id WHERE m.project_id = ?", $pn_project_id);
		
		# -- get specimen meta bib refs
		$q_specimen_meta_bibs = $o_db->query("SELECT s.body_mass_bibref_id, s.locality_absolute_age_bibref_id, s.locality_relative_age_bibref_id FROM ms_specimens s INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id WHERE m.project_id = ?", $pn_project_id);
		
		if($q_bibs->numRows() > 0){
			while($q_bibs->nextRow()){
				$va_bibref_ids[$q_bibs->get("bibref_id")] = $q_bibs->get("bibref_id");
			}
		}
		if($q_media_bibs->numRows() > 0){
			while($q_media_bibs->nextRow()){
				$va_bibref_ids[$q_media_bibs->get("bibref_id")] = $q_media_bibs->get("bibref_id");
			}
		}
		if($q_specimen_bibs->numRows() > 0){
			while($q_specimen_bibs->nextRow()){
				$va_bibref_ids[$q_specimen_bibs->get("bibref_id")] = $q_specimen_bibs->get("bibref_id");
			}
		}
		if($q_specimen_meta_bibs->numRows() > 0){
			while($q_specimen_meta_bibs->nextRow()){
				if($q_specimen_meta_bibs->get("body_mass_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("body_mass_bibref_id")] = $q_specimen_meta_bibs->get("body_mass_bibref_id");
				}
				if($q_specimen_meta_bibs->get("locality_absolute_age_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("locality_absolute_age_bibref_id")] = $q_specimen_meta_bibs->get("locality_absolute_age_bibref_id");
				}
				if($q_specimen_meta_bibs->get("locality_relative_age_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("locality_relative_age_bibref_id")] = $q_specimen_meta_bibs->get("locality_relative_age_bibref_id");
				}
			}
		}
		$vn_num_citations = sizeof($va_bibref_ids);

		return $vn_num_citations;
	}
	# ----------------------------------------
	# --- $pn_owned_by true returns only media owned directly by the project - this excludes read only media
	function getProjectMedia($pn_owned_by = null, $ps_order_by = null, $pa_options=null) {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }

		if(!$ps_order_by){
			$ps_order_by = "media_id";
		}
		if(!$pa_options){
			$pa_options = array();
		}

		$vs_order_by = "";
		switch($ps_order_by){
			case "number":
				$vs_order_by = " s.institution_code, s.collection_code, 
					s.catalog_number";
			break;
			# -----------------
			case "taxon":
				$vs_order_by = " t.genus, t.species, t.subspecies";
			break;
			# -----------------
			case "added":
				$vs_order_by = " m.created_on";
			break;
			# -----------------
			case "modified":
				$vs_order_by = " m.last_modified_on";
			break;
			# -----------------
			default:
				$vs_order_by = " m.media_id";
			break;
		}

		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		$vs_select_join = "
			SELECT DISTINCT m.media_id, m.media, m.specimen_id, m.published, 
				m.reviewer_id, m.title, m.project_id, m.element, m.created_on, 
				m.last_modified_on, s.institution_code, s.collection_code, 
				s.catalog_number, t.taxon_id, t.species, t.genus, t.ht_family, 
				t.ht_order, t.ht_class 
			FROM ms_media m
			LEFT JOIN ms_specimens AS s ON s.specimen_id = m.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = m.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id ";

		$o_db = $this->getDb();
		if($pn_owned_by){
			$qr = $o_db->query($vs_select_join.
				"WHERE m.project_id = ?".$vs_published_where.
				"ORDER BY".$vs_order_by, $vn_project_id);
		}else{
			$qr = $o_db->query($vs_select_join."
				LEFT JOIN ms_media_x_projects AS mxp ON m.media_id = mxp.media_id
				WHERE (m.project_id = ? OR mxp.project_id = ?)".$vs_published_where.
				"ORDER BY".$vs_order_by, $vn_project_id, $vn_project_id);
		}

		return $qr;
	}
	# ----------------------------------------
	# --- $pn_owned_by true returns only media owned directly by the project - this excludes read only media
	function getProjectMediaNestTaxonomy($pn_owned_by = null, $pb_vertnet = false, $pa_options=null) {
		// Return media groups in a series of nested taxonomic (family, order, etc.) arrays
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }

		// Constructing db query
		if(!$ps_order_by){
			$ps_order_by = "media_id";
		}
		if(!$pa_options){
			$pa_options = array();
		}

		$vs_order_by = "";
		switch($ps_order_by){
			case "number":
				$vs_order_by = " s.institution_code, s.collection_code, 
					s.catalog_number";
			break;
			# -----------------
			case "taxon":
				$vs_order_by = " t.genus, t.species, t.subspecies";
			break;
			# -----------------
			case "created_on":
				$vs_order_by = " m.created_on";
			break;
			# -----------------
			case "last_modified_on":
				$vs_order_by = " m.last_modified_on";
			break;
			# -----------------
			default:
				$vs_order_by = " m.media_id";
			break;
		}

		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		if($pb_vertnet){
			$vs_select_join = "
			SELECT DISTINCT m.media_id, m.media, m.specimen_id, m.published, 
				m.title, m.project_id, m.element, m.created_on, 
				m.last_modified_on, s.institution_code, s.collection_code, 
				s.catalog_number, t.taxon_id, t.species, t.genus, 
				g.name AS 'vn_genus', f.name AS 'ht_family', 
				o.name AS 'ht_order', c.name AS 'ht_class', 
				g.taxon_id AS 'vn_taxon_id' 
			FROM ms_media m
			LEFT JOIN ms_specimens AS s ON s.specimen_id = m.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = m.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
			LEFT JOIN ms_specimens_x_resolved_taxonomy AS xrt ON m.specimen_id = xrt.specimen_id
			LEFT JOIN ms_resolved_taxonomy AS g ON xrt.taxon_id = g.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS f ON g.parent_id = f.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS o ON f.parent_id = o.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS  c ON o.parent_id = c.taxon_id ";
		}else{
			$vs_select_join = "
			SELECT DISTINCT m.media_id, m.media, m.specimen_id, m.published, 
			m.title, m.project_id, m.element, m.created_on, m.last_modified_on, 
			s.institution_code, s.collection_code, s.catalog_number, t.taxon_id, 
			t.species, t.genus, t.ht_family, t.ht_order, t.ht_class 
			FROM ms_media m
			LEFT JOIN ms_specimens AS s ON s.specimen_id = m.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = m.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id ";
		}
		
		$o_db = $this->getDb();
		if($pn_owned_by){
			$qr = $o_db->query($vs_select_join."WHERE m.project_id = ?".
				$vs_published_where." ORDER BY".$vs_order_by, $vn_project_id);
		}else{
			$qr = $o_db->query($vs_select_join."
				LEFT JOIN ms_media_x_projects AS mxp ON m.media_id = mxp.media_id 
				WHERE (m.project_id = ? OR mxp.project_id = ?)".
				$vs_published_where." ORDER BY".$vs_order_by, $vn_project_id, 
				$vn_project_id);
		}
		
		// Constructing taxonomically nested arrays 
		$va_taxon_levels = ['Class' => 'ht_class', 'Order' => 'ht_order', 
			'Family' => 'ht_family', 'Genus' => 'genus', 'Species' => 'species'];

		$t_media = new ms_media();
		$va_nm = array();
		$vn_count = 0;

		while ($qr->nextRow()) {
			$va_media = $qr->getRow();
			$va_media['preview'] = 
				$t_media->getPreviewMediaFile($va_media['media_id']);

			$va_st = array();
			$va_no_link = array();

			foreach ($va_taxon_levels as $vs_taxon_display => $vs_taxon_name) {
				$vb_no_link = false;
				$vs_t = trim($va_media[$vs_taxon_name]);
				if (!$vs_t) {
					$vs_t = $vs_taxon_display." not defined";
					$vb_no_link = true;
				}
				$va_st[$vs_taxon_name] = $vs_t;
				$va_no_link[$vs_taxon_name] = $vb_no_link;
			}

			if ($va_no_link["ht_class"]) { 
				$va_nm[$va_st["ht_class"]]["no_link"] = $va_no_link["ht_class"]; 
			}
			if ($va_no_link["ht_order"]) { 
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]]["no_link"] = $va_no_link["ht_order"]; 
			}
			if ($va_no_link["ht_family"]) { 
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]]["no_link"] = $va_no_link["ht_family"]; 
			}
			if ($va_no_link["genus"]) { 
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]]["no_link"] = $va_no_link["genus"]; 
			}
			if ($va_no_link["species"]) { 
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["no_link"] = $va_no_link["species"]; 
			}

			if (!isset($va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["media"][$va_media['media_id']])) {
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["media"][$va_media['media_id']] = $va_media;
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["taxon_id"] = $va_media['taxon_id'];
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["species"] = $va_media['species'];
				$va_nm[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["genus"] = $va_media['genus'];

				$vn_count++;
			}
		}

		return array("media" => $va_nm, "numMedia" => $vn_count);
	}
	# ----------------------------------------
	# $ps_order_by = number (s.institution_code, s.collection_code, s.catalog_number), taxon (t.genus, t.species, t.subspecies)
	# ----------------------------------------
	function getProjectSpecimens($pa_versions=null, $ps_order_by=null, $pa_options=null) {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		if(!$ps_order_by){
			$ps_order_by = "number";
		}
		if(!$pa_options){
			$pa_options = array();
		}
		$vs_order_by = "";
		$vs_order_by_joins = "";
		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		switch($ps_order_by){
			case "number":
				$vs_order_by = " s.institution_code, s.collection_code, s.catalog_number";
			break;
			# -----------------
			case "taxon":
				$vs_order_by = " t.genus, t.species, t.subspecies";
				$vs_order_by_joins = "LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
										LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id";
			break;
			# -----------------
			case "added":
				$vs_order_by = " s.created_on";
			break;
			# -----------------
			case "modified":
				$vs_order_by = " s.last_modified_on";
			break;
		}
		
		$o_db = $this->getDb();		
		$qr = $o_db->query("
			SELECT DISTINCT s.*, m.media_id, m.published, p.name project_name, u.fname, u.lname, u.email, sp.link_id linked_specimen
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
			LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
			LEFT JOIN ca_users AS u ON p.user_id = u.user_id
			LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
			LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
			".$vs_order_by_joins."
			WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
			(s.project_id = ?
			OR m.project_id = ?
			OR mp.project_id = ?
			OR sp.project_id = ?)".$vs_published_where."
			ORDER BY ".$vs_order_by
		, $vn_project_id, $vn_project_id, $vn_project_id, $vn_project_id);
			
		if (!is_array($pa_versions) || !sizeof($pa_versions)) {
			$pa_versions = array('small', 'preview190');
		}
		
		$va_specimens = array();
		while($qr->nextRow()) {
			$va_specimen = $qr->getRow();
			
			if(!isset($va_specimens[$va_specimen['specimen_id']])) {
				$va_specimen['media'] = array();
				$va_specimens[$va_specimen['specimen_id']] = $va_specimen;
			}
			if ($vn_media_id = $va_specimen['media_id']) {
				$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id] = array();
				$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id]['published'] = $va_specimen['published'];
				$t_media = new ms_media();
				$va_media_preview_file_info = $t_media->getPreviewMediaFile($vn_media_id, $pa_versions, ($pa_options["published_media_only"]) ? true : false);
				foreach($pa_versions as $vs_version) {
					$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
				}
			}
		}

		return $va_specimens;
	}
	# ----------------------------------------
	# options include taxonomy_type = ht_family, genus, or species; taxonomy_term = name of ht_family, genus or species to limit query to
	# ----------------------------------------
	function getProjectSpecimensNestTaxonomy($pa_versions=null, $pb_vertnet=false, $pa_options=null) {
		// Return specimens in a series of nested taxonomic (family, order, etc.) arrays
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		if(!$pa_options){
			$pa_options = array();
		}

		// Constructing db query
		$vs_order_by = " s.institution_code, s.collection_code, 
			s.catalog_number";
		
		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		if($pb_vertnet){
			$vs_select_from = "
			SELECT s.*, m.media_id, m.published, p.name AS 'project_name', 
				u.fname, u.lname, u.email, sp.link_id AS 'linked_specimen', 
				t.species, t.genus AS 'genus', g.name AS 'vn_genus', 
				f.name AS 'ht_family', o.name AS 'ht_order', 
				c.name AS 'ht_class', t.taxon_id, g.taxon_id AS 'vn_taxon_id'   
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
			LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
			LEFT JOIN ca_users AS u ON p.user_id = u.user_id
			LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
			LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON s.specimen_id = sxt.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
			LEFT JOIN ms_specimens_x_resolved_taxonomy AS xrt ON s.specimen_id = xrt.specimen_id
			LEFT JOIN ms_resolved_taxonomy AS g ON xrt.taxon_id = g.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS f ON g.parent_id = f.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS o ON f.parent_id = o.taxon_id
			LEFT JOIN ms_resolved_taxonomy AS  c ON o.parent_id = c.taxon_id ";
		}else{
			$vs_select_from = "
			SELECT DISTINCT s.*, m.media_id, m.published, p.name project_name, 
				u.fname, u.lname, u.email, sp.link_id linked_specimen, 
				t.species, t.genus, t.ht_family, t.ht_order, t.ht_class, 
				t.taxon_id
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
			LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
			LEFT JOIN ca_users AS u ON p.user_id = u.user_id
			LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
			LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id ";
		}

		$o_db = $this->getDb();
		if($pa_options["taxonomy_type"] && $pa_options["taxonomy_term"]){
			$qr = $o_db->query($vs_select_from."
				WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
				(s.project_id = ?
				OR m.project_id = ?
				OR mp.project_id = ?
				OR sp.project_id = ?)
				AND (t.".$pa_options["taxonomy_type"]." = ?) ".
				$vs_published_where." ORDER BY ".$vs_order_by, $vn_project_id, 
				$vn_project_id, $vn_project_id, $vn_project_id, 
				$pa_options["taxonomy_term"]);
		}else{
			$qr = $o_db->query($vs_select_from."
				WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
				(s.project_id = ?
				OR m.project_id = ?
				OR mp.project_id = ?
				OR sp.project_id = ?)".$vs_published_where." ORDER BY ".
				$vs_order_by, $vn_project_id, $vn_project_id, $vn_project_id, 
				$vn_project_id);
		}	

		// Constructing taxonomically nested arrays 			
		if (!is_array($pa_versions) || !sizeof($pa_versions)) {
			$pa_versions = array('small', 'preview190');
		}
		
		$va_taxon_levels = ['Class' => 'ht_class', 'Order' => 'ht_order', 
			'Family' => 'ht_family', 'Genus' => 'genus', 
			'Species' => 'species'];

		$va_ns = array();
		$vn_count = 0;

		while ($qr->nextRow()) {
			$va_specimen = $qr->getRow();
			$va_st = array();
			$va_no_link = array();
			foreach ($va_taxon_levels as $vs_taxon_display => $vs_taxon_name) {
				$vb_no_link = false;
				$vs_t = trim($va_specimen[$vs_taxon_name]);
				if (!$vs_t) {
					$vs_t = $vs_taxon_display." not defined";
					$vb_no_link = true;
				}
				$va_st[$vs_taxon_name] = $vs_t;
				$va_no_link[$vs_taxon_name] = $vb_no_link;
			}

			if ($va_no_link["ht_class"]) { 
				$va_ns[$va_st["ht_class"]]["no_link"] = $va_no_link["ht_class"]; 
			}
			if ($va_no_link["ht_order"]) { 
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]]["no_link"] = $va_no_link["ht_order"]; 
			}
			if ($va_no_link["ht_family"]) { 
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]]["no_link"] = $va_no_link["ht_family"]; 
			}
			if ($va_no_link["genus"]) { 
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]]["no_link"] = $va_no_link["genus"]; 
			}
			if ($va_no_link["species"]) { 
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["no_link"] = $va_no_link["species"]; 
			}

			if (!isset($va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']])) {
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']] = $va_specimen;
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["taxon_id"] = $qr->get("taxon_id");
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["species"] = $qr->get("species");
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["genus"] = $qr->get("genus");
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["ht_family"] = $qr->get("ht_family");
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["ht_order"] = $qr->get("ht_order");
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["ht_class"] = $qr->get("ht_class");

				$vn_count++;
			}
			if ($vn_media_id = $va_specimen['media_id']) {
				$t_media = new ms_media();
				$va_media_preview_file_info = $t_media->getPreviewMediaFile($vn_media_id, $pa_versions, ($pa_options["published_media_only"]) ? true : false);
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id] = array();
				$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['published'] = $va_specimen['published'];
				foreach($pa_versions as $vs_version) {
					$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
					$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["media"][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_ns[$va_st["ht_class"]][$va_st["ht_order"]][$va_st["ht_family"]][$va_st["genus"]][$va_st["species"]]["media"][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
				}
			}
		}
			
		return array("specimen" => $va_ns, "numSpecimen" => $vn_count);
	}
	# ----------------------------------------
	# $ps_group_by = string (genus, species)
	# options include taxonomy_type = genus or species; taxonomy_term = name of ht_family, genus or species to limit query to
	# ----------------------------------------
	function getProjectSpecimensByTaxonomy($pa_versions=null, $ps_group_by=null, $pa_options=null) {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		if(!$ps_group_by || (!in_array($ps_group_by, array("genus", "species", "ht_family")))){
			$ps_group_by = "genus";
		}
		switch($ps_group_by){
			case "ht_family":
				$ps_group_by_display = "family";
			break;
			# ------------------------------------------
			default:
				$ps_group_by_display = $ps_group_by;
			break;
			# ------------------------------------------
		
		}
		if(!$pa_options){
			$pa_options = array();
		}
		# --- order by the group_by
		$vs_order_by = "";
		$vs_order_by = " t.".$ps_group_by;
		
		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		
		$o_db = $this->getDb();		
		if($pa_options["taxonomy_type"] && $pa_options["taxonomy_term"]){
			$qr = $o_db->query("
				SELECT DISTINCT s.*, m.media_id, m.published, p.name project_name, u.fname, u.lname, u.email, sp.link_id linked_specimen, t.species, t.genus, t.ht_family, t.taxon_id
				FROM ms_specimens s
				LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
				LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
				LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
				LEFT JOIN ca_users AS u ON p.user_id = u.user_id
				LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
				LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
				LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
				LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
				WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
				(s.project_id = ?
				OR m.project_id = ?
				OR mp.project_id = ?
				OR sp.project_id = ?)
				AND (t.".$pa_options["taxonomy_type"]." = ?) ".$vs_published_where."
				ORDER BY ".$vs_order_by
			, $vn_project_id, $vn_project_id, $vn_project_id, $vn_project_id, $pa_options["taxonomy_term"]);
		}else{
			$qr = $o_db->query("
				SELECT DISTINCT s.*, m.media_id, m.published, p.name project_name, u.fname, u.lname, u.email, sp.link_id linked_specimen, t.species, t.genus, t.ht_family, t.taxon_id
				FROM ms_specimens s
				LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
				LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
				LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
				LEFT JOIN ca_users AS u ON p.user_id = u.user_id
				LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
				LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
				LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
				LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
				WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
				(s.project_id = ?
				OR m.project_id = ?
				OR mp.project_id = ?
				OR sp.project_id = ?)".$vs_published_where."
				ORDER BY ".$vs_order_by
			, $vn_project_id, $vn_project_id, $vn_project_id, $vn_project_id);
		}	
			
		if (!is_array($pa_versions) || !sizeof($pa_versions)) {
			$pa_versions = array('small', 'preview190');
		}
		
		$va_specimens_by_taxonomy = array();
		$vn_count = 0;
		while($qr->nextRow()) {
			$va_specimen = $qr->getRow();
			$vs_group_by = trim($va_specimen[$ps_group_by]);
			$vb_no_link = false;
			if(!$vs_group_by){
				$vs_group_by = $ps_group_by_display." not defined";
				$vb_no_link = true;
			}
			if(!isset($va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']])) {
				$va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']] = $va_specimen;
				$va_specimens_by_taxonomy[$vs_group_by]["taxon_id"] = $qr->get("taxon_id");
				$va_specimens_by_taxonomy[$vs_group_by]["genus"] = $qr->get("genus");
				$va_specimens_by_taxonomy[$vs_group_by]["ht_family"] = $qr->get("ht_family");
				$va_specimens_by_taxonomy[$vs_group_by]["no_link"] = $vb_no_link;
				
				$vn_count++;
			}
			if ($vn_media_id = $va_specimen['media_id']) {
				$t_media = new ms_media();
				$va_media_preview_file_info = $t_media->getPreviewMediaFile($vn_media_id, $pa_versions, ($pa_options["published_media_only"]) ? true : false);
				$va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id] = array();
				$va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['published'] = $va_specimen['published'];
				foreach($pa_versions as $vs_version) {
					$va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_specimens_by_taxonomy[$vs_group_by]["specimens"][$va_specimen['specimen_id']]['media'][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
					$va_specimens_by_taxonomy[$vs_group_by]["media"][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_specimens_by_taxonomy[$vs_group_by]["media"][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
				}
			}
		}

		return array("specimen" => $va_specimens_by_taxonomy, "numSpecimen" => $vn_count);
	}
	# ----------------------------------------
	# $ps_order_by = number (s.institution_code, s.collection_code, s.catalog_number)
	# ----------------------------------------
	function getProjectSpecimenWithoutTaxonomy($pa_versions=null, $ps_order_by=null, $ps_taxonomy_field=null, $pa_options=null) {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		if(!$ps_order_by){
			$ps_order_by = "number";
		}
		if(!$pa_options){
			$pa_options = array();
		}
		$vs_order_by = "";
		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		switch($ps_order_by){
			case "number":
				$vs_order_by = " s.institution_code, s.collection_code, s.catalog_number";
			break;
			# -----------------
		}
		$vs_taxonomy_field_where = "";
		if($ps_taxonomy_field){
			$vs_taxonomy_field_where = " OR (tn.".$ps_taxonomy_field." = '')";
		}
		$o_db = $this->getDb();
		
		
		$qr = $o_db->query("
			SELECT DISTINCT s.*, m.media_id, p.name project_name, sp.link_id linked_specimen
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
			LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
			LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
			LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS st ON st.specimen_id = s.specimen_id
			LEFT JOIN ms_taxonomy_names AS tn ON tn.alt_id = st.alt_id
			WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
			(s.project_id = ?
			OR m.project_id = ?
			OR mp.project_id = ?
			OR sp.project_id = ?)".$vs_published_where."
			
			AND 
			((st.link_id IS NULL)".$vs_taxonomy_field_where.")
			
			
			ORDER BY ".$vs_order_by
		, $vn_project_id, $vn_project_id, $vn_project_id, $vn_project_id);
			
			
		if (!is_array($pa_versions) || !sizeof($pa_versions)) {
			$pa_versions = array('small', 'preview190');
		}
		
		$va_specimens = array();
		while($qr->nextRow()) {
			$va_specimen = $qr->getRow();
			
			if(!isset($va_specimens[$va_specimen['specimen_id']])) {
				$va_specimen['media'] = array();
				$va_specimens[$va_specimen['specimen_id']] = $va_specimen;
			}
			if ($vn_media_id = $va_specimen['media_id']) {
				$t_media = new ms_media();
				$va_media_preview_file_info = $t_media->getPreviewMediaFile($vn_media_id, $pa_versions, ($pa_options["published_media_only"]) ? true : false);
				$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id] = array();
				foreach($pa_versions as $vs_version) {
					$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id]['tags'] = $va_media_preview_file_info["media"];
					$va_specimens[$va_specimen['specimen_id']]['media'][$vn_media_id]['urls'] = $va_media_preview_file_info["urls"];
				}
			}
		}

		return $va_specimens;
	}
	# ----------------------------------------
	function getProjectSpecimensCountWithFamily($pa_options=null) {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		if(!$pa_options){
			$pa_options = array();
		}
		# --- order by the group_by
		$vs_order_by = "";
		$vs_order_by = " t.".$ps_group_by;
		
		$vs_published_where = "";
		if($pa_options["published_media_only"]){
			$vs_published_where = " AND m.published != 0 ";
		}
		
		
		$o_db = $this->getDb();				
		$qr = $o_db->query("
			SELECT DISTINCT s.specimen_id
			FROM ms_specimens s
			LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
			LEFT JOIN ms_projects AS mproj ON m.project_id = mproj.project_id
			LEFT JOIN ms_projects AS p ON s.project_id = p.project_id
			LEFT JOIN ca_users AS u ON p.user_id = u.user_id
			LEFT JOIN ms_media_x_projects AS mp ON m.media_id = mp.media_id
			LEFT JOIN ms_specimens_x_projects AS sp ON s.specimen_id = sp.specimen_id
			LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
			LEFT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
			WHERE ((mproj.deleted != 1) OR (mproj.deleted IS NULL)) AND 
			t.ht_family != '' AND
			(s.project_id = ?
			OR m.project_id = ?
			OR mp.project_id = ?
			OR sp.project_id = ?)".$vs_published_where
		, $vn_project_id, $vn_project_id, $vn_project_id, $vn_project_id);	
			
		$vn_count = $qr->numRows();

		return $vn_count;
	}
	# ----------------------------------------
	function getProjectTaxonomy() {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
				SELECT DISTINCT tn.*, p.name
				FROM ms_taxonomy_names tn 
				LEFT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.taxon_id = tn.taxon_id
				LEFT JOIN ms_media AS m ON m.specimen_id = sxt.specimen_id
				LEFT JOIN ms_projects AS p ON tn.project_id = p.project_id
				WHERE m.project_id = ? OR tn.project_id = ? 
				ORDER BY tn.genus, tn.species, tn.subspecies
		", $vn_project_id, $vn_project_id);

		return $qr;
	}
	# ----------------------------------------
	function getProjectFacilities() {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
				SELECT DISTINCT f.*, p.name projectName, u.email, u.fname, u.lname
				FROM ms_facilities f
				LEFT JOIN ms_media AS m ON m.facility_id = f.facility_id
				LEFT JOIN ms_projects AS p on f.project_id = p.project_id
				LEFT JOIN ca_users AS u on p.user_id = u.user_id
				WHERE m.project_id = ? OR f.project_id = ?
				ORDER BY f.name, f.institution
		", $vn_project_id, $vn_project_id);

		return $qr;
	}
	# ----------------------------------------
	# --- returns all citation ids used in project NOT created by project
	function getProjectCitationIDs($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$va_bibref_ids = array();
		# --- get bib refs created by projects in case they are not in use
		$q_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b WHERE b.project_id = ?", $pn_project_id);
		
		# --- get media bib refs - in case they were made in another project
		$q_media_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b INNER JOIN ms_media_x_bibliography AS mxb ON mxb.bibref_id = b.bibref_id INNER JOIN ms_media AS m ON mxb.media_id = m.media_id WHERE m.project_id = ?", $pn_project_id);
		
		# -- get specimen bib refs
		$q_specimen_bibs = $o_db->query("SELECT b.bibref_id FROM ms_bibliography b INNER JOIN ms_specimens_x_bibliography AS sxb ON sxb.bibref_id = b.bibref_id INNER JOIN ms_media AS m ON sxb.specimen_id = m.specimen_id WHERE m.project_id = ?", $pn_project_id);
		
		# -- get specimen meta bib refs
		$q_specimen_meta_bibs = $o_db->query("SELECT s.body_mass_bibref_id, s.locality_absolute_age_bibref_id, s.locality_relative_age_bibref_id FROM ms_specimens s INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id WHERE m.project_id = ?", $pn_project_id);
		
		if($q_bibs->numRows() > 0){
			while($q_bibs->nextRow()){
				$va_bibref_ids[$q_bibs->get("bibref_id")] = $q_bibs->get("bibref_id");
			}
		}
		if($q_media_bibs->numRows() > 0){
			while($q_media_bibs->nextRow()){
				$va_bibref_ids[$q_media_bibs->get("bibref_id")] = $q_media_bibs->get("bibref_id");
			}
		}
		if($q_specimen_bibs->numRows() > 0){
			while($q_specimen_bibs->nextRow()){
				$va_bibref_ids[$q_specimen_bibs->get("bibref_id")] = $q_specimen_bibs->get("bibref_id");
			}
		}
		if($q_specimen_meta_bibs->numRows() > 0){
			while($q_specimen_meta_bibs->nextRow()){
				if($q_specimen_meta_bibs->get("body_mass_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("body_mass_bibref_id")] = $q_specimen_meta_bibs->get("body_mass_bibref_id");
				}
				if($q_specimen_meta_bibs->get("locality_absolute_age_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("locality_absolute_age_bibref_id")] = $q_specimen_meta_bibs->get("locality_absolute_age_bibref_id");
				}
				if($q_specimen_meta_bibs->get("locality_relative_age_bibref_id")){
					$va_bibref_ids[$q_specimen_meta_bibs->get("locality_relative_age_bibref_id")] = $q_specimen_meta_bibs->get("locality_relative_age_bibref_id");
				}
			}
		}
		return $va_bibref_ids;
	}
	# ----------------------------------------
	function getProjectMediaCounts($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c, published
			FROM ms_media b
			WHERE b.project_id = ?
			GROUP BY published
		", $pn_project_id);
		
		$va_counts = array();
		while($qr->nextRow()){
			$va_counts[$qr->get('published')] = (int)$qr->get('c');
		}
		return $va_counts;
	}
	# ----------------------------------------
	function getProjectMediaFileCounts($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		# --- first get the counts of media file pub status that is inherited from group
		$qr = $o_db->query("
			SELECT count(*) c, m.published
			FROM ms_media_files mf
			INNER JOIN ms_media AS m ON mf.media_id = m.media_id
			WHERE m.project_id = ? AND mf.published IS null
			GROUP BY m.published
		", $pn_project_id);
		$va_counts = array();
		while($qr->nextRow()){
			$va_counts[$qr->get('published')] = (int)$qr->get('c');
		}
		$qr = $o_db->query("
			SELECT count(*) c, mf.published
			FROM ms_media_files mf
			INNER JOIN ms_media AS m ON mf.media_id = m.media_id
			WHERE m.project_id = ? AND mf.published IS NOT NULL
			GROUP BY mf.published
		", $pn_project_id);
		while($qr->nextRow()){
			$va_counts[$qr->get('published')] = (int)$qr->get('c') + $va_counts[$qr->get('published')];
		}
		return $va_counts;
	}
	# ----------------------------------------
	# --- $pn_published is value to set published field to
	function publishAllProjectMedia($pn_published, $pn_project_id=null) {
		if(!$pn_published) { return null; }
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = new Db();
		$qr_res = $o_db->query("
			SELECT media_id
			FROM ms_media
			WHERE 
				project_id = ? AND published = 0
		", $pn_project_id);
		
		$vn_pub_count = 0;
		while($qr_res->nextRow()){ 
			$t_media = new ms_media($qr_res->get('media_id'));
			$t_media->setMode(ACCESS_WRITE);
			$t_media->set('published', $pn_published);
			$t_media->set('published_on','now');
			$t_media->update();
			if ($t_media->numErrors() == 0) {
				$vn_pub_count++;
				# Publish ARKs for any newly published media files
				$va_media_files = $t_media->getMediaFiles();
				if (sizeof($va_media_files)) {
					foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
						if (is_null($t_media_file->get("published")) 
							|| ($t_media_file->get("published") === ""))
						{
							$t_user = new ca_users(
								($t_media_file->get('user_id') ? 
								$t_media_file->get('user_id') : 
								$this->get('user_id')));
							$va_ark = $t_media_file->publishARK(
								$vs_user_fname = $t_user->get('fname'),
								$vs_user_lname = $t_user->get('lname')
							);
						}
					}
				}
			}
		}
		
		return $vn_pub_count;
	}
	# ----------------------------------------
	function publishAllProjectMediaFiles($pn_project_id=null) {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = new Db();
		$qr_res = $o_db->query("
			SELECT mf.media_file_id
			FROM ms_media_files mf
			INNER JOIN ms_media as m ON m.media_id = mf.media_id
			WHERE 
				m.project_id = ? AND mf.published = 0
		", $pn_project_id);
		
		$vn_pub_count = 0;
		while($qr_res->nextRow()){ 
			$t_media_file = new ms_media_files($qr_res->get('media_file_id'));
			$t_media_file->setMode(ACCESS_WRITE);
			$t_media_file->set('published', 1);
			$t_media_file->update();
			if ($t_media_file->numErrors() == 0) {
				$vn_pub_count++;
				# Publish ARK
				$t_user = new ca_users(($t_media_file->get('user_id') ? 
					$t_media_file->get('user_id') : 
					$this->get('user_id')));
				$va_ark = $t_media_file->publishARK(
					$vs_user_fname = $t_user->get('fname'),
					$vs_user_lname = $t_user->get('lname')
				);
			}
		}
		
		return $vn_pub_count;
	}
	# ------------------------------------------------------
 	/**
 	 * @param int $pn_project_id
 	 * @param array $pa_options Options are:
 	 *		status = limits returned requests to a given status. Possible values are these constants (not strings!): __MS_DOWNLOAD_REQUEST_NEW__, __MS_DOWNLOAD_REQUEST_APPROVED__, __MS_DOWNLOAD_REQUEST_DENIED__, __MS_DOWNLOAD_REQUEST_ALL__
 	 */
 	public function getDownloadRequestsForProject($pn_project_id=null, $pa_options=null) {
 		if(!($vn_project_id = $pn_project_id)) { 
 			if (!($vn_project_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}

		$vs_status_sql = '';
 		if (isset($pa_options['status'])) {
 			switch((int)$pa_options['status']) {
 				case __MS_DOWNLOAD_REQUEST_NEW__:
 					$vs_status_sql = " AND (r.status = 0)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_APPROVED__:
 					$vs_status_sql = " AND (r.status = 1)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_DENIED__:
 					$vs_status_sql = " AND (r.status = 2)";
 					break;
 			}
 		}
 		
 		$o_db = $this->getDb();
 		
 		$qr_res = $o_db->query("
 			SELECT r.*, u.*
 			FROM ms_media_download_requests r
 			INNER JOIN ms_media AS m ON m.media_id = r.media_id 
 			INNER JOIN ca_users AS u ON u.user_id = r.user_id
 			WHERE 
 				m.project_id = ? {$vs_status_sql}
 			ORDER BY r.requested_on DESC
 		", array((int)$vn_project_id));
 		return $qr_res->getAllRows();
 	}
	# ----------------------------------------
 	/**
 	 * pn_user_id -> user to approve requests - NOT the user who made the request
 	 * @param array $pa_options Options are:
 	 *		status = limits returned requests to a given status. Possible values are these constants (not strings!): __MS_DOWNLOAD_REQUEST_NEW__, __MS_DOWNLOAD_REQUEST_APPROVED__, __MS_DOWNLOAD_REQUEST_DENIED__, __MS_DOWNLOAD_REQUEST_ALL__
 	 */
 	public function getDownloadRequestsForUser($pn_user_id=null, $pa_options=null) {
 		if (!$pn_user_id) { return null; }
		# --- get projects for user
		$va_projects = $this->getProjectsForMember($pn_user_id);
		$va_project_ids = array();
		if(is_array($va_projects) && sizeof($va_projects)){
			foreach($va_projects as $va_project){
				if($va_project["membership_type"] == 1){
					$va_project_ids[] = $va_project["project_id"];
				}
			}
			if(sizeof($va_project_ids) == 0){
				return null;
			}
		}else{
			return null;
		}
		$vs_status_sql = '';
 		if (isset($pa_options['status'])) {
 			switch((int)$pa_options['status']) {
 				case __MS_DOWNLOAD_REQUEST_NEW__:
 					$vs_status_sql = " AND (r.status = 0)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_APPROVED__:
 					$vs_status_sql = " AND (r.status = 1)";
 					break;
 				case __MS_DOWNLOAD_REQUEST_DENIED__:
 					$vs_status_sql = " AND (r.status = 2)";
 					break;
 			}
 		}
 		
 		$o_db = $this->getDb();
 		
 		$qr_res = $o_db->query("
 			SELECT r.*, u.*, p.project_id, p.name
 			FROM ms_media_download_requests r
 			INNER JOIN ms_media AS m ON m.media_id = r.media_id 
 			INNER JOIN ca_users AS u ON u.user_id = r.user_id
 			INNER JOIN ms_projects AS p ON m.project_id = p.project_id 
 			WHERE
 				((m.reviewer_id IS NULL) OR 
 				(m.reviewer_id = {$pn_user_id})) AND 
 				(m.project_id IN (".join(", ", $va_project_ids).")) {$vs_status_sql}
 			ORDER BY m.project_id, r.requested_on DESC
 		");
 		return $qr_res->getAllRows();
 	}
 	# ----------------------------------------
}
?>