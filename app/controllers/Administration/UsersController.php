<?php
/* ----------------------------------------------------------------------
 * app/controllers/Administration/UserController.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2010 Whirl-i-Gig
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

 	require_once(__CA_MODELS_DIR__.'/ca_users.php');

 	class UsersController extends ActionController {
 		# -------------------------------------------------------
 		private $pt_user;
 		# -------------------------------------------------------
 		#
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			if(!$po_request->user->canDoAction("is_administrator")) { die("Insufficient privileges"); }
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 		}
 		# -------------------------------------------------------
 		public function Edit() {
 			$t_user = $this->getUserObject();
 			
 			 $va_profile_prefs = $t_user->getValidPreferences('profile');
 			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
 				$va_elements = array();
				foreach($va_profile_prefs as $vs_pref) {
					$va_pref_info = $t_user->getPreferenceInfo($vs_pref);
					$va_elements[$vs_pref] = array('element' => $t_user->preferenceHtmlFormElement($vs_pref), 'formatted_element' => $t_user->preferenceHtmlFormElement($vs_pref, "<div class='formLabel'><b>".$va_pref_info['label']."</b><br/>^ELEMENT</div>"), 'info' => $va_pref_info, 'label' => $va_pref_info['label']);
				}
				
				$this->view->setVar("profile_settings", $va_elements);
			}
			
 			$this->render('user_edit_html.php');
 		}
 		# -------------------------------------------------------
 		public function Save() {
 			JavascriptLoadManager::register('tableList');
 			
 			$t_user = $this->getUserObject();
 			$t_user->setMode(ACCESS_WRITE);
 			foreach($t_user->getFormFields() as $vs_f => $va_field_info) {
 				switch($vs_f) {
 					case 'active':
 						if (((bool)$_REQUEST[$vs_f]) && (!$t_user->get('active'))) {
 							$t_user->set('approved_on', "now");
# 							# --- send email confirmation
#							# -- generate mail text from template
#							ob_start();
#							$fname = $t_user->get("fname");
#							$ps_email = $t_user->get("email");
#							require($this->request->getViewsDirectoryPath()."/mailTemplates/reg_approval.tpl");
								
#							$vs_mail_message = ob_get_contents(); 
#							ob_end_clean();
#							caSendmail($t_user->get('email'), $this->request->config->get("ca_admin_email"), "[".$this->request->config->get("app_display_name")."] "._t("Your account has been activated!"), $vs_mail_message);

 						}
 						break;
 					case 'approved_on':
 						continue(2);
 						break;
 				}
 				$t_user->set($vs_f, $_REQUEST[$vs_f]);
 				if ($t_user->numErrors()) {
 					print_r($t_user->getErrors());
 					$this->request->addActionErrors($t_user->errors(), 'field_'.$vs_f);
 				}
 			}

 			if ($this->request->getParameter('password', pString) != $this->request->getParameter('password_confirm', pString)) {
 				$this->request->addActionError(new Error(1050, _t("Password does not match confirmation. Please try again."), "administrate/UserController->Save()", '', false, false), 'field_password');
 			} 
 			
 			// user profile values
			$va_profile_prefs = $t_user->getValidPreferences('profile');
			if (is_array($va_profile_prefs) && sizeof($va_profile_prefs)) {
				foreach($va_profile_prefs as $vs_pref) {
					$t_user->setPreference($vs_pref, $this->request->getParameter('pref_'.$vs_pref, pString));
				}
			}
 			
 			AppNavigation::clearMenuBarCache($this->request);	// clear menu bar cache since changes may affect content
 			
 			if($this->request->numActionErrors() == 0) {
				if (!$t_user->getPrimaryKey()) {
					$t_user->insert();
					$vs_message = _t("Added user");
				} else {
					$t_user->update();
					$vs_message = _t("Saved changes to user");
				}
				
				if ($t_user->numErrors()) {
					foreach ($t_user->errors() as $o_e) {
						$this->request->addActionError($o_e, 'general');
						
						$this->notification->addNotification($o_e->getErrorDescription(), __NOTIFICATION_TYPE_ERROR__);
					}
				} else {
					// Save roles
					$va_set_user_roles = $this->request->getParameter('roles', pArray);
					if(!is_array($va_set_user_roles)) { $va_set_user_roles = array(); }
					
					$va_existing_user_roles = $t_user->getUserRoles();
					$va_role_list = $t_user->getRoleList();
					
					foreach($va_role_list as $vn_role_id => $va_role_info) {
						if ($va_existing_user_roles[$vn_role_id] && !in_array($vn_role_id, $va_set_user_roles)) {
							// remove role
							$t_user->removeRoles($vn_role_id);
							continue;
						}
						
						if (!$va_existing_user_roles[$vn_role_id] && in_array($vn_role_id, $va_set_user_roles)) {
							// add role
							$t_user->addRoles($vn_role_id);
							continue;
						}
					}
					
					$this->notification->addNotification($vs_message, __NOTIFICATION_TYPE_INFO__);
				}
			} else {
				$this->notification->addNotification(_t("Your entry has errors. See below for details."), __NOTIFICATION_TYPE_ERROR__);
			}

			if ($this->request->numActionErrors()) {
				$this->render('user_edit_html.php');
			} else {
				// success
				
				// If we are editing the user record of the currently logged in user
				// we have a problem: the request object flushes out changes to its own user object
				// for the logged-in user at the end of the request overwriting any changes we've made.
				//
				// To avoid this we check here to see if we're editing the currently logged-in
				// user and reload the request's copy if needed.
				if ($t_user->getPrimaryKey() == $this->request->user->getPrimaryKey()) {
					$this->request->user->load($t_user->getPrimaryKey());
				}
				
				$this->ListUsers();
 			}
 		}
 		# -------------------------------------------------------
 		public function ListUsers() {
 			JavascriptLoadManager::register('tableList');
 			if (($vn_userclass = $this->request->getParameter('userclass', pInteger)) == '') {
 				$vn_userclass = $this->request->user->getVar('ca_users_default_userclass');
 			} else {
 				$this->request->user->setVar('ca_users_default_userclass', $vn_userclass);
 			}
 			if (($vn_userclass < 0) || ($vn_user_class >= 2)) { $vn_userclass = 0; }
 			$t_user = $this->getUserObject();
 			
 			$this->view->setVar('userclass', $vn_userclass);
 			$this->view->setVar('userclass_displayname', $t_user->getChoiceListValue('userclass', $vn_userclass));
 			
 			
 			$vs_sort_field = $this->request->getParameter('sort', pString);
 			$this->view->setVar('user_list', $t_user->getUserList(array('sort' => $vs_sort_field, 'sort_direction' => 'asc', 'userclass' => $vn_userclass)));

 			$this->render('user_list_html.php');
 		}
 		# -------------------------------------------------------
 		public function approveRequest() {
 			$t_user = $this->getUserObject();
 			$t_user->setMode(ACCESS_WRITE);
 			$t_user->set("userclass", 1);
 			$t_user->update();
 			if ($t_user->numErrors()) {
				$this->notification->addNotification("There were errors: ".join("; ", $t_user->getErrors(), __NOTIFICATION_TYPE_ERROR__));
			}else{
				# --- send notification email
				# -- generate mail text from template to notifiy administrator - get both html and text versions
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/admin_approve_full_access.tpl");
				$vs_mail_message_text = ob_get_contents();
				ob_end_clean();
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/admin_approve_full_access_html.tpl");
				$vs_mail_message_html = ob_get_contents();
				ob_end_clean();
				if(caSendmail($t_user->get("email"), "do-not-reply@morphosource.org", _t("MorphoSource request approved"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
					$this->notification->addNotification(_t("User is approved to make contributions to MorphoSource and was notified via email"), __NOTIFICATION_TYPE_INFO__);
				}else{
					$this->notification->addNotification(_t("User is approved to make contributions to MorphoSource BUT was not notified via email"), __NOTIFICATION_TYPE_INFO__);
				}
			}
			
 			$this->ListUsers();
 		}
 		# -------------------------------------------------------
 		public function denyRequest() {
 			$t_user = $this->getUserObject();
 			$t_user->setMode(ACCESS_WRITE);
 			$t_user->set("userclass", 100);
 			$t_user->update();
 			if ($t_user->numErrors()) {
				$this->notification->addNotification("There were errors: ".join("; ", $t_user->getErrors(), __NOTIFICATION_TYPE_ERROR__));
			}else{
				# --- send notification email
				# -- generate mail text from template to notifiy administrator - get both html and text versions
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/admin_deny_full_access.tpl");
				$vs_mail_message_text = ob_get_contents();
				ob_end_clean();
				ob_start();
				require($this->request->getViewsDirectoryPath()."/mailTemplates/admin_deny_full_access_html.tpl");
				$vs_mail_message_html = ob_get_contents();
				ob_end_clean();
				if(caSendmail($t_user->get("email"), "do-not-reply@morphosource.org", _t("MorphoSource request denied"), $vs_mail_message_text, $vs_mail_message_html, null, null)){
					$this->notification->addNotification(_t("User denied ability to make contributions to MorphoSource and was notified via email"), __NOTIFICATION_TYPE_INFO__);
				}else{
					$this->notification->addNotification(_t("User denied ability to make contributions to MorphoSource BUT was not notified via email"), __NOTIFICATION_TYPE_INFO__);
				}
			}
			
 			$this->ListUsers();
 		}
 		# -------------------------------------------------------
 		public function Delete() {
 			$t_user = $this->getUserObject();
 			if ($this->request->getParameter('confirm', pInteger)) {
 				$t_user->setMode(ACCESS_WRITE);
 				$t_user->delete(false);

 				if ($t_user->numErrors()) {
 					foreach ($t_user->errors() as $o_e) {
						$this->request->addActionError($o_e, 'general');
					}
 				} else {
 					$this->notification->addNotification(_t("Deleted user"), __NOTIFICATION_TYPE_INFO__);
 				}
 				$this->ListUsers();
 				return;
 			} else {
 				$this->render('user_delete_html.php');
 			}
 		}
 		# -------------------------------------------------------
 		# Utilities
 		# -------------------------------------------------------
 		private function getUserObject($pb_set_view_vars=true, $pn_user_id=null) {
 			if (!($t_user = $this->pt_user)) {
				if (!($vn_user_id = $this->request->getParameter('user_id', pInteger))) {
					$vn_user_id = $pn_user_id;
				}
				$t_user = new ca_users($vn_user_id);
			}
 			if ($pb_set_view_vars){
 				$this->view->setVar('user_id', $vn_user_id);
 				$this->view->setVar('t_user', $t_user);
 			}
 			$this->pt_user = $t_user;
 			return $t_user;
 		}
 		# -------------------------------------------------------
 	}
 ?>