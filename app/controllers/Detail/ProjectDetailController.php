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
 			
 			$vs_specimens_group_by = $this->request->getParameter('specimens_group_by', pString);
			
			if($vs_specimens_group_by){
				$this->request->session->setVar('specimens_group_by', $vs_specimens_group_by);
			}elseif($this->request->session->getVar('specimens_group_by')){
				$vs_specimens_group_by = $this->request->session->getVar('specimens_group_by');
			}else{
				$vs_specimens_group_by = "specimen";
			}
			$this->view->setVar("specimens_group_by", $vs_specimens_group_by);		
			
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
			$vs_specimens_group_by = $this->request->session->getVar('sBT_taxon_term');
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