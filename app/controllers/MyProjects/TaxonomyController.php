<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/TaxonomyController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class TaxonomyController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			
			protected $opo_item;
			protected $opo_item2;
			protected $opn_item_id;
			protected $opn_item2_id;
			protected $ops_item_name;
			protected $ops_name_singular;
			protected $ops_name_plural;
			protected $ops_primary_key;
			protected $ops_primary_key2;

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
 			}else{
 				$this->notification->addNotification("Please select a project", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));
 			}
			$this->view->setvar("project", $this->opo_project);
			
			# --- load the object
			$this->opo_item = new ms_taxonomy_names();
			$this->opn_item_id = $this->request->getParameter($this->opo_item->getProperty("PRIMARY_KEY"), pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the taxonomy name is part of the current project
				if($this->opo_item->get("project_id") != $this->opn_project_id){
					$this->notification->addNotification("The taxonomy record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));				
				}
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
					$this->ops_item_name .= $this->opo_item->getProperty("LIST_DELIMITER");
				}
			}
			$this->view->setvar("item_name", $this->ops_item_name);
			
			# --- load info for ms_taxonomy - some form elements will also show up in the taxonomy form
			$this->opo_item2 = new ms_taxonomy();
			if($this->opo_item->get("taxon_id")){
				$this->opo_item2_id = $this->opo_item->get("taxon_id");
				$this->opo_item2->load($this->opn_item2_id);
			}
			$this->view->setvar("item2", $this->opo_item2);
			$this->ops_primary_key2 = $this->opo_item2->getProperty("PRIMARY_KEY");
			$va_list_fields2 = $this->opo_item2->getProperty("LIST_FIELDS");
			$this->view->setvar("list_fields2", $va_list_fields2);
			
 		}
 		# -------------------------------------------------------
 		public function form() {
			# --- if specimen_id is passed this form is being loaded as a quick add in another form and the new taxon needs to be linked to the specimen
			if($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
				$this->view->setVar("specimen_id", $pn_specimen_id);
				$t_specimen = new ms_specimens($pn_specimen_id);
				$this->view->setVar("specimen_name", $t_specimen->getSpecimenName());
			}
			# --- if media_id is passed this form is being loaded as a quick add in the specimen form of the media info page - need to redirect back to media info page
			if($pn_media_id = $this->request->getParameter('media_id', pInteger)){
				$this->view->setVar("media_id", $pn_media_id);
			}
			$this->render('Taxonomy/form_html.php');
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$q_listings = $this->opo_project->getProjectTaxonomy();
			$this->view->setVar("listings", $q_listings);
			$this->render('Taxonomy/list_html.php');
 		}
 		# -------------------------------------------------------
 		public function save() {
			# get names of form fields
			$va_fields2 = $this->opo_item2->getFormFields();
			$va_errors = array();
			# --- if specimen_id is passed this form is being loaded as a quick add in another form and the new taxon needs to be linked to the specimen
			if($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
				$this->view->setVar("specimen_id", $pn_specimen_id);
			}
			# --- if media_id is passed this form is being loaded as a quick add in the specimen form of the media info page - need to redirect back to media info page
			if($pn_media_id = $this->request->getParameter('media_id', pInteger)){
				$this->view->setVar("media_id", $pn_media_id);
			}
			
			# loop through ms_taxonomy fields
			while(list($vs_f,$va_attr) = each($va_fields2)) {
				switch($vs_f) {
					# -----------------------------------------------
					case 'project_id':
						if(!$this->opo_item2->get("project_id")){
							$this->opo_item2->set($vs_f,$this->opn_project_id);
						}
						break;
					# -----------------------------------------------
					case 'user_id':
						if(!$this->opo_item2->get("user_id")){
							$this->opo_item2->set($vs_f,$this->request->user->get("user_id"));
						}
						break;
					# -----------------------------------------------
					default:
						$this->opo_item2->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
					# -----------------------------------------------
				}
				if ($this->opo_item2->numErrors() > 0) {
					foreach ($this->opo_item2->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}

			$va_fields = $this->opo_item->getFormFields();
			# loop through ms_taxonomy_names fields
			while(list($vs_f,$va_attr) = each($va_fields)) {
				
				switch($vs_f) {
					# -----------------------------------------------
					case 'project_id':
						if(!$this->opo_item->get("project_id")){
							$this->opo_item->set($vs_f,$this->opn_project_id);
						}
						break;
					# -----------------------------------------------
					case 'taxon_id':
						# --- set taxon_id after insert ms_taxonomy
						continue;
						break;
					# -----------------------------------------------
					case 'user_id':
						if(!$this->opo_item->get("user_id")){
							$this->opo_item->set($vs_f,$this->request->user->get("user_id"));
						}
						break;
					# -----------------------------------------------
					case 'is_primary':
						$this->opo_item->set($vs_f,1);
						break;
					# -----------------------------------------------
					default:
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
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
				# do insert or update for ms_taxonomy
				$this->opo_item2->setMode(ACCESS_WRITE);
				if ($this->opo_item2->get($this->ops_primary_key2)){
					$this->opo_item2->update();
				} else {
					$this->opo_item2->insert();
				}	
				if ($this->opo_item2->numErrors()) {
					foreach ($this->opo_item2->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					# --- set taxon_id for ms_taxonomy_names
					if(!$this->opo_item->get("taxon_id") && $this->opo_item2->get("taxon_id")){
						$this->opo_item->set("taxon_id",$this->opo_item2->get("taxon_id"));
					}
				}
			}
		
			if (sizeof($va_errors) == 0) {
				# do insert or update for ms_taxonomy_names
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
				if(!$pn_media_id && !$pn_specimen_id){
					$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				}
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
				if($pn_specimen_id){
					# --- if specimen_id is passed, need to add link to it
					# --- first remove exisiting links
					$o_db = new Db();
					$t_remove_specimen_links = $o_db->query("DELETE FROM ms_specimens_x_taxonomy WHERE specimen_id = ?", $pn_specimen_id);
					$t_specimens_x_taxonomy = new ms_specimens_x_taxonomy();
					$t_specimens_x_taxonomy->set("specimen_id",$pn_specimen_id);
					$t_specimens_x_taxonomy->set("alt_id",$this->opo_item->get("alt_id"));
					$t_specimens_x_taxonomy->set("user_id",$this->request->user->get("user_id"));
					$t_specimens_x_taxonomy->set("taxon_id",$this->opo_item->get("taxon_id"));
	
					# do insert
					$t_specimens_x_taxonomy->setMode(ACCESS_WRITE);
					$t_specimens_x_taxonomy->insert();
		
					if ($t_specimens_x_taxonomy->numErrors()) {
						foreach ($t_specimens_x_taxonomy->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						$vs_message = "Saved specimen taxonomy";
					}
				}
				if($pn_media_id){
					# --- redirect to media controller
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Media", "specimenLookup", array("message" => $vs_message, "media_id" => $pn_media_id)));
					return;
				}elseif($pn_specimen_id){
					# --- redirect to specimens controller
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Specimens", "specimenTaxonomyLookup", array("message" => $vs_message, "specimen_id" => $pn_specimen_id)));
					return;
				}
				
				
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
				$this->view->setVar("item_name", $this->ops_item_name);
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>