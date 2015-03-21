<?php
/* ----------------------------------------------------------------------
 * app/controllers/Administration/ProjectsController.php :
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

 	require_once(__CA_MODELS_DIR__.'/ms_projects.php');
 	require_once(__CA_MODELS_DIR__.'/ms_specimens.php');

 	class ProjectsController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			if(!$po_request->user->canDoAction("is_administrator")) { die("Insufficient privileges"); }
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 		}
 		# -------------------------------------------------------
 		public function ListProjects() {
 			JavascriptLoadManager::register('tableList');
 			$t_project = new ms_projects();
 			
 			$o_db = new Db();
 			$q_all_projects = $o_db->query("SELECT project_id from ms_projects where deleted = 0 order by project_id desc");

 			$this->view->setVar('project', $t_project);
 			$this->view->setVar('all_projects', $q_all_projects);

 			$this->render('project_list_html.php');
 		}
 		# -------------------------------------------------------
 		public function DownloadMediaReport() {		
			$o_db = new Db();
			$q_projects = $o_db->query("SELECT name, project_id from ms_projects order by name");
			$va_project_medias[] = array();
			$t_specimen = new ms_specimens();
			$va_specimen_names = array();
			while($q_projects->nextRow()){
				$va_media = array();
				# --- select media fro project
				$q_media = $o_db->query("
					SELECT m.media_id, m.specimen_id, m.element, m.side, m.project_id
					FROM ms_media m
					WHERE m.project_id = ?
					ORDER BY m.media_id
				", $q_projects->get("project_id"));
				if($q_media->numRows()){
					while($q_media->nextRow()){
						$vs_specimen_name = "";
						if(!$va_specimen_name[$q_media->get("specimen_id")]){
							$vs_specimen_name = $t_specimen->getSpecimenName($q_media->get("specimen_id"));
						}
						$vs_specimen_name = str_replace("<em>", "", $vs_specimen_name);
						$vs_specimen_name = str_replace("</em>", "", $vs_specimen_name);
						# --- get the number of files for the group
						$q_media_files = $o_db->query("SELECT media_file_id FROM ms_media_files WHERE media_id = ?", $q_media->get("media_id"));
						$va_media[$vs_specimen_name]["M".$q_media->get("media_id")] = array("element" => $q_media->get("element"), "side" => $q_media->get("side"), "num_files" => $q_media_files->numRows());
					}
				}
				$va_project_medias[$q_projects->get("name")] = $va_media;
				
			}
			#print "<pre>";
			#print_r($va_project_medias);
			#print "</pre>";
			#exit;
			
			
			
			
			
			
			if(sizeof($va_project_medias)){
 				header("Content-Disposition: attachment; filename=MorphoSourceMediaByProject_".date("m-d-y").".xls");
				header("Content-type: application/vnd.ms-excel");
				$va_rows = array();
				$va_row = array("Project", "Specimen", "Media group", "Element", "Side", "Number of files");
				$va_rows[] = join("\t", $va_row);
				$vs_display_project = "";
				foreach($va_project_medias as $vs_project => $va_project_media){
					foreach($va_project_media as $vs_specimen => $va_media){
						foreach($va_media as $vs_media_group => $va_media_info){
							$va_row = array($vs_project, $vs_specimen, $vs_media_group, $va_media_info["element"], $va_media_info["side"], $va_media_info["num_files"]);
							$va_rows[] = join("\t", $va_row);
						}
					}
				}
			}
			$this->response->addContent(join("\n", $va_rows), 'view');
 		}
 		# -------------------------------------------------------
 	}
 ?>