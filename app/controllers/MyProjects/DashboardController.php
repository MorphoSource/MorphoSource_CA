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
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_movement_requests.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_projects.php");
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
			$vs_specimens_order_by = $this->request->getParameter('specimens_order_by', pString);
			
			if($vs_specimens_order_by){
				$this->request->session->setVar('specimens_order_by', $vs_specimens_order_by);
			}elseif($this->request->session->getVar('specimens_order_by')){
				$vs_specimens_order_by = $this->request->session->getVar('specimens_order_by');
			}else{
				$vs_specimens_order_by = "number";
			}
			$this->view->setVar("specimens_order_by", $vs_specimens_order_by);		
			
			$vs_specimens_group_by = $this->request->getParameter('specimens_group_by', pString);
			
			if($vs_specimens_group_by){
				$this->request->session->setVar('specimens_group_by', $vs_specimens_group_by);
			}elseif($this->request->session->getVar('specimens_group_by')){
				$vs_specimens_group_by = $this->request->session->getVar('specimens_group_by');
			}else{
				$vs_specimens_group_by = "specimen";
			}
			$this->view->setVar("specimens_group_by", $vs_specimens_group_by);		
			
			
			$o_db = new Db();
			$q_institutions = $o_db->query("SELECT * FROM ms_institutions WHERE user_id = ?", $this->request->user->get("user_id"));
			$this->view->setVar("institution_count", $q_institutions->numRows());
			
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$this->view->setVar("num_projects", sizeof($va_projects));
			$this->view->setVar("media_counts", $this->opo_project->getProjectMediaCounts());
			$this->view->setVar("media_file_counts", $this->opo_project->getProjectMediaFileCounts());
 			$this->render('Dashboard/dashboard_html.php');
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
 			$this->MarkDownloadRequest(1);
 		}
 		# -------------------------------------------------------
 		public function DenyDownloadRequest() {
 			if(!$this->request->user->isFullAccessUser()){
 				$this->projectList();
 				return;
 			}
 			$this->MarkDownloadRequest(2);
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
					$this->render('Dashboard/manage_all_download_requests_html.php');
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
 			$this->render('Dashboard/manage_all_download_requests_html.php');
 		}
 		# -------------------------------------------------------
 		public function approveAllDownloadRequests(){
 			$va_all_requests = $this->opo_project->getDownloadRequestsForUser($this->request->user->get("user_id"), array('status' => __MS_DOWNLOAD_REQUEST_NEW__));

 			foreach($va_all_requests as $va_request){
 				$this->MarkDownloadRequest(1, $va_request["request_id"], true);
 			}
 			$this->view->setVar("approval_success", 1);
 			$this->render('Dashboard/manage_all_download_requests_html.php');
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
			$vs_specimens_group_by = $this->request->session->getVar('specimens_group_by');
			if(!in_array($vs_specimens_group_by, array("genus", "species"))){
				$vs_specimens_group_by = "genus";
			}
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
			
			$this->render('Dashboard/specimens_by_taxonomy_html.php');
		}
  		# -------------------------------------------------------
			

 	}
 ?>