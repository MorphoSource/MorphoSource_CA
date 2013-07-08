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
 			$vn_project_id = $this->request->getParameter('project_id', pInteger);
 			if($vn_project_id){
 				# --- select project
 				msSelectProject($this, $this->request);
 			}
 			if($this->request->session->getVar('current_project_id') && $this->opo_project->isMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id'))){
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
 			JavascriptLoadManager::register("cycle");
			if(!$this->opn_project_id){
				$this->projectList();
				return;
			}
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$this->view->setVar("num_projects", sizeof($va_projects));
			$this->view->setVar("media_counts", $this->opo_project->getProjectMediaCounts());
 			$this->render('Dashboard/dashboard_html.php');
 		}
 		# -------------------------------------------------------
 		public function publishAllMedia() {
 			if($this->opn_project_id){
 				$vn_num_published = $this->opo_project->publishAllProjectMedia();	
 				
 				if($vn_num_published > 0) {
 					$this->opo_project->setMode(ACCESS_WRITE);
 					$this->opo_project->set('published', 1);
 					$this->opo_project->update();
 					$this->notification->addNotification(_t('Published %1 media', $vn_num_published), __NOTIFICATION_TYPE_INFO__);
 					if ($this->opo_project->numErrors() > 0) {
 						$this->notification->addNotification(_t('Could not mark project as published: %1', join('; ', $this->opo_project->getErrors())), __NOTIFICATION_TYPE_ERROR__);
 					} 
 				} else {
 					$this->notification->addNotification(_t('Could not publish media'), __NOTIFICATION_TYPE_ERROR__);
 				}
 			}
 			$this->dashboard();
 		}
 		# -------------------------------------------------------
 		public function ApproveDownloadRequest() {
 			$this->MarkDownloadRequest(1);
 		}
 		# -------------------------------------------------------
 		public function DenyDownloadRequest() {
 			$this->MarkDownloadRequest(2);
 		}
 		# -------------------------------------------------------
 		public function MarkDownloadRequest($pn_value) {
 			if($this->opn_project_id){
 				$vn_request_id = $this->request->getParameter('request_id', pInteger);
 				$t_req = new ms_media_download_requests($vn_request_id);
 				$t_media = new ms_media($t_req->get('media_id'));
 				if ($t_media->get('project_id') == $this->opn_project_id) {
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
										'user' => $this->request->user,
										'media' => $t_media,
										'project' => $this->opo_project,
										'downloadRequest' => $t_req,
										'request' => $this->request
									));
									break;
								case 2:
									caSendMessageUsingView($this->request, $vs_email, 'do-not-reply@morphosource.org', "[Morphosource] DENIED request for download of media M".$t_req->get('media_id'), 'user_download_request_denied_notification.tpl', array(
										'user' => $this->request->user,
										'media' => $t_media,
										'project' => $this->opo_project,
										'downloadRequest' => $t_req,
										'request' => $this->request
									));
									break;
							}
						}
 					}
 				}
 			}
 			
 			$this->render('Dashboard/pending_download_requests_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>