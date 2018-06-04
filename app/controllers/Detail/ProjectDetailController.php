<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/SpecimenDetailController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_institutions.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/ca/ResultContext.php');
 
 	class ProjectDetailController extends ActionController {
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
 			protected $ops_context = '';

 		# -------------------------------------------------------
 		/**
 		 * Sets current browse context
 		 * Settings for the current browse are stored per-context. This means if you
 		 * have multiple interfaces in the same application using browse services
 		 * you can keep their settings (and caches) separate by varying the context.
 		 *
 		 * The browse engine and browse controller both have their own context settings
 		 * but the BaseDetailController is setup to make the browse engine's context its own.
 		 * Thus you only need set the context for the engine; the controller will inherit it.
 		 */
 		public function setContext($ps_context) {
 			$this->ops_context = $ps_context;
 		}
 		# -------------------------------------------------------
 		/**
 		 * Returns the current browse context
 		 */
 		public function getContext($ps_context) {
 			return $this->ops_context;
 		}
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			# --- load the project object
			$this->opo_item = new ms_projects();
			$this->opn_item_id = $this->request->getParameter('project_id', pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
			}
			if(!$this->opo_item->get("project_id")){
				$this->notification->addNotification("Invalid project_id", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
			}
 			if (!$this->opo_item->get("publication_status") == 1) {
 				$this->notification->addNotification("Item is not published", __NOTIFICATION_TYPE_ERROR__);
 				$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
 			}
			$this->view->setvar("item_id", $this->opn_item_id);
			$this->view->setvar("project_id", $this->opn_item_id);
			$this->view->setvar("item", $this->opo_item);
			# Next and previous navigation
 			$opo_result_context = new ResultContext($this->request, "ms_projects", ResultContext::getLastFind($this->request, "ms_projects"));
			# Is the item we're show details for in the result set?
 			$this->view->setVar('is_in_result_list', ($opo_result_context->getIndexInResultList($this->opn_item_id) != '?'));
 					
 			$this->view->setVar('next_id', $opo_result_context->getNextID($this->opn_item_id));
 			$this->view->setVar('previous_id', $opo_result_context->getPreviousID($this->opn_item_id));
 			$this->view->setVar('result_context', $opo_result_context);
 			
 		}
 		# -------------------------------------------------------
 		public function show() {
 			JavascriptLoadManager::register("cycle");
			if($this->opn_item_id){
 				# --- can user edit record?
				$vb_show_edit_link = false;
				$t_project = new ms_projects();
				if($this->request->isLoggedIn() && $t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id"))){
					$vb_show_edit_link = true;
				}
 			}
 			$this->view->setVar("show_edit_link", $vb_show_edit_link);
 			
			$this->view->setVar("media_counts", 
				$this->opo_item->getProjectMediaCounts());
			$this->view->setVar("all_linked_media",
				$this->opo_item->numAllMedia());
			$this->view->setVar("specimen_count", 
				$this->opo_item->numSpecimens());
			$this->view->setVar("project_name",
				$this->opo_item->get("name"));
			$this->view->setVar("project_members",
				$this->opo_item->getMembers());
			$this->view->setVar("project_abstract",
				$this->opo_item->get("abstract"));
			$this->view->setVar("project_id",
				$this->opo_item->getPrimaryKey());
			$this->view->setVar("project_url",
				$this->opo_item->get("url"));

			// Sort variable (number, taxon, etc.) handling
			if ($this->request->getParameter('s', pString)) {
				$vs_specimens_group_by = 
					$this->request->getParameter('s', pString);
			} elseif ($this->request->session->getVar('specimens_group_by')) {
				$vs_specimens_group_by = 
					$this->request->session->getVar('specimens_group_by');
			} else {
				$vs_specimens_group_by = 'n';
			}
			if (!in_array($vs_specimens_group_by, 
				['n', 't', 'a', 'm', 'u', 'v'])) {
				$vs_specimens_group_by = 'n';
			}
			$this->view->setVar('specimens_group_by', $vs_specimens_group_by);
			$this->request->session->setVar('specimens_group_by', 
				$vs_specimens_group_by);		

			// Entity format (tile, list) variable handling
			if ($this->request->getParameter('f', pString)) {
				$vs_entity_format = $this->request->getParameter('f', pString);
			} elseif ($this->request->session->getVar('entity_format')){
				$vs_entity_format = 
					$this->request->session->getVar('entity_format');
			} else {
				$vs_entity_format = 't';
			}
			if (!in_array($vs_entity_format, ['t', 'l'])) {
				$vs_entity_format = 't';
			}
			$this->view->setVar("entity_format", $vs_entity_format);
			$this->request->session->setVar('entity_format', $vs_entity_format);

			// Entity type (specimen, media) variable handling			
			if ($this->request->getParameter('t', pString)) {
				$vs_entity_type = $this->request->getParameter('t', pString);
			} elseif ($this->request->session->getVar('entity_type')) {
				$vs_entity_type = $this->request->session->getVar('entity_type');
			}else{
				$vs_entity_type = 's';
			}
			if (!in_array($vs_entity_type, ['s', 'm'])) {
				$vs_entity_type = 's';
			}
			$this->view->setVar('entity_type', $vs_entity_type);
			$this->request->session->setVar('entity_type', $vs_entity_type);

			// Get entity data
			if ($vs_entity_type == 's') {
				switch ($vs_specimens_group_by) {
					case 'u':
						$va_specimens_by_taxonomy = $this->opo_item->
							getProjectSpecimensNestTaxonomy(null, 0, 
								array("published_media_only" => true));
						$va_entity = $va_specimens_by_taxonomy['specimen'];
						$vn_count = $va_specimens_by_taxonomy['numSpecimen'];
						$vb_entity_nest = 1;
						break;
					case 'v':
						$va_specimens_by_taxonomy = $this->opo_item->
							getProjectSpecimensNestTaxonomy(null, 1, 
								array("published_media_only" => true));
						$va_entity = $va_specimens_by_taxonomy['specimen'];
						$vn_count = $va_specimens_by_taxonomy['numSpecimen'];
						$vb_entity_nest = 1;
						break;
					default:
						switch ($vs_specimens_group_by) {
							case 'n':
								$vs_order_by = 'number';
								break;
							case 't':
								$vs_order_by = 'taxon';
								break;
							case 'a':
								$vs_order_by = 'added';
								break;
							case 'm':
								$vs_order_by = 'modified';
								break;
							default:
								$vs_order_by = 'number';
								break;
						}
						$va_entity = $this->opo_item->
							getProjectSpecimens(null, $vs_order_by, 
								array("published_media_only" => true));
						$vn_count = is_array($va_entity) ? 
							sizeof($va_entity) : 0;
						$vb_entity_nest = 0;
						break;
				}
			} elseif ($vs_entity_type == 'm') {
				switch ($vs_specimens_group_by) {
					case 'u':
						$va_media_by_taxonomy = $this->opo_item->
							getProjectMediaNestTaxonomy(null, 0, 
								array(
									"published_media_only" => true,
									"all_specimen_media" => true
							));
						$va_entity = $va_media_by_taxonomy['media'];
						$vn_count = $va_media_by_taxonomy['numMedia'];
						$vb_entity_nest = 1;
						break;
					case 'v':
						$va_media_by_taxonomy = $this->opo_item->
							getProjectMediaNestTaxonomy(null, 1, 
								array(
									"published_media_only" => true,
									"all_specimen_media" => true
							));
						$va_entity = $va_media_by_taxonomy['media'];
						$vn_count = $va_media_by_taxonomy['numMedia'];
						$vb_entity_nest = 1;
						break;
					default:
						switch ($vs_specimens_group_by) {
							case 'n':
								$vs_order_by = 'number';
								break;
							case 't':
								$vs_order_by = 'taxon';
								break;
							case 'a':
								$vs_order_by = 'added';
								break;
							case 'm':
								$vs_order_by = 'modified';
								break;
							default:
								$vs_order_by = 'number';
								break;
						}
						$qr = $this->opo_item->
							getProjectMedia(null, $vs_order_by, 
								array(
									"published_media_only" => true,
									"all_specimen_media" => true
							));
						$va_entity = array();
						$t_media = new ms_media();
						while ($qr->nextRow()) {
							$va_media = $qr->getRow();
							if(!isset($va_entity[$va_media['media_id']])) {
								$va_media['preview'] = 
									$t_media->getPreviewMediaFile(
										$va_media['media_id']); 
								$va_entity[$va_media['media_id']] = $va_media;
							}
						}
						$vn_count = is_array($va_entity) ? 
							sizeof($va_entity) : 0;
						$vb_entity_nest = 0;
						break;
				}
			} 

			$this->view->setVar('va_entity', $va_entity);
			$this->view->setVar('vn_count', $vn_count);
			$this->view->setVar('vb_entity_nest', $vb_entity_nest);		
			
 			$this->render('ms_project_detail_html.php');
 		}
 		# -------------------------------------------------------
 		function specimenByTaxonomy() {
 			JavascriptLoadManager::register("cycle");
			$vn_taxon_id = $this->request->getParameter('taxon_id', pInteger);
			if(!$vn_taxon_id){
				$this->show();
				return;
			}
			# --- are we showing by genus or species?
			$vs_specimens_group_by = 
				$this->request->getParameter('specimens_group_by', pString);
			if(!in_array($vs_specimens_group_by, array("genus", "species", "ht_family"))){
				$vs_specimens_group_by = "genus";
			}
			# --- select the genus or taxa we want to show specimen for
			$o_db = new Db();
			$q_taxonomy = $o_db->query("SELECT ".$vs_specimens_group_by." FROM ms_taxonomy_names WHERE taxon_id = ?", $vn_taxon_id);
			$vs_taxon = "";
			if($q_taxonomy->numRows()){
				while($q_taxonomy->nextRow()){
					$vs_taxon = $q_taxonomy->get($vs_specimens_group_by);
				}
			}
			# --- get the specimen
			$va_specimens_by_taxomony = $this->opo_item->getProjectSpecimensByTaxonomy(null, $vs_specimens_group_by, array("published_media_only" => true, "taxonomy_term" => $vs_taxon, "taxonomy_type" => $vs_specimens_group_by));
			
			$this->view->setVar("specimens_by_taxomony", $va_specimens_by_taxomony);
			$this->view->setVar("taxomony_term", $vs_taxon);
			
			$this->render('specimens_by_taxonomy_html.php');
		}
  		# -------------------------------------------------------
 		function specimenWithoutTaxonomy() {
 			JavascriptLoadManager::register("cycle");
			if(!$this->opn_item_id){
				$this->dashboard();
				return;
			}
			# --- are we showing those missing genus, species, or family?
			$vs_specimens_group_by = $this->request->getParameter('specimens_group_by', pString);
			if(!in_array($vs_specimens_group_by, array("genus", "species", "ht_family"))){
				$vs_specimens_group_by = "genus";
			}
			$vs_specimens_group_by_display = $vs_specimens_group_by;
			if($vs_specimens_group_by == "ht_family"){
				$vs_specimens_group_by_display = "family";
			}
			# --- 
			$o_db = new Db();
			
			# --- get the specimen
			$va_specimens_by_taxomony = $this->opo_item->getProjectSpecimenWithoutTaxonomy(null, null, $vs_specimens_group_by, array("published_media_only" => true));
			
			$this->view->setVar("specimens_by_taxomony", $va_specimens_by_taxomony);
			$this->view->setVar("taxomony_term", $vs_specimens_group_by);
			$this->view->setVar("taxomony_term_display", $vs_specimens_group_by_display);
			
			$this->render('specimen_without_taxonomy_html.php');
		}
  		# -------------------------------------------------------
 	}
 ?>
