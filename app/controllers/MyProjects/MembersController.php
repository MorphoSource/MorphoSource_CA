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
			$vn_select_project_id = $this->request->getParameter('select_project_id', pInteger);
			if($vn_select_project_id){
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
			if ($this->opo_project->get("project_id") && (($this->opo_project->get("user_id") != $this->request->user->get("user_id")) && !$this->request->user->canDoAction("is_administrator"))) {
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
 		public function setNewAdmin() {
			if($pn_new_admin_id = $this->request->getParameter('new_admin_id', pInteger)){
				$t_old_admin = new ca_users($this->opo_project->get("user_id"));
				$t_new_admin = new ca_users($pn_new_admin_id);
				$this->opo_project->set('user_id', $pn_new_admin_id);
				if (sizeof($va_errors) == 0) {
					# do update
					$this->opo_project->setMode(ACCESS_WRITE);
					$this->opo_project->update();
					if ($this->opo_project->numErrors()) {
						foreach ($this->opo_project->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}
				}
				if(sizeof($va_errors) > 0){
					$this->notification->addNotification("Could not change project administrator".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					$this->view->setVar("errors", $va_errors);
					$this->form();
				}else{
					# -- generate mail text from template - get both html and text versions
					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/make_admin.tpl");
					$vs_mail_message_text = ob_get_contents();
					ob_end_clean();
					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/make_admin_html.tpl");
					$vs_mail_message_html = ob_get_contents();
					ob_end_clean();
									
					if(caSendmail($t_new_admin->get("email"), array($t_old_admin->get("email") => $t_old_admin->get("fname")." ".$t_old_admin->get("lname")), _t("Message from MorphoSource P".$this->opn_project_id), $vs_mail_message_text, $vs_mail_message_html, null, null)){
						$vs_message = $t_new_admin->get("fname")." ".$t_new_admin->get("lname")." has been notified via email that they are the new project admininstrator";
					}else{
						$vs_message = "Project administrator has been changed to ".$t_new_admin->get("fname")." ".$t_new_admin->get("lname");
					}

					$this->notification->addNotification($vs_message, __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "Dashboard"));
				}
			}else{
				$this->notification->addNotification("Could not change the project administrator", __NOTIFICATION_TYPE_ERROR__);
				$this->listForm();
			}
 		}
 		# -------------------------------------------------------
 		public function addUserManageDownloads() {
			if($pn_user_id = $this->request->getParameter('user_id', pInteger)){
				$t_user = new ca_users($pn_user_id);
				$t_user->addRoles(array("downloads"));
				$this->notification->addNotification("User will be notified of download requests", __NOTIFICATION_TYPE_ERROR__);			
			}else{
				$this->notification->addNotification("Could not add role manage_download_requests - user_id is not defined", __NOTIFICATION_TYPE_ERROR__);
			}
			$this->listForm();
 		}
 		# -------------------------------------------------------
 		public function removeUserManageDownloads() {
			if($pn_user_id = $this->request->getParameter('user_id', pInteger)){
				$t_user = new ca_users($pn_user_id);
				$t_user->removeRoles(array("downloads"));
				$this->notification->addNotification("User will no longer be notified of download requests", __NOTIFICATION_TYPE_ERROR__);			
			}else{
				$this->notification->addNotification("Could not remove role manage_download_requests - user_id is not defined", __NOTIFICATION_TYPE_ERROR__);
			}
			$this->listForm();
 		}
 		# -------------------------------------------------------
 		public function lookUpMember() {
			$this->view->setVar("member_email", $this->request->getParameter('member_email', pString));
			if($this->request->getParameter('form_submitted', pInteger)){
				$o_purifier = new HTMLPurifier();
				$va_errors = array();
				$ps_member_email = $o_purifier->purify($this->request->getParameter('member_email', pString));
				if(!$ps_member_email){
					$va_errors["member_email"] = "Please enter an email address";
				}else{
					$va_member_emails = array();
					$va_member_emails = explode(",",$ps_member_email);
					$va_new_members = array();
					$va_existing_members = array();
					
					foreach($va_member_emails as $vs_member_email){
						$vs_member_email = trim($vs_member_email);
						if(!caCheckEmailAddress($vs_member_email)){
							$va_errors["member_email"] = $va_errors["member_email"].$vs_member_email._t("is not a valid email address")."<br/>";
						}else{
							# --- get the user id of the member if they are already registered in MorphoSource
							$t_user = new ca_users();
							$t_user->load(array("email" => $vs_member_email));
							if($t_user->get("user_id")){
								# --- if member exists, check if member is already linked to this project							
								if($this->opo_project_users->load(array("user_id" => $t_user->get("user_id"), "project_id" => $this->opn_project_id))){
									$va_errors["member_email"] = $va_errors["member_email"]._t("User with email %1 is already a member of this project", $vs_member_email)."<br/>";
								}else{
									$va_existing_members[] = array("email" => $vs_member_email, "user_id" => $t_user->get("user_id"), "fname" => $t_user->get("fname"), "lname" => $t_user->get("lname"));
								}
							}else{
								$va_new_members[] = $vs_member_email;
							}
													
						}
					}
				}
				if(sizeof($va_errors)){
					$this->view->setVar("member_email", $ps_member_email);
					$this->view->setVar("errors", $va_errors);
					$this->render('Members/ajax_lookup_member_form_html.php');
				}else{
					$this->view->setVar("new_members", $va_new_members);
					$this->view->setVar("exisiting_members", $va_existing_members);			
					$this->render('Members/ajax_invite_member_form_html.php');
				}
			}else{
				$this->render('Members/ajax_lookup_member_form_html.php');
			}
 		}
 		# -------------------------------------------------------
 		public function inviteMember() {
			$this->view->setVar("member_email", $this->request->getParameter('member_email', pString));
			$vs_existing_member_ids = $this->request->getParameter('existing_member_ids', pString);
			$o_purifier = new HTMLPurifier();
			$va_errors = array();
			$va_new_members = array();
			$ps_member_message = $o_purifier->purify($this->request->getParameter('member_message', pString));
			$this->view->SetVar("member_message", $ps_member_message);
			# --- check errors
			$vn_num_new_members = $this->request->getParameter('num_new_members', pInteger);
			if($vn_num_new_members){
				$vn_i = 1;
				while($vn_i <= $vn_num_new_members){
					if(!$this->request->getParameter('member_fname'.$vn_i, pString)){
						$va_errors["member_fname".$vn_i] = "Please enter the first name of the new member";
					}else{
						$this->view->setVar("member_fname".$vn_i, $this->request->getParameter('member_fname'.$vn_i, pString));
					}
					if(!$this->request->getParameter('member_lname'.$vn_i, pString)){
						$va_errors["member_lname".$vn_i] = "Please enter the last name of the new member";
					}else{
						$this->view->setVar("member_lname".$vn_i, $this->request->getParameter('member_lname'.$vn_i, pString));
					}
					$vs_email = $o_purifier->purify($this->request->getParameter('member_email'.$vn_i, pString));
					$va_new_members[$vs_email] = array("fname" => $o_purifier->purify($this->request->getParameter('member_fname'.$vn_i, pString)), "lname" => $o_purifier->purify($this->request->getParameter('member_lname'.$vn_i, pString)), "email" => $vs_email);
					$vn_i++;
				}
				if(sizeof($va_errors)){
					$this->view->setVar("invite_errors", $va_errors);
					$this->lookUpMember();
					return;
				}else{
					# --- insert the new members
					foreach($va_new_members as $vs_email => $va_new_member_info){
						# --- add the new member
						$t_new_user = new ca_users();
						$t_new_user->setMode(ACCESS_WRITE);
						$t_new_user->set("fname", $va_new_member_info["fname"]);
						$t_new_user->set("lname", $va_new_member_info["lname"]);
						$t_new_user->set("email", $va_new_member_info["email"]);
						$t_new_user->set("user_name", $va_new_member_info["email"]);
						$t_new_user->set("active", 1);
						$t_new_user->set("userclass", 1);
						$vn_password = rand();
						$t_new_user->set("password", $vn_password);
						$va_new_members[$vs_email]["password"] = $vn_password;
						$t_new_user->set("confirmed_on", time());
						$t_new_user->insert();
						if($t_new_user->numErrors()){
							$va_errors["general"] = join(", ", $t_new_user->getErrors());
							$this->view->setVar("invite_errors", $va_errors);
							$this->lookUpMember();
							return;
						}else{
							$va_new_members[$vs_email]["user_id"] = $t_new_user->get("user_id");
						}
					}
				}
			}
			# --- if there are also existing members that need to be contacted and linked to project, add them to the $va_new_members array
			if($vs_existing_member_ids){
				$va_existing_member_ids = explode(",", $vs_existing_member_ids);
				$t_user = new ca_users();
				foreach($va_existing_member_ids as $vn_existing_member_id){
					$t_user->load($vn_existing_member_id);
					$va_new_members[$t_user->get("email")] = array("fname" => $t_user->get("fname"), "lname" => $t_user->get("lname"), "email" => $t_user->get("email"), "user_id" => $t_user->get("user_id"));
				}
			}
			# --- get name and email of project admin to use as from info for email
			$o_db = new Db();
			$q_project_admin = $o_db->query("SELECT u.lname, u.fname, u.email FROM ca_users u INNER JOIN ms_projects p ON p.user_id = u.user_id WHERE p.project_id = ?", $this->opo_project->get("project_id"));
			if($q_project_admin->numRows()){
				$q_project_admin->nextRow();
				$vs_from_name = trim($q_project_admin->get("fname")." ".$q_project_admin->get("lname"));
				$vs_from_email = $q_project_admin->get("email");
			}
			foreach($va_new_members as $va_new_member_info){				
				# --- insert link btw user and project
				$t_project_users = new ms_project_users();
				$t_project_users->setMode(ACCESS_WRITE);
				$t_project_users->set("user_id", $va_new_member_info["user_id"]);
				$t_project_users->set("project_id", $this->opo_project->get("project_id"));
				$t_project_users->set("membership_type", 1);
				$t_project_users->set("active", 1);
				$t_project_users->insert();
				if($t_project_users->numErrors()){
					$va_errors["general"] = join(", ", $t_project_users->getErrors());
					$this->view->setVar("invite_errors", $va_errors);
					$this->lookUpMember();
					return;
				}else{
					$vs_member_name = trim($va_new_member_info["fname"]." ".$va_new_member_info["lname"]);
					$vn_password = $va_new_member_info["password"];
					# -- generate mail text from template - get both html and text versions
					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/invite_member.tpl");
					$vs_mail_message_text = ob_get_contents();
					ob_end_clean();
					ob_start();
					require($this->request->getViewsDirectoryPath()."/mailTemplates/invite_member_html.tpl");
					$vs_mail_message_html = ob_get_contents();
					ob_end_clean();
									
					if(caSendmail($va_new_member_info["email"], array($vs_from_email => $vs_from_name), _t("Invitation to MorphoSource Project"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
						$vn_email_success = 1;
					}else{
						$vn_email_fail = 1;
					}
				}
			}				
			if($vn_email_success){
				$this->notification->addNotification(_t("The new member has been added to your project and sent an email notification."), "message");
				
			}
			if($vn_email_fail){
				$this->notification->addNotification(_t("The new member has been added to your project, but the notification email could not be sent."), "message");		
			}
			# --- returning nothing will trigger redirect to reload list of project members showing newly added member
			return;
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