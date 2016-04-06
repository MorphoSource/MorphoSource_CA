<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/ProjectController.php
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
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class ProjectController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			
			protected $ops_name_singular;
			protected $ops_name_plural;
			protected $ops_primary_key;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_project = new ms_projects();
 			# --- only load current project if you're not making a new one
 			$vn_new_project = $this->request->getParameter('new_project', pInteger);
 			if($vn_new_project){
 				$this->request->session->setVar('current_project_id', '');
 				$this->request->session->setVar('current_project_name', '');
 			}else{
				# --- is there a project already selected, are we selecting a project
				$vn_select_project_id = $this->request->getParameter('select_project_id', pInteger);
				if($vn_select_project_id){
					# --- select project
					msSelectProject($this, $this->request);
				}
			}
 			if($this->request->session->getVar('current_project_id') && ($this->request->user->canDoAction("is_administrator") || $this->opo_project->isFullAccessMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id')))){
 				$this->opn_project_id = $this->request->session->getVar('current_project_id');
				$this->opo_project->load($this->opn_project_id);
				$this->ops_project_name = $this->opo_project->get("name");
				$this->view->setvar("project_id", $this->opn_project_id);
				$this->view->setvar("project_name", $this->ops_project_name);
 			}
			$this->view->setvar("project", $this->opo_project);
			
			$this->ops_name_singular = $this->opo_project->getProperty("NAME_SINGULAR");
			$this->ops_name_plural = $this->opo_project->getProperty("NAME_PLURAL");
			$this->ops_primary_key = $this->opo_project->getProperty("PRIMARY_KEY");
			$this->view->setvar("name_singular", $this->ops_name_singular);
			$this->view->setvar("name_plural", $this->ops_name_plural);
			$this->view->setvar("primary_key", $this->ops_primary_key);
 		} 		
 		# -------------------------------------------------------
 		public function listItems() {
			$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
 		}
 		# -------------------------------------------------------
 		function form() {
			# --- only project owner can edit project info - or if no project_id is selected this is a new form
			if ((!$this->opo_project->get("project_id")) || $this->request->user->canDoAction("is_administrator") || ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") == $this->request->user->get("user_id")))) {
				$this->render('Project/form_html.php');
				return;
 			}
 			$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
 		}
 		# -------------------------------------------------------
 		public function save() {
			# --- only project owner can edit project info
			if (!$this->request->user->canDoAction("is_administrator") && ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") != $this->request->user->get("user_id")))) {
				//$this->form();
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
				return;
 			}

			# get names of form fields
			$va_fields = $this->opo_project->getFormFields();
			$va_errors = array();
			# loop through fields
			
			while(list($vs_f,$va_attr) = each($va_fields)) {
				
				switch($vs_f) {
					case 'user_id':
						if(!$this->opo_project->get("project_id")){
							$this->opo_project->set($vs_f,$this->request->user->get("user_id"));
						}
						break;
					default:
						$this->opo_project->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
				}
				if ($this->opo_project->numErrors() > 0) {
					foreach ($this->opo_project->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}
		
			if (sizeof($va_errors) == 0) {
				# do insert or update
				$this->opo_project->setMode(ACCESS_WRITE);
				if ($this->opo_project->get($this->ops_primary_key)){
					$this->opo_project->update();
				} else {
					$this->opo_project->insert();
					if (!$this->opo_project->numErrors()) {
						# --- insert link btw user and project
						$t_project_users = new ms_project_users();
						$t_project_users->setMode(ACCESS_WRITE);
						$t_project_users->set("user_id", $this->request->getUserID());
						$t_project_users->set("project_id", $this->opo_project->get("project_id"));
						$t_project_users->set("membership_type", 1);
						$t_project_users->set("active", 1);
						$t_project_users->insert();
						if($t_project_users->numErrors()){
							$va_errors["general"] = join(", ", $t_project_users->getErrors());
						}else{
							msSelectProject($this, $this->request, $this->opo_project->get("project_id"));
						}
					}
				}
	
				if ($this->opo_project->numErrors()) {
					foreach ($this->opo_project->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$this->notification->addNotification("Saved project", __NOTIFICATION_TYPE_INFO__);
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "Dashboard"));
			}
 			 			
 		}
 		# -------------------------------------------------------
 		public function delete() {
			# --- only project owner can edit project info
			if (!$this->request->user->canDoAction("is_administrator") && ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") != $this->request->user->get("user_id")))) {
				//$this->form();
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
				return;
 			}
 			if($this->opo_project->numSpecimens()){
 				$this->notification->addNotification("You can not delete this project since it contains specimen", __NOTIFICATION_TYPE_INFO__);
				$this->form();
				return;
 			}

 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
				$this->opo_project->setMode(ACCESS_WRITE);
				$this->opo_project->set("deleted", 1);
				$this->opo_project->set("publication_status", 0);
				$this->opo_project->update();
				if ($this->opo_project->numErrors()) {
					foreach ($this->opo_project->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
					if(sizeof($va_errors) > 0){
						$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					}
					$this->form();
				}else{
					$this->notification->addNotification("Deleted project", __NOTIFICATION_TYPE_INFO__);
					$this->request->session->setVar('current_project_id', '');
 					$this->request->session->setVar('current_project_name', '');
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "Dashboard"));					
				}
				
			}else{
				$vn_num_institutions = "";
				
				# --- get the number of records associated with the project to warn the user before deleting
				$o_db = new Db();
				$q_num_facilites = $o_db->query("SELECT count(*) c FROM ms_facilities WHERE project_id = ?", $this->opn_project_id);
				$q_num_facilites->nextRow();
				$vn_num_facilities = $q_num_facilites->get("c");
				
				$q_num_specimens = $o_db->query("SELECT count(*) c FROM ms_specimens WHERE project_id = ?", $this->opn_project_id);
				$q_num_specimens->nextRow();
				$vn_num_specimens = $q_num_specimens->get("c");
				
				$q_num_media_groups = $o_db->query("SELECT count(*) c FROM ms_media WHERE project_id = ?", $this->opn_project_id);
				$q_num_media_groups->nextRow();
				$vn_num_media_groups = $q_num_media_groups->get("c");
				
				$q_num_media_files = $o_db->query("SELECT count(mf.media_file_id) c FROM ms_media_files mf INNER JOIN ms_media as m ON mf.media_id = m.media_id WHERE m.project_id = ?", $this->opn_project_id);
				$q_num_media_files->nextRow();
				$vn_num_media_files = $q_num_media_files->get("c");
				
				$vs_message = "Your project contains the following records: <ul style='width:200px; margin: 0px auto 0px auto;'>
								<li style='text-align:left; font-weight:normal;'>".$vn_num_media_groups." media group".(($vn_num_media_groups == 1) ? "" : "s")."</li>
								<li style='text-align:left; font-weight:normal;'>".$vn_num_media_files." media file".(($vn_num_media_files == 1) ? "" : "s")."</li>
								<li style='text-align:left; font-weight:normal;'>".$vn_num_specimens." specimen".(($vn_num_specimens == 1) ? "" : "s")."</li>
								<li style='text-align:left; font-weight:normal;'>".$vn_num_facilities." facilit".(($vn_num_facilities == 1) ? "y" : "ies")."</li>
								</ul><br/><br/>";
				if($vn_num_facilities || $vn_num_specimens){
					$vs_message .= "<b>Facilities and specimen will not be deleted since they can be used by other projects in the community.</b><br/><br/>";
				}
				
				$this->view->setVar("message", $vs_message);
				$this->view->setVar("item_name", $this->ops_project_name);
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>