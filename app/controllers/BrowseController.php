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
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class BrowseController extends ActionController {
 		# -------------------------------------------------------
			/** 
			 * declare table instance
			*/
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			# --- check for last browse so can load it
 			$this->view->setvar("browse_institution_id", $this->request->session->getVar("browse_institution_id"));
 			$this->view->setvar("browse_bibref_id", $this->request->session->getVar("browse_bibref_id"));
 			$this->view->setvar("browse_project_id", $this->request->session->getVar("browse_project_id"));
 			$this->view->setvar("browse_genus", $this->request->session->getVar("browse_genus"));
 			$this->view->setvar("browse_species", $this->request->session->getVar("browse_species"));
 		}
 		# -------------------------------------------------------
 		function Index() { 			
			$this->render('Browse/browse_main_html.php');
 		}
 		# -------------------------------------------------------
 		function institutionList(){
			$o_db = new Db();
			$q_institutions = $o_db->query("SELECT name, institution_id from ms_institutions order by name");
			$this->view->setvar("institutions", $q_institutions);
			$this->render('Browse/institution_list_html.php');
 		}
 		# -------------------------------------------------------
 		function genusList(){
			$o_db = new Db();
			$q_genus = $o_db->query("SELECT DISTINCT genus FROM ms_taxonomy_names order by genus");
			$this->view->setvar("genus", $q_genus);
			$this->render('Browse/genus_list_html.php');
 		}
 		# -------------------------------------------------------
 		function speciesList(){			
 			$ps_genus = $this->request->getParameter('genus', pString);
 			if($ps_genus){
				$o_db = new Db();
				$q_species = $o_db->query("SELECT DISTINCT species FROM ms_taxonomy_names WHERE genus = ? order by species", $ps_genus);
				$this->view->setvar("species", $q_species);
				$this->render('Browse/species_list_html.php');
			}
 		}
 		# -------------------------------------------------------
 		function bibliographyList(){
			$o_db = new Db();
			$q_bib = $o_db->query("SELECT DISTINCT b.bibref_id from ms_bibliography b INNER JOIN ms_specimens_x_bibliography AS sxb ON sxb.bibref_id = b.bibref_id ORDER BY b.authors");
			$this->view->setvar("bibliography", $q_bib);
			$this->render('Browse/bibliography_list_html.php');
 		}
 		# -------------------------------------------------------
 		function projectList(){
			$o_db = new Db();
			$q_projects = $o_db->query("SELECT project_id, name, abstract from ms_projects WHERE publication_status = 1 ORDER BY name");
			$this->view->setvar("projects", $q_projects);
			$this->render('Browse/project_list_html.php');
 		}
 		# -------------------------------------------------------
 		function specimenResults(){
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
 				$q_specimens = $o_db->query("SELECT specimen_id FROM ms_specimens WHERE institution_id = ? order by institution_code, collection_code, catalog_number", $pn_institution_id);
 			}elseif($pn_bibref_id){
 				$q_specimens = $o_db->query("SELECT s.specimen_id FROM ms_specimens_x_bibliography sxb INNER JOIN ms_specimens AS s ON sxb.specimen_id = s.specimen_id WHERE sxb.bibref_id = ? order by s.institution_code, s.collection_code, s.catalog_number", $pn_bibref_id);
 			}elseif($pn_project_id){
 				$q_specimens = $o_db->query("SELECT specimen_id FROM ms_specimens WHERE project_id = ? order by institution_code, collection_code, catalog_number", $pn_project_id);
 			}elseif($ps_genus){
 				$q_specimens = $o_db->query("SELECT s.specimen_id FROM ms_specimens_x_taxonomy sxt INNER JOIN ms_taxonomy_names AS tn ON tn.alt_id = sxt.alt_id INNER JOIN ms_specimens s ON sxt.specimen_id = s.specimen_id WHERE tn.genus = ? order by s.institution_code, s.collection_code, s.catalog_number", $ps_genus);
 			}elseif($ps_species){
 				$q_specimens = $o_db->query("SELECT s.specimen_id FROM ms_specimens_x_taxonomy sxt INNER JOIN ms_taxonomy_names AS tn ON tn.alt_id = sxt.alt_id INNER JOIN ms_specimens s ON sxt.specimen_id = s.specimen_id WHERE tn.species = ? order by s.institution_code, s.collection_code, s.catalog_number", $ps_species);
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