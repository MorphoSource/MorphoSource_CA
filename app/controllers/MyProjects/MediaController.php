<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/MediaController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class MediaController extends ActionController {
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
 			JavascriptLoadManager::register("panel");
 			JavascriptLoadManager::register("3dmodels");
 			
 			
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
 			if($this->request->session->getVar('current_project_id') && $this->opo_project->isMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id'))){
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
			
			# --- load the media object
			$this->opo_item = new ms_media();
			$this->opn_item_id = $this->request->getParameter('media_id', pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
				# --- check if the media is part of the current project
				if($this->opo_item->get("project_id") != $this->opn_project_id){
					$this->notification->addNotification("The media record you are trying to access is not part of the project you are currently editing", __NOTIFICATION_TYPE_ERROR__);
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
					$this->ops_item_name .= ", ";
				}
			}
			$this->view->setvar("item_name", $this->ops_item_name);
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard"));
 		}
 		# -------------------------------------------------------
 		public function form() {
 			# --- pass the facility name for preloading lookup if available
 			if($this->opo_item->get("facility_id")){
 				$t_facility = new ms_facilities($this->opo_item->get("facility_id"));
 				$this->view->setVar("facility_name", $t_facility->get("name").(($t_facility->get("name") && $t_facility->get("institution")) ? ", " : "").$t_facility->get("institution"));
 			}
			$this->render('Media/form_html.php');
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
					case 'media':
						if($_FILES['media']['tmp_name']){
							$this->opo_item->set('media', $_FILES['media']['tmp_name'], array('original_filename' => $_FILES['media']['name']));
						}elseif(!$this->opo_item->get('media')){
							$va_errors[$vs_f] = "Please upload a media file";
						}
						break;
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
					case 'published':
						if($_REQUEST['published'] != $this->opo_item->get('published')){
							if(($_REQUEST['published'] == 1)){
								# --- publishing media so set published on date
								$this->opo_item->set("published_on",'now');
							}else{
								# --- unpublishing media so clear published_on date
								$this->opo_item->set("published_on",null);
							}
						}
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
					# -----------------------------------------------
					case 'facility_id':
						if($_REQUEST["facility_id"]){
							$this->opo_item->set("facility_id",$_REQUEST["facility_id"]);
						}elseif($_REQUEST["name"]){
							# --- check if facility info was entered in AJAX form
							$t_facility = new ms_facilities();
							$va_facility_fields = $t_facility->getFormFields();
							$va_facility_errors = array();
							while(list($vs_facility_f,$va_facility_attr) = each($va_facility_fields)) {
								switch($vs_facility_f) {
									# -----------------------------------------------
									case 'project_id':
										if(!$t_facility->get("project_id")){
											$t_facility->set($vs_facility_f,$this->opn_project_id);
										}
										break;
									# -----------------------------------------------
									case 'user_id':
										if(!$t_facility->get("user_id")){
											$t_facility->set($vs_facility_f,$this->request->user->get("user_id"));
										}
										break;
									# -----------------------------------------------
									default:
										$t_facility->set($vs_facility_f,$_REQUEST[$vs_facility_f]); # set field values
										break;
									# -----------------------------------------------
								}
								if ($t_facility->numErrors() > 0) {
									foreach ($t_facility->getErrors() as $vs_e) {
										$va_facility_errors[$vs_facility_f] = $vs_e;
									}
								}
							}
							if (sizeof($va_facility_errors) == 0) {
								$t_facility->setMode(ACCESS_WRITE);
								$t_facility->insert();
							}
							if ($t_facility->numErrors()) {
								foreach ($t_facility->getErrors() as $vs_e) {  
									$va_errors["general"] = $vs_e;
								}
							}else{
								$this->opo_item->set("facility_id",$t_facility->get("facility_id"));
							}
						}else{
							$va_errors[$vs_f] = "Please enter the facility the media was scanned at";
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
				$vb_was_insert = false;
				$this->opo_item->setMode(ACCESS_WRITE);
				if ($this->opo_item->get($this->ops_primary_key)){
					$this->opo_item->update();
				} else {
					$this->opo_item->insert();
					$vb_was_insert = true;
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
				$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->mediaInfo();
			} 			 			
 		}
 		
 		# -------------------------------------------------------
 		public function mediaViewer() {
 			$pn_media_id = $this->request->getParameter('media_id', pInteger);
 			// TODO: does user own this media?
 			$t_media = new ms_media($pn_media_id);
 			$this->view->setVar('t_media', $t_media);
 			$this->render('Media/ajax_media_viewer_html.php');
 		}
 		# -------------------------------------------------------
 		public function mediaInfo() {
 			$this->render('Media/media_info_html.php');
 		}
 		# -------------------------------------------------------
 		public function publish() {
			if($this->opo_item->get("media_id") && $this->opo_item->get("media")){
				$this->opo_item->set('published', 1);
				$this->opo_item->set('published_on', 'now');
				if (sizeof($va_errors) == 0) {
					# do update
					$this->opo_item->setMode(ACCESS_WRITE);
					$this->opo_item->update();
					if ($this->opo_item->numErrors()) {
						foreach ($this->opo_item->getErrors() as $vs_e) {  
							$va_errors["general"] = $vs_e;
						}
					}else{
						$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
					}
				}
				if(sizeof($va_errors) > 0){
					$this->notification->addNotification("Could not publish media".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					$this->view->setVar("errors", $va_errors);
					$this->form();
				}else{
					$this->opn_item_id = $this->opo_item->get("media_id");
					$this->view->setVar("item_id", $this->opn_item_id);
					$this->view->setVar("item", $this->opo_item);
					$this->mediaInfo();
				}
			}
 		}
 		# -------------------------------------------------------
 		public function specimenLookup() {
 			# --- pass the name of the current specimen
 			if($this->opo_item->get("specimen_id")){
 				$t_specimen = new ms_specimens();
 				$t_specimen->load($this->opo_item->get("specimen_id"));
 				$va_name = array();
 				if($t_specimen->get("institution_code")){
 					$va_name[] = $t_specimen->get("institution_code");
 				}
 				if($t_specimen->get("collection_code")){
 					$va_name[] = $t_specimen->get("collection_code");
 				}
 				if($t_specimen->get("catalog_number")){
 					$va_name[] = $t_specimen->get("catalog_number");
 				}
 				$vs_specimen_name = implode("/", $va_name);
 				if($vs_specimen_name){
 					$this->view->setVar("specimen_name", $vs_specimen_name);
 				}
 				# --- check if there is a taxonommic name for the specimen
 				$va_specimen_taxonomy = $t_specimen->getSpecimenTaxonomy();
 				$this->view->setVar("specimen_taxonomy", $va_specimen_taxonomy);
 				
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Media/media_specimen_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkSpecimen() {
			$va_errors = array();
			$vs_message = "";
			# --- set specimen that is passed from lookup form
			if($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)){
				$this->opo_item->set("specimen_id",$pn_specimen_id);
				if ($this->opo_item->numErrors() > 0) {
					foreach ($this->opo_item->getErrors() as $vs_e) {
						$va_errors["specimen_id"] = $vs_e;
					}
				}
			}else{
				$va_errors["general"] = _t("Please select a specimen");
			}		
			if (sizeof($va_errors) == 0) {
				# do update
				$this->opo_item->setMode(ACCESS_WRITE);
				$this->opo_item->update();
	
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$vs_message = "Saved media specimen";
				}
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("message", $vs_message);
				$this->specimenLookup();
			}else{
				$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->view->setVar("message", $vs_message);
				$this->specimenLookup();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function linkSpecimenTaxon() {
			$va_errors = array();
			$vs_message = "";
			# --- check the specimen_id and alt_id (key for ms_taxonomy_names) are passed through lookup form
			if(($pn_specimen_id = $this->request->getParameter('specimen_id', pInteger)) && ($pn_alt_id = $this->request->getParameter('alt_id', pInteger))){
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
				$this->specimenLookup();
			}else{
				#$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->view->setVar("taxonomy_message", $vs_message);
				$this->specimenLookup();
			} 			 			
 		}
  		# -------------------------------------------------------
 		public function bibliographyLookup() {
 			# --- pass the list of linked bib citations
 			$va_bib_citations = array();
 			if($this->opn_item_id){
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id, mxb.pp FROM ms_media_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.media_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "page" => $q_bib->get("pp"), "link_id" => $q_bib->get("link_id"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
 				$this->view->setVar("bib_citations", $va_bib_citations);
 				$ps_new_message = $this->request->getParameter('message', pString);
 				$this->view->setVar("new_message", urldecode($ps_new_message));
 			}
 			$this->render('Media/media_bibliography_form_html.php');
 		}
 		# -------------------------------------------------------
 		public function linkBibliography() {
			$va_errors = array();
			$vs_message = "";
			# --- make link to bib that is passed from lookup form
			if($pn_bibliography_id = $this->request->getParameter('bibliography_id', pInteger)){
				$t_bib_link = new ms_media_x_bibliography();
				# --- check that there is not already a link to this bib ref
				if($t_bib_link->load(array("bibref_id" => $pn_bibliography_id, "media_id" => $this->opn_item_id))){
					$va_errors["general"] = "There is already a link to this bibliographic citation";
				}else{
					$t_bib_link->set("bibref_id",$pn_bibliography_id);
					$t_bib_link->set("media_id",$this->opn_item_id);
					$t_bib_link->set("user_id",$this->request->user->get("user_id"));
					if($vs_page = $this->request->getParameter('page', pString)){
						$t_bib_link->set("pp",$vs_page);
					}
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
					$vs_message = "Saved media bibliography";
				}
			}
			if(sizeof($va_errors) > 0){
				$vs_message = "There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : "");
				$this->view->setVar("message", $vs_message);
				$this->bibliographyLookup();
			}else{
				$this->opn_item_id = $this->opo_item->get("media_id");
				$this->view->setVar("message", $vs_message);
				$this->bibliographyLookup();
			} 			 		
 		}
 		# -------------------------------------------------------
 		public function removeBibliography() {
 			if($pn_link_id = $this->request->getParameter('link_id', pInteger)){
 				$t_bib_link = new ms_media_x_bibliography($pn_link_id);
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
					$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "Dashboard"));
				}				
			}else{
				$this->view->setVar("item_name", $this->ops_item_name);
				$this->render('General/delete_html.php');
			}
 		}
 		# -------------------------------------------------------
		/**
		 * Download media
		 */ 
		public function DownloadMedia() {
			$ps_version = "original";
			
			$va_versions = $this->opo_item->getMediaVersions('media');
			
			if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
			$this->view->setVar('version', $ps_version);
			
			$va_version_info = $this->opo_item->getMediaInfo('media', $ps_version);
			$this->view->setVar('version_info', $va_version_info);

			$va_info = $this->opo_item->getMediaInfo('media');
			$vs_idno_proc = $this->opo_item->get('media_id');
			if ($va_info['ORIGINAL_FILENAME']) {
				$va_tmp = explode('.', $va_info['ORIGINAL_FILENAME']);
				if (sizeof($va_tmp) > 1) { 
					if (strlen($vs_ext = array_pop($va_tmp)) < 3) {
						$va_tmp[] = $vs_ext;
					}
				}
				$this->view->setVar('version_download_name', join('_', $va_tmp).'.'.$va_version_info['EXTENSION']);					
			} else {
				$this->view->setVar('version_download_name', 'morphosourceM'.$vs_idno_proc.'.'.$va_version_info['EXTENSION']);
			}
			$this->view->setVar('version_path', $this->opo_item->getMediaPath('media', $ps_version));
			
			$vn_rc = $this->render('Media/media_download_binary.php');
			return $vn_rc;
		}
 		# -------------------------------------------------------
 	}
 ?>