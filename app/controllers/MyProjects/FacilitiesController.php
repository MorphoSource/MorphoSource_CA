<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/FacilitesController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_scanners.php");
 	require_once(__CA_MODELS_DIR__."/ms_scanner_modes.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class FacilitiesController extends ActionController {
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
			
			# --- load the object
			$this->opo_item = new ms_facilities();
			$this->opn_item_id = $this->request->getParameter($this->opo_item->getProperty("PRIMARY_KEY"), pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the facility is part of the current project or a project user has access to
				$t_project = new ms_projects();
				if(($this->opo_item->get("project_id") != $this->opn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id")))){
					$this->notification->addNotification("The facility record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));				
				}
				
				// load related scanners
				$va_scanners = $this->opo_item->getScanners();

				foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
					// print_r('MODALITY '.$va_mode_list['modality']);
					$scanner_id = $va_mode_list['scanner_id'];
					$va_scanner_array = $va_scanners['va_scanner_rows'][$scanner_id];

					if(array_key_exists(mode_3, $va_scanner_array)){
						$this->notification->addNotification("The Scanner you are trying to add a modality to already has the max number of modalities.", __NOTIFICATION_TYPE_ERROR__);
					}
					elseif (array_key_exists(mode_2, $va_scanner_array)) {
						$va_scanners['va_scanner_rows'][$scanner_id]['mode_3'] = $va_mode_list['modality'];
					}
					elseif(array_key_exists(mode_1, $va_scanner_array)){
						$va_scanners['va_scanner_rows'][$scanner_id]['mode_2'] = $va_mode_list['modality'];
					}
					else{
						$va_scanners['va_scanner_rows'][$scanner_id]['mode_1'] = $va_mode_list['modality'];
					}

				} 
				// die(print_r($va_scanners['va_scanner_rows']));
				$this->view->setVar('scannerList', $va_scanners['va_scanner_rows']);
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
			
 		}
 		# -------------------------------------------------------
 		public function form() {
 			$this->view->setVar("media_id", $this->request->getParameter('media_id', pInteger));
 			$scanner_modes = new ms_scanner_modes();
 			$this->view->setVar("modality", $scanner_modes);
			$this->render('Facilities/form_html.php');
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$o_db = new Db();
			$q_listings = $this->opo_project->getProjectFacilities();
			$this->view->setVar("listings", $q_listings);
			$this->render('Facilities/list_html.php');
 		}
 		# -------------------------------------------------------
 		private function loadModality() {
 			// load related scanners
			$va_scanners = $this->opo_item->getScanners();
			// die(print_r($va_scanners['va_scanner_rows']['va_scanner_modes']));
			foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
				// print_r('MODALITY '.$va_mode_list['modality']);
				$scanner_id = $va_mode_list['scanner_id'];
				$va_scanner_array = $va_scanners['va_scanner_rows'][$scanner_id];

				if(array_key_exists(mode_3, $va_scanner_array)){
					$this->notification->addNotification("The Scanner you are trying to add a modality to already has the max number of modalities.", __NOTIFICATION_TYPE_ERROR__);
				}
				elseif (array_key_exists(mode_2, $va_scanner_array)) {
					$va_scanners['va_scanner_rows'][$scanner_id]['mode_3'] = $va_mode_list['modality'];
				}
				elseif(array_key_exists(mode_1, $va_scanner_array)){
					$va_scanners['va_scanner_rows'][$scanner_id]['mode_2'] = $va_mode_list['modality'];
				}
				else{
					$va_scanners['va_scanner_rows'][$scanner_id]['mode_1'] = $va_mode_list['modality'];
				}
			} 
 		}
 		# -------------------------------------------------------
 		public function save() {
			# get names of form fields
			$va_fields = $this->opo_item->getFormFields();
			$va_scanners = $this->opo_item->getScanners();
			$va_scanner_modes = $va_scanners['va_scanner_modes'];
			$va_errors = array();

			// die(print_r($va_scanner_modes));
			# loop through fields
			while(list($vs_f,$va_attr) = each($va_fields)) {
				
				switch($vs_f) {
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
					default:
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
						// print_r($vs_f.':'.$_REQUEST[$vs_f].'---');
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
				$this->opo_item->setMode(ACCESS_WRITE);
				if ($this->opo_item->get($this->ops_primary_key)){
					$this->opo_item->update();
				} else {
					$this->opo_item->insert();
				}
	
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
				
					// print_r($_REQUEST);
					// print_r('-----');
					$va_errors = array();
				// Save scanners
					foreach($_REQUEST as $vs_key => $vm_val) {
					
						// look for new scanners
						if (preg_match("!^scanner_name_new_([\d]+)$!", $vs_key, $va_matches)) {
							$vs_name = $this->request->getParameter($vs_key, pString);

							if (!strlen(trim($vs_name))) { continue; } 
							$vs_desc = $this->request->getParameter('scanner_description_new_'.$va_matches[1], pString);
							
							$t_scanner = new ms_scanners();
							$t_scanner->setMode(ACCESS_WRITE);
							$t_scanner->set('facility_id', $this->opo_item->getPrimaryKey());
							$t_scanner->set('name', $vs_name);
							$t_scanner->set('description', $vs_desc);
							$t_scanner->set('user_id', $this->request->getUserID());
							$t_scanner->insert();

							if ($t_scanner->numErrors()) {
								$va_errors['new_'.$va_matches[1]][] = array('errorDescription' => join("; ", $t_scanner->getErrors()));
							}
							else{

								//add new modalities
								for($m=1; $m<=3; $m++){
									// print_r($m.'---');
									// print_r($t_scanner->get('scanner_id').'***');
									$m=(string)$m;
									$vs_mode = $this->request->getParameter('scanner_modality_new_'.$va_matches[1].'_'.$m, pInteger);
									// print_r('scanner_modality_new_'.$va_matches[1].'_'.$m.': '.$vs_mode);
									if ($vs_mode) {
										$t_scanner_mode = new ms_scanner_modes();
										$t_scanner_mode->setMode(ACCESS_WRITE);
										$t_scanner_mode->set('user_id', $this->request->getUserID());
										$t_scanner_mode->set('scanner_id', $t_scanner->get('scanner_id'));
										$t_scanner_mode->set('modality', $vs_mode);
										$t_scanner_mode->insert();
									}
									// if ($t_scanner_mode->numErrors()) {
									// 	$va_errors[$t_scanner_mode->getPrimaryKey()][] = array('errorDescription' => join("; ", $t_scanner_mode->getErrors()));
									// }
								}

							}
						}
						
						// look for scanners to edit
						if (preg_match("!^scanner_name_([\d]+)$!", $vs_key, $va_matches)) {
							$vs_name = $this->request->getParameter($vs_key, pString);
							$vs_desc = $this->request->getParameter('scanner_description_'.$va_matches[1], pString);
							
							$t_scanner = new ms_scanners($va_matches[1]);
							if (!$t_scanner->getPrimaryKey() || ($t_scanner->get('facility_id') != $this->opo_item->getPrimaryKey())) { continue; }
							$t_scanner->setMode(ACCESS_WRITE);
							$t_scanner->set('name', $vs_name);
							$t_scanner->set('description', $vs_desc);
							$t_scanner->update();
							
							if ($t_scanner->numErrors()) {
								$va_errors[$t_scanner->getPrimaryKey()][] = array('errorDescription' => join("; ", $t_scanner->getErrors()));
							}
						}

						//look for modalities to edit
						for($m=1; $m<=3; $m++){
							$m=(string)$m;
							print_r('VA_MATCHES: '.$va_matches[1].'---');
							$vs_mode = $this->request->getParameter('scanner_modality_'.$va_matches[1].'_'.$m, pInteger);
							print_r('WE SHALL SEE'.$va_scanners['va_scanner_rows'][$va_scanner_array[1]]['mode_3'].'***');
				
							if($vs_mode){
								// print('---in vs_mode if section---');
								// print_r('scanner_modality_'.$va_matches[1].'_'.$m.': '.$vs_mode);
								// print('---LOOK FOR MODALITIES TO EDIT---');
								$num_matches=0;
								$num_modalities=0;
								foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
									$num_modalities++;
									if($va_mode_list['scanner_id'] == $va_matches[1]){
										print('***scanner_id has match***');
										$mode_id = $va_mode_list['scanner_mode_id'];

										print('^^^^^^^^^^^^^^^^^');
										print_r($va_mode_list['modality']);
										print('^^^^^^^^^^^^^^^^^');
										if($va_mode_list['modality'] == $vs_mode){
											print('DO NOTHING');
										}
										elseif ($num_matches < 3) {
											print('HELLO THERE');
											// $t_scanner_mode = new ms_scanner_modes($mode_id);
											// $t_scanner_mode->setMode(ACCESS_WRITE);
											// $t_scanner_mode->delete();
											// $t_scanner_mode->set('user_id', $this->request->getUserID());
											// $t_scanner_mode->set('scanner_id', $t_scanner->get('scanner_id'));
											// $t_scanner_mode->set('modality', $vs_mode);
											// $t_scanner_mode->insert();
										}
										$num_matches++;
									}
								}
								// print_r('   NUMBER OF MODALITIES: '.$num_modalities);
								if($num_matches == 0){
									print('xxxthere were 0 entries in modes for scannerxxx');
									$t_scanner_mode = new ms_scanner_modes();
									$t_scanner_mode->setMode(ACCESS_WRITE);
									$t_scanner_mode->set('user_id', $this->request->getUserID());
									$t_scanner_mode->set('scanner_id', $t_scanner->get('scanner_id'));
									$t_scanner_mode->set('modality', $vs_mode);
									$t_scanner_mode->insert();
								}
							}
							$num_matches=0;
						}
						// foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
						// 	if($va_mode_list['scanner_id']== $va_matches[1]){
						// 		print_r($va_mode_list['scanner_mode_id'].'---');
						// 		$mode_id = $va_mode_list['scanner_mode_id'];
						// 		for($m=1; $m<=3; $m++){
						// 			$m=(string)$m;
						// 			$vs_mode = $this->request->getParameter('scanner_modality_'.$va_matches[1].'_'.$m, pInteger);
						// 			print_r('scanner_modality_'.$va_matches[1].'_'.$m.': '.$vs_mode);
						// 			if ($vs_mode) {
						// 				print('---in vs_mode if section---');
						// 				// $t_scanner_mode = new ms_scanner_modes();
						// 				// $t_scanner_mode->setMode(ACCESS_WRITE);
						// 				// $t_scanner_mode->set('user_id', $this->request->getUserID());
						// 				// $t_scanner_mode->set('scanner_id', $t_scanner->get('scanner_id'));
						// 				// $t_scanner_mode->set('modality', $vs_mode);
						// 				// $t_scanner_mode->insert();
						// 			}
						// 		}
						// 		// $t_scanner_mode = new ms_scanner_modes($mode_id);
						// 		// $t_scanner_mode->setMode(ACCESS_WRITE);
						// 		// $t_scanner_mode->delete();
						// 	}
						// }
						
						// look for scanners to delete
						if (preg_match("!^msFacilityScannerList_([\d]+)_delete$!", $vs_key, $va_matches)) {
							// print('VA_MATCHES----');
							// die(print_r($va_matches[1]));
							$t_scanner = new ms_scanners($va_matches[1]);
							if (!$t_scanner->getPrimaryKey() || ($t_scanner->get('facility_id') != $this->opo_item->getPrimaryKey())) { $t_scanner->dump();  continue; }
							// print('IN DELETE');
							// die(print_r($va_scanner_modes));
							foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
								if($va_mode_list['scanner_id']== $va_matches[1]){
									print_r($va_mode_list['scanner_mode_id'].'_');
									$mode_id = $va_mode_list['scanner_mode_id'];
									$t_scanner_mode = new ms_scanner_modes($mode_id);
									$t_scanner_mode->setMode(ACCESS_WRITE);
									$t_scanner_mode->delete();
								}
							}

							$t_scanner->setMode(ACCESS_WRITE);
							$t_scanner->delete();
							
							if ($t_scanner->numErrors()) {
								$va_errors[$t_scanner->getPrimaryKey()][] = array('errorDescription' => join("; ", $t_scanner->getErrors()));
								print('in errors');
							}
						}
					}
					$this->view->setVar('scannerErrors', $va_errors);
					
					// load related scanners
					$va_scanners = $this->opo_item->getScanners();
					// die(print_r($va_scanners['va_scanner_rows']['va_scanner_modes']));
					foreach($va_scanners['va_scanner_modes'] as $va_mode_list){
						// print_r('MODALITY '.$va_mode_list['modality']);
						$scanner_id = $va_mode_list['scanner_id'];
						$va_scanner_array = $va_scanners['va_scanner_rows'][$scanner_id];

						if(array_key_exists(mode_3, $va_scanner_array)){
							$this->notification->addNotification("The Scanner you are trying to add a modality to already has the max number of modalities.", __NOTIFICATION_TYPE_ERROR__);
						}
						elseif (array_key_exists(mode_2, $va_scanner_array)) {
							$va_scanners['va_scanner_rows'][$scanner_id]['mode_3'] = $va_mode_list['modality'];
						}
						elseif(array_key_exists(mode_1, $va_scanner_array)){
							$va_scanners['va_scanner_rows'][$scanner_id]['mode_2'] = $va_mode_list['modality'];
						}
						else{
							$va_scanners['va_scanner_rows'][$scanner_id]['mode_1'] = $va_mode_list['modality'];
						}

					} 
					$this->view->setVar('scannerList', $va_scanners['va_scanner_rows']);
				
				$this->opn_item_id = $this->opo_item->get($this->ops_primary_key);
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->form();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function delete() {
 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
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
					$this->listItems();
				}				
			}else{
				# --- check if this facility is used by other projects
				$o_db = new Db();
				$q_other_projects = $o_db->query("SELECT DISTINCT project_id FROM ms_media WHERE project_id != ? and facility_id = ?", $this->opn_project_id, $this->opn_item_id);
				if($q_other_projects->numRows()){
					$this->notification->addNotification("You can not delete this facility because it is in use by ".$q_other_projects->numRows()." other project".(($q_other_projects->numRows() == 1) ? "" : "s").". ", __NOTIFICATION_TYPE_INFO__);
					$this->listItems();
				}else{
					# --- check to see if there are media linked to this facility that will be deleted
					$q_media_usage = $o_db->query("SELECT media_id from ms_media where facility_id = ?", $this->opn_item_id);
					if($q_media_usage->numRows()){
						$va_media = array();
						while($q_media_usage->nextRow()){
							$va_media[] = "M".$q_media_usage->get("media_id");
						}
						$this->notification->addNotification("You can not delete this facility because it is used by ".$q_media_usage->numRows()." project media:<br/>".join(", ", $va_media), __NOTIFICATION_TYPE_INFO__);
						$this->listItems();
					}else{
						$this->view->setVar("item_name", $this->ops_item_name);
						$this->render('General/delete_html.php');
					}
				}
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>