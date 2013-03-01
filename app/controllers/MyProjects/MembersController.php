<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/MembersController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_project_users.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
	require_once(__CA_LIB_DIR__."/core/Parsers/htmlpurifier/HTMLPurifier.standalone.php");
 
 	class MembersController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			
			protected $opo_project_users;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_project = new ms_projects();
 			# --- is there a project already selected, are we selecting a project
			$vn_project_id = $this->request->getParameter('project_id', pInteger);
			if($vn_project_id){
				# --- select project
				msSelectProject($this->request);
			}
 			if($this->request->session->getVar('current_project_id') && $this->opo_project->isMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id'))){
 				$this->opn_project_id = $this->request->session->getVar('current_project_id');
				$this->opo_project->load($this->opn_project_id);
				$this->ops_project_name = $this->opo_project->get("name");
				$this->view->setvar("project_id", $this->opn_project_id);
				$this->view->setvar("project_name", $this->ops_project_name);
 			}
 			# --- only project owner can edit project info
			if ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") != $this->request->user->get("user_id"))) {
				$this->form();
				return;
 			}
			$this->view->setvar("project", $this->opo_project);
			$this->opo_project_users = new ms_project_users();
 		}
 		# -------------------------------------------------------
 		public function listForm() {

			$this->view->setvar("project_users", $this->opo_project_users);
			$this->render('Members/members_html.php');
 		}
 		# -------------------------------------------------------
 		public function lookUpMember() {
			if($this->request->getParameter('member_email', pString)){
				$va_errors = array();
				$o_purifier = new HTMLPurifier();
				$ps_member_email = $o_purifier->purify($this->request->getParameter('member_email', pString));
				
				# --- check vars are set and email addresses are valid
				$vs_member_email_process = array();
				if(!$ps_member_email){
					$va_errors["member_email"] = _t("Please enter a valid email address");
				}else{
					$ps_member_email = trim($ps_member_email);
					if(!caCheckEmailAddress($ps_member_email)){
						$ps_member_email = "";
						$va_errors["member_email"] = _t("Please enter a valid email address");
					}
				}
				if(sizeof($va_errors)){
					$this->view->setVar("errors", $va_errors);
					$this->render('Members/ajax_lookup_member_form_html.php');
				}else{
					// SETH ADDED THIS JUST TO HAVE SOME OUTPUT RETURNED 2/28/2013
					$this->view->setVar("errors", array("member_email" => "Let's do something for {$ps_member_email}"));
					$this->render('Members/ajax_lookup_member_form_html.php');
				}
			}else{
				$this->render('Members/ajax_lookup_member_form_html.php');
			}
 		}
 		# -------------------------------------------------------
 		public function addMember() {
			# --- insert link btw user and project
			$this->opo_project_users->setMode(ACCESS_WRITE);
			$this->opo_project_users->set("user_id", $this->request->getUserID());
			$this->opo_project_users->set("project_id", $this->opo_project->get("project_id"));
			$this->opo_project_users->set("membership_type", 1);
			$this->opo_project_users->set("active", 1);
			$this->opo_project_users->insert();
			if($this->opo_project_users->numErrors()){
				$va_errors["general"] = join(", ", $t_project_users->getErrors());
			}else{
				# --- send email invite to new member
				$ps_to_email = $this->request->getParameter('to_email', pString);
				# --- get name and email of project admin to use as from info for email
				$o_db = new Db();
				$q_project_admin = $o_db->query("SELECT u.lname, u.fname, u.email FROM ca_users u INNER JOIN ms_projects p ON p.user_id = u.user_id WHERE p.project_id = ?", $this->opo_project->get("project_id"));
				if($q_project_admin->numRows()){
					$q_project_admin->nextRow();
					$vs_from_name = trim($q_project_admin->get("fname")." ".$q_project_admin->get("lname"));
					$vs_from_email = $q_project_admin->get("email");
				}
				$ps_message = $this->request->getParameter('message', pString);
				
				$o_purifier = new HTMLPurifier();
				$ps_message = $o_purifier->purify($ps_message);
				
				
				# -- generate mail text from template - get both html and text versions
				ob_start();
				require($this->request->getViewsDirectoryPath()."/MyProjects/mailTemplates/invite_member_email_text.tpl");
				$vs_mail_message_text = ob_get_contents();
				ob_end_clean();
				ob_start();
				require($this->request->getViewsDirectoryPath()."/MyProjects/mailTemplates/invite_member_email_html.tpl");
				$vs_mail_message_html = ob_get_contents();
				ob_end_clean();
								
				if(caSendmail($ps_message, array($vs_from_email => $vs_from_name), _t("Invitation to MorphoSource Project"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
 					$this->notification->addNotification(_t("Your email was sent"), "message");
 				}else{
 					$this->notification->addNotification(_t("Your email could not be sent"), "message");
 					$va_errors_email_set["email"] = 1;
 				}

			}
 		
 		}
 		# -------------------------------------------------------
 		public function delete() {
			# --- only project owner can edit project info
			if ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") != $this->request->user->get("user_id"))) {
				$this->form();
				return;
 			}

 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
				$this->opo_project->setMode(ACCESS_WRITE);
				$this->opo_project->delete(true);
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
				$this->view->setVar("item_name", $this->ops_project_name);
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>