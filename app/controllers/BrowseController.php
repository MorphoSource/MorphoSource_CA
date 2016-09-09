<?php
/* ----------------------------------------------------------------------
 * app/controllers/BrowseController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_resolved_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class BrowseController extends ActionController {
 		# -------------------------------------------------------
			/** 
			 * declare table instance
			*/
			protected $opo_project;
			protected $opa_project_ids;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			$this->opo_project = new ms_projects();
 			$this->opa_project_ids = caExtractArrayValuesFromArrayOfArrays($this->opo_project->getProjectsForMember($this->request->getUserID()), 'project_id');
 			if (!$this->opa_project_ids) { $this->opa_project_ids = array(); }
 			
 			# --- check for last browse so can load it
 			$this->view->setvar("browse_institution_id", $this->request->session->getVar("browse_institution_id"));
 			$this->view->setvar("browse_bibref_id", $this->request->session->getVar("browse_bibref_id"));
 			$this->view->setvar("browse_project_id", $this->request->session->getVar("browse_project_id"));
 			$this->view->setvar("browse_taxon_id", $this->request->session->getVar("browse_taxonomy"));
 			//$this->view->setvar("browse_species", $this->request->session->getVar("browse_species"));
 			$this->view->setvar("browse_taxonomy", $this->request->session->getVar("browse_taxonomy"));
 			
 			$this->view->setvar("last_browse", $this->request->session->getVar("last_browse"));
 		}
 		# -------------------------------------------------------
 		function Index() { 			
			$this->render('Browse/browse_main_html.php');
 		}
 		# -------------------------------------------------------
 		function institutionList(){
 			$this->request->session->setVar("last_browse", "institutions");
			$o_db = new Db();
			$va_params = array();
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}
			
			$q_institutions = $o_db->query("
				SELECT DISTINCT i.name, i.institution_id 
				FROM ms_institutions i
				INNER JOIN ms_specimens AS s ON i.institution_id = s.institution_id
				INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id
				WHERE
					m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "")."
				ORDER BY i.name", $va_params);
			$this->view->setvar("institutions", $q_institutions);
			$this->render('Browse/institution_list_html.php');
 		}
 		# -------------------------------------------------------
 		function taxonList(){
 			$this->request->session->setVar("last_browse", "taxonomy");
			$o_db = new Db();
			$va_params = array();
			
 				
			if($pn_taxon_id = $this->request->getParameter('taxon_id', pInteger)) {
 				$this->request->session->setVar("browse_taxonomy", $pn_taxon_id);
 			} else {
 				$pn_taxon_id = $this->request->session->getVar("browse_taxonomy");
 			}
			$ps_rank = strtolower($this->request->getParameter('rank', pString));
			if (!in_array($ps_rank, ms_resolved_taxonomy::$s_ranks)) { $ps_rank = 'Phylum'; }
			
			$va_path = [];
			
			$t_taxon = new ms_resolved_taxonomy($pn_taxon_id);
			if ($t_taxon->getPrimaryKey()) {
				//
				// Browse on a specific taxon
				//
				$va_ancestors = array_reverse($t_taxon->getHierarchyAncestors(null, ['includeSelf' => true]));
				$vn_i = 1;
				foreach($va_ancestors as $va_ancestor) {
					$va_path[] = "<a href='#' ".(($vn_i < sizeof($va_ancestors)) ? "class='blueText'" : "")." onClick='jQuery(\"#browseArea\").load(\"".caNavUrl($this->request, '', 'Browse', 'taxonList', ['taxon_id' => $va_ancestor['NODE']['taxon_id'], 'rank' => $va_ancestor['NODE']['rank']])."\"); return false;'>".$va_ancestor['NODE']['name']."</a> (".$va_ancestor['NODE']['published_specimen_count'].")";
					$vn_i++;
				}
				
				// Return children of the specified taxon
				$vs_where_sql = 'parent_id = ?';
				$va_params[] = (int)$pn_taxon_id;
				
				$qr_children = $t_taxon->getHierarchy($pn_taxon_id);
				$va_child_ids = $qr_children->getAllFieldValues('taxon_id');
				
				$va_specimens = [];
				
				// Don't generate specimen lists at the highest levels
				if (!in_array($ps_rank, ['kingdom'])) {
					// grab all specimens attached to at least one child taxon
					$va_specimen_query_params = [$va_child_ids];
					
					$vs_published_sql = '';
					if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
						$va_specimen_query_params[] =  $this->opa_project_ids;
						$vs_published_sql = ' AND ((m.published >= 1) OR (m.published = 0 AND m.project_id IN (?)))';
					} else {
						$vs_published_sql = ' AND (m.published >= 1)';
					}
					$q_specimens = $o_db->query("
						SELECT DISTINCT sxrt.specimen_id
						FROM ms_resolved_taxonomy rt
						INNER JOIN ms_specimens_x_resolved_taxonomy AS sxrt ON sxrt.taxon_id = rt.taxon_id
						INNER JOIN ms_specimens AS s ON s.specimen_id = sxrt.specimen_id
						INNER JOIN ms_media AS m ON s.specimen_id = m.specimen_id
						WHERE
							rt.taxon_id IN (?) {$vs_published_sql}
						
						ORDER BY s.institution_code, s.collection_code, s.catalog_number
					", $va_specimen_query_params);
					
					$va_specimens = $q_specimens->getAllFieldValues('specimen_id');
				}
			} else {
				//
				// Start at specified rank
				//
				$vs_where_sql = 'rank = ?';
				$va_params[] = $ps_rank;
				$va_specimens = [];
			}
			
			$this->view->setVar('specimens', $va_specimens);
			$this->view->setVar('taxon_id', $pn_taxon_id);
			$this->view->setVar('rank', $ps_rank);
			$this->view->setVar('path', $va_path);
			$this->view->setVar('nextRank', $vs_next_rank = (($vn_index = array_search($ps_rank, ms_resolved_taxonomy::$s_ranks)) >= 0) ? ms_resolved_taxonomy::$s_ranks[$vn_index + 1] : "class");
			
			$q_taxa = $o_db->query("
				SELECT rt.name, rt.taxon_id, rt.rank, rt.published_specimen_count
				FROM ms_resolved_taxonomy rt
				WHERE
					{$vs_where_sql} AND published_specimen_count > 0
				ORDER BY rt.name
			", $va_params);
			
			$this->view->setVar("taxa", $q_taxa);
			$this->render('Browse/taxon_list_html.php');
 		}
 		# -------------------------------------------------------
 		function specimenListForTaxon(){
			$pn_taxon_id = $this->request->getParameter('taxon_id', pInteger);
			
			$t_taxon = new ms_resolved_taxonomy($pn_taxon_id);
			if ($t_taxon->getPrimaryKey()) {
				$va_child_ids = $t_taxon->getHierarchy(null, ['idsOnly' => true]);
				print_R($va_child_ids);
			}
			
 		}
 		# -------------------------------------------------------
 		function genusList(){
			$o_db = new Db();
			$va_params = array();
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}
			
			$q_genus = $o_db->query($x="
				SELECT DISTINCT t.genus 
				FROM ms_taxonomy_names t
				INNER JOIN ms_specimens_x_taxonomy AS sxt ON sxt.taxon_id = t.taxon_id
				INNER JOIN ms_media AS m ON sxt.specimen_id = m.specimen_id
				WHERE
					m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "")."
				ORDER BY t.genus
			", $va_params);
			$this->view->setvar("genus", $q_genus);
			$this->render('Browse/genus_list_html.php');
 		}
 		# -------------------------------------------------------
 		function speciesList(){		
 			$ps_genus = $this->request->getParameter('genus', pString);
 			$va_params = array($ps_genus);
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}	
 			if($ps_genus){
				$o_db = new Db();
				$q_species = $o_db->query("
					SELECT DISTINCT t.species 
					FROM ms_taxonomy_names t 
					INNER JOIN ms_specimens_x_taxonomy AS sxt ON sxt.taxon_id = t.taxon_id
					INNER JOIN ms_media AS m ON sxt.specimen_id = m.specimen_id
					WHERE t.genus = ? AND (m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "").")
					ORDER BY t.species", $va_params);
				$this->view->setvar("species", $q_species);
				$this->render('Browse/species_list_html.php');
			}
 		}
 		# -------------------------------------------------------
 		function bibliographyList(){
 			$this->request->session->setVar("last_browse", "bibliography");
 			$va_params = array();
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}
			$o_db = new Db();
			$q_bib = $o_db->query("
				SELECT DISTINCT b.bibref_id 
				FROM ms_bibliography b 
				INNER JOIN ms_specimens_x_bibliography AS sxb ON sxb.bibref_id = b.bibref_id 
				INNER JOIN ms_media AS m ON sxb.specimen_id = m.specimen_id
				WHERE
					m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "")."
				ORDER BY b.authors
			", $va_params);
			$this->view->setvar("bibliography", $q_bib);
			$this->render('Browse/bibliography_list_html.php');
 		}
 		# -------------------------------------------------------
 		function projectList(){
 			$this->request->session->setVar("last_browse", "projects");
 			$va_params = array();
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}
			$o_db = new Db();
			$q_projects = $o_db->query("
				SELECT DISTINCT p.project_id, p.name, p.abstract 
				FROM ms_projects p
				LEFT JOIN ms_specimens AS s ON p.project_id = s.project_id
				LEFT JOIN ms_media AS m ON m.specimen_id = s.specimen_id
				WHERE p.publication_status >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "")."
				ORDER BY p.name", $va_params);
			$this->view->setvar("projects", $q_projects);
			$this->render('Browse/project_list_html.php');
 		}
 		# -------------------------------------------------------
 		function specimenResults(){
 			$va_params = array();
			if ($this->request->isLoggedIn() && sizeof($this->opa_project_ids)) {
				$va_params[] =  $this->opa_project_ids;
			}
 			# --- accepts institution_id, taxonomy, bibref_if
 			$pn_institution_id = $this->request->getParameter('institution_id', pInteger);
 			$pn_bibref_id = $this->request->getParameter('bibref_id', pInteger);
 			$pn_project_id = $this->request->getParameter('project_id', pInteger);
 			$ps_genus = $this->request->getParameter('genus', pString);
 			$ps_species = $this->request->getParameter('species', pString);
 			# --- save these as session vars so we can recreate the last browse
 			$this->request->session->setVar("browse_institution_id", $pn_institution_id);
 			$this->request->session->setVar("browse_bibref_id", $pn_bibref_id);
 			$this->request->session->setVar("browse_project_id", $pn_project_id);
 			if(!$ps_species){
 				$this->request->session->setVar("browse_genus", $ps_genus);
 			}
 			$this->request->session->setVar("browse_species", $ps_species);
			
			$o_db = new Db();
			if($pn_institution_id){
				array_unshift($va_params, $pn_institution_id);
 				$q_specimens = $o_db->query("
 					SELECT DISTINCT s.specimen_id 
 					FROM ms_specimens s
 					INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id
 					WHERE s.institution_id = ? AND (m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "").")
 					ORDER BY s.institution_code, s.collection_code, s.catalog_number", $va_params);
 			}elseif($pn_bibref_id){
				array_unshift($va_params, $pn_bibref_id);
 				$q_specimens = $o_db->query("
 					SELECT DISTINCT s.specimen_id 
 					FROM ms_specimens_x_bibliography sxb 
 					INNER JOIN ms_specimens AS s ON sxb.specimen_id = s.specimen_id 
 					INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id
 					WHERE sxb.bibref_id = ? AND (m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "").")
 					ORDER BY s.institution_code, s.collection_code, s.catalog_number", $va_params);
 			}elseif($pn_project_id){
				array_unshift($va_params, $pn_project_id);
				array_unshift($va_params, $pn_project_id);
 				$q_specimens = $o_db->query("
 					SELECT DISTINCT s.specimen_id 
 					FROM ms_specimens s
 					INNER JOIN ms_media AS m ON m.specimen_id = s.specimen_id
 					WHERE (s.project_id = ? OR m.project_id = ?) AND (m.published >= 1 ".((sizeof($this->opa_project_ids)) ? " OR (m.published = 0 AND m.project_id IN (?))" : "").")
 					ORDER BY s.institution_code, s.collection_code, s.catalog_number", $va_params);
 			}
 			
 			$va_specimen_result_ids = array();
 			if($q_specimens->numRows()){
				while($q_specimens->nextRow()){
					$va_specimen_result_ids[] = $q_specimens->get("specimen_id");
				}
			} 	
			
			$this->view->setVar("specimens", $va_specimen_result_ids);
			
 			$o_result_context = new ResultContext($this->request, "ms_specimens", "specimen_browse");
 			$o_result_context->setAsLastFind();
			$o_result_context->setResultList($va_specimen_result_ids);
			$o_result_context->saveContext();
 			
 			$this->render('Browse/specimen_results_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>