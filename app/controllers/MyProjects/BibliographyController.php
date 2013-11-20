<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/BibliographyController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_bibliography.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class BibliographyController extends ActionController {
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
			
			# --- load the bib object
			$this->opo_item = new ms_bibliography();
			$this->opn_item_id = $this->request->getParameter($this->opo_item->getProperty("PRIMARY_KEY"), pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the bib is part of the current project or a project the user has access to
				$t_project = new ms_projects();
				if(($this->opo_item->get("project_id") != $this->opn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id")))){
					$this->notification->addNotification("The bibliography record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
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
			
 		}
 		# -------------------------------------------------------
 		public function form() {
			$this->view->setVar("media_id", $this->request->getParameter('media_id', pInteger));
			$this->view->setVar("specimen_id", $this->request->getParameter('specimen_id', pInteger));
			$this->render('Bibliography/form_html.php');
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$o_db = new Db();
			$va_bibref_ids = $this->opo_project->getProjectCitationIDs();
			
			if(sizeof($va_bibref_ids)){
				$q_listings = $o_db->query("SELECT b.*, p.name FROM ms_bibliography b INNER JOIN ms_projects AS p ON b.project_id = p.project_id WHERE b.bibref_id IN (".join(", ", $va_bibref_ids).") ORDER BY b.authors");
				$this->view->setVar("listings", $q_listings);
				$this->view->setVar("num_listings", $q_listings->numRows());
			}else{
				$this->view->setVar("num_listings", 0);
			}
			$this->render('Bibliography/list_html.php');
 		}
 		# -------------------------------------------------------
 		public function save() {
			# get names of form fields
			$va_fields = $this->opo_item->getFormFields();
			$va_errors = array();
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
					# --- if a media_id or specimen_id has been passed to this form and the item is being inserted
					# --- it means we are quick adding a bib in the context of the media/specimen form
					# --- so load, set and save the ms_media_x_bibliography/ ms_specimen_x_bibliography and if no errors, redirect to the media/specimen controller
					if($pn_media_id = $this->request->getParameter('media_id', pInteger)){
						$t_bib_link = new ms_media_x_bibliography();
						$t_bib_link->set("bibref_id",$this->opo_item->get("bibref_id"));
						$t_bib_link->set("media_id",$pn_media_id);
						$t_bib_link->set("user_id",$this->request->user->get("user_id"));
						if($_REQUEST["page"]){
							$t_bib_link->set("pp",$_REQUEST["page"]);
						}
						$t_bib_link->setMode(ACCESS_WRITE);
						$t_bib_link->insert();
			
						if ($t_bib_link->numErrors()) {
							foreach ($t_bib_link->getErrors() as $vs_e) {  
								$va_errors["general"] = $vs_e;
							}
							$vs_message = join(", ", $va_errors);
						}else{
							$vs_message = "Saved media bibliography";
							# --- redirect to media controller
							$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Media", "bibliographyLookup", array("message" => $vs_message, "media_id" => $pn_media_id)));
							return;
						}
					}elseif($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
						$t_bib_link = new ms_specimens_x_bibliography();
						$t_bib_link->set("bibref_id",$this->opo_item->get("bibref_id"));
						$t_bib_link->set("specimen_id",$pn_specimen_id);
						$t_bib_link->set("user_id",$this->request->user->get("user_id"));
						if($_REQUEST["page"]){
							$t_bib_link->set("pp",$_REQUEST["page"]);
						}
						$t_bib_link->setMode(ACCESS_WRITE);
						$t_bib_link->insert();
			
						if ($t_bib_link->numErrors()) {
							foreach ($t_bib_link->getErrors() as $vs_e) {  
								$va_errors["general"] = $vs_e;
							}
							$vs_message = join(", ", $va_errors);
						}else{
							$vs_message = "Saved specimen bibliography";
							# --- redirect to specimen controller
							$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Specimens", "bibliographyLookup", array("message" => $vs_message, "specimen_id" => $pn_specimen_id)));
							return;
						}
					}else{
						$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
					}
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
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
				# --- check if the bib is in use in other projects or in specimen records - do not let this be deleted
				$o_db = new Db();
				$q_other_projects1 = $o_db->query("SELECT m.project_id FROM ms_media m INNER JOIN ms_media_x_bibliography AS mb ON mb.media_id = m.media_id WHERE m.project_id != ? AND mb.bibref_id = ?", $this->opn_project_id, $this->opn_item_id);
				$q_other_projects2 = $o_db->query("SELECT s.project_id FROM ms_specimens s INNER JOIN ms_specimens_x_bibliography AS sb ON sb.specimen_id = s.specimen_id WHERE s.project_id != ? AND sb.bibref_id = ?", $this->opn_project_id, $this->opn_item_id);
				$q_specimens = $o_db->query("SELECT specimen_id FROM ms_specimens WHERE body_mass_bibref_id = ? OR locality_absolute_age_bibref_id = ? OR locality_relative_age_bibref_id = ?", $this->opn_item_id, $this->opn_item_id, $this->opn_item_id);
				if($q_other_projects1->numRows() || $q_other_projects2->numRows() || $q_specimens->numRows()){
					if($q_other_projects1->numRows() || $q_other_projects2->numRows()){
						$this->notification->addNotification("You can not delete this bibliographic reference because it is in use by ".($q_other_projects1->numRows() + $q_other_projects2->numRows())." other project".((($q_other_projects1->numRows() + $q_other_projects2->numRows()) > 1) ? "s" : ""), __NOTIFICATION_TYPE_INFO__);
					}
					if($q_specimens->numRows()){
						$this->notification->addNotification("You can not delete this bibliographic reference because it is in use by ".($q_specimens->numRows())." specimen".(($q_specimens->numRows() == 1) ? "" : "s"), __NOTIFICATION_TYPE_INFO__);
					}
					$this->listItems();
				}else{
					$q_projects_links1 = $o_db->query("SELECT m.project_id FROM ms_media m INNER JOIN ms_media_x_bibliography AS mb ON mb.media_id = m.media_id WHERE m.project_id = ? AND mb.bibref_id = ?", $this->opn_project_id, $this->opn_item_id);
					$q_projects_links2 = $o_db->query("SELECT s.project_id FROM ms_specimens s INNER JOIN ms_specimens_x_bibliography AS sb ON sb.specimen_id = s.specimen_id WHERE s.project_id = ? AND sb.bibref_id = ?", $this->opn_project_id, $this->opn_item_id);
					if($q_projects_links1->numRows() || $q_projects_links2->numRows()){
						$this->view->setvar("message", "This bibliographic reference is being used by ".(($q_projects_links1->numRows()) ? $q_projects_links1->numRows()." project media, " : "").(($q_projects_links2->numRows()) ? $q_projects_links2->numRows()." project specimen" : "").".");
					}
					$this->view->setVar("item_name", $this->opo_item->getCitationText());
					$this->render('General/delete_html.php');
				}
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>