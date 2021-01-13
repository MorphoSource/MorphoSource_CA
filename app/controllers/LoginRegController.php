<?php
/* ----------------------------------------------------------------------
 * includes/LoginRegController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2012 Whirl-i-Gig
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
	require_once(__CA_MODELS_DIR__."/ca_users.php");
 
 	class LoginRegController extends ActionController {
 		# -------------------------------------------------------
 		function form($t_user = "") {
 			if ($vs_last_page = $this->request->getParameter("site_last_page", pString)) { # --- last_page is passed as "Sets" if was trying to add an image to set, "Bookmarks" if user was trying to bookmark an item
				$this->request->session->setVar('site_last_page', $vs_last_page);
				$this->request->session->setVar('site_last_page_media_id', $this->request->getParameter("media_id", pInteger));
			}
 			
 			if(!is_object($t_user)){
 				$t_user = new ca_users();
 			}
 			$this->view->setVar("fname", $t_user->htmlFormElement("fname","<div><b>"._t("First name")."</b><br/>^ELEMENT</div>"));
 			$this->view->setVar("lname", $t_user->htmlFormElement("lname","<div><b>"._t("Last name")."</b><br/>^ELEMENT</div>"));
 			$this->view->setVar("email", $t_user->htmlFormElement("email","<div><b>"._t("Email address")."</b><br/>^ELEMENT</div>"));
 			$this->view->setVar("password", $t_user->htmlFormElement("password","<div><b>"._t("Password")."</b><br/>^ELEMENT</div>", array('value' => '')));
 			
 			$va_profile_prefs = $t_user->getValidPreferences('profile');
 			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
 				$va_elements = array();
				foreach($va_profile_prefs as $vs_pref) {
					if($vs_pref == 'user_upload_directory') { continue; }
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					$va_elements[$vs_pref] = array('element' => $t_user->preferenceHtmlFormElement($vs_pref, '', array('useTable' => true, 'numTableColumns' => 2)), 'formatted_element' => $t_user->preferenceHtmlFormElement($vs_pref, "<div><b>".$va_pref_info['label']."</b><br/>^ELEMENT</div>", array("useTable" => true, "numTableColumns" => 2)), 'info' => $va_pref_info, 'label' => $va_pref_info['label']);
				}
				
				$this->view->setVar("profile_settings", $va_elements);
			}
 			
 			$this->render('LoginReg/loginreg_html.php');
 		}
 		# -------------------------------------------------------
 		function login() {
 			$this->notification->addNotification(_t("Logging in is disabled"), __NOTIFICATION_TYPE_INFO__);
			$this->response->setRedirect('/');
			return;
 		}
 		# -------------------------------------------------------
 		function Logout() {
 			$this->request->session->setVar('current_project_id', '');
 			if ($vs_default_action = $this->request->config->get('default_action')) {
				$va_tmp = explode('/', $vs_default_action);
				$vs_action = array_pop($va_tmp);
				if (sizeof($va_tmp)) { $vs_controller = array_pop($va_tmp); }
				if (sizeof($va_tmp)) { $vs_module_path = join('/', $va_tmp); }
			} else {
				$vs_controller = 'Splash';
				$vs_action = 'Index';
			}
			$vs_url = caNavUrl($this->request, $vs_module_path, $vs_controller, $vs_action);
			
 			$this->request->deauthenticate();
 			$this->notification->addNotification(_t("You have been logged out"), __NOTIFICATION_TYPE_INFO__);
 			$this->response->setRedirect($vs_url);
 			
 			$t_user = new ca_users();
			
			$this->form($t_user);
 		}
 		# -------------------------------------------------------
 		function register() {
 			$this->notification->addNotification(_t("Registering new user is disabled"), __NOTIFICATION_TYPE_INFO__);
			$this->response->setRedirect('/');
			return;
 		}
 		# -------------------------------------------------------
 		function resetSend(){
 			$this->notification->addNotification(_t("Reset password is disabled"), __NOTIFICATION_TYPE_INFO__);
			$this->response->setRedirect('/');
			return;
 		}
 		# -------------------------------------------------------
 		function resetSave(){
			$this->notification->addNotification(_t("Reset password is disabled"), __NOTIFICATION_TYPE_INFO__);
			$this->response->setRedirect('/');
			return;		
 		}
 		# -------------------------------------------------------
 		function profileForm($t_user = "") {
			if(!$this->request->isLoggedIn()){
				$this->notification->addNotification(_t("User is not logged in"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			if ($this->request->config->get('dont_allow_registration_and_login')) {
				$this->notification->addNotification(_t("Registration is not enabled"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			MetaTagManager::setWindowTitle(_t("User Profile"));
			if(!is_object($t_user)){
				$t_user = $this->request->user;
			}
			$this->view->setVar("t_user", $t_user);
		
			$va_profile_prefs = $t_user->getValidPreferences('profile');
			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
				$va_elements = array();
				foreach($va_profile_prefs as $vs_pref) {
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					$va_elements[$vs_pref] = array('element' => $t_user->preferenceHtmlFormElement($vs_pref, "", array("useTable" => true, "numTableColumns" => 4)), 'formatted_element' => $t_user->preferenceHtmlFormElement($vs_pref, "<div><b>".$va_pref_info['label']."</b><br/>^ELEMENT</div>", array("useTable" => true, "numTableColumns" => 4)), 'info' => $va_pref_info, 'label' => $va_pref_info['label']);
				}

				$this->view->setVar("profile_settings", $va_elements);
			}

			$this->render("LoginReg/form_profile_html.php");
		}
		# ------------------------------------------------------
		function profileSave() {
			if(!$this->request->isLoggedIn()){
				$this->notification->addNotification(_t("User is not logged in"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			if ($this->request->config->get('dont_allow_registration_and_login')) {
				$this->notification->addNotification(_t("Registration is not enabled"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			MetaTagManager::setWindowTitle(_t("User Preferences"));
			$t_user = $this->request->user;
			$t_user->purify(true);
		
			$ps_email = $this->request->getParameter("email", pString);
			$ps_fname = $this->request->getParameter("fname", pString);
			$ps_lname = $this->request->getParameter("lname", pString);
			$ps_password = $this->request->getParameter("password", pString);
			$ps_password2 = $this->request->getParameter("password2", pString);
			$ps_security = $this->request->getParameter("security", pString);
			$pn_terms = $this->request->getParameter("pref_user_profile_terms_conditions", pInteger);
			

			$va_errors = array();

			if(!$pn_terms){
				$va_errors["user_profile_terms_conditions"] = _t("Please agree to the Terms and Conditions");
			}
			if (!caCheckEmailAddress($ps_email)) {
				$va_errors["email"] = _t("E-mail address is not valid.");
			}else{
				$t_user->set("email", $ps_email);
				$t_user->set("user_name",$ps_email);
			}
			if (!$ps_fname) {
				$va_errors["fname"] = _t("Please enter your first name");
			}else{
				$t_user->set("fname", $ps_fname);
			}
			if (!$ps_lname) {
				$va_errors["lname"] = _t("Please enter your last name");
			}else{
				$t_user->set("lname", $ps_lname);
			}
			if ($ps_password) {
				if($ps_password != $ps_password2){
					$va_errors["password"] = _t("Passwords do not match");
				}else{
					$t_user->set("password", $ps_password);
				}
			}

			// Check user profile responses
			$va_profile_prefs = $t_user->getValidPreferences('profile');
			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
				foreach($va_profile_prefs as $vs_pref) {
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					if($va_pref_info["formatType"] == "FT_ARRAY"){
						# checkboxes
						$vs_pref_value = $this->request->getParameter('pref_'.$vs_pref, pArray);
					}else{
						$vs_pref_value = $this->request->getParameter('pref_'.$vs_pref, pString);
					}
					if(ISSET($_REQUEST['pref_'.$vs_pref])){
						if (!$t_user->isValidPreferenceValue($vs_pref, $vs_pref_value)) {
							$va_errors[$vs_pref] = join("; ", $t_user->getErrors());

							$t_user->clearErrors();
						}else{
							$t_user->setPreference($vs_pref, $vs_pref_value);
						}
					}
				}
			}		
		
			if(sizeof($va_errors) == 0){
				if(sizeof($va_errors) == 0){
					# --- there are no errors so update new user record
					$t_user->setMode(ACCESS_WRITE);
					$t_user->update();
					if($t_user->numErrors()) {
						$va_errors["general"] = join("; ", $t_user->getErrors());
					}else{
						#success
						$this->notification->addNotification(_t("Updated preferences"), __NOTIFICATION_TYPE_INFO__);
						// If we are editing the user record of the currently logged in user
						// we have a problem: the request object flushes out changes to its own user object
						// for the logged-in user at the end of the request overwriting any changes we've made.
						//
						// To avoid this we check here to see if we're editing the currently logged-in
						// user and reload the request's copy if needed.
						$this->request->user->load($t_user->getPrimaryKey());
					}
				}
			}
			if(sizeof($va_errors)){
				$this->notification->addNotification(_t("There were errors, your preferences could not be updated"), __NOTIFICATION_TYPE_ERROR__);
				$this->view->setVar("errors", $va_errors);
			}
			$this->profileForm();
		}
 		# -------------------------------------------------------
 		function termsForm($t_user = "") {
			if(!$this->request->isLoggedIn()){
				$this->notification->addNotification(_t("User is not logged in"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			if ($this->request->config->get('dont_allow_registration_and_login')) {
				$this->notification->addNotification(_t("Registration is not enabled"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			MetaTagManager::setWindowTitle(_t("User Profile"));
			if(!is_object($t_user)){
				$t_user = $this->request->user;
			}
			$this->view->setVar("t_user", $t_user);
		
			//  Only doing a subset of the profile here
			#$va_profile_prefs = $t_user->getValidPreferences('profile');
			$va_profile_prefs = array("user_profile_terms_conditions", "user_profile_professional_affiliation", "user_profile_professional_affiliation_other", "user_profile_visualize_software", "user_profile_visualize_software_other", "user_profile_mesh_filetype", "user_profile_mesh_filetype_other", "user_profile_volume_filetype", "user_profile_volume_filetype_other", "user_3D_printer", "user_3D_printer_software");
			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
				$va_elements = array();
				foreach($va_profile_prefs as $vs_pref) {
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					$va_elements[$vs_pref] = array('element' => $t_user->preferenceHtmlFormElement($vs_pref, "", array("useTable" => true, "numTableColumns" => 4)), 'formatted_element' => $t_user->preferenceHtmlFormElement($vs_pref, "<div><b>".$va_pref_info['label']."</b><br/>^ELEMENT</div>", array("useTable" => true, "numTableColumns" => 4)), 'info' => $va_pref_info, 'label' => $va_pref_info['label']);
				}

				$this->view->setVar("profile_settings", $va_elements);
			}

			$this->render("LoginReg/form_terms_html.php");
		}
		# ------------------------------------------------------
		function termsSave() {
			if(!$this->request->isLoggedIn()){
				$this->notification->addNotification(_t("User is not logged in"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			if ($this->request->config->get('dont_allow_registration_and_login')) {
				$this->notification->addNotification(_t("Registration is not enabled"), __NOTIFICATION_TYPE_ERROR__);
				$this->redirect(caNavUrl($this->request, '', '', ''));
				return;
			}
			MetaTagManager::setWindowTitle(_t("Terms and Conditions"));
			$t_user = $this->request->user;
			$t_user->purify(true);
		
			$va_errors = array();
			$pn_terms = $this->request->getParameter("pref_user_profile_terms_conditions", pInteger);
			
			if(!$pn_terms){
				$va_errors["user_profile_terms_conditions"] = _t("Please agree to the Terms and Conditions");
			}

			// Check user profile responses
			//  Only doing a subset of the profile here
			#$va_profile_prefs = $t_user->getValidPreferences('profile');
			$va_profile_prefs = array("user_profile_terms_conditions", "user_profile_professional_affiliation", "user_profile_professional_affiliation_other", "user_profile_visualize_software", "user_profile_visualize_software_other", "user_profile_mesh_filetype", "user_profile_mesh_filetype_other", "user_profile_volume_filetype", "user_profile_volume_filetype_other", "user_3D_printer", "user_3D_printer_software");
			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
				foreach($va_profile_prefs as $vs_pref) {
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					if($va_pref_info["formatType"] == "FT_ARRAY"){
						# checkboxes
						$vs_pref_value = $this->request->getParameter('pref_'.$vs_pref, pArray);
					}else{
						$vs_pref_value = $this->request->getParameter('pref_'.$vs_pref, pString);
					}
					if(ISSET($_REQUEST['pref_'.$vs_pref])){
						if (!$t_user->isValidPreferenceValue($vs_pref, $vs_pref_value)) {
							$va_errors[$vs_pref] = join("; ", $t_user->getErrors());

							$t_user->clearErrors();
						}else{
							$t_user->setPreference($vs_pref, $vs_pref_value);
						}
					}
				}
			}		
		
			if(sizeof($va_errors) == 0){
				if(sizeof($va_errors) == 0){
					# --- there are no errors so update new user record
					$t_user->setMode(ACCESS_WRITE);
					$t_user->update();
					if($t_user->numErrors()) {
						$va_errors["general"] = join("; ", $t_user->getErrors());
					}else{
						#success
						$this->notification->addNotification(_t("Updated preferences"), __NOTIFICATION_TYPE_INFO__);
						// If we are editing the user record of the currently logged in user
						// we have a problem: the request object flushes out changes to its own user object
						// for the logged-in user at the end of the request overwriting any changes we've made.
						//
						// To avoid this we check here to see if we're editing the currently logged-in
						// user and reload the request's copy if needed.
						$this->request->user->load($t_user->getPrimaryKey());
												
						# --- redirect to last page before login						
						$vo_session = $this->request->getSession();
						$vs_last_page = $vo_session->getVar('site_last_page');
						$vo_session->setVar('site_last_page', "");
					
						switch($vs_last_page){
							case "Bookmarks":
								$this->response->setRedirect(caNavUrl($this->request, "", "Bookmarks", "addBookmark", array("row_id" => $vo_session->getVar('site_last_page_row_id'), "tablename" => $vo_session->getVar('site_last_page_tablename'))));
							break;
							# --------------------
							case "Sets":
								$this->response->setRedirect(caNavUrl($this->request, "", "Sets", "addItem", array("object_id" => $vo_session->getVar('site_last_page_object_id'))));
							break;
							# --------------------
							case "MediaDetail":
								$this->response->setRedirect(caNavUrl($this->request, "Detail", "MediaDetail", "Show", array("media_id" => $vo_session->getVar('site_last_page_media_id'))));
							break;
							# --------------------
							default:
								if (!($vs_url = $this->request->session->getVar('pawtucket2_last_page'))) {
									$vs_action = $vs_controller = $vs_module_path = '';
									if ($vs_default_action = $this->request->config->get('default_action')) {
										$va_tmp = explode('/', $vs_default_action);
										$vs_action = array_pop($va_tmp);
										if (sizeof($va_tmp)) { $vs_controller = array_pop($va_tmp); }
										if (sizeof($va_tmp)) { $vs_module_path = join('/', $va_tmp); }
									} else {
										$vs_controller = 'Splash';
										$vs_action = 'Index';
									}
									$vs_url = caNavUrl($this->request, $vs_module_path, $vs_controller, $vs_action);
								} 
								$this->response->setRedirect($vs_url);
							break;
							# --------------------
						}
						return;
					}
				}
			}
			if(sizeof($va_errors)){
				$this->notification->addNotification(_t("There were errors, your preferences could not be updated"), __NOTIFICATION_TYPE_ERROR__);
				$this->view->setVar("errors", $va_errors);
			}
			$this->termsForm();
		}
		# ------------------------------------------------------
 	}
 ?>