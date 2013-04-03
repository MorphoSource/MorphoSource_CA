<?php
/** ---------------------------------------------------------------------
 * app/models/ms_projects.php
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
	require_once(__CA_MODELS_DIR__."/ms_project_users.php");

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
		'published_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Published on'), 'DESCRIPTION' => _t('Project publication date.'),
		),
		'publication_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Publication status'), 'DESCRIPTION' => _t('Publication status'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Unpublished') 	=> 0,
					_t('Pubished')	=> 1
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
				(pu.user_id = ?) AND (pu.active = 1)
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
	function numMedia($pn_project_id = "") {
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
	function numSpecimens($pn_project_id = "") {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_specimens s
			WHERE s.project_id = ?
		", $pn_project_id);
		
		$vn_num_specimens = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_specimens = $qr->get("c");
		}
		return $vn_num_specimens;
	}
	# ----------------------------------------
	function numCitations($pn_project_id = "") {
		if(!$pn_project_id){
			$pn_project_id = $this->getPrimaryKey();
		}
		if (!$pn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT count(*) c
			FROM ms_bibliography b
			WHERE b.project_id = ?
		", $pn_project_id);
		
		$vn_num_citations = 0;
		if($qr->numRows()){
			$qr->nextRow();
			$vn_num_citations = $qr->get("c");
		}
		return $vn_num_citations;
	}
	# ----------------------------------------
	function getProjectMedia() {
		$vn_project_id = $this->getPrimaryKey();
		if (!$vn_project_id) { return null; }
		
		$o_db = $this->getDb();
		$qr = $o_db->query("
			SELECT m.media_id, m.media, m.specimen_id
			FROM ms_media m
			WHERE m.project_id = ?
			ORDER BY m.media_id
		", $vn_project_id);

		return $qr;
	}
	# ----------------------------------------
}
?>