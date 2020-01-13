<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/SpecimensController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_institutions.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_BASE_DIR__."/vendor/autoload.php");
 	require_once(__CA_LIB_DIR__."/ca/Search/TaxonomyNamesSearch.php");
 	require_once(__CA_LIB_DIR__."/ca/Search/SpecimenSearch.php");
 
 	class SpecimensController extends ActionController {
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
			protected $ops_read_only;	#read only mode displays a summary of specimen info instead of form since specimen was entered by another project

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
			$this->opo_item = new ms_specimens();
			$this->opn_item_id = $this->request->getParameter($this->opo_item->getProperty("PRIMARY_KEY"), pInteger);
			$this->ops_read_only = 0;
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the record is part of the current project or a project the user has access to
				$t_project = new ms_projects();
				if(($this->opo_item->get("project_id") != $this->opn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id")))){
					$this->ops_read_only = 1;
					#$this->notification->addNotification("The specimen record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
					#$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));				
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
					$this->ops_item_name .= ", ";
				}
			}
			$this->view->setvar("item_name", $this->ops_item_name);
			$this->view->setvar("read_only", $this->ops_read_only);
			
 		}
 		# -------------------------------------------------------
 		public function form() {
 			JavascriptLoadManager::register("panel");
 			
 			$this->view->setVar("media_id", $this->request->getParameter('media_id', pInteger));
			if($this->ops_read_only){
				$this->render('Specimens/summary_html.php');
			}else{
				$this->render('Specimens/form_html.php');
			}
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$vs_specimens_order_by = $this->request->getParameter('specimens_order_by', pString);
			
			if($vs_specimens_order_by){
				$this->request->session->setVar('specimens_order_by', $vs_specimens_order_by);
			}elseif($this->request->session->getVar('specimens_order_by')){
				$vs_specimens_order_by = $this->request->session->getVar('specimens_order_by');
			}else{
				$vs_specimens_order_by = "number";
			}
			$this->view->setVar("specimens_order_by", $vs_specimens_order_by);			
			$va_specimens = $this->opo_project->getProjectSpecimens(null, $vs_specimens_order_by);
			$this->view->setVar("specimens", $va_specimens);
			$this->render('Specimens/list_html.php');
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
					case 'institution_id':
						if($_REQUEST["institution_id"]){
							$this->opo_item->set("institution_id",$_REQUEST["institution_id"]);
						}elseif($_REQUEST["name"]){
							# --- check if institution info was entered in AJAX form
							$t_institution = new ms_institutions();
							$va_institution_fields = $t_institution->getFormFields();
							$va_institution_errors = array();
							while(list($vs_institution_f,$va_institution_attr) = each($va_institution_fields)) {
								switch($vs_institution_f) {
									# -----------------------------------------------
									case 'user_id':
										if(!$t_institution->get("user_id")){
											$t_institution->set($vs_institution_f,$this->request->user->get("user_id"));
										}
										break;
									# -----------------------------------------------
									default:
										$t_institution->set($vs_institution_f,$_REQUEST[$vs_institution_f]); # set field values
										break;
									# -----------------------------------------------
								}
								if ($t_institution->numErrors() > 0) {
									foreach ($t_institution->getErrors() as $vs_e) {
										$va_institution_errors[$vs_institution_f] = $vs_e;
									}
								}
							}
							if (sizeof($va_institution_errors) == 0) {
								$t_institution->setMode(ACCESS_WRITE);
								$t_institution->insert();
							}
							if ($t_institution->numErrors()) {
								foreach ($t_institution->getErrors() as $vs_e) {  
									$va_errors["general"] = $vs_e;
								}
							}else{
								$this->opo_item->set("institution_id",$t_institution->get("institution_id"));
							}
						}else{
							$va_errors[$vs_f] = "Please enter the institution the specimen is located at";
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
					# --- if a media_id has been passed to this form and the item is being inserted
					# --- it means we are quick adding a specimen in the context of the media form
					# --- so load, set and save the media form and if no errors, redirect to the media controller
					if($pn_media_id = $this->request->getParameter('media_id', pInteger)){
						$t_media = new ms_media($pn_media_id);
						$t_media->set("specimen_id",$this->opo_item->get("specimen_id"));
						$t_media->setMode(ACCESS_WRITE);
						$t_media->update();
			
						if ($t_media->numErrors()) {
							foreach ($t_media->getErrors() as $vs_e) {  
								$va_errors["general"] = $vs_e;
							}
							$vs_message = join(", ", $va_errors);
						}else{
							$vs_message = "Saved media specimen";
						}
						# --- redirect to media controller
						$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Media", "specimenLookup", array("message" => $vs_message, "media_id" => $pn_media_id)));
						return;
					}else{
						$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
					}
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(join("; ", $va_errors)), __NOTIFICATION_TYPE_INFO__);
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
 			if($this->opo_item->getSpecimenMediaIDs()){
 				$this->notification->addNotification("You cannot delete specimen with media", __NOTIFICATION_TYPE_INFO__);
				$this->form();
				return;
 			}
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
				# --- check if this specimen is used by other projects
				$o_db = new Db();
				$q_other_projects = $o_db->query("SELECT DISTINCT project_id FROM ms_media WHERE project_id != ? and specimen_id = ?", $this->opn_project_id, $this->opn_item_id);
				if($q_other_projects->numRows()){
					$this->notification->addNotification("You can not delete this specimen because it is in use by ".$q_other_projects->numRows()." other project".(($q_other_projects->numRows() == 1) ? "" : "s").". ", __NOTIFICATION_TYPE_INFO__);
					$this->listItems();
				}else{
					# --- check to see if there are media linked to this specimen that will be deleted
					$q_usage = $o_db->query("SELECT media_id from ms_media where specimen_id = ?", $this->opn_item_id);
					if($q_usage->numRows()){
						$this->view->setVar("message", "This specimen is used by ".$q_usage->numRows()." project media.  The media will be deleted along with the specimen.");
					}
					$this->view->setVar("item_name", $this->ops_item_name);
					$this->render('General/delete_html.php');
				}
			}
 		}
 		# -------------------------------------------------------
 		public function specimenTaxonomyLookup() {
 			# --- pass the name of the current specimen
 			if($this->opo_item->get("specimen_id")){
 				# --- check if there is a taxonommic name for the specimen
 				$va_specimen_taxonomy = $this->opo_item->getSpecimenTaxonomy();
 				$this->view->setVar("specimen_taxonomy", $va_specimen_taxonomy);
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Specimens/specimen_taxonomy_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkSpecimen() {
			$va_errors = array();
			$vs_message = "";
			# --- check the specimen_id and alt_id (key for ms_taxonomy_names) are passed through lookup form
			if($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
				# --- check the specimen is not already linked
				$t_specimens_x_projects = new ms_specimens_x_projects();
				$t_specimens_x_projects->load(array("specimen_id" => $pn_specimen_id, "project_id" => $this->opn_project_id));
				if(!$t_specimens_x_projects->get("link_id")){
					$t_specimens_x_projects->set("specimen_id", $pn_specimen_id);
					$t_specimens_x_projects->set("project_id", $this->opn_project_id);
				
					# do insert
					$t_specimens_x_projects->setMode(ACCESS_WRITE);
					$t_specimens_x_projects->insert();
	
					if ($t_specimens_x_projects->numErrors()) {
						foreach ($t_specimens_x_projects->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						$vs_message = "Linked specimen to project";
					}
				}else{
					$vs_message = "Specimen already linked to project";
				}
			}else{
				$va_errors["general"] = _t("Please select a specimen");
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->notification->addNotification($vs_message, __NOTIFICATION_TYPE_INFO__);
				$this->form();
			}else{
				$this->notification->addNotification($vs_message, __NOTIFICATION_TYPE_INFO__);
				$this->form();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function unlinkSpecimen() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_specimens_x_projects = new ms_specimens_x_projects($pn_link_id);
 				if($t_specimens_x_projects->get("project_id") == $this->opn_project_id){
					$t_specimens_x_projects->setMode(ACCESS_WRITE);
					$t_specimens_x_projects->delete();
					if ($t_specimens_x_projects->numErrors() > 0) {
						foreach ($t_specimens_x_projects->getErrors() as $vs_e) {
							$va_errors[] = $vs_e;
						}
						$this->view->setVar("message", join(", ", $va_errors));
						$this->form();
					}else{
						$this->notification->addNotification("Unlinked specimen from project", __NOTIFICATION_TYPE_INFO__);
						$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
					}
				}else{
					$this->notification->addNotification("invalid link_id", __NOTIFICATION_TYPE_INFO__);
					$this->form();
				}
 			}
  		}
 		# -------------------------------------------------------
 		public function linkSpecimenTaxon() {
			$va_errors = array();
			$vs_message = "";
			# --- check the specimen_id and alt_id (key for ms_taxonomy_names) are passed through lookup form
			if(($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)) && ($pn_alt_id = $this->request->getParameter('alt_id', pInteger))){
				# --- first remove exisiting links
				$o_db = new Db();
				$t_remove_specimen_links = $o_db->query("DELETE FROM ms_specimens_x_taxonomy WHERE specimen_id = ?", $pn_specimen_id);
				$t_specimens_x_taxonomy = new ms_specimens_x_taxonomy();
				$t_specimens_x_taxonomy->set("specimen_id",$pn_specimen_id);
				$t_specimens_x_taxonomy->set("alt_id",$pn_alt_id);
				$t_specimens_x_taxonomy->set("user_id",$this->request->user->get("user_id"));
				# --- get the taxon_id from the ms_taxonomy_names records
				$t_taxonomy_names = new ms_taxonomy_names($pn_alt_id);
				$t_specimens_x_taxonomy->set("taxon_id",$t_taxonomy_names->get("taxon_id"));

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
			}else{
				$va_errors["general"] = _t("Please select a taxonomic name");
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("taxonomy_message", $vs_message);
				$this->specimenTaxonomyLookup();
			}else{
				#$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->view->setVar("message", $vs_message);
				$this->specimenTaxonomyLookup();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function bibliographyLookup() {
 			# --- pass the list of linked bib citations
 			$va_bib_citations = array();
 			if($this->opn_item_id){
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id FROM ms_specimens_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.specimen_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "link_id" => $q_bib->get("link_id"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
 				$this->view->setVar("bib_citations", $va_bib_citations);
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Specimens/specimen_bibliography_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkBibliography() {
			$va_errors = array();
			$vs_message = "";
			# --- if no specimen_id is passed, we are on the list form and linking a bib to all project specimen user has ability to edit
			if(!$this->opn_item_id){
				# --- make links to project specimen to bib that is passed from lookup form
				if($pn_bibliography_id = $this->request->getParameter('bibliography_id', pInteger)){
					# --- get all project specimen to link
					$o_db = new Db();
 					$q_project_specimen = $o_db->query("SELECT specimen_id FROM ms_specimens WHERE project_id = ?", $this->opn_project_id);
					if($q_project_specimen->numRows()){
						while($q_project_specimen->nextRow()){
							$t_bib_link = new ms_specimens_x_bibliography();
							# --- check that there is not already a link to this bib ref
							if(!$t_bib_link->load(array("bibref_id" => $pn_bibliography_id, "specimen_id" => $q_project_specimen->get("specimen_id")))){
								$t_bib_link->set("bibref_id",$pn_bibliography_id);
								$t_bib_link->set("specimen_id",$q_project_specimen->get("specimen_id"));
								$t_bib_link->set("user_id",$this->request->user->get("user_id"));
								if ($t_bib_link->numErrors() > 0) {
									foreach ($t_bib_link->getErrors() as $vs_e) {
										$va_errors["bibliography_id"] = $vs_e;
									}
								}		
								if (sizeof($va_errors) == 0) {
									# do insert
									$t_bib_link->setMode(ACCESS_WRITE);
									$t_bib_link->insert();
									
									if ($t_bib_link->numErrors()) {
										foreach ($t_bib_link->getErrors() as $vs_e) {  
											$va_errors["general"] = $va_errors["general"]."M".$q_project_specimen->get("specimen_id")." ".$vs_e.", ";
										}
									}
								}
							}
						}
						if(sizeof($va_errors) > 0){
							$vs_message = "There were errors:".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
						}else{
							$vs_message = _t("Citation was linked to all project specimen");
						} 
					}
				}else{
					$vs_message = _t("Please select a bibliography");
				}
			}else{
				# --- make link to bib that is passed from lookup form
				if($pn_bibliography_id = $this->request->getParameter('bibliography_id', pInteger)){
					$t_bib_link = new ms_specimens_x_bibliography();
					# --- check that there is not already a link to this bib ref
					if($t_bib_link->load(array("bibref_id" => $pn_bibliography_id, "specimen_id" => $this->opn_item_id))){
						$va_errors["general"] = "There is already a link to this bibliographic citation";
					}else{
						$t_bib_link->set("bibref_id",$pn_bibliography_id);
						$t_bib_link->set("specimen_id",$this->opn_item_id);
						$t_bib_link->set("user_id",$this->request->user->get("user_id"));
						#if($vs_page = $this->request->getParameter('page', pString)){
						#	$t_bib_link->set("pp",$vs_page);
						#}
						if ($t_bib_link->numErrors() > 0) {
							foreach ($t_bib_link->getErrors() as $vs_e) {
								$va_errors["bibliography_id"] = $vs_e;
							}
						}
					}
				}else{
					$va_errors["general"] = _t("Please select a bibliography");
				}		
				if (sizeof($va_errors) == 0) {
					# do insert
					$t_bib_link->setMode(ACCESS_WRITE);
					$t_bib_link->insert();
		
					if ($t_bib_link->numErrors()) {
						foreach ($t_bib_link->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						$vs_message = "Saved specimen bibliography";
					}
				}
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("message", $vs_message);
				$this->bibliographyLookup();
			}else{
				$this->opn_item_id = $this->opo_item->get("specimen_id");
				$this->view->setVar("message", $vs_message);
				$this->bibliographyLookup();
			} 			 		
 		}
 		# -------------------------------------------------------
 		public function removeBibliography() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_bib_link = new ms_specimens_x_bibliography($pn_link_id);
 				$t_bib_link->setMode(ACCESS_WRITE);
 				$t_bib_link->delete();
				if ($t_bib_link->numErrors() > 0) {
					foreach ($t_bib_link->getErrors() as $vs_e) {
						$va_errors[] = $vs_e;
					}
					$this->view->setVar("message", join(", ", $va_errors));
				}
 			}
 			$this->bibliographyLookup();
 		}
 		# -------------------------------------------------------
 		public function confirmMove() {
 			$vn_specimen_id = $this->request->getParameter("specimen_id", pInteger);
 			$vn_proj_from = $this->request->getParameter("proj_from", pInteger);
 			$vn_proj_to = $this->request->getParameter("proj_to", pInteger);

 			$this->view->setVar("specimen_id", $vn_specimen_id);
 			$this->view->setVar("proj_from", $vn_proj_from);
 			$this->view->setVar("proj_to", $vn_proj_to);

 			$this->render("confirm_move_html.php");
 		}	
 		# -------------------------------------------------------
 		public function moveSpecimen() {
 			# Verify user can move this specimen
 			$t_from_project = new ms_projects($this->opo_item->get("project_id"));
 			if(!$t_from_project->isFullAccessMember($this->request->user->get("user_id"))){
 				$this->notification->addNotification("Not authorized to access this resource", __NOTIFICATION_TYPE_ERROR__);
 				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));

 			}
			if($this->opo_item->get("specimen_id") && ($pn_move_project_id = $this->request->getParameter('move_project_id', pInteger))){
				# --- change user_id in specimen record to the project admin of the project you're moving the media to
				$t_move_project = new ms_projects($pn_move_project_id);
				$this->opo_item->set("user_id", $t_move_project->get("user_id"));
				$this->opo_item->set('project_id', $pn_move_project_id);
				if (sizeof($va_errors) == 0) {
					# do update
					$this->opo_item->setMode(ACCESS_WRITE);
					$this->opo_item->update();
					if ($this->opo_item->numErrors()) {
						foreach ($this->opo_item->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
						$this->notification->addNotification("Could not move specimen".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
						$this->view->setVar("errors", $va_errors);
						$this->form();
					}else{
						$this->notification->addNotification("Moved ".$this->ops_name_singular." to P".$pn_move_project_id, __NOTIFICATION_TYPE_INFO__);
						$this->opn_item_id = "";
						$this->view->setVar("item_id", "");
						$this->view->setVar("item", new ms_specimens());
						$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
					}
				}
			}else{
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
			}
 		}
 		# -------------------------------------------------------
 		public function lookupSpecimen() {
			$va_lookup_fields = array("Institution Code" => "institutioncode", "Collection Code" => "collectioncode", "Catalog Number" => "catalognumber", "Genus" => "genus", "Species" => "specificepithet");
			$this->view->setVar("lookup_fields", $va_lookup_fields);
			$va_errors = array();
			if($this->request->getParameter('doLookup', pInteger)){
				# build array of lookup terms to send to iDigBio
				$va_lookup_values = array();
				$va_lookup_values2 = array();
				$vb_search_twice = false;
				foreach($va_lookup_fields as $vs_label => $vs_field){
					if($vs_tmp = $this->request->getParameter($vs_field, pString)){
						$va_lookup_values[$vs_field] = $vs_tmp;
						switch($vs_field){
							case "collectioncode":
								# --- skip
							break;
							# ----------------
							case "catalognumber":
								# --- merge collectioncode and catalognumber
								if($va_lookup_values["collectioncode"] && $vs_tmp){
									$va_lookup_values2[$vs_field] = $va_lookup_values["collectioncode"]."-".$vs_tmp;
									$vb_search_twice = true;
								}
							break;
							# ----------------
							default:
								$va_lookup_values2[$vs_field] = $vs_tmp;
							break;
							# ----------------
						}
					}
				}
				if(sizeof($va_lookup_values)){
					# --- search iDigBio
					$t_specimen = new ms_specimens();
					$va_results = $t_specimen->getIDBSpecimenInfo($va_lookup_values);
					if($va_results["success"]){
						$va_data = $va_results["data"];
					}
					if($vb_search_twice){
						# --- search again with the collection and catalog number combo
						$va_results2 = $t_specimen->getIDBSpecimenInfo($va_lookup_values2);
						if($va_results2["success"]){
							# --- merge results if the first search found something
							if(is_array($va_data) && sizeof($va_data)){
								$va_data["items"] = array_merge($va_data["items"], $va_results2["data"]["items"]);
								$va_data["itemCount"] = $va_data["itemCount"] + $va_results2["data"]["itemCount"];
							}else{
								$va_data = $va_results2["data"];
							}
						}
						
					}
					if(is_array($va_data) && sizeof($va_data)){
						$this->view->setVar("results", $va_data);
					}else{
						$va_errors[] = "No results found on idigbio.org";
					}
					# --- search MorphoSource
					$o_search = new SpecimenSearch();
					# --- build the search terms
					$va_search_parts = array();
					if($ps_institution_code = $this->request->getParameter("institutioncode", pString)){
						$va_search_parts[] = "ms_specimens.institution_code:".$ps_institution_code;
					}
					if($ps_collection_code = $this->request->getParameter("collectioncode", pString)){
						$va_search_parts[] = "ms_specimens.collection_code:".$ps_collection_code;
					}
					if($ps_catalog_number = $this->request->getParameter("catalognumber", pString)){
						if($ps_collection_code && $ps_catalog_number){
							$va_search_parts[] = "(ms_specimens.catalog_number:".$ps_catalog_number." OR ms_specimens.catalog_number:".$ps_collection_code."-".$ps_catalog_number.")";
						}else{
							$va_search_parts[] = "ms_specimens.catalog_number:".$ps_catalog_number;
						}
					}
					if($ps_genus = $this->request->getParameter("genus", pString)){
						$va_search_parts[] = "ms_taxonomy_names.genus:".$ps_genus;
					}
					if($ps_species = $this->request->getParameter("specificepithet", pString)){
						$va_search_parts[] = "ms_taxonomy_names.species:".$ps_species;
					}
					#print join(" AND ", $va_search_parts);
					$qr_ms_specimens = $o_search->search(join(" AND ", $va_search_parts));
					$this->view->setVar("morphosource_results", $qr_ms_specimens);
					$this->view->setVar("num_morphosource_results", $qr_ms_specimens->numHits());
					if(!$qr_ms_specimens->numHits()){
						$va_errors[] = "No results found on MorphoSource";
					}
				}else{
					$va_errors[] = "Please enter a search term";
					
				}
				if(sizeof($va_errors)){
					$this->view->setVar("errors", $va_errors);
				}
			}
			$this->render('Specimens/specimen_lookup_html.php');
		}
		# -------------------------------------------------------
		public function importIDBSpecimen() {
			if($vs_uuid = $this->request->getParameter('uuid', pString)){
				$t_specimen = new ms_specimens();
				$va_import_results = $t_specimen->importIDBSpecimen($this->request, $this->opn_project_id, $vs_uuid);
				if($va_import_results["success"]){
					$this->opo_item = $va_import_results["specimen"];
					$this->notification->addNotification($va_import_results["message"], __NOTIFICATION_TYPE_INFO__);								
										
					# --- email Doug about Specimen import --- douglasmb@gmail.com
					caSendMessageUsingView($this->request, 'douglasmb@gmail.com', 'do-not-reply@morphosource.org', "[Morphosource] iDigBio specimen import notification", 'idigbio_specimen_import_notification.tpl', array(
						'user' => $this->request->user,
						'specimen' => $this->opo_item,
						'project' => $this->opo_project,
						'request' => $this->request
					));
					$this->view->setvar("item_id", $this->opo_item->get("specimen_id"));
					$this->view->setvar("item", $this->opo_item);
					$this->form();
					return;
				}else{
					$this->notification->addNotification("There were errors importing the specimen: ".join("; ", $va_import_results["errors"]), __NOTIFICATION_TYPE_INFO__);
					$this->view->setVar("errors", $va_import_results["errors"]);
					$this->lookupSpecimen();
					return;
				}
				
			}else{
				$this->view->setVar("errors", array("Could not import specimen; no uuid passed"));
				$this->lookupSpecimen();
			}
		}
		# -------------------------------------------------------
 	}
 ?>