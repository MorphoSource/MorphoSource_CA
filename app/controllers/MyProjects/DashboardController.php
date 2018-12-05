<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/DashboardController.php
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
 * ----------------------------------------------------------------------
 */
 
 	require_once(__CA_LIB_DIR__."/core/Error.php");
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_movement_requests.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class DashboardController extends ActionController {
 		# -------------------------------------------------------
			/** 
			 * declare table instance
			*/
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_project = new ms_projects();
 			# --- is there a project already selected, are we selecting a project, or should we default to the list of user's projects
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
				$this->view->setvar("project", $this->opo_project);
 			}
 		}
 		# -------------------------------------------------------
 		function projectList() {
			# --- get list of available projects for user
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$this->view->setvar("projects", $va_projects);
			
 			$this->render('Dashboard/projects_list_html.php');
 		}
 		# -------------------------------------------------------
 		function dashboard() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			JavascriptLoadManager::register("cycle");
			if(!$this->opn_project_id){
				$this->projectList();
				return;
			}
					
			// Sort variable handling
			if ($this->request->getParameter('s', pString)) {
				$vs_specimens_group_by = 
					$this->request->getParameter('s', pString);
			} elseif ($this->request->session->getVar('specimens_group_by')) {
				$vs_specimens_group_by = 
					$this->request->session->getVar('specimens_group_by');
			} else {
				$vs_specimens_group_by = 'number';
			}
			if (!in_array($vs_specimens_group_by, 
				['n', 't', 'a', 'm', 'u', 'v'])) {
				$vs_specimens_group_by = 'n';
			}
			$this->view->setVar('specimens_group_by', $vs_specimens_group_by);
			$this->request->session->setVar('specimens_group_by', 
				$vs_specimens_group_by);		

			// Entity format variable handling
			if ($this->request->getParameter('f', pString)) {
				$vs_entity_format = $this->request->getParameter('f', pString);
			} elseif ($this->request->session->getVar('entity_format')){
				$vs_entity_format = 
					$this->request->session->getVar('entity_format');
			} else {
				$vs_entity_format = 't';
			}
			if (!in_array($vs_entity_format, ['t', 'l'])) {
				$vs_entity_format = 't';
			}
			$this->view->setVar("entity_format", $vs_entity_format);
			$this->request->session->setVar('entity_format', $vs_entity_format);

			// Entity type variable handling			
			if ($this->request->getParameter('t', pString)) {
				$vs_entity_type = $this->request->getParameter('t', pString);
			} elseif ($this->request->session->getVar('entity_type')) {
				$vs_entity_type = $this->request->session->getVar('entity_type');
			}else{
				$vs_entity_type = 's';
			}
			if (!in_array($vs_entity_type, ['s', 'm'])) {
				$vs_entity_type = 's';
			}
			$this->view->setVar('entity_type', $vs_entity_type);
			$this->request->session->setVar('entity_type', $vs_entity_type);

			// Initial db query
			$o_db = new Db();
			$q_institutions = $o_db->query(
				"SELECT * FROM ms_institutions WHERE user_id = ?", 
				$this->request->user->get("user_id"));
			$this->view->setVar("institution_count", 
				$q_institutions->numRows());
			
			$va_projects = $this->opo_project->getProjectsForMember(
				$this->request->user->get("user_id"));
			$this->view->setVar("num_projects", sizeof($va_projects));
			$this->view->setVar("media_counts", 
				$this->opo_project->getProjectMediaCounts());
			$this->view->setVar("media_file_counts", 
				$this->opo_project->getProjectMediaFileCounts());

			// Get entity data
			if ($vs_entity_type == 's') {
				switch ($vs_specimens_group_by) {
					case 'u':
						$va_specimens_by_taxonomy = $this->opo_project->
							getProjectSpecimensNestTaxonomy(null, 0);
						$va_entity = $va_specimens_by_taxonomy['specimen'];
						$vn_count = $va_specimens_by_taxonomy['numSpecimen'];
						$vb_entity_nest = 1;
						break;
					case 'v':
						$va_specimens_by_taxonomy = $this->opo_project->
							getProjectSpecimensNestTaxonomy(null, 1);
						$va_entity = $va_specimens_by_taxonomy['specimen'];
						$vn_count = $va_specimens_by_taxonomy['numSpecimen'];
						$vb_entity_nest = 1;
						break;
					default:
						switch ($vs_specimens_group_by) {
							case 'n':
								$vs_order_by = 'number';
								break;
							case 't':
								$vs_order_by = 'taxon';
								break;
							case 'a':
								$vs_order_by = 'added';
								break;
							case 'm':
								$vs_order_by = 'modified';
								break;
							default:
								$vs_order_by = 'number';
								break;
						}
						$va_entity = $this->opo_project->
							getProjectSpecimens(null, $vs_order_by);
						$vn_count = is_array($va_entity) ? 
							sizeof($va_entity) : 0;
						$vb_entity_nest = 0;
						break;
				}
			} elseif ($vs_entity_type == 'm') {
				switch ($vs_specimens_group_by) {
					case 'u':
						$va_media_by_taxonomy = $this->opo_project->
							getProjectMediaNestTaxonomy(null, 0);
						$va_entity = $va_media_by_taxonomy['media'];
						$vn_count = $va_media_by_taxonomy['numMedia'];
						$vb_entity_nest = 1;
						break;
					case 'v':
						$va_media_by_taxonomy = $this->opo_project->
							getProjectMediaNestTaxonomy(null, 1);
						$va_entity = $va_media_by_taxonomy['media'];
						$vn_count = $va_media_by_taxonomy['numMedia'];
						$vb_entity_nest = 1;
						break;
					default:
						switch ($vs_specimens_group_by) {
							case 'n':
								$vs_order_by = 'number';
								break;
							case 't':
								$vs_order_by = 'taxon';
								break;
							case 'a':
								$vs_order_by = 'added';
								break;
							case 'm':
								$vs_order_by = 'modified';
								break;
							default:
								$vs_order_by = 'number';
								break;
						}
						$qr = $this->opo_project->
							getProjectMedia(null, $vs_order_by);
						$va_entity = array();
						$t_media = new ms_media();
						while ($qr->nextRow()) {
							$va_media = $qr->getRow();
							if(!isset($va_entity[$va_media['media_id']])) {
								$va_media['preview'] = 
									$t_media->getPreviewMediaFile(
										$va_media['media_id']); 
								$va_entity[$va_media['media_id']] = $va_media;
							}
						}
						$vn_count = is_array($va_entity) ? 
							sizeof($va_entity) : 0;
						$vb_entity_nest = 0;
						break;
				}
			} 

			$this->view->setVar('va_entity', $va_entity);
			$this->view->setVar('vn_count', $vn_count);
			$this->view->setVar('vb_entity_nest', $vb_entity_nest);

 			$this->render('Dashboard/dashboard_html.php');
 		}
 		# -------------------------------------------------------
 		public function batchGeneralSave() {
 			// Entity viewer batch edit general media group options

			// Error and message variables
			$va_nonbib_attempt = array();
			$va_errors = array();
			$va_success = array();
			$vs_message = array();

			// Form fields
			$section_f = array(
				"bibliography" => array("bibliography_id"), 
				"media_citation" => array("media_citation_instruction1", 
					"media_citation_instruction2", "media_citation_instruction3"), 
				"copyright" => array("copyright_permission", "copyright_license", 
					"copyright_info"), 
				"grant_support" => array("grant_support"));
			$f_type = array(
				"bibliography_id" => pInteger, 
				"media_citation_instruction1" => pString, 
				"media_citation_instruction2" => pString, 
				"media_citation_instruction3" => pString, 
				"copyright_permission" => pInteger, 
				"copyright_license" => pInteger, 
				"copyright_info" => pString, 
				"grant_support" => pString);

			$f_val = array();
			foreach ($f_type as $f => $type) {
					$f_val[$f] = $this->request->getParameter($f, $type);
			}
			
			// Save logic
			// TODO: Validation for all form fields being entered within a sub-category
			// TODO: Validation for everything being empty
			$va_media_ids = $this->request->getParameter('media_ids', pArray);
			if(!is_array($va_media_ids)){
				$this->notification->addNotification(
					"You did not select any media groups.", 
					__NOTIFICATION_TYPE_ERROR__);
			}else{
				$t_media = new ms_media();
				$t_bib_link = new ms_media_x_bibliography();
				foreach ($va_media_ids as $media_id) {
					$va_item_errors = array();
					$t_media->load($media_id);
					$media_set = FALSE;
					foreach ($section_f as $section => $f) {
						// Special case for bibliographic citation
						if ($section == "bibliography" && 
							$f_val['bibliography_id']) {
							$t_bib_link->load(
								array("bibref_id" => $f_val['bibliography_id'], 
									"media_id" => $media_id));
							if (!$t_bib_link->get("link_id")) {
								$t_bib_link->set("bibref_id", 
									$f_val['bibliography_id']);
								$t_bib_link->set("media_id", $media_id);
								$t_bib_link->set("user_id", 
									$this->request->user->get("user_id"));	
								if ($t_bib_link->numErrors() > 0) {
									foreach ($t_bib_link->getErrors() as $vs_e) {
										$va_item_errors["bibliography"][] = $vs_e;
									}
								}

								if (!array_key_exists("bibliography", 
									$va_item_errors)) {
									// do insert
									$t_bib_link->setMode(ACCESS_WRITE);
									$t_bib_link->insert();

									if ($t_bib_link->numErrors()) {
										foreach ($t_bib_link->getErrors() as $vs_e) {  
											$va_errors["bibliography"] = 
												$va_errors["bibliography"].
												"M".$media_id." ".$vs_e.", ";
										}
									} else {
										$va_success[] = "bibliography";
									}
								}else{
									$va_errors["bibliography"] = 
										$va_errors["bibliography"].
										"M".$media_id." ".
										implode(", ", 
											$va_item_errors["bibliography"]).
										"; ";
								}
							}
						} else {
							// All non-bibliographic citation cases
							$section_f_val = array_intersect_key($f_val, 
								array_flip($f));
							$section_attempt = FALSE;	
							foreach ($section_f_val as $key => $value) {
								if (($key == 'media_citation_instruction2' && $value == 'originally appearing in') 
									|| ($key == 'media_citation_instruction3' && $value == ', the collection of which was funded by ')) {
									continue;
								}
								if ($value) {
									$media_set = TRUE;
									$section_attempt = TRUE;
									$t_media->set($key, $value);

									if ($t_media->numErrors() > 0) {
										foreach ($t_media->getErrors() as $vs_e) {
											$va_item_errors[$section][] = $vs_e;
										}
										$t_media->clearErrors();
									}
								}
							}
							if ($section_attempt) {
								$va_nonbib_attempt[] = $section;
							}
						}
					}

					// Was there an attempt to change any nonbib fields?
					if ($media_set == TRUE) {
						if (sizeof($va_item_errors) == 0) {
							// do update
							$t_media->setMode(ACCESS_WRITE);
							$t_media->update();
								
							if ($t_media->numErrors()) {
								foreach ($t_media->getErrors() as $vs_e) {  
									$va_errors["general"] = $va_errors["general"].
									"M".$media_id." ".$vs_e.", ";
								}
							}else{
								$va_success = array_merge($va_success, 
									$va_nonbib_attempt);
							}
						}else{
							foreach ($va_item_errors as $key => $value) {
								$va_errors[$key] = $va_errors[$key].
									"M".$media_id." ".
									implode(", ", $va_item_errors[$key])."; ";
							}
						}
					}
				}	

				// Construct error and success messages
				if (sizeof($va_errors) > 0) {
					$vs_message = "There were errors.";
					foreach ($va_errors as $key => $value) {
						$vs_message = $vs_message." ".$key.": ".$value;
					}
					$this->notification->addNotification($vs_message, 
						__NOTIFICATION_TYPE_ERROR__);
				}elseif (sizeof($va_success) > 0) {
					$vs_message = "General details successfully updated for 
						selected media groups.";
					$this->notification->addNotification($vs_message, 
						__NOTIFICATION_TYPE_INFO__);
				}else {
					$vs_message = "No changes were made.";
					$this->notification->addNotification($vs_message, 
						__NOTIFICATION_TYPE_INFO__);
				}
			}
			$this->dashboard();
		}
		# -------------------------------------------------------
		public function batchScanOriginSave() {
			// Error and message variables
			$va_attempt = array();
			$va_errors = array();
			$va_success = array();
			$vs_message = "";

			// Form fields
			$f_types = array(
				"facility_id" => pInteger, 
				"scanner_id" => pInteger, 
				"scanner_x_resolution" => pFloat, 
				"scanner_y_resolution" => pFloat, 
				"scanner_z_resolution" => pFloat, 
				"scanner_voltage" => pFloat, 
				"scanner_amperage" => pFloat, 
				"scanner_watts" => pFloat, 
				"scanner_exposure_time" => pFloat, 
				"scanner_filter" => pString, 
				"scanner_projections" => pString, 
				"scanner_frame_averaging" => pString, 
				"scanner_wedge" => pString, 
				"scanner_calibration_shading_correction" => pInteger, 
				"scanner_calibration_flux_normalization" => pInteger, 
				"scanner_calibration_geometric_calibration" => pInteger, 
				"scanner_calibration_description" => pString, 
				"scanner_technicians" => pString);

			$f_val = array();
			foreach ($f_types as $f => $ftype) {
				$f_val[$f] = $this->request->getParameter($f, $ftype);
			}

			// Save logic
			$va_media_ids = $this->request->getParameter('media_ids', pArray);
			if(!is_array($va_media_ids)){
				$this->notification->addNotification(
					"You did not select any media groups.", 
					__NOTIFICATION_TYPE_ERROR__);
			}else{
				$t_media = new ms_media();
				foreach ($va_media_ids as $media_id) {
					$va_item_errors = array();
					$t_media->load($media_id);
					$media_set = FALSE;
					foreach($f_val as $f_name => $val) {
						if ($val) {
							$media_set = TRUE;
							$va_attempt[] = $f_name;
							$t_media->set($f_name, $val);
							if ($t_media->numErrors() > 0) {
								foreach ($t_media->getErrors() as $vs_e) {
										$va_item_errors[$f_name][] = $vs_e;
									}
								$t_media->clearErrors();	
							}
						}
					}

					// Was there an attempt to update any fields?
					if ($media_set == TRUE) {
						if (sizeof($va_item_errors) == 0) {
							// Do update
							$t_media->setMode(ACCESS_WRITE);
							$t_media->update();
								
							if ($t_media->numErrors()) {
								foreach ($t_media->getErrors() as $vs_e) {  
									$va_errors["general"] = 
										$va_errors["general"].
										"M".$media_id." ".$vs_e.", ";
								}
							}else {
								$va_success = array_merge($va_success, 
									$va_attempt);
							}
						}else{
							foreach ($va_item_errors as $key => $value) {
								$va_errors[$key] = $va_errors[$key].
									"M".$media_id." ".
									implode(", ", $va_item_errors[$key])."; ";
							}
						}
					}
				}

				// Construct error and success messages
				if (sizeof($va_errors) > 0) {
					$vs_message = "There were errors.";
					foreach ($va_errors as $key => $value) {
						$vs_message = $vs_message." ".$key.": ".$value;
					}
					$this->notification->addNotification($vs_message, 
						__NOTIFICATION_TYPE_ERROR__);
				}else{
					if (sizeof($va_success) > 0) {
						$vs_message = "Scan origin details successfully updated 
							for selected media groups.";
						$this->notification->addNotification($vs_message, 
							__NOTIFICATION_TYPE_INFO__);
					}
				}
				
			}
			$this->dashboard();
		}
 		# -------------------------------------------------------
 		public function publishAllMedia() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			$pn_published = $this->request->getParameter('published', pInteger);
 			if($this->opn_project_id && $pn_published){
 				$vn_num_published = $this->opo_project->publishAllProjectMedia($pn_published);	
 				$t_media = new ms_media();
 				if($vn_num_published > 0) {
 					$this->notification->addNotification(_t('Published %1 media with setting: %2', $vn_num_published, $t_media->formatPublishedText($pn_published)), __NOTIFICATION_TYPE_INFO__);
 				} else {
 					$this->notification->addNotification(_t('Could not publish media'), __NOTIFICATION_TYPE_ERROR__);
 				}
 			}
 			$this->dashboard();
 		}
 		# -------------------------------------------------------
 		public function publishAllMediaFiles() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			if($this->opn_project_id){
 				$vn_num_published = $this->opo_project->publishAllProjectMediaFiles();	
 				if($vn_num_published > 0) {
 					$this->notification->addNotification(_t('Published %1 media files', $vn_num_published), __NOTIFICATION_TYPE_INFO__);
 				} else {
 					$this->notification->addNotification(_t('Could not publish media files'), __NOTIFICATION_TYPE_ERROR__);
 				}
 			}
 			$this->dashboard();
 		}
 		# -------------------------------------------------------
 		public function ApproveDownloadRequest() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}

 			$pn_user_id = $this->request->user->get('user_id');
 			$pn_request_id = $this->request->getParameter('request_id', pInteger);
 			$t_req = new ms_media_download_requests($pn_request_id);
 			$t_media = new ms_media($t_req->get('media_id'));

 			// Is user reviewer for media? Else is user project member?
 			if ($t_media->userCanApproveDownloadRequest($pn_user_id)){
 				$this->MarkDownloadRequest(1);
 			}	
 		}
 		# -------------------------------------------------------
 		public function DenyDownloadRequest() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			
 			$pn_user_id = $this->request->user->get('user_id');
 			$pn_request_id = $this->request->getParameter('request_id', pInteger);
 			$t_req = new ms_media_download_requests($pn_request_id);
 			$t_media = new ms_media($t_req->get('media_id'));

 			// Is user reviewer for media? Else is user project member?
 			if ($t_media->userCanApproveDownloadRequest($pn_user_id)){
 				$this->MarkDownloadRequest(2);
 			}
 		}
 		# -------------------------------------------------------
 		public function MarkDownloadRequest($pn_value, $pn_request_id = null, $vb_dont_load_view = false) {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			#if($this->opn_project_id){
 				if(!$pn_request_id){
 					$pn_request_id = $this->request->getParameter('request_id', pInteger);
 				}
 				
 				$t_req = new ms_media_download_requests($pn_request_id);
 				$t_media = new ms_media($t_req->get('media_id'));
 				$t_project = new ms_projects($t_media->get("project_id"));
 				if (($t_media->get('project_id') == $this->opn_project_id) || ($this->opo_project->isFullAccessMember($this->request->user->get("user_id"), $t_media->get('project_id')))) {
 					$t_req->setMode(ACCESS_WRITE);
 					$t_req->set('status', $pn_value);
 					$t_req->update();
 					
 					if (!$t_req->numErrors()) {
 						// send mail
						$t_user = new ca_users($t_req->get('user_id'));
						if ($vs_email = $t_user->get('email')) {
							$t_project = new ms_projects($t_media->get('project_id'));
							switch($pn_value) {
								case 1:
									caSendMessageUsingView($this->request, $vs_email, 'do-not-reply@morphosource.org', "[Morphosource] APPROVED request for download of media M".$t_req->get('media_id'), 'user_download_request_approved_notification.tpl', array(
										'user' => $t_user,
										'media' => $t_media,
										'project' => $t_project,
										'downloadRequest' => $t_req,
										'request' => $this->request
									));
									break;
								case 2:
									caSendMessageUsingView($this->request, $vs_email, 'do-not-reply@morphosource.org', "[Morphosource] DENIED request for download of media M".$t_req->get('media_id'), 'user_download_request_denied_notification.tpl', array(
										'user' => $t_user,
										'media' => $t_media,
										'project' => $t_project,
										'downloadRequest' => $t_req,
										'request' => $this->request
									));
									break;
							}
						}
 					}
 				}
 			#}
 			
 			if(!$vb_dont_load_view){
				if($this->request->getParameter('manage_all', pInteger)){
					$this->manageAllDownloadRequests();
				}else{
					$this->render('Dashboard/pending_download_requests_html.php');
				}
			}
 		}
 		# -------------------------------------------------------
 		public function ApproveMediaMovementRequest() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			$this->MarkMediaMovementRequest(1);
 		}
 		# -------------------------------------------------------
 		public function DenyMediaMovementRequest() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			$this->MarkMediaMovementRequest(2);
 		}
 		# -------------------------------------------------------
 		public function MarkMediaMovementRequest($pn_value) {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			if($this->opn_project_id){
 				$vn_request_id = $this->request->getParameter('request_id', pInteger);
 				$t_req = new ms_media_movement_requests($vn_request_id);
 				$t_media = new ms_media($t_req->get('media_id'));
				$t_req->setMode(ACCESS_WRITE);
				$t_req->set('status', $pn_value);
				$t_req->update();
				
				if (!$t_req->numErrors()) {
					// send mail
					$t_user = new ca_users($t_req->get('user_id'));
					if ($vs_email = $t_user->get('email')) {
						switch($pn_value) {
							case 1:
								caSendMessageUsingView($this->request, $vs_email, 'do-not-reply@morphosource.org', "[Morphosource] APPROVED request for ".(($t_req->get('type') == 1) ? "move" : "share")." of media M".$t_req->get('media_id'), 'move_media_request_approved_notification.tpl', array(
									'user' => $this->request->user,
									'media' => $t_media,
									'project' => $this->opo_project,
									'movementRequest' => $t_req,
									'request' => $this->request
								));
								
								$va_errors = array();
								if($t_req->get("type") == 1){
									# --- MOVE		
									$t_move_project = new ms_projects($t_req->get("to_project_id"));
									# --- first share
									$t_media_x_projects = new ms_media_x_projects();
									# --- is there already a share record for this item?
									$t_media_x_projects->load(array("media_id" => $t_media->get("media_id"), "project_id" => $t_media->get("project_id")));
									if(!$t_media_x_projects->get("link_id")){
										$t_media_x_projects->set("media_id",$t_media->get("media_id"));
										$t_media_x_projects->set("project_id",$t_media->get("project_id"));
										if ($t_media_x_projects->numErrors() == 0) {
											# do insert
											$t_media_x_projects->setMode(ACCESS_WRITE);
											$t_media_x_projects->insert();
											if ($t_media_x_projects->numErrors()) {
												foreach ($t_media_x_projects->getErrors() as $vs_e) {  
													$va_errors["general"] = "Could not share media: ".join(", ", $t_media_x_projects->getErrors());
												}
											}
										}else{
											$va_errors["general"] = "Could share media: ".join(", ", $t_media_x_projects->getErrors());
										}
									}
									# --- move
									if(sizeof($va_errors) == 0){
										# --- if there is a share record, delete it first
										$t_media_x_projects = new ms_media_x_projects();
										# --- is there already a share record for this item?
										$t_media_x_projects->load(array("media_id" => $t_media->get("media_id"), "project_id" => $t_move_project->get("project_id")));
										if($t_media_x_projects->get("link_id")){
											$t_media_x_projects->setMode(ACCESS_WRITE);
											$t_media_x_projects->delete();
										}
										$t_media->set("user_id", $t_move_project->get("user_id"));
										$t_media->set('project_id', $t_move_project->get("project_id"));
										# do update
										$t_media->setMode(ACCESS_WRITE);
										$t_media->update();
										if ($t_media->numErrors()) {
											foreach ($t_media->getErrors() as $vs_e) {  
												$va_errors["general"] = $vs_e;
											}
										}else{
											$vs_message = "Successfully moved media";
										}
									}
								}else{
									# --- share
									$t_media_x_projects = new ms_media_x_projects();
									$t_media_x_projects->load(array("media_id" => $t_media->get("media_id"), "project_id" => $t_media->get("project_id")));
									if(!$t_media_x_projects->get("link_id")){
										$t_media_x_projects->set("media_id",$t_media->get("media_id"));
										$t_media_x_projects->set("project_id",$this->opo_project->get("project_id"));
										if ($t_media_x_projects->numErrors() == 0) {
											# do insert
											$t_media_x_projects->setMode(ACCESS_WRITE);
											$t_media_x_projects->insert();
											if ($t_media_x_projects->numErrors()) {
												foreach ($t_media_x_projects->getErrors() as $vs_e) {  
													$va_errors["general"] = "Could not share media: ".join(", ", $t_media_x_projects->getErrors());
												}
											}else{
												$vs_message = "Successfully shared media";
											}
										}else{
											$va_errors["general"] = "Could not share media: ".join(", ", $t_media_x_projects->getErrors());
										}
									}else{
										$vs_message = "Successfully shared media";
									}								
								}
								if($va_errors["general"]){
									$this->view->setVar("move_media_message", "Could not ".(($t_req->get("type") == 1) ? "move" : "share")." media:".$va_errors["general"]);
								}else{
									$this->view->setVar("move_media_message", $vs_message);
								}
								
							break;
							# ------------------------------------------------------
							case 2:
								caSendMessageUsingView($this->request, $vs_email, 'do-not-reply@morphosource.org', "[Morphosource] DENIED request for ".(($t_req->get('type') == 1) ? "move" : "share")." of media M".$t_req->get('media_id'), 'move_media_request_denied_notification.tpl', array(
									'user' => $this->request->user,
									'media' => $t_media,
									'project' => $this->opo_project,
									'movementRequest' => $t_req,
									'request' => $this->request
								));
								$this->view->setVar("move_media_message", "Request was denied");
							break;
							# ------------------------------------------------------
						}
					}
				}
 			}
 			
 			$this->render('Dashboard/media_movement_requests_html.php');
 		}
 		# -------------------------------------------------------
 		public function requestFullAccess(){
 			$vs_message = $this->request->getParameter('message', pString);
 			if(!$vs_message){
 				$this->notification->addNotification(_t('Please enter a message describing the project you would like to create.'), __NOTIFICATION_TYPE_INFO__);
 				$this->projectList();
 				return;
 			}
 			$t_user = $this->request->user;
 			$t_user->set("userclass", 50);
 			$t_user->setMode(ACCESS_WRITE);
			$t_user->update();
			if($t_user->numErrors()) {
				$this->notification->addNotification(_t('Your request could not be sent. '.join("; ", $t_user->getErrors())), __NOTIFICATION_TYPE_INFO__);
			}else{
 				# -- generate mail text from template to notifiy administrator - get both html and text versions
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/request_full_access.tpl");
				$vs_mail_message_text = ob_get_contents();
				ob_end_clean();
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/request_full_access_html.tpl");
				$vs_mail_message_html = ob_get_contents();
				ob_end_clean();
				if(caSendmail($this->request->config->get("contributor_request_email"), "do-not-reply@morphosource.org", _t("User request to contribute to MorphoSource"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
					$this->notification->addNotification(_t('Your request was sent.'), __NOTIFICATION_TYPE_INFO__);
					$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
				}else{
					$this->notification->addNotification(_t('Your request could not be sent at this time.'), __NOTIFICATION_TYPE_INFO__);
				}
			}
 			
 		
 		}
 		# -------------------------------------------------------
 		public function manageAllDownloadRequests(){
 			$va_all_requests = $this->opo_project->getDownloadRequestsForUser(
 				$this->request->user->get("user_id"), 
 				array('status' => __MS_DOWNLOAD_REQUEST_NEW__));
 			$this->view->setVar("all_requests", $va_all_requests);

 			$this->render('Dashboard/manage_all_download_requests_html.php');
 		}
 		# -------------------------------------------------------
 		public function approveAllDownloadRequests(){
 			$pn_user_id = $this->request->user->get("user_id");
 			$va_all_requests = $this->opo_project->getDownloadRequestsForUser(
 				$pn_user_id, array('status' => __MS_DOWNLOAD_REQUEST_NEW__));

 			$t_req = new ms_media_download_requests();
 			$t_media = new ms_media();

 			$vb_failure = 0;
 			foreach($va_all_requests as $va_request){
 				$t_req->load($va_request['request_id']);
 				$t_media->load($t_req->get('media_id'));
 				if ($t_media->userCanApproveDownloadRequest($pn_user_id)) {
 					$this->MarkDownloadRequest(1, $va_request["request_id"], true);
 				} else {
 					$vb_failure = 1;
 				}
 			}

			if ($vb_failure) {
				$this->notification->addNotification(
					_t('Not authorized to approve one or more download requests'),
					__NOTIFICATION_TYPE_ERROR__);
			} else {
				$this->view->setVar("approval_success", 1);
			}

 			$this->render('Dashboard/manage_all_download_requests_html.php');
 		}
  		# -------------------------------------------------------
  		public function manageDownloadsApproveDeny(){
  			$va_request_ids = $this->request->getParameter('request_ids', pArray);
  			$vs_mode = $this->request->getParameter('approve_or_deny', pString);
  			$pn_user_id = $this->request->user->get("user_id");

  			if (!$vs_mode || (sizeof($va_request_ids) == 0)){
  				$this->manageAllDownloadRequests();
  				return;
  			}

  			$t_req = new ms_media_download_requests();
 			$t_media = new ms_media();

 			$vb_failure = 0;
 			foreach($va_request_ids as $vn_request_id){
 				$t_req->load($vn_request_id);
 				$t_media->load($t_req->get('media_id'));
 				if ($t_media->userCanApproveDownloadRequest($pn_user_id)) {
 					if ($vs_mode == 'approve'){
 						$this->MarkDownloadRequest(1, $vn_request_id, true);
 					} elseif ($vs_mode == 'deny'){
 						$this->MarkDownloadRequest(2, $vn_request_id, true);
 					}else{
 						$vb_failure = 1;
 						break;
 					}
 				} else {
 					$vb_failure = 1;
 					break;
 				}
 			}

 			if ($vb_failure) {
				$this->notification->addNotification(
					_t('Not authorized to approve one or more download requests'),
					__NOTIFICATION_TYPE_ERROR__);
			} else {
				$vs_verb = ($vs_mode == 'approve' ? 'approved' : 'denied');
				$notif_txt = sizeof($va_request_ids)
					._t(' download requests successfully ').$vs_verb;
				$this->notification->addNotification($notif_txt, __NOTIFICATION_TYPE_ERROR__);
			}

			$this->manageAllDownloadRequests();
  		}
  		# -------------------------------------------------------
 		function specimenByTaxonomy() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			JavascriptLoadManager::register("cycle");
			if(!$this->opn_project_id){
				$this->dashboard();
				return;
			}
			$vn_taxon_id = $this->request->getParameter('taxon_id', pInteger);
			if(!$vn_taxon_id){
				$this->dashboard();
				return;
			}
			# --- are we showing by genus or species?
			$vs_specimens_group_by = 
				$this->request->getParameter('specimens_group_by', pString);
			if(!in_array($vs_specimens_group_by, array("genus", "species", "ht_family"))){
				$vs_specimens_group_by = "genus";
			}
			$this->request->session->setVar('sBT_taxon_term', 
				$vs_specimens_group_by);

			# --- select the genus or taxa we want to show specimen for
			$o_db = new Db();
			$q_taxonomy = $o_db->query("SELECT ".$vs_specimens_group_by." FROM ms_taxonomy_names WHERE taxon_id = ?", $vn_taxon_id);
			$vs_taxon = "";
			if($q_taxonomy->numRows()){
				while($q_taxonomy->nextRow()){
					$vs_taxon = $q_taxonomy->get($vs_specimens_group_by);
				}
			}
			# --- get the specimen
			$va_specimens_by_taxomony = $this->opo_project->getProjectSpecimensByTaxonomy(null, $vs_specimens_group_by, array("taxonomy_term" => $vs_taxon, "taxonomy_type" => $vs_specimens_group_by));
			
			$this->view->setVar("specimens_by_taxomony", $va_specimens_by_taxomony);
			$this->view->setVar("taxomony_term", $vs_taxon);
			$this->view->setVar("taxon_id", $vn_taxon_id);
			
			$this->render('Dashboard/specimens_by_taxonomy_html.php');
		}
  		# -------------------------------------------------------
 		function specimenWithoutTaxonomy() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			JavascriptLoadManager::register("cycle");
			if(!$this->opn_project_id){
				$this->dashboard();
				return;
			}
			# --- are we showing those missing genus, species, or family?
			$vs_specimens_group_by = 
				$this->request->getParameter('specimens_group_by', pString);
			if(!in_array($vs_specimens_group_by, array("genus", "species", "ht_family"))){
				$vs_specimens_group_by = "genus";
			}
			$vs_specimens_group_by_display = $vs_specimens_group_by;
			if($vs_specimens_group_by == "ht_family"){
				$vs_specimens_group_by_display = "family";
			}
			# --- 
			$o_db = new Db();
			
			# --- get the specimen
			$va_specimens_by_taxomony = $this->opo_project->getProjectSpecimenWithoutTaxonomy(null, null, $vs_specimens_group_by);
			
			$this->view->setVar("specimens_by_taxomony", $va_specimens_by_taxomony);
			$this->view->setVar("taxomony_term", $vs_specimens_group_by);
			$this->view->setVar("taxomony_term_display", $vs_specimens_group_by_display);
			
			$this->render('Dashboard/specimen_without_taxonomy_html.php');
		}
		# -------------------------------------------------------
		function assignAllMediaFileDOIs() {
			if (!$this->request->user->canDoAction("is_administrator")) {
				$this->dashboard();
				return;
			}

			$va_project_media = $this->opo_project->getProjectMedia(true);
			

			while ($va_project_media->nextRow()) {
				$va_media = $va_project_media->getRow();
				$t_media = new ms_media($va_media['media_id']);
				$va_media_files = $t_media->getMediaFiles();
				foreach ($va_media_files as $t_media_file) {
					$t_user = new ca_users((
						$vn_user_id = $t_media_file->get('user_id') ? 
						$t_media_file->get('user_id') : 
						$this->opo_project->get('user_id')));
					
					$va_doi = $t_media_file->getDOI(
						$t_user->get('fname'), 
						$t_user->get('lname'));

					if (!$va_doi["success"]) {
						$this->notification->addNotification(
							$va_doi["error"], __NOTIFICATION_TYPE_ERROR__);
					}
				}
			}

			$this->dashboard();
		}		
 	}
 ?>