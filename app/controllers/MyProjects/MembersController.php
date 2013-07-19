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
 	require_once(__CA_MODELS_DIR__."/ca_users.php");
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
				msSelectProject($this, $this->request);
			}
 			if($this->request->session->getVar('current_project_id') && ($this->request->user->canDoAction("is_administrator") || $this->opo_project->isMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id')))){
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
 		public function index() {
 			$this->listForm();
 		}
 		# -------------------------------------------------------
 		public function listForm() {

			$this->view->setvar("project_users", $this->opo_project_users);
			$this->render('Members/members_html.php');
 		}
 		# -------------------------------------------------------
 		public function lookUpMember() {
			if($this->request->getParameter('form_submitted', pInteger)){
				$va_errors = array();
				$o_purifier = new HTMLPurifier();
				$ps_member_email = $o_purifier->purify($this->request->getParameter('member_email', pString));
				
				# --- check vars are set and email addresses are valid
				if(!$ps_member_email){
					$va_errors["member_email"] = _t("Please enter a valid email address");
				}else{
					$ps_member_email = trim($ps_member_email);
					if(!caCheckEmailAddress($ps_member_email)){
						$ps_member_email = "";
						$va_errors["member_email"] = _t("Please enter a valid email address");
					}else{
						# --- get the user id of the member if they are already registered in MorphoSource
						$t_user = new ca_users();
						$t_user->load(array("email" => $ps_member_email));
						$pn_member_user_id = $t_user->get("user_id");
						$ps_member_fname = $t_user->get("fname");
						$ps_member_lname = $t_user->get("lname");
						# --- if member exists, check if member is already linked to this project
						
						if($this->opo_project_users->load(array("user_id" => $pn_member_user_id, "project_id" => $this->opn_project_id))){
							$va_errors["member_email"] = _t("User with email %1 is already a member of this project", $ps_member_email);
						}
					}
				}
				if(sizeof($va_errors)){
					$this->view->setVar("errors", $va_errors);
					$this->render('Members/ajax_lookup_member_form_html.php');
				}else{
					$this->view->setVar("member_user_id", $pn_member_user_id);
					$this->view->setVar("member_email", $ps_member_email);
					$this->view->setVar("member_fname", $ps_member_fname);
					$this->view->setVar("member_lname", $ps_member_lname);				
					$this->render('Members/ajax_invite_member_form_html.php');
				}
			}else{
				$this->render('Members/ajax_lookup_member_form_html.php');
			}
 		}
 		# -------------------------------------------------------
 		public function inviteMember() {
			$ps_member_email = $this->request->getParameter('member_email', pString);
			$this->view->setVar("member_email", $ps_member_email);
			$pn_member_user_id = $this->request->getParameter('member_user_id', pInteger);
			$this->view->setVar("member_user_id", $pn_member_user_id);
			if($this->request->getParameter('form_submitted', pInteger)){
				$va_errors = array();
				$o_purifier = new HTMLPurifier();
				$ps_member_lname = $o_purifier->purify($this->request->getParameter('member_lname', pString));
				$ps_member_fname = $o_purifier->purify($this->request->getParameter('member_fname', pString));
				$ps_member_message = $o_purifier->purify($this->request->getParameter('member_message', pString));
				$this->view->setVar("member_message", $ps_member_message);
				$this->view->setVar("member_fname", $ps_member_fname);
				$this->view->setVar("member_lname", $ps_member_lname);
				if(!$pn_member_user_id){
					# --- check the fname lname has been sent
					if(!$ps_member_fname){
						$va_errors["member_fname"] = _t("Please enter the first name of the new member");
					}
					if(!$ps_member_lname){
						$va_errors["member_lname"] = _t("Please enter the last name of the new member");
					}
					if(sizeof($va_errors)){
						$this->view->setVar("errors", $va_errors);
						$this->render('Members/ajax_invite_member_form_html.php');
						return;
					}else{
						# --- add the new member
						$t_new_user = new ca_users();
						$t_new_user->setMode(ACCESS_WRITE);
						$t_new_user->set("fname", $ps_member_fname);
						$t_new_user->set("lname", $ps_member_lname);
						$t_new_user->set("email", $ps_member_email);
						$t_new_user->set("user_name", $ps_member_email);
						$t_new_user->set("active", 1);
						$t_new_user->set("userclass", 1);
						$vn_password = rand();
						$t_new_user->set("password", $vn_password);
						$t_new_user->set("confirmed_on", time());
						$t_new_user->insert();
						if($t_new_user->numErrors()){
							$va_errors["general"] = join(", ", $t_new_user->getErrors());
							$this->render('Members/ajax_invite_member_form_html.php');
							return;
						}else{
							$pn_member_user_id = $t_new_user->get("user_id");
						}
					}
				}else{
					$t_new_user = new ca_users($pn_member_user_id);
				}
				if($pn_member_user_id && (sizeof($va_errors) == 0)){				
					# --- insert link btw user and project
					$this->opo_project_users->setMode(ACCESS_WRITE);
					$this->opo_project_users->set("user_id", $pn_member_user_id);
					$this->opo_project_users->set("project_id", $this->opo_project->get("project_id"));
					$this->opo_project_users->set("membership_type", 1);
					$this->opo_project_users->set("active", 1);
					$this->opo_project_users->insert();
					if($this->opo_project_users->numErrors()){
						$va_errors["general"] = join(", ", $this->opo_project_users->getErrors());
						$this->render('Members/ajax_invite_member_form_html.php');
						return;
					}else{
						# --- get name and email of project admin to use as from info for email
						$o_db = new Db();
						$q_project_admin = $o_db->query("SELECT u.lname, u.fname, u.email FROM ca_users u INNER JOIN ms_projects p ON p.user_id = u.user_id WHERE p.project_id = ?", $this->opo_project->get("project_id"));
						if($q_project_admin->numRows()){
							$q_project_admin->nextRow();
							$vs_from_name = trim($q_project_admin->get("fname")." ".$q_project_admin->get("lname"));
							$vs_from_email = $q_project_admin->get("email");
						}
						$vs_member_name = trim($t_new_user->get("fname")." ".$t_new_user->get("lname"));
						# -- generate mail text from template - get both html and text versions
						ob_start();
						require($this->request->getViewsDirectoryPath()."/mailTemplates/invite_member.tpl");
						$vs_mail_message_text = ob_get_contents();
						ob_end_clean();
						ob_start();
						require($this->request->getViewsDirectoryPath()."/mailTemplates/invite_member_html.tpl");
						$vs_mail_message_html = ob_get_contents();
						ob_end_clean();
										
						if(caSendmail($ps_member_email, array($vs_from_email => $vs_from_name), _t("Invitation to MorphoSource Project"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
							$this->notification->addNotification(_t("The new member has been added to your project and sent an email notification."), "message");
						}else{
							$this->notification->addNotification(_t("The new member has been added to your project, but the notification email could not be sent."), "message");
							$va_errors_email_set["email"] = 1;
						}
						# --- returning nothing will trigger redirect to reload list of project members showing newly added member
						return;
					}
				}	
			}else{
				$this->render('Members/ajax_invite_member_form_html.php');
			} 		
 		}
 		# -------------------------------------------------------
 		public function delete() {
			# --- only project owner can edit project info
			if ($this->opo_project->get("project_id") && ($this->opo_project->get("user_id") != $this->request->user->get("user_id"))) {
				$this->form();
				return;
 			}

 			$vn_user_id = $this->request->getParameter('user_id', pInteger);
 			$this->view->setVar("user_id", $vn_user_id);
 			$this->view->setVar("primary_key", "user_id");
 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
				$this->opo_project_users->load(array("user_id" => $vn_user_id));
				$this->opo_project_users->setMode(ACCESS_WRITE);
				$this->opo_project_users->delete(false);
				if ($this->opo_project_users->numErrors()) {
					foreach ($this->opo_project_users->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
					if(sizeof($va_errors) > 0){
						$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					}
				}else{
					$this->notification->addNotification("Removed user", __NOTIFICATION_TYPE_INFO__);
				}	
				$this->listForm();
			}else{
				$t_user = new ca_users($vn_user_id);
				$this->view->setVar("item_name", trim($t_user->get("fname")." ".$t_user->get("lname")));
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>