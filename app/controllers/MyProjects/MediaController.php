<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/MediaController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2015 Whirl-i-Gig
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
 * ----------------------------------------------------------------------
 */
	require_once(__CA_APP_DIR__."/helpers/htmlFormHelpers.php");
 	require_once(__CA_LIB_DIR__."/core/Error.php");
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_movement_requests.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/core/Parsers/ZipStream.php');
 	require_once(__CA_LIB_DIR__.'/ms/DOI.php');
 	require_once(__CA_LIB_DIR__.'/ms/ARK.php');
 	require_once(__CA_MODELS_DIR__."/ms_media_shares.php");
 	
 	class MediaController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			
			protected $opo_item;
			protected $opn_item_id;
			protected $ops_item_name;
			protected $ops_name_singular;
			protected $ops_name_plural;
			protected $ops_primary_key;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			JavascriptLoadManager::register("panel");
 			JavascriptLoadManager::register("3dmodels");
 			JavascriptLoadManager::register("formrepeater");
 			JavascriptLoadManager::register("jfu");
 			
 			
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access this form", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_project = new ms_projects();
			# --- is there a project already selected, are we selecting a project
			$vn_select_project_id = $this->request->getParameter('select_project_id', pInteger);
			if($vn_select_project_id){
				# --- select project
				msSelectProject($this, $this->request);
			}
 			if($this->request->session->getVar('current_project_id') && ($this->request->user->canDoAction("is_administrator") || $this->opo_project->isFullAccessMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id')))){
 				$this->opn_project_id = $this->request->session->getVar('current_project_id');
				$this->opo_project->load($this->opn_project_id);
				$this->ops_project_name = $this->opo_project->get("name");
				$this->view->setvar("project_id", $this->opn_project_id);
				$this->view->setvar("project_name", $this->ops_project_name);
 			}else{
 				$this->notification->addNotification("Please select a project", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));
 			}
			$this->view->setvar("project", $this->opo_project);
			
			# --- load the media object
			$this->opo_item = new ms_media();
			$this->opn_item_id = $this->request->getParameter('media_id', pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the record is part of the current project or a project the user has access to
				$t_project = new ms_projects();
				if(($this->opo_item->get("project_id") != $this->opn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id")))){
					$this->notification->addNotification("The media record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));				
				}
			}
			$this->view->setvar("item_id", $this->opn_item_id);
			$this->view->setvar("item", $this->opo_item);
			$this->ops_name_singular = $this->opo_item->getProperty("NAME_SINGULAR");
			$this->ops_name_plural = $this->opo_item->getProperty("NAME_PLURAL");
			$this->ops_primary_key = $this->opo_item->getProperty("PRIMARY_KEY");
			$this->view->setvar("name_singular", $this->ops_name_singular);
			$this->view->setvar("name_plural", $this->ops_name_plural);
			$this->view->setvar("primary_key", $this->ops_primary_key);
			$this->view->setvar($this->ops_primary_key, $this->opo_item->get($this->ops_primary_key));
			$this->ops_item_name = "";
			$va_list_fields = $this->opo_item->getProperty("LIST_FIELDS");
			$this->view->setvar("list_fields", $va_list_fields);
			$i = 0;
			foreach($va_list_fields as $vs_field){
				$this->ops_item_name .= $this->opo_item->get($vs_field);
				$i++;
				if(($i < sizeof($va_list_fields)) && ($this->opo_item->get($vs_field))){
					$this->ops_item_name .= ", ";
				}
			}
			$this->view->setvar("item_name", $this->ops_item_name);
			$t_media_files = new ms_media_files();
 			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
 			if($pn_media_file_id && ($this->request->getParameter('formaction', pString) == "editMediaFile")){
 				$t_media_files->load($pn_media_file_id);
 			}
 			$this->view->setVar("t_media_file", $t_media_files);
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$q_listings = $this->opo_project->getProjectMedia();
			$this->view->setvar("listings", $q_listings);
			$this->render('Media/list_html.php');
 		}
 		# -------------------------------------------------------
 		public function reviewPublicationSettings() {
			$q_listings = $this->opo_project->getProjectMedia(true);
			$this->view->setvar("listings", $q_listings);
			$this->render('Media/review_publication_status_html.php');
 		}
 		# -------------------------------------------------------
 		public function batchPublicationSave() {
			$va_media_ids = $this->request->getParameter('media_ids', pArray);
			$va_media_file_ids = $this->request->getParameter('media_file_ids', pArray);
			if(!is_array($va_media_ids) && !is_array($va_media_file_ids)){
				$this->notification->addNotification("You did not select any media groups or files", __NOTIFICATION_TYPE_ERROR__);
			}else{
				$vn_pub = $this->request->getParameter('published', pInteger);
				$vn_reviewer = null;
				if (($vn_pub) == 2 && ($this->request->getParameter('reviewer_id', pInteger))) {
					$vn_reviewer = $this->request->getParameter('reviewer_id', pInteger);
				}
				$o_db = new Db();
				if(is_array($va_media_file_ids) && sizeof($va_media_file_ids)){
					$o_db->query("UPDATE ms_media_files SET published = ? WHERE media_file_id IN (".join(", ", $va_media_file_ids).")", $vn_pub);
					if (($vn_pub == 1) || ($vn_pub == 2)) {
						foreach ($va_media_file_ids as $vn_media_file_id) {
							$t_media_file = 
								new ms_media_files($vn_media_file_id);
							# Publish ARK
							$t_user = new ca_users(
								($t_media_file->get('user_id') ? 
								$t_media_file->get('user_id') : 
								$this->opo_project->get('user_id')));
							$va_ark = $t_media_file->publishARK(
								$vs_user_fname = $t_user->get('fname'),
								$vs_user_lname = $t_user->get('lname')
							);
						}
					}

					if ($vn_pub == 0) {
						foreach ($va_media_file_ids as $vn_media_file_id) {
							$t_media_file = 
								new ms_media_files($vn_media_file_id);
							# Unpublish ARK if published
							$va_ark = $t_media_file->unpublishARK();
						}
					}
				}

				if(is_array($va_media_ids) && sizeof($va_media_ids)){
					$o_db->query("UPDATE ms_media 
						SET published = ?,
						reviewer_id = ? 
						WHERE media_id IN (".join(", ", $va_media_ids).") 
						and project_id = ?", $vn_pub, $vn_reviewer, $this->opn_project_id);
				
					if (($vn_pub == 1) || ($vn_pub == 2)) {
						foreach ($va_media_ids as $vn_media_id) {
							$t_media = new ms_media($vn_media_id);
							$va_media_files = $t_media->getMediaFiles();
							if (sizeof($va_media_files)) {
								foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
									if (is_null($t_media_file->get("published")) 
										|| ($t_media_file->get("published") === ""))
									{
										# Publish ARKs
										$t_user = new ca_users(
											($t_media_file->get('user_id') ? 
											$t_media_file->get('user_id') : 
											$this->opo_project->get('user_id')));
										$va_ark = $t_media_file->publishARK(
											$vs_user_fname = $t_user->get('fname'),
											$vs_user_lname = $t_user->get('lname')
										);
									}
								}
							}
						}
					}

					if ($vn_pub == 0) {
						foreach ($va_media_ids as $vn_media_id) {
							$t_media = new ms_media($vn_media_id);
							$va_media_files = $t_media->getMediaFiles();
							if (sizeof($va_media_files)) {
								foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
									if (is_null($t_media_file->get("published")) 
										|| ($t_media_file->get("published") === ""))
									{
										# Unpublish ARKs
										$va_ark = $t_media_file->unpublishARK();
									}
								}
							}
						}
					}
				}
				
				$this->notification->addNotification("Updated publication status", __NOTIFICATION_TYPE_ERROR__);
			}
			$this->reviewPublicationSettings();
 		}
 		# -------------------------------------------------------
 		public function form() {
 			# --- clone_id is the media record we are "cloning" when making a *new* media record.
 			if(!$this->opo_item->get("media_id")){
 				# --- derived_from_media_id creates a link to a record the new media group was derived from 
 				# --- lookup_derived_from_media_id is passed through a look up in the media group form
 				# --- we use the clone function to clone fields from the parent media group
 				$pn_lookup_derived_from_media_id = $this->request->getParameter('lookup_derived_from_media_id', pInteger);
 				if($pn_lookup_derived_from_media_id){
 					$pn_clone_id = $pn_lookup_derived_from_media_id;
 				}else{
 					$pn_clone_id = $this->request->getParameter('clone_id', pInteger);
 				}
 				$t_clone = new ms_media($pn_clone_id);
 				
 				$va_clone_fields = array("specimen_id", "facility_id", "notes", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_acquisition_time", "scanner_wedge", "scanner_calibration_check", "scanner_calibration_description", "scanner_technicians", "element", "title", "side", "scanner_id", "grant_support", "media_citation_instruction1", "media_citation_instruction2", "media_citation_instruction3");
 				foreach($va_clone_fields as $vs_f){
					$this->opo_item->set($vs_f, $t_clone->get($vs_f));
				}
				$this->request->setParameter("specimen_id", $t_clone->get('specimen_id'));
 				if($pn_lookup_derived_from_media_id){
 					$this->opo_item->set("derived_from_media_id", $pn_lookup_derived_from_media_id);
 				}
 				$this->view->setvar("item", $this->opo_item);
			}
 			
 			//# --- pass the facility name for preloading lookup if available
 			//if($this->opo_item->get("facility_id")){
 			//	$t_facility = new ms_facilities($this->opo_item->get("facility_id"));
 			//	$this->view->setVar("facility_name", $t_facility->get("name").(($t_facility->get("name") && $t_facility->get("institution")) ? ", " : "").$t_facility->get("institution"));
 			//}
 			
 			// Pass list of scanners by facility_id
 			$this->view->setVar('scannerListByFacilityID', ms_facilities::scannerListByFacilityID());
			$this->render('Media/form_html.php');
 		}
 		# -------------------------------------------------------
 		public function save() {
			# get names of form fields
			$va_fields = $this->opo_item->getFormFields();
			$va_errors = array();
			$vs_pub_init = $this->opo_item->get("published");
			# loop through fields
#print "<pre>";
#print sizeof($this->request->getParameter('media', pArray));
#print_r($this->request->getParameter('media', pArray));	
#print_r($_FILES);
#print "</pre>";
#exit;
			$va_update_opts = array();
			while(list($vs_f,$va_attr) = each($va_fields)) {		
				switch($vs_f) {
// 					# -----------------------------------------------
// 					case 'media':
// 						$vs_media_source = $this->request->getParameter('mediaSource', pString);
// 						if (
// 							($vs_media_source == 'server')
// 							&&
// 							($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
// 							&&
// 							($vs_upload_base_directory = $this->opo_item->getAppConfig()->get('upload_base_directory'))
// 							&&
// 							(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
// 						) {
// 							$vs_media_path = str_replace("/..", "", escapeshellcmd($this->request->getParameter('mediaServerPath', pString)));
// 							if ($vs_media_path && file_exists($vs_user_upload_directory.$vs_media_path)) {
// 								$this->opo_item->set('media', $vs_user_upload_directory.$vs_media_path, array('original_filename' => $vs_media_path));
// 							}
// 						} else {
// 							if($_FILES['media']['tmp_name']){
// 								$this->opo_item->set('media', $_FILES['media']['tmp_name'], array('original_filename' => $_FILES['media']['name']));
// 							}elseif(!$this->opo_item->get('media')){
// 								$va_errors[$vs_f] = "Please upload a media file";
// 							}
// 						}
// 						$this->request->user->setVar('lastMediaSource', $vs_media_source);
// 						break;
					# -----------------------------------------------
					case 'project_id':
						if(!$this->opo_item->get("project_id")){
							$this->opo_item->set($vs_f,$this->opn_project_id);
						}
						break;
					# -----------------------------------------------
					case 'user_id':
						if(!$this->opo_item->get("user_id")){
							$this->opo_item->set($vs_f,$this->request->user->get("user_id"));
						}
						break;
					# -----------------------------------------------
					case 'published':
						if($_REQUEST['published'] != $this->opo_item->get('published')){
							if(($_REQUEST['published'] > 0)){
								# --- publishing media so set published on date
								$this->opo_item->set("published_on",'now');
							}else{
								# --- unpublishing media so clear published_on date
								$this->opo_item->set("published_on",null);
							}
						}
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
					# -----------------------------------------------
					case 'facility_id':
						if($_REQUEST["facility_id"]){
							$this->opo_item->set("facility_id",$_REQUEST["facility_id"]);
						}elseif($_REQUEST["name"]){
							# --- check if facility info was entered in AJAX form
							$t_facility = new ms_facilities();
							$va_facility_fields = $t_facility->getFormFields();
							$va_facility_errors = array();
							while(list($vs_facility_f,$va_facility_attr) = each($va_facility_fields)) {
								switch($vs_facility_f) {
									# -----------------------------------------------
									case 'project_id':
										if(!$t_facility->get("project_id")){
											$t_facility->set($vs_facility_f,$this->opn_project_id);
										}
										break;
									# -----------------------------------------------
									case 'user_id':
										if(!$t_facility->get("user_id")){
											$t_facility->set($vs_facility_f,$this->request->user->get("user_id"));
										}
										break;
									# -----------------------------------------------
									default:
										$t_facility->set($vs_facility_f,$_REQUEST[$vs_facility_f]); # set field values
										break;
									# -----------------------------------------------
								}
								if ($t_facility->numErrors() > 0) {
									foreach ($t_facility->getErrors() as $vs_e) {
										$va_facility_errors[$vs_facility_f] = $vs_e;
									}
								}
							}
							if (sizeof($va_facility_errors) == 0) {
								$t_facility->setMode(ACCESS_WRITE);
								$t_facility->insert();
							}
							if ($t_facility->numErrors()) {
								foreach ($t_facility->getErrors() as $vs_e) {  
									$va_errors["general"] = $vs_e;
								}
							}else{
								$this->opo_item->set("facility_id",$t_facility->get("facility_id"));
							}
						}else{
							$va_errors[$vs_f] = "Please enter the facility the media was scanned at";
						}
						break;
					# -----------------------------------------------
					default:
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
					# -----------------------------------------------
				}
				if ($this->opo_item->numErrors() > 0) {
					foreach ($this->opo_item->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}
		
			if (sizeof($va_errors) == 0) {
				# do insert or update
				$vb_was_insert = false;
				$this->opo_item->setMode(ACCESS_WRITE);
				if ($this->opo_item->get($this->ops_primary_key)){
					$this->opo_item->update($va_update_opts);
				} else {
					$this->opo_item->insert();
					$vb_was_insert = true;
				}
	
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					# was media group published, how about pre-existing files
					$vs_pub_final = $this->opo_item->get("published");
					if ( ( ($vs_pub_final == 1) || ($vs_pub_final == 2) ) && 
						!( ($vs_pub_init == 1) || ($vs_pub_init == 2) ) ) {
						$va_media_files = $this->opo_item->getMediaFiles();
						if (sizeof($va_media_files)) {
							foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
								if (is_null($t_media_file->get("published")) 
									|| ($t_media_file->get("published") === ""))
								{
									# Publish ARK
									$t_user = new ca_users(($t_media_file->get('user_id') ? 
										$t_media_file->get('user_id') : 
										$this->opo_project->get('user_id')));
									$va_ark = $t_media_file->publishARK(
										$vs_user_fname = $t_user->get('fname'),
										$vs_user_lname = $t_user->get('lname')
									);
									if (!$va_ark["success"]) {
										$va_errors["general"] .= $va_ark["error"];
									}
								}
							}
						}
					}

					# was media group unpublished, how about pre-existing files
					if ( ($vs_pub_final == 0 ) 
						&& ( ($vs_pub_init == 1) || ($vs_pub_init == 2) ) ) 
					{
						$va_media_files = $this->opo_item->getMediaFiles();
						if (sizeof($va_media_files)) {
							foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
								if (is_null($t_media_file->get("published")) 
									|| ($t_media_file->get("published") === ""))
								{
									# Unpublish ARK
									$va_ark = $t_media_file->unpublishARK();
									if (!$va_ark["success"]) {
										$va_errors["general"] .= $va_ark["error"];
									}
								}
							}
						}
						
					}

					# --- are there media files to add?
					if(is_array($_FILES) && sizeof($_FILES)){
						$va_media_files_info = $this->request->getParameter('media', pArray);
						$vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory');
						$vs_upload_base_directory = $this->opo_item->getAppConfig()->get('upload_base_directory');
						foreach($va_media_files_info as $vn_key => $va_media_file_info){
							$va_errors_file = array();

//smctodo: overriding temp file and path
//foreach(array("jfu_media_file_name", "jfu_media_file_path") as $vs_f){
//}
                       
$jfu_media_file_name = 'notset';
$jfu_media_file_path = 'notset';
if (isset($_REQUEST['jfu_media_file_name'][$vn_key])) 
    $jfu_media_file_name = $_REQUEST['jfu_media_file_name'][$vn_key];
if (isset($_REQUEST['jfu_media_file_path'][$vn_key]))
    $jfu_media_file_path = $_REQUEST['jfu_media_file_path'][$vn_key];

$_FILES['media']['tmp_name'][$vn_key] = "/nfs/images/tmp/jfu/".$jfu_media_file_path; 
$_FILES['media']['name'][$vn_key] = $jfu_media_file_name;

error_log( print_r( 'smc in mediaController vn_key='.$vn_key.', name='.$_FILES['media']['name'][$vn_key], true ) ); 
error_log( print_r( 'smc in mediaController vn_key='.$vn_key.',tmp_name='.$_FILES['media']['tmp_name'][$vn_key], true ) ); 

                            $vs_tmp = "";
							$vs_tmp = $_FILES["media"]["tmp_name"][$vn_key];
							if($vs_tmp || ($va_media_file_info["mediaServerPath"] && $vs_user_upload_directory && $vs_upload_base_directory && (preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory)))){
								$t_media_file = new ms_media_files();
								$t_media_file->set("media_id", $this->opo_item->get("media_id"));
								$t_media_file->set("user_id",$this->request->user->get("user_id"));
								foreach(array("title", "element", "side", "published", "notes", "file_type", "derived_from_media_file_id") as $vs_f){
									$t_media_file->set($vs_f, $va_media_file_info[$vs_f]);
									if ($t_media_file->numErrors() > 0) {
										foreach ($t_media_file->getErrors() as $vs_e) {
											$va_errors_file[$vs_f] = $vs_e;
										}
									}
								}
								if($this->opo_item->get("derived_from_media_id")){
									$t_media_file->set("file_type", 2);
								}
								# upload media
								if($vs_tmp){
									$t_media_file->set('media', $vs_tmp, array('original_filename' => $_FILES['media']['name'][$vn_key]));
									if ($t_media_file->numErrors() > 0) {
										foreach ($t_media_file->getErrors() as $vs_e) {
											$va_errors_file["media"] = $vs_e;
										}
									}
								}elseif($va_media_file_info["mediaServerPath"]){
									$vs_media_path = str_replace("/..", "", escapeshellcmd($va_media_file_info["mediaServerPath"]));
									if ($vs_media_path && file_exists($vs_user_upload_directory.$vs_media_path)) {
										$t_media_file->set('media', $vs_user_upload_directory.$vs_media_path, array('original_filename' => $vs_media_path));
									}
								}
								if(sizeof($va_errors_file)){
									$this->notification->addNotification("There were errors while uploading media file:".implode(", ", $va_errors_file), __NOTIFICATION_TYPE_INFO__);
								}else{
									$t_media_file->setMode(ACCESS_WRITE);
									$t_media_file->insert();
									if ($t_media_file->numErrors() == 0) {
										# Assign ARK
										# db NULL weirdly returned as empty string
										if (is_null($t_media_file->get("published")) 
											|| ($t_media_file->get("published") === ""))
										{
											$vs_published = $this->opo_item->get("published");
										} else {
											$vs_published = $t_media_file->get("published");
										}
										
										if ($vs_published == 0) { // Reserved ARK
											$va_ark = $t_media_file->getARK($va_reserved = true);
										} else { // Published ARK
											$t_user = new ca_users(($t_media_file->get('user_id') ? 
												$t_media_file->get('user_id') : 
												$this->opo_project->get('user_id')));
											$va_ark = $t_media_file->getARK(
												$va_reserved = false,
												$vs_user_fname = $t_user->get('fname'),
												$vs_user_lname = $t_user->get('lname')
											);
										}
										if (!$va_ark["success"]) {
											$va_errors["general"] .= $va_ark["error"];
										}

										// Set as preview file if there isn't one already for this media
										if(!$vb_preview){
											$t_media_file->set('use_for_preview', 1);
											$t_media_file->update();
										}
										$vb_preview = true;

										# --- check for preview files
										// Update media previews?
										if (isset($_FILES['mediaPreviews']['tmp_name'][$vn_key]) && $_FILES['mediaPreviews']['tmp_name'][$vn_key] && $_FILES['mediaPreviews']['size'][$vn_key]) {
											$t_media_file->set('media', $_FILES['mediaPreviews']['tmp_name'][$vn_key], array(
												'original_filename' => $_FILES['mediaPreviews']['name'][$vn_key]
											));
											$va_update_opts['updateOnlyMediaVersions'] = array('icon', 'tiny', 'thumbnail', 'widethumbnail', 'small', 'preview', 'preview190', 'widepreview', 'medium', 'mediumlarge', 'large');
											$t_media_file->update($va_update_opts);
										}
									}
								}
							}
						}
					}
					$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
				$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->mediaInfo();
			} 			 			
 		}
 		
 		# -------------------------------------------------------
 		public function mediaViewer() {
 			$pn_media_id = $this->request->getParameter('media_id', pInteger);
 			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
 			// TODO: does user own this media?
 			$t_media = new ms_media($pn_media_id);
 			$this->view->setVar('t_media', $t_media);
 			$t_media_file = new ms_media_files($pn_media_file_id);
 			$this->view->setVar('t_media_file', $t_media_file);
 			$this->render('Media/ajax_media_viewer_html.php');
 		}
 		# -------------------------------------------------------
 		public function mediaInfo() {
 			$this->render('Media/media_info_html.php');
 		}
 		# -------------------------------------------------------
 		public function publish() {
			if($this->opo_item->get("media_id")){
				$this->opo_item->set('published', 1);
				$this->opo_item->set('published_on', 'now');
				if (sizeof($va_errors) == 0) {
					# do update
					$this->opo_item->setMode(ACCESS_WRITE);
					$this->opo_item->update();
					if ($this->opo_item->numErrors()) {
						foreach ($this->opo_item->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						# Were any media files published?
						$va_media_files = $this->opo_item->getMediaFiles();
						if (sizeof($va_media_files)) {
							foreach ($va_media_files as $vn_media_file_id => $t_media_file) {
								if (is_null($t_media_file->get("published")) 
									|| ($t_media_file->get("published") === ""))
								{
									# Publish ARKs
									$t_user = new ca_users(($t_media_file->get('user_id') ? 
										$t_media_file->get('user_id') : 
										$this->opo_project->get('user_id')));
									$va_ark = $t_media_file->publishARK(
										$vs_user_fname = $t_user->get('fname'),
										$vs_user_lname = $t_user->get('lname')
									);
									if (!$va_ark["success"]) {
										$va_errors["general"] .= $va_ark["error"];
									}
								}
							}
						}

						$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
					}
				}
				if(sizeof($va_errors) > 0){
					$this->notification->addNotification("Could not publish media".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					$this->view->setVar("errors", $va_errors);
					$this->form();
				}else{
					$this->opn_item_id = $this->opo_item->get("media_id");
					$this->view->setVar("item_id", $this->opn_item_id);
					$this->view->setVar("item", $this->opo_item);
					$this->mediaInfo();
				}
			}
 		}
 		# -------------------------------------------------------
 		public function linkMediaFile() {
			$va_errors = array();
			$vs_message = "";
			$t_media_file = new ms_media_files();
			$vs_media_server_path = $this->request->getParameter('mediaServerPath', pString);
			if (
				($vs_media_server_path)
				&&
				($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
				&&
				($vs_upload_base_directory = $t_media_file->getAppConfig()->get('upload_base_directory'))
				&&
				(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
			) {
				$vs_media_path = str_replace("/..", "", escapeshellcmd($vs_media_server_path));
				if ($vs_media_path && file_exists($vs_user_upload_directory.$vs_media_path)) {
					$t_media_file->set('media', $vs_user_upload_directory.$vs_media_path, array('original_filename' => $vs_media_path));
				}
			} else {
//smctodo: overriding temp file and path
$jfu_media_file_name = 'notset';
$jfu_media_file_path = 'notset';
if (isset($_REQUEST['jfu_media_file_name'][0]))
    $jfu_media_file_name = $_REQUEST['jfu_media_file_name'][0];
if (isset($_REQUEST['jfu_media_file_path'][0]))
    $jfu_media_file_path = $_REQUEST['jfu_media_file_path'][0];
    
error_log( print_r( 'smc in MediaController jfu_media_file_name='.$jfu_media_file_name, true ) ); 

$_FILES['media']['tmp_name'] = "/nfs/images/tmp/jfu/".$jfu_media_file_path; 
$_FILES['media']['name'] = $jfu_media_file_name;
                
error_log( print_r( 'smc media tmp_name='.$_FILES['media']['tmp_name'], true ) ); 
                
				if($_FILES['media']['tmp_name']){
					$t_media_file->set('media', $_FILES['media']['tmp_name'], array('original_filename' => $_FILES['media']['name']));
				}elseif(!$t_media_file->get('media')){
					// smc old media field not needed any more
                    // $va_errors["media"] = "Please upload a media file";
				}
			}
			foreach(array("title", "element", "side", "published", "notes", "file_type", "derived_from_media_file_id") as $vs_f){
				$t_media_file->set($vs_f, $_REQUEST[$vs_f]);
				if ($t_media_file->numErrors() > 0) {
					foreach ($t_media_file->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}

			$t_media_file->set("user_id", $this->request->user->get("user_id"));
			if ($t_media_file->numErrors() > 0) {
				foreach ($t_media_file->getErrors() as $vs_e) {
					$va_errors["user_id"] = $vs_e;
				}
			}

			$t_media_file->set("media_id", $this->opo_item->get("media_id"));
			if ($t_media_file->numErrors() > 0) {
				foreach ($t_media_file->getErrors() as $vs_e) {
					$va_errors["media_id"] = $vs_e;
				}
			}

			if (sizeof($va_errors) == 0) {
				# do insert
				$t_media_file->setMode(ACCESS_WRITE);
				$t_media_file->insert();
	
				if ($t_media_file->numErrors()) {
					foreach ($t_media_file->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					# Assign ARK
					# db NULL weirdly returned as empty string
					if (is_null($t_media_file->get("published")) 
						|| ($t_media_file->get("published") === "")) 
					{
						$t_media = new ms_media($t_media_file->get("media_id"));
						$vs_published = $t_media->get("published");
					} else {
						$vs_published = $t_media_file->get("published");
					}
					
					if ($vs_published == 0) { // Reserved ARK
						$va_ark = $t_media_file->getARK($va_reserved = true);
					} else { // Published ARK
						$t_user = new ca_users(($t_media_file->get('user_id') ? 
							$t_media_file->get('user_id') : 
							$this->opo_project->get('user_id')));
						$va_ark = $t_media_file->getARK(
							$va_reserved = false,
							$vs_user_fname = $t_user->get('fname'),
							$vs_user_lname = $t_user->get('lname')
						);
					}
					if (!$va_ark["success"]) {
						$va_errors["general"] .= $va_ark["error"];
					}

					// Set as preview file if there isn't one already for this media
					$o_db = new Db();
					$q_preview = $o_db->query("SELECT media_file_id FROM ms_media_files WHERE use_for_preview = 1 and media_id = ?", $t_media_file->get("media_id"));
					if($q_preview->numRows() == 0){
						$t_media_file->set('use_for_preview', 1);
						$t_media_file->update();
					}
					// Update media previews?
					if (isset($_FILES['mediaPreviews']['tmp_name']) && $_FILES['mediaPreviews']['tmp_name'] && $_FILES['mediaPreviews']['size']) {
						$t_media_file->set('media', $_FILES['mediaPreviews']['tmp_name'], array(
							'original_filename' => $_FILES['mediaPreviews']['name']
						));
						$va_update_opts['updateOnlyMediaVersions'] = array('icon', 'tiny', 'thumbnail', 'widethumbnail', 'small', 'preview', 'preview190', 'widepreview', 'medium', 'mediumlarge', 'large');
						$t_media_file->update($va_update_opts);
					}
					
					$vs_message = "Saved media file";
				}

				

			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("mediaFileMessage", $vs_message);
				$this->view->setVar("mediaFileErrors", $va_errors);
			}else{
				$this->view->setVar("mediaFileMessage", $vs_message);
			}			 			
			$this->mediaInfo();
 		}
 		# -------------------------------------------------------
 		public function updateMediaFile() {
			$va_errors = array();
			$vs_message = "";
			$t_media_file = new ms_media_files();
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$t_media_file->load($pn_media_file_id);
			$vs_pub_init = $t_media_file->get("published");
			if (is_null($vs_pub_init) || ($vs_pub_init === "")) {
				$vs_pub_init = $this->opo_item->get("published");
			}
			$va_fields = array("published", "title", "element", "side", "notes", "file_type", "derived_from_media_file_id");
			foreach($va_fields as $vs_f){
				$t_media_file->set($vs_f,$_REQUEST[$vs_f]);

				if ($t_media_file->numErrors() > 0) {
					foreach ($t_media_file->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}
			if (sizeof($va_errors) == 0) {
				# do update
				$t_media_file->setMode(ACCESS_WRITE);
				$t_media_file->update();
	
				if ($t_media_file->numErrors()) {
					foreach ($t_media_file->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					# ARK Updates
					$vs_pub_final = $t_media_file->get("published");
					if (is_null($vs_pub_final) || ($vs_pub_final === "")) {
						$vs_pub_final = $this->opo_item->get("published");
					}

					# Publish ARK if now published, previously unpublished
					if ( ( ($vs_pub_final == 1) || ($vs_pub_final == 2) ) 
						&& !( ($vs_pub_init == 1) || ($vs_pub_init == 2) ) ) 
					{
						$t_user = new ca_users(($t_media_file->get('user_id') ? 
							$t_media_file->get('user_id') : 
							$this->opo_project->get('user_id')));
						$va_ark = $t_media_file->publishARK(
							$vs_user_fname = $t_user->get('fname'),
							$vs_user_lname = $t_user->get('lname')
						);
						if (!$va_ark["success"]) {
							$va_errors["general"] .= $va_ark["error"];
						}
					}
					# Unpublish ARK if previously published, now unpublished
					if ( ($vs_pub_final == 0 ) 
						&& ( ($vs_pub_init == 1) || ($vs_pub_init == 2) ) ) 
					{
						$va_ark = $t_media_file->unpublishARK();
						if (!$va_ark["success"]) {
							$va_errors["general"] .= $va_ark["error"];
						}
					}


					# Update media previews?
					if (isset($_FILES['mediaPreviews']['tmp_name']) && $_FILES['mediaPreviews']['tmp_name'] && $_FILES['mediaPreviews']['size']) {
						$t_media_file->set('media', $_FILES['mediaPreviews']['tmp_name'], array(
							'original_filename' => $_FILES['mediaPreviews']['name']
						));
						$va_update_opts['updateOnlyMediaVersions'] = array('icon', 'tiny', 'thumbnail', 'widethumbnail', 'small', 'preview', 'preview190', 'widepreview', 'medium', 'mediumlarge', 'large');
						$t_media_file->update($va_update_opts);
					}
					
					$this->notification->addNotification("Saved media file", __NOTIFICATION_TYPE_INFO__);
				}
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("mediaFileMessage", $vs_message);
				$this->view->setVar("mediaFileErrors", $va_errors);
			}else{
				$this->view->setVar("mediaFileMessage", $vs_message);
				$t_media_file = new ms_media_files();
			}
			$this->view->setVar("t_media_file", $t_media_file);
			$this->mediaInfo();
 		}
 		# -------------------------------------------------------
 		public function setMediaPreview() {
			$va_errors = array();
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$t_media_file = new ms_media_files();
			$t_media_file->load($pn_media_file_id);
			if(!$t_media_file->get("media_file_id")){
				$va_errors["general"] = "media file id not defined";
				$this->mediaInfo();
			}
			# --- unset current file selected for user as preview
			$o_db = new Db();
			$q_unset_preview = $o_db->query("UPDATE ms_media_files SET use_for_preview = 0 WHERE use_for_preview = 1 and media_id = ? and media_file_id != ?", $t_media_file->get("media_id"), $t_media_file->get("media_file_id"));
					
			$t_media_file->set('use_for_preview', 1);
			$t_media_file->setMode(ACCESS_WRITE);
			$t_media_file->update();

			if ($t_media_file->numErrors()) {
				foreach ($t_media_file->getErrors() as $vs_e) {  
					$va_errors["general"] = $vs_e;
				}
				if(sizeof($va_errors) > 0){
					$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				}
			}
			$this->mediaInfo();
 		}
 		# -------------------------------------------------------
 		public function specimenLookup() {
 			# --- pass the name of the current specimen
 			if($this->opo_item->get("specimen_id")){
 				$t_specimen = new ms_specimens();
 				$t_specimen->load($this->opo_item->get("specimen_id"));
 				$va_name = array();
 				if($t_specimen->get("institution_code")){
 					$va_name[] = $t_specimen->get("institution_code");
 				}
 				if($t_specimen->get("collection_code")){
 					$va_name[] = $t_specimen->get("collection_code");
 				}
 				if($t_specimen->get("catalog_number")){
 					$va_name[] = $t_specimen->get("catalog_number");
 				}
 				$vs_specimen_name = implode("/", $va_name);
 				if($vs_specimen_name){
 					$this->view->setVar("specimen_name", $vs_specimen_name);
 				}
 				# --- check if there is a taxonommic name for the specimen
 				$va_specimen_taxonomy = $t_specimen->getSpecimenTaxonomy();
 				$this->view->setVar("specimen_taxonomy", $va_specimen_taxonomy);
 				
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Media/media_specimen_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkSpecimen() {
			$va_errors = array();
			$vs_message = "";
			# --- set specimen that is passed from lookup form
			if($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
				$this->opo_item->set("specimen_id",$pn_specimen_id);
				if ($this->opo_item->numErrors() > 0) {
					foreach ($this->opo_item->getErrors() as $vs_e) {
						$va_errors["specimen_id"] = $vs_e;
					}
				}
			}else{
				$va_errors["general"] = _t("Please select a specimen");
			}		
			if (sizeof($va_errors) == 0) {
				# do update
				$this->opo_item->setMode(ACCESS_WRITE);
				$this->opo_item->update();
	
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$vs_message = "Saved media specimen";
				}
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("message", $vs_message);
				$this->specimenLookup();
			}else{
				$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->view->setVar("message", $vs_message);
				$this->specimenLookup();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function linkSpecimenTaxon() {
			$va_errors = array();
			$vs_message = "";
			# --- check the specimen_id and alt_id (key for ms_taxonomy_names) are passed through lookup form
			if(($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)) && ($pn_alt_id = $this->request->getParameter('alt_id', pInteger))){
				$t_specimens_x_taxonomy = new ms_specimens_x_taxonomy();
				$t_specimens_x_taxonomy->set("specimen_id",$pn_specimen_id);
				$t_specimens_x_taxonomy->set("alt_id",$pn_alt_id);
				$t_specimens_x_taxonomy->set("user_id",$this->request->user->get("user_id"));
				# --- get the taxon_id from the ms_taxonomy_names records
				$t_taxonomy_names = new ms_taxonomy_names($pn_alt_id);
				$t_specimens_x_taxonomy->set("taxon_id",$t_taxonomy_names->get("taxon_id"));

				# do insert
				$t_specimens_x_taxonomy->setMode(ACCESS_WRITE);
				$t_specimens_x_taxonomy->insert();
	
				if ($t_specimens_x_taxonomy->numErrors()) {
					foreach ($t_specimens_x_taxonomy->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$vs_message = "Saved specimen taxonomy";
				}
			}else{
				$va_errors["general"] = _t("Please select a taxonomic name");
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("taxonomy_message", $vs_message);
				$this->specimenLookup();
			}else{
				#$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->view->setVar("taxonomy_message", $vs_message);
				$this->specimenLookup();
			} 			 			
 		}
  		# -------------------------------------------------------
 		public function bibliographyLookup() {
 			# --- pass the list of linked bib citations
 			$va_bib_citations = array();
 			if($this->opn_item_id){
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id, mxb.media_file_id FROM ms_media_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.media_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "link_id" => $q_bib->get("link_id"), "media_file_id" => $q_bib->get("media_file_id"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
 				$this->view->setVar("bib_citations", $va_bib_citations);
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Media/media_bibliography_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkBibliography() {
			$va_errors = array();
			$vs_message = "";
			$vn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			# --- if no media_id is passed, we are on the list form and linking a bib to all project media
			if(!$this->opn_item_id){
				if($pn_bibliography_id = $this->request->getParameter('bibliography_id', pInteger)){
					# --- get all project media to link
					$o_db = new Db();
 					$q_project_media = $o_db->query("SELECT media_id FROM ms_media WHERE project_id = ?", $this->opn_project_id);
					if($q_project_media->numRows()){
						while($q_project_media->nextRow()){
							$va_item_errors = array();
							$t_bib_link = new ms_media_x_bibliography();
							# --- check that there is not already a link to this bib ref
							$t_bib_link->load(array("bibref_id" => $pn_bibliography_id, "media_id" => $q_project_media->get("media_id")));
							if(!$t_bib_link->get("link_id")){
								$t_bib_link->set("bibref_id",$pn_bibliography_id);
								$t_bib_link->set("media_id",$q_project_media->get("media_id"));
								$t_bib_link->set("user_id",$this->request->user->get("user_id"));
								if($vn_media_file_id){
									$t_bib_link->set("media_file_id",$vn_media_file_id);
								}
								if ($t_bib_link->numErrors() > 0) {
									foreach ($t_bib_link->getErrors() as $vs_e) {
										$va_item_errors["bibliography_id"] = $va_item_errors["bibliography_id"].$vs_e;
									}
								}
								if (sizeof($va_item_errors) == 0) {
									# do insert
									$t_bib_link->setMode(ACCESS_WRITE);
									$t_bib_link->insert();
						
									if ($t_bib_link->numErrors()) {
										foreach ($t_bib_link->getErrors() as $vs_e) {  
											$va_errors["general"] = $va_errors["general"]."M".$q_project_media->get("media_id")." ".$vs_e.", ";
										}
									}
								}
							}	
						}
						if(sizeof($va_errors) > 0){
							$vs_message = "There were errors:".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
							$this->view->setVar("message", $vs_message);
						}else{
							$this->view->setVar("message", "Citation was linked to all project media");
						} 
						
					}
				}else{
					$this->view->setVar("message", _t("Please select a bibliography"));
				}
				$this->bibliographyLookup();
			}else{			
				# --- make link to bib that is passed from lookup form
				if($pn_bibliography_id = $this->request->getParameter('bibliography_id', pInteger)){
					$t_bib_link = new ms_media_x_bibliography();
					# --- check that there is not already a link to this bib ref
					if($t_bib_link->load(array("bibref_id" => $pn_bibliography_id, "media_id" => $this->opn_item_id))){
						$va_errors["general"] = "There is already a link to this bibliographic citation";
					}else{
						$t_bib_link->set("bibref_id",$pn_bibliography_id);
						$t_bib_link->set("media_id",$this->opn_item_id);
						$t_bib_link->set("user_id",$this->request->user->get("user_id"));
						if($vn_media_file_id){
							$t_bib_link->set("media_file_id",$vn_media_file_id);
						}
						#if($vs_page = $this->request->getParameter('page', pString)){
						#	$t_bib_link->set("pp",$vs_page);
						#}
						if ($t_bib_link->numErrors() > 0) {
							foreach ($t_bib_link->getErrors() as $vs_e) {
								$va_errors["bibliography_id"] = $vs_e;
							}
						}
					}
				}else{
					$va_errors["general"] = _t("Please select a bibliography");
				}		
				if (sizeof($va_errors) == 0) {
					# do insert
					$t_bib_link->setMode(ACCESS_WRITE);
					$t_bib_link->insert();
		
					if ($t_bib_link->numErrors()) {
						foreach ($t_bib_link->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						$vs_message = "Saved media bibliography";
					}
				}
				if(sizeof($va_errors) > 0){
					$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
					$this->view->setVar("message", $vs_message);
					$this->bibliographyLookup();
				}else{
					$this->opn_item_id = $this->opo_item->get("media_id");
					$this->view->setVar("message", $vs_message);
					$this->bibliographyLookup();
				} 			 		
			}
 		}
 		# -------------------------------------------------------
 		public function removeBibliography() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_bib_link = new ms_media_x_bibliography($pn_link_id);
 				$t_bib_link->setMode(ACCESS_WRITE);
 				$t_bib_link->delete();
				if ($t_bib_link->numErrors() > 0) {
					foreach ($t_bib_link->getErrors() as $vs_e) {
						$va_errors[] = $vs_e;
					}
					$this->view->setVar("message", join(", ", $va_errors));
				}
 			}
 			$this->bibliographyLookup();
 		}
 		# -------------------------------------------------------
 		public function delete() {
 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
				# --- delete media files for the media
				$o_db = new Db();
				$q_media_files = $o_db->query("SELECT media_file_id FROM ms_media_files WHERE media_id = ?", $this->opo_item->get("media_id"));
				if($q_media_files->numRows()){
					$t_media_file = new ms_media_files();
					while($q_media_files->nextRow()){
						$t_media_file->load($q_media_files->get("media_file_id"));

						# Delete ARK or make unavailable
						if ($t_media_file->get("ark")) {
							if ($t_media_file->get("ark_reserved")) {
								$t_media_file->deleteARK();
							} else {
								$t_media_file->unpublishARK();
							}
						}

						$t_media_file->setMode(ACCESS_WRITE);
						$t_media_file->delete(true);
						if ($t_media_file->numErrors()) {
							foreach ($t_media_file->getErrors() as $vs_e) {  
								$va_errors["media_files"] = $vs_e;
							}
						}
					}
				}
				if(sizeof($va_errors) == 0){
					$this->opo_item->setMode(ACCESS_WRITE);
					$this->opo_item->delete(true);
					if ($this->opo_item->numErrors()) {
						foreach ($this->opo_item->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
						if(sizeof($va_errors) > 0){
							$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
						}
						$this->form();
					}else{
						$this->notification->addNotification("Deleted ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
						$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "Dashboard"));
					}				
				}else{
					$this->notification->addNotification("There were errors".(($va_errors["media_files"]) ? ": ".$va_errors["media_files"] : ""), __NOTIFICATION_TYPE_INFO__);
				}
			}else{
				$this->view->setVar("item_name", $this->ops_item_name);
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 		public function deleteMediaFile() { 			
			$t_media_file = new ms_media_files();
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$t_media_file->load($pn_media_file_id);
			if(!$t_media_file->get("media_file_id")){
				$va_errors["general"] = "media file id not defined";
				$this->mediaInfo();
			}
			
			$va_errors = array();

			# Delete ARK or make unavailable
			if ($t_media_file->get("ark")) {
				if ($t_media_file->get("ark_reserved")) {
					$t_media_file->deleteARK();
				} else {
					$t_media_file->unpublishARK();
				}
			}

			$t_media_file->setMode(ACCESS_WRITE);
			$t_media_file->delete(true);
			if ($t_media_file->numErrors()) {
				foreach ($t_media_file->getErrors() as $vs_e) {  
					$va_errors["general"] = $vs_e;
				}
				if(sizeof($va_errors) > 0){
					$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				}
				$this->form();
			}else{
				$this->notification->addNotification("Deleted media file", __NOTIFICATION_TYPE_INFO__);
				$this->mediaInfo();
			}
 		}
 		# -------------------------------------------------------
		/**
		 * 
		 */ 
		public function GetDOI() {
			if (!$this->request->user->canDoAction('can_create_doi')) { die("Not allowed to allocate DOIs"); }
			
			$t_media_file = new ms_media_files(
				$this->request->getParameter('media_file_id', pInteger));			
			$t_user = new ca_users(($vn_user_id = $t_media_file->get('user_id') ? $t_media_file->get('user_id') : $this->opo_project->get('user_id')));

			$va_doi = $t_media_file->getDOI($t_user->get('fname'), 
				$t_user->get('lname'));
			if ($va_doi["success"]) {
				$this->notification->addNotification(
					"Obtained DOI for media", __NOTIFICATION_TYPE_INFO__);
			} else {
				$this->notification->addNotification(
					$va_doi["error"], __NOTIFICATION_TYPE_ERROR__);
			}
			$this->mediaInfo();
		}
 		# -------------------------------------------------------
		/**
		 * Download media
		 */ 
		public function DownloadMedia() {
			$t_media_file = new ms_media_files();
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$t_media_file->load($pn_media_file_id);
			if(!$t_media_file->get("media_file_id")){
				$va_errors["general"] = "media file id not defined";
				$this->mediaInfo();
			}
			$vs_element = "";
			if($t_media_file->get("element")){
				$vs_element = "_".$t_media_file->get("element");
			}else{
				$t_media = new ms_media($t_media_file->get("media_id"));
				if($t_media->get("element")){
					$vs_element = "_".$t_media->get("element");
				}
			}
			$vs_element = str_replace(" ", "_", $vs_element);
			$ps_version = "_archive_";
			
			$va_versions = $t_media_file->getMediaVersions('media');
			
			if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
			if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
			
			$va_version_info = $t_media_file->getMediaInfo('media', $ps_version);
			
			$va_info = $t_media_file->getMediaInfo('media');
			$vs_idno_proc = $this->opo_item->get('media_id');
			$t_specimens = new ms_specimens();
			$vs_specimen_number = $t_specimens->getSpecimenNumber($this->opo_item->get("specimen_id"));
			$vs_specimen_name = str_replace(" ", "_", strip_tags(array_shift($t_specimens->getSpecimenTaxonomy($this->opo_item->get("specimen_id")))));
			
			# --- record download
			$this->opo_item->recordDownload($this->request->getUserID(), $this->opo_item->get("media_id"), $pn_media_file_id);
			
			if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
			set_time_limit($vn_limit * 2);
			
			$o_zip = new ZipStream();
			$vs_path = $t_media_file->getMediaPath('media', $ps_version);
			if(file_exists($vs_path)){
				$o_zip->addFile($vs_path, $vs_specimen_number.'_M'.$vs_idno_proc.'-'.$pn_media_file_id.'.'.$va_version_info['EXTENSION']);
			}
			
			# --- generate text file for media downloaded and add it to zip
			$vs_tmp_file_name = '';
			$vs_text_file_name = 'Morphosource_'.$vs_specimen_number.'_M'.$vs_idno_proc.'-'.$pn_media_file_id.'.csv';
			$va_text_file_text = $t_media_file->mediaMdText(array($pn_media_file_id), $t_specimens);
			if(sizeof($va_text_file_text)){
				$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'mediaDownloadTxt');
				$vo_file = fopen($vs_tmp_file_name, "w");
				foreach($va_text_file_text as $va_row){
					fputcsv($vo_file, $va_row);			
				}
				fclose($vo_file);
				$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name);
			}

			# --- include download agreement form and add it to zip ---
			$vs_pdf_file_name = 'MorphoSource_download_use_agreement.pdf';
			$vs_pdf_file_location = $this->request->getThemeDirectoryPath().
				'/static/'.$vs_pdf_file_name;
			$o_zip->addFile($vs_pdf_file_location, $vs_pdf_file_name);
			
			$this->view->setVar('zip_stream', $o_zip);
		
			$this->view->setVar('version_path',$vs_path = $t_media_file->getMediaPath('media', $ps_version));
			$this->view->setVar('version_download_name', 'Morphosource_'.$vs_specimen_number.'_M'.$vs_idno_proc.'-'.$pn_media_file_id.'.zip');
				
			$this->response->sendHeaders();
			$vn_rc = $this->render('Media/media_download_binary.php');
			
			$this->response->sendContent();
			
			if ($vs_tmp_file_name) { @unlink($vs_tmp_file_name); }
		}
		# -------------------------------------------------------
		/**
		 * Download all media files for media
		 */ 
		public function DownloadAllMedia() {
			# --- get all media files
			$o_db = new Db();
			$q_media_files = $o_db->query("SELECT mf.media, mf.media_file_id, mf.element, m.element media_element FROM ms_media_files mf INNER JOIN ms_media AS m ON mf.media_id = m.media_id WHERE mf.media_id = ?", $this->opo_item->get("media_id"));
			if($q_media_files->numRows()){
				$t_specimens = new ms_specimens();
				$t_media_file = new ms_media_files();
				$vs_specimen_number = $t_specimens->getSpecimenNumber($this->opo_item->get("specimen_id"));
				$vs_specimen_name = str_replace(" ", "_", strip_tags(array_shift($t_specimens->getSpecimenTaxonomy($this->opo_item->get("specimen_id")))));
				$va_media_file_ids = array();
				while($q_media_files->nextRow()){
					$t_media_file->load($q_media_files->get("media_file_id"));
					$vs_element = "";
					if($q_media_files->get("element")){
						$vs_element = "_".$q_media_files->get("element");
					}elseif($q_media_files->get("media_element")){
						$vs_element = "_".$q_media_files->get("media_element");
					}
					$vs_element = str_replace(" ", "_", $vs_element);
					$ps_version = "_archive_";
					$va_versions = $t_media_file->getMediaVersions('media');
					if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
					if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
					$vs_idno_proc = $this->opo_item->get('media_id');
					$va_version_info = $t_media_file->getMediaInfo('media', $ps_version);
				
					$vs_file_name = $vs_specimen_number.'_M'.$vs_idno_proc.'-'.$q_media_files->get("media_file_id").'_'.$vs_specimen_name.$vs_element.'.'.$va_version_info['EXTENSION'];
					$vs_file_path = $q_media_files->getMediaPath('media', $ps_version);
					$va_file_names[$vs_file_name] = true;
					$va_file_paths[$vs_file_path] = $vs_file_name;
					# --- record download
					$this->opo_item->recordDownload($this->request->getUserID(), $this->opo_item->get("media_id"), $q_media_files->get("media_file_id"));			
					$va_media_file_ids[] = $q_media_files->get("media_file_id");
				}
			}
			if (sizeof($va_file_paths)) {
				if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
				set_time_limit($vn_limit * 2);
				
				$o_zip = new ZipStream();
				foreach($va_file_paths as $vs_path => $vs_name) {
					if(file_exists($vs_path)){
						$o_zip->addFile($vs_path, $vn_name);
					}
				}
				# --- generate text file for media downloaded and add it to zip
				$vs_tmp_file_name = '';
				$vs_text_file_name = 'Morphosource_'.$vs_specimen_name.'_M'.$vs_idno_proc.'.csv';
				$va_text_file_text = $t_media_file->mediaMdText($va_media_file_ids, $t_specimens);
				if(sizeof($va_text_file_text)){
					$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'mediaDownloadTxt');
					$vo_file = fopen($vs_tmp_file_name, "w");
					foreach($va_text_file_text as $va_row){
						fputcsv($vo_file, $va_row);			
					}
					fclose($vo_file);
					$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name);
				}
				
				# --- include download agreement form and add it to zip ---
				$vs_pdf_file_name = 'MorphoSource_download_use_agreement.pdf';
				$vs_pdf_file_location = $this->request->getThemeDirectoryPath().
					'/static/'.$vs_pdf_file_name;
				$o_zip->addFile($vs_pdf_file_location, $vs_pdf_file_name);
				
				$this->view->setVar('zip_stream', $o_zip);
				$this->view->setVar('version_download_name', 'Morphosource_'.$vs_specimen_name.'_M'.$vs_idno_proc.'.zip');
				
				$this->response->sendHeaders();
				$vn_rc = $this->render('Media/media_download_binary.php');
				$this->response->sendContent();
			
				if ($vs_tmp_file_name) { @unlink($vs_tmp_file_name); }
			}
				
			return $vn_rc;
		}
		# -------------------------------------------------------
 		public function confirmMove() {
 			$vn_media_id = $this->request->getParameter("media_id", pInteger);
 			$vn_proj_from = $this->request->getParameter("proj_from", pInteger);
 			$vn_proj_to = $this->request->getParameter("proj_to", pInteger);

 			$this->view->setVar("media_id", $vn_media_id);
 			$this->view->setVar("proj_from", $vn_proj_from);
 			$this->view->setVar("proj_to", $vn_proj_to);

 			$this->render("confirm_move_html.php");
 		}
 		# -------------------------------------------------------
 		public function moveMedia() {
 			# Verify user can move this media
 			$t_from_project = new ms_projects($this->opo_item->get("project_id"));
 			if(!$t_from_project->isFullAccessMember($this->request->user->get("user_id"))){
 				$this->notification->addNotification("Not authorized to access this resource", __NOTIFICATION_TYPE_ERROR__);
 				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));

 			}
			if($this->opo_item->get("media_id") && ($pn_move_project_id = $this->request->getParameter('move_project_id', pInteger))){
				# --- change user_id in media record to the project admin of the project you're moving the media to
				$t_move_project = new ms_projects($pn_move_project_id);
				# --- user is member of project media is being transfered to, so just move it!
				if($t_move_project->isFullAccessMember($this->request->user->get("user_id"))){
					$this->opo_item->set("user_id", $t_move_project->get("user_id"));
					$this->opo_item->set('project_id', $pn_move_project_id);
					if (sizeof($va_errors) == 0) {
						# do update
						$this->opo_item->setMode(ACCESS_WRITE);
						$this->opo_item->update();
						if ($this->opo_item->numErrors()) {
							foreach ($this->opo_item->getErrors() as $vs_e) {  
								$va_errors["general"] = $vs_e;
							}
						}else{
							$this->notification->addNotification("Media ownership has been transfered to P".$pn_move_project_id.".  The media will still appear in ".$this->ops_project_name." since you are a member of both projects.", __NOTIFICATION_TYPE_INFO__);
							$t_media_x_projects = new ms_media_x_projects();
							$t_media_x_projects->load(array("media_id" => $this->opo_item->get("media_id"), "project_id" => $this->opn_project_id));
							if(!$t_media_x_projects->get("link_id")){
								$t_media_x_projects->set("media_id",$this->opo_item->get("media_id"));
								$t_media_x_projects->set("project_id",$this->opn_project_id);
								if ($t_media_x_projects->numErrors() == 0) {
									# do insert
									$t_media_x_projects->setMode(ACCESS_WRITE);
									$t_media_x_projects->insert();
						
									if ($t_media_x_projects->numErrors()) {
											foreach ($t_media_x_projects->getErrors() as $vs_e) {  
												$va_errors["general"] = "Could not link media as read only: ".join(", ", $t_media_x_projects->getErrors());
											}
										}
								}else{
									$va_errors["general"] = "Could not link media as read only: ".join(", ", $t_media_x_projects->getErrors());
								}
							}
						}
					}
					if(sizeof($va_errors) > 0){
						print "Could not move media".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
						exit;
					}else{
						$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
					}
				}else{
					# --- user wants to transfer media to a project they are not a member
					$o_db = new Db();
					$q_project_admin = $o_db->query("SELECT u.fname, u.lname, u.email, p.name FROM ms_projects p INNER JOIN ca_users AS u ON p.user_id = u.user_id WHERE p.project_id = ?", $pn_move_project_id);
					if($q_project_admin->numRows()){
						$q_project_admin->nextRow();
						$vs_move_project_admin_name = trim($q_project_admin->get("fname")." ".$q_project_admin->get("lname"));
						$vs_move_project_admin_email = $q_project_admin->get("email");
						$vs_move_project_name = $q_project_admin->get("name");
					}
					$this->view->setVar("move_project_name", $vs_move_project_name);
					$this->view->setVar("move_project_admin_name", $vs_move_project_admin_name);
					$this->view->setVar("move_project_admin_email", $vs_move_project_admin_email);
					if($this->request->getParameter('move_form_submit', pInteger)){
						# --- comment form was submitted so send an email to the project admin, notifying them of the move request
						# --- fill out ms_media_movement_requests record
						$t_media_movement_requests = new ms_media_movement_requests();
						$t_media_movement_requests->set("user_id",$this->request->user->get("user_id"));
						$t_media_movement_requests->set("media_id",$this->opo_item->get("media_id"));
						$t_media_movement_requests->set("to_project_id",$pn_move_project_id);
						$t_media_movement_requests->set("type",1); # --- move
						$t_media_movement_requests->set("status",0); # --- new
						$o_purifier = new HTMLPurifier();
    					$vs_comment = $o_purifier->purify($this->request->getParameter('moveComment', pString));
						if($vs_comment){
							$t_media_movement_requests->set("request",$vs_comment);
						}
						if ($t_media_movement_requests->numErrors() == 0) {
							# do insert
							$t_media_movement_requests->setMode(ACCESS_WRITE);
							$t_media_movement_requests->insert();
				
							if ($t_media_movement_requests->numErrors()) {
								print "Could not make media_movement_requests record: ".join(", ", $t_media_movement_requests->getErrors());
								exit;
							}else{
								# --- send email to project admin
								# -- generate mail text from template - get both html and text versions
								# --- get current user's name and email address
								$q_user_info = $o_db->query("SELECT fname, lname, email from ca_users where user_id = ?", $this->request->user->get("user_id"));
								if($q_user_info->numRows()){
									$q_user_info->nextRow();
									$vs_user_name = trim($q_user_info->get("fname")." ".$q_user_info->get("lname"));
									$vs_user_email = $q_user_info->get("email");
								}
								$vs_specimen_info = "";
								if($this->opo_item->get("specimen_id")){
									$t_specimen = new ms_specimens($this->opo_item->get("specimen_id"));
									$vs_specimen_info = $t_specimen->getSpecimenName();
								}
	
								ob_start();
								require($this->request->getViewsDirectoryPath()."/mailTemplates/move_media_request.tpl");
								$vs_mail_message_text = ob_get_contents();
								ob_end_clean();
								ob_start();
								require($this->request->getViewsDirectoryPath()."/mailTemplates/move_media_request_html.tpl");
								$vs_mail_message_html = ob_get_contents();
								ob_end_clean();
												
								if(caSendmail($vs_move_project_admin_email, array($vs_user_email => $vs_user_name), _t("Message from MorphoSource P".$this->opn_project_id), $vs_mail_message_text, $vs_mail_message_html, null, null)){
									$vs_message = $vs_move_project_admin_name." has been notified of your move request.  You will be notified via email when they accept or reject the request.";
								}else{
									$vs_message = "Could not send email notification";
								}
								$this->view->setVar("message", $vs_message);
								$this->render('Media/move_media_sent_html.php');
							}
						}else{
							print "Could not make media_movement_requests record: ".join(", ", $t_media_movement_requests->getErrors());
							exit;
						}
					}else{
						# --- render form to get comment
						$this->view->setVar("move_project_id", $pn_move_project_id);
						$this->render('Media/move_media_form_html.php');					
					}
				}
			}else{
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Media", "mediaInfo", array("media_id" => $this->opo_item->get("media_id"))));
			}
 		}
 		# -------------------------------------------------------
 		public function shareMedia() {
			if($this->opo_item->get("media_id") && ($pn_share_project_id = $this->request->getParameter('share_project_id', pInteger))){
				$t_share_project = new ms_projects($pn_share_project_id);
				# --- user is member of project media is being shared with to, so just share it!
				if($t_share_project->isFullAccessMember($this->request->user->get("user_id"))){
					$t_media_x_projects = new ms_media_x_projects();
					if($t_media_x_projects->load(array("media_id" => $this->opo_item->get("media_id"), "project_id" => $pn_share_project_id))){
						$this->notification->addNotification("Already shared media with P".$pn_share_project_id, __NOTIFICATION_TYPE_INFO__);
						$this->mediaInfo();
					}else{
						$t_media_x_projects->set("media_id",$this->opo_item->get("media_id"));
						$t_media_x_projects->set("project_id",$pn_share_project_id);
						if ($t_media_x_projects->numErrors() == 0) {
							# do insert
							$t_media_x_projects->setMode(ACCESS_WRITE);
							$t_media_x_projects->insert();
				
							if ($t_media_x_projects->numErrors()) {
								foreach ($t_media_x_projects->getErrors() as $vs_e) {  
									$va_errors["general"] = "Could not share media as read only: ".join(", ", $t_media_x_projects->getErrors());
								}
							}
						}else{
							$va_errors["general"] = "Could not share media as read only: ".join(", ", $t_media_x_projects->getErrors());
						}
						if(sizeof($va_errors) > 0){
							$this->notification->addNotification("Could not share media".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
							$this->view->setVar("errors", $va_errors);
						}else{
							$this->notification->addNotification("Shared media", __NOTIFICATION_TYPE_INFO__);
						}
						$this->mediaInfo();
					}
				}else{
					# --- user wants to share media with a project they are not a member
					$o_db = new Db();
					$q_project_admin = $o_db->query("SELECT u.fname, u.lname, u.email, p.name FROM ms_projects p INNER JOIN ca_users AS u ON p.user_id = u.user_id WHERE p.project_id = ?", $pn_share_project_id);
					if($q_project_admin->numRows()){
						$q_project_admin->nextRow();
						$vs_move_project_admin_name = $vs_share_project_admin_name = trim($q_project_admin->get("fname")." ".$q_project_admin->get("lname"));
						$vs_move_project_admin_email = $vs_share_project_admin_email = $q_project_admin->get("email");
						$vs_move_project_name = $vs_share_project_name = $q_project_admin->get("name");
					}
					$this->view->setVar("share_project_name", $vs_share_project_name);
					$this->view->setVar("share_project_admin_name", $vs_share_project_admin_name);
					$this->view->setVar("share_project_admin_email", $vs_share_project_admin_email);
					if($this->request->getParameter('share_form_submit', pInteger)){
						# --- comment form was submitted so send an email to the project admin, notifying them of the share request
						# --- fill out ms_media_movement_requests record
						$t_media_movement_requests = new ms_media_movement_requests();
						$t_media_movement_requests->set("user_id",$this->request->user->get("user_id"));
						$t_media_movement_requests->set("media_id",$this->opo_item->get("media_id"));
						$t_media_movement_requests->set("to_project_id",$pn_share_project_id);
						$t_media_movement_requests->set("type",2); # --- share
						$t_media_movement_requests->set("status",0); # --- new
						$o_purifier = new HTMLPurifier();
    					$vs_comment = $o_purifier->purify($this->request->getParameter('shareComment', pString));
						if($vs_comment){
							$t_media_movement_requests->set("request",$vs_comment);
						}
						if ($t_media_movement_requests->numErrors() == 0) {
							# do insert
							$t_media_movement_requests->setMode(ACCESS_WRITE);
							$t_media_movement_requests->insert();
				
							if ($t_media_movement_requests->numErrors()) {
								print "Could not make media_movement_requests record: ".join(", ", $t_media_movement_requests->getErrors());
								exit;
							}else{
								# --- send email to project admin
								# -- generate mail text from template - get both html and text versions
								# --- get current user's name and email address
								$q_user_info = $o_db->query("SELECT fname, lname, email from ca_users where user_id = ?", $this->request->user->get("user_id"));
								if($q_user_info->numRows()){
									$q_user_info->nextRow();
									$vs_user_name = trim($q_user_info->get("fname")." ".$q_user_info->get("lname"));
									$vs_user_email = $q_user_info->get("email");
								}
								$vs_specimen_info = "";
								if($this->opo_item->get("specimen_id")){
									$t_specimen = new ms_specimens($this->opo_item->get("specimen_id"));
									$vs_specimen_info = $t_specimen->getSpecimenName();
								}
	
								ob_start();
								require($this->request->getViewsDirectoryPath()."/mailTemplates/move_media_request.tpl");
								$vs_mail_message_text = ob_get_contents();
								ob_end_clean();
								ob_start();
								require($this->request->getViewsDirectoryPath()."/mailTemplates/move_media_request_html.tpl");
								$vs_mail_message_html = ob_get_contents();
								ob_end_clean();
												
								if(caSendmail($vs_move_project_admin_email, array($vs_user_email => $vs_user_name), _t("Message from MorphoSource P".$this->opn_project_id), $vs_mail_message_text, $vs_mail_message_html, null, null)){
									$vs_message = $vs_move_project_admin_name." has been notified of your share request.  You will be notified via email when they accept or reject the request.";
								}else{
									$vs_message = "Could not send email notification";
								}
								$this->view->setVar("message", $vs_message);
								$this->render('Media/share_media_form_html.php');
							}
						}else{
							print "Could not make media_movement_requests record: ".join(", ", $t_media_movement_requests->getErrors());
							exit;
						}
					}else{
						# --- render form to get comment
						$this->view->setVar("show_comment_form", 1);
						$this->view->setVar("share_project_id", $pn_share_project_id);
						$this->render('Media/share_media_form_html.php');					
					}
				}
			}else{
				$this->listItems();
			}
 		}
 		# -------------------------------------------------------
 		public function removeShareMedia() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_share_media = new ms_media_x_projects($pn_link_id);
 				$t_share_media->setMode(ACCESS_WRITE);
 				$t_share_media->delete();
				if ($t_share_media->numErrors() > 0) {
					foreach ($t_share_media->getErrors() as $vs_e) {
						$va_errors[] = $vs_e;
					}
					$this->view->setVar("message", join(", ", $va_errors));
				}else{
					$this->notification->addNotification("Removed read only access", __NOTIFICATION_TYPE_INFO__);
				}
 			}
 			$this->mediaInfo();
 		}
 		# -------------------------------------------------------
 		public function derivativePreview(){
 			if($pn_media_derivative_id = $this->request->getParameter('media_derivative_id', pInteger)){
 				$t_parent = new ms_media($pn_media_derivative_id);
 				$this->view->setVar("parent", $t_parent);
 			}
 			$this->render('Media/derivative_preview_html.php');
 		}
 		# -------------------------------------------------------
 		public function shareMediaUser(){
			if($this->opo_item->get("media_id") && ($pn_share_user_id = $this->request->getParameter('share_user_id', pInteger))){
				$t_share_media = new ms_media_shares();
				
				$t_share_media->set("media_id", $this->opo_item->get("media_id"));
				$t_share_media->set("user_id", $pn_share_user_id);
				$t_share_media->set("shared_by_user_id", $this->request->user->get("user_id"));
				$vs_use_restrictions = $this->request->getParameter('use_restrictions', pString);
				$t_share_media->set("use_restrictions", $vs_use_restrictions);
				if ($t_share_media->numErrors() == 0) {
					# do insert
					$t_share_media->setMode(ACCESS_WRITE);
					$t_share_media->insert();
		
					if ($t_share_media->numErrors()) {
						foreach ($t_share_media->getErrors() as $vs_e) {  
							$va_errors["general"] = "Could not share media: ".join(", ", $t_share_media->getErrors());
						}
					}
				}else{
					$va_errors["general"] = "Could not share media: ".join(", ", $t_share_media->getErrors());
				}
				if(sizeof($va_errors) > 0){
					$vs_message = $va_errors["general"];
				}else{
					
					# --- send email to user
					# -- generate mail text from template - get both html and text versions
					# --- get current user's name and email address
					$o_db = new Db();
					$q_user_info = $o_db->query("SELECT fname, lname, email from ca_users where user_id = ?", $pn_share_user_id);
					if($q_user_info->numRows()){
						$q_user_info->nextRow();
						$vs_user_name = trim($q_user_info->get("fname")." ".$q_user_info->get("lname"));
						$vs_user_email = $q_user_info->get("email");
					}
					$vs_specimen_info = "";
					if($this->opo_item->get("specimen_id")){
						$t_specimen = new ms_specimens($this->opo_item->get("specimen_id"));
						$vs_specimen_info = $t_specimen->getSpecimenName();
					}
					$vs_sharing_name = trim($this->request->user->get("fname")." ".$this->request->user->get("lname"));

					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/share_media.tpl");
					$vs_mail_message_text = ob_get_contents();
					ob_end_clean();
					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/share_media_html.tpl");
					$vs_mail_message_html = ob_get_contents();
					ob_end_clean();
									
					if(caSendmail($vs_user_email, $this->request->user->get("email"), _t("MorphoSource Media Share"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
						$vs_message = $vs_user_name." has been emailed how to access the media.";
					}else{
						$vs_message = "Could not send email notification";
					}
				}
			}else{
				$vs_message = "Please search for and select a user from the options provided";
			}
			$this->view->setVar("message", $vs_message);
			$this->render('Media/share_media_user_form_html.php');	
				
			
 		}
 		# -------------------------------------------------------
 		public function removeShareMediaUser() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_media_shares = new ms_media_shares($pn_link_id);
 				$t_media_shares->setMode(ACCESS_WRITE);
 				$t_media_shares->delete();
				if ($t_media_shares->numErrors() > 0) {
					foreach ($t_media_shares->getErrors() as $vs_e) {
						$va_errors[] = $vs_e;
					}
					$this->view->setVar("message", join(", ", $va_errors));
				}else{
					$this->notification->addNotification("Removed user's access to media", __NOTIFICATION_TYPE_INFO__);
				}
 			}
 			$this->mediaInfo();
 		}
 		# -------------------------------------------------------
 	}
 ?>
