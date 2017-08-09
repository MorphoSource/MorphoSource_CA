<?php
/** ---------------------------------------------------------------------
 * app/models/ms_media_files.php
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
require_once(__CA_MODELS_DIR__."/ms_media_files_multifiles.php");

BaseModel::$s_ca_models_definitions['ms_media_files'] = array(
 	'NAME_SINGULAR' 	=> _t('media file'),
 	'NAME_PLURAL' 		=> _t('media files'),
 	'FIELDS' 			=> array(
 		'media_file_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Media id'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this media')
		),
		'derived_from_media_file_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => 'Derived from', 'DESCRIPTION' => 'Media file id of file media was derived from'
		),
		'title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 60, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Title'), 'DESCRIPTION' => _t('Optional display title for image.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'media_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Media id', 'DESCRIPTION' => 'Media id'
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
				"DISPLAY_WIDTH" => 30, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => false, 
				"DEFAULT" => "",
				
				"MEDIA_PROCESSING_SETTING" => 'media_files',
				
				"LABEL" => "Select media file", 
				"DESCRIPTION" => "Use the button below to select a media file on your harddrive to upload."
		),
		'element' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 25, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => TRUE, 
				'DEFAULT' => '',
				'LABEL' => _t('Description/Element'), 'DESCRIPTION' => _t('Element of specimen, if different from what was entered in the general information for the media.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'side' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => "UNKNOWN",
				'LABEL' => _t('Side'), 'DESCRIPTION' => _t('Side of specimen depicted by media, if different from what was entered in the general information for the media'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Not Applicable" => "NA",
					"Unknown" => "UNKNOWN",
					"Left" => "LEFT",
					"Right" => "RIGHT",
					"Midline" => "MIDLINE"
				)
		),
		'use_for_preview' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_CHECKBOXES, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => 0,
				'BOUNDS_VALUE' => array(0,1),
				'LABEL' => _t('Use this file as preview for entire media record?'), 'DESCRIPTION' => _t('Use this file as preview for entire media record?')
		),
		'file_type' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => 0,
				'LABEL' => _t('File type'), 'DESCRIPTION' => _t('File type'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Raw file of group" => 1,
					"derivative file" => 2
				)
		),
		'distance_units' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 150, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => 0,
				'LABEL' => _t('Distance units of coordinate system for mesh files'), 'DESCRIPTION' => _t('Distance units of coordinate system for mesh files'),
				"BOUNDS_CHOICE_LIST"=> array(
					"microns" => 6,
					"millimeters" => 1,
					"centimeters" => 2,
					"meters" => 3,
					"inches" => 4,
					"other" => 5
				)
		),
		'max_distance_x' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 65, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Maximum distance in X direction in millimeters between points of mesh coordinates'), 'DESCRIPTION' => _t('Maximum distance in X direction in millimeters between points of mesh coordinates wedge.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'max_distance_3d' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 65, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Maximum distance in 3-dimensional space in millimeters between two most distant points on mesh surface'), 'DESCRIPTION' => _t('Maximum distance in 3-dimensional space in millimeters between two most distant points on mesh surface'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Notes'), 'DESCRIPTION' => _t('Notes about the media file.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'media_metadata' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 5,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Metadata from media file'), 'DESCRIPTION' => _t('Metadata from media file.')
		),
		'published' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 70, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => 0,
				'LABEL' => _t('Publication status (leave empty to inherit publication status from the media group)'), 'DESCRIPTION' => _t('Release to public search?'),
				"BOUNDS_CHOICE_LIST"=> array(
					"Not published / Not available in public search" => 0,
					"Published / available in public search and for download" => 1,
					"Published / available in public search / users must request download permission" => 2
				)
		),
		'doi' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'DISPLAY_WIDTH' => 63, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('DOI'), 'DESCRIPTION' => _t('DOI for media.')
		),
		'published_on' => array(
				'FIELD_TYPE' => FT_DATETIME, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Media published on'), 'DESCRIPTION' => _t('Date/time the Media was published.'),
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
		),
		'batch_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_OMIT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Batch Import Status'), 'DESCRIPTION' => _t('used to find newly imported records for review'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('Batch uploaded') => 1
				)
		)
 	)
);

class ms_media_files extends BaseModel {
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
	protected $TABLE = 'ms_media_files';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'media_file_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('media_file_id');

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
	# Search
	# ------------------------------------------------------
	protected $SEARCH_CLASSNAME = 'MediaFileSearch';
	protected $SEARCH_RESULT_CLASSNAME = 'MediaFileSearchResult';
	
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
				$o_db= $this->getDb();
				$q_project = $o_db->query("SELECT project_id from ms_media where media_id = ?", $this->get('media_id'));
				$q_project->nextRow();
				$t_project = new ms_projects($q_project->get('project_id'));
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
				
				$o_db= $this->getDb();
				$q_project = $o_db->query("SELECT project_id from ms_media where media_id = ?", $this->get('media_id'));
				$q_project->nextRow();
				$t_project = new ms_projects($q_project->get('project_id'));
				
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
		$o_db= $this->getDb();
		$q_project = $o_db->query("SELECT project_id from ms_media where media_id = ?", $this->get('media_id'));
		$q_project->nextRow();
		$vn_project_id = $q_project->get('project_id');
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

	# ------------------------------------------------------
 	# Multifiles
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function addFile($ps_filepath, $ps_resource_path='/', $pb_allow_duplicates=true) {
 		if(!$this->getPrimaryKey()) { return null; }
 		if (!trim($ps_resource_path)) { $ps_resource_path = '/'; }
 		
 		$t_multifile = new ms_media_files_multifiles();
 		if (!$pb_allow_duplicates) {
 			if ($t_multifile->load(array('resource_path' => $ps_resource_path, 'media_file_id' => $this->getPrimaryKey()))) {
 				return null;
 			}
 		}
 		$t_multifile->setMode(ACCESS_WRITE);
 		$t_multifile->set('media_file_id', $this->getPrimaryKey());
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
 		
 		$t_multifile = new ms_media_files_multifiles($pn_multifile_id);
 		
 		if ($t_multifile->get('media_file_id') == $this->getPrimaryKey()) {
 			$t_multifile->setMode(ACCESS_WRITE);
 			$t_multifile->delete();
 			
			if ($t_multifile->numErrors()) {
				$this->errors = array_merge($this->errors, $t_multifile->errors);
				return false;
			}
		} else {
			$this->postError(2720, _t('File is not part of this media'), 'ms_media_files->removeFile()');
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
 	 * @param int $pn_media_file_id The media_file_id of the media to return files for. If omitted the currently loaded media is used. If no media_file_id is specified and no row is loaded null will be returned.
 	 * @param int $pn_start The index of the first file to return. Files are numbered from zero. If omitted the first file found is returned.
 	 * @param int $pn_num_files The maximum number of files to return. If omitted all files are returned.
 	 * @param array $pa_versions A list of file versions to return. If omitted only the "preview" version is returned.
 	 * @return array A list of files attached to the media. If no files are associated an empty array is returned.
 	 */
 	public function getFileList($pn_media_file_id=null, $pn_start=null, $pn_num_files=null, $pa_versions=null) {
 		if(!($vn_media_file_id = $pn_media_file_id)) { 
 			if (!($vn_media_file_id = $this->getPrimaryKey())) {
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
 			FROM ms_media_files_multifiles
 			WHERE
 				media_file_id = ?
 			{$vs_limit_sql}
 		", (int)$vn_media_file_id);
 		
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
 	
 		$t_multifile = new ms_media_files_multifiles($pn_multifile_id);
 		
 		if ($t_multifile->get('media_file_id') == $this->getPrimaryKey()) {
 			return $t_multifile;
 		}
 		return null;
 	}
 	# ------------------------------------------------------
 	/**
 	 *
 	 */
 	public function numFiles($pn_media_file_id=null) { 		
 		if(!($vn_media_file_id = $pn_media_file_id)) { 
 			if (!($vn_media_file_id = $this->getPrimaryKey())) {
 				return null; 
 			}
 		}
 		
 		$o_db= $this->getDb();
 		$qr_res = $o_db->query("
 			SELECT count(*) c
 			FROM ms_media_files_multifiles
 			WHERE
 				media_file_id = ?
 		", (int)$vn_media_file_id);
 		
 		if($qr_res->nextRow()) {
 			return intval($qr_res->get('c'));
 		}
 		return 0;
 	}
	# ------------------------------------------------------
	/* get content for text file to download with media files
	*  $pa_media_file_ids = array of media file ids to get MD for
	*  $t_specimen = ms_specimens object
	*/
	public function mediaMdText($pa_media_file_ids, $t_specimen = null) {
		if(sizeof($pa_media_file_ids)){
			$o_db = $this->getDb();
			if(!$t_specimen){
				$t_specimen = new ms_specimens();
			}
			$t_media = new ms_media();
			$t_media_file = new ms_media_files();
			$q_media_files = $o_db->query("
				SELECT mf.media_file_id, mf.title file_title, mf.notes file_notes, mf.side file_side, mf.element file_element, mf.media file_media, mf.doi, mf.file_type, mf.distance_units, mf.max_distance_x, mf.max_distance_3d, m.*, f.name facility, i.name institution, s.locality_description, s.relative_age, s.absolute_age, scan.name scanner
				FROM ms_media_files mf 
				INNER JOIN ms_media as m ON mf.media_id = m.media_id
				LEFT JOIN ms_specimens as s ON m.specimen_id = s.specimen_id
				LEFT JOIN ms_facilities as f ON f.facility_id = m.facility_id
				LEFT JOIN ms_institutions as i ON s.institution_id = i.institution_id
				LEFT JOIN ms_scanners as scan ON scan.scanner_id = m.scanner_id
				WHERE mf.media_file_id IN (".join(", ", $pa_media_file_ids).")");
			$va_all_md = array();
			if($q_media_files->numRows()){
				$va_specimen_info = array();
				# --- header row
				$va_header = array(
									"media",
									"downloaded file name",
									"doi",
									"file type",
									"file size",
									"title",
									"notes",
									"specimen",
									"specimen taxonomy",
									"insitution",
									"description/element",
									"side",
									"locality",
									"relative age",
									"absolute age",
									"facility",
									"scanner type",
									"x res",
									"y res",
									"z res",
									"voltage",
									"amperage",
									"watts",
									"projections",
									"frame averaging",
									"wedge",
									"shading correction",
									"flux normalization",
									"geometric callibration",
									"calibration description",
									"Distance units of coordinate system for mesh files",
									"Max X distance btw points of mesh coordinates (mm)",
									"Max 3D distance btw two most distant points on mesh surface (mm)",
									"technicians",
									"grant support",
									"copyright holder",
									"copyright license",
									"citation instruction statement (to be copy-pasted into acknolwedgements)"
								);
				#$va_all_md[] = join(",", $va_header);
				#file_type, mf.distance_units, mf.max_distance_x, mf.max_distance_3d
				$va_all_md[] = $va_header;
				while($q_media_files->nextRow()){
					$va_media_md = array();
					$vs_specimen_taxonomy = $vs_specimen_name = "";
					if(!$va_specimen_info[$q_media_files->get("specimen_id")]){
						if($q_media_files->get("specimen_id")){
							$vs_specimen_name = $t_specimen->getSpecimenNumber($q_media_files->get("specimen_id"));
							$va_specimen_info[$q_media_files->get("specimen_id")]["specimen_name"] = $vs_specimen_name;
							$vs_specimen_taxonomy = join(", ", $t_specimen->getSpecimenTaxonomy($q_media_files->get("specimen_id")));
							$va_specimen_info[$q_media_files->get("specimen_id")]["specimen_taxonomy"] = join(", ", $t_specimen->getSpecimenTaxonomy($q_media_files->get("specimen_id")));
						}
					}else{
						$vs_specimen_name = $va_specimen_info[$q_media_files->get("specimen_id")]["specimen_name"];
						$vs_specimen_taxonomy = $va_specimen_info[$q_media_files->get("specimen_id")]["specimen_taxonomy"];
					}
					$va_versions = $q_media_files->getMediaVersions('file_media');
					$va_properties = $q_media_files->getMediaInfo('file_media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
					
					$va_media_md[] = "M".$q_media_files->get("media_id")."-".$q_media_files->get("media_file_id");
					$va_media_md[] = $vs_specimen_name.'_M'.$q_media_files->get("media_id").'-'.$q_media_files->get("media_file_id").'.'.$va_properties['EXTENSION'];
				
					$va_tmp = preg_split("![ ]*\|[ ]*!", $q_media_files->get('doi'));
					$va_media_md[] = trim($va_tmp[0]);
					$vs_file_type = $t_media_file->getChoiceListValue("file_type", $q_media_files->get("file_type"));
					$va_media_md[] = (($vs_file_type) ? $vs_file_type."; " : "").$va_properties['MIMETYPE'];
					$va_media_md[] = caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize']);
					$va_media_md[] = $q_media_files->get("file_title");
					$va_media_md[] = $q_media_files->get("file_notes");
					$va_media_md[] = $vs_specimen_name;
					$va_media_md[] = $vs_specimen_taxonomy;
					$va_media_md[] = $q_media_files->get("institution");
					if($q_media_files->get("file_element")){
						$va_media_md[] = preg_replace("/\r|\n/", " ", $q_media_files->get("file_element"));
					}else{
						$va_media_md[] = preg_replace("/\r|\n/", " ", $q_media_files->get("element"));
					}
					if($q_media_files->get("file_side")){
						$va_media_md[] = $t_media_file->getChoiceListValue("side", $q_media_files->get("file_side"));
					}else{
						$va_media_md[] = $t_media_file->getChoiceListValue("side", $q_media_files->get("side"));
					}
					$va_media_md[] = $q_media_files->get("locality_description");
					$va_media_md[] = $q_media_files->get("relative_age");
					$va_media_md[] = $q_media_files->get("absolute_age");
					$va_media_md[] = $q_media_files->get("facility");
					$va_media_md[] = $q_media_files->get("scanner");
					$va_media_md[] = $q_media_files->get("scanner_x_resolution")." mm";
					$va_media_md[] = $q_media_files->get("scanner_y_resolution")." mm";
					$va_media_md[] = $q_media_files->get("scanner_z_resolution")." mm";
					$va_media_md[] = $q_media_files->get("scanner_voltage")." kv";
					$va_media_md[] = $q_media_files->get("scanner_amperage")." �a";
					$va_media_md[] = $q_media_files->get("scanner_watts")." W";
					$va_media_md[] = $q_media_files->get("scanner_projections");
					$va_media_md[] = $q_media_files->get("scanner_frame_averaging");
					if($q_media_files->get("scanner_wedge")){
						$va_media_md[] = "Yes";
					}else{
						$va_media_md[] = "No";
					}
					if($q_media_files->get("scanner_calibration_shading_correction")){
						$va_media_md[] = "Yes";
					}else{
						$va_media_md[] = "No";
					}
					if($q_media_files->get("scanner_calibration_flux_normalization")){
						$va_media_md[] = "Yes";
					}else{
						$va_media_md[] = "No";
					}
					$va_media_md[] = $q_media_files->get("scanner_calibration_geometric_calibration");
					$va_media_md[] = $q_media_files->get("scanner_calibration_description");
					$va_media_md[] = $t_media_file->getChoiceListValue("distance_units", $q_media_files->get("distance_units"));
					$va_media_md[] = $q_media_files->get("max_distance_x");
					$va_media_md[] = $q_media_files->get("max_distance_3d");
					
					$va_media_md[] = $q_media_files->get("scanner_technicians");
					$va_media_md[] = $q_media_files->get("grant_support");
					$va_media_md[] = $q_media_files->get("copyright_info");
					$va_media_md[] = $t_media->getChoiceListValue("copyright_license", $q_media_files->getChoiceListValue("copyright_license"));
					if($q_media_files->get("media_citation_instruction1")){
						$va_media_md[] = "Citation: ".$t_media->getMediaCitationInstructionsFromFields(array("media_citation_instruction1" => $q_media_files->get("media_citation_instruction1"), "media_citation_instruction2" => $q_media_files->get("media_citation_instruction2"), "media_citation_instruction3" => $q_media_files->get("media_citation_instruction3")));
					}else{
						$va_media_md[] = "";
					}
					
					#$va_all_md[] = join(",", $va_media_md);
					$va_all_md[] = $va_media_md;
				}
				#return join($va_all_md, "\n")."\n\nThis text file is a selective, not an exhaustive distillation of the metadata available for your downloaded files. If you require more information, it may still be available within MorphoSource and you should seek it there before contacting the data author or making the assumption that it does not exist.\n\n";
				$va_all_md[] = array("This text file is a selective, not an exhaustive distillation of the metadata available for your downloaded files. If you require more information, it may still be available within MorphoSource and you should seek it there before contacting the data author or making the assumption that it does not exist.");
				return $va_all_md;
			}
		}else{
			return array();
		}
	}
	
	# ------------------------------------------------------
}
