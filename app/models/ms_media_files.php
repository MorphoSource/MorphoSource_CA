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
				"DISPLAY_WIDTH" => 50, "DISPLAY_HEIGHT" => 1,
				"IS_NULL" => false, 
				"DEFAULT" => "",
				
				"MEDIA_PROCESSING_SETTING" => 'media_files',
				
				"LABEL" => "Select media file", 
				"DESCRIPTION" => "Use the button below to select a media file on your harddrive to upload."
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
	protected $SEARCH_CLASSNAME = ;
	protected $SEARCH_RESULT_CLASSNAME = ;
	
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
}
?>