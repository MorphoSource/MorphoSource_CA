<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/DashboardController.php
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
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class StatsController extends ActionController {
 		# -------------------------------------------------------
			/** 
			 * declare table instance
			*/
			protected $opo_project;
			protected $opo_specimen;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			JavascriptLoadManager::register('tableList');
 			$this->opo_project = new ms_projects();
 			$this->opo_specimen = new ms_specimens();
 		}
 		# -------------------------------------------------------
 		function dashboard() {
			$va_rows = array();
			# --- get list of available projects for user
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$this->view->setvar("projects", $va_projects);
			$o_db = new Db();
			if(sizeof($va_projects)){
				foreach($va_projects as $va_project){
					$this->opo_project->load($va_project["project_id"]);
					$va_project_specimens = $this->opo_project->getProjectSpecimens();
					if(sizeof($va_project_specimens)){
						foreach($va_project_specimens as $va_project_specimen){
							$va_specimen_media_ids = $this->opo_specimen->getSpecimenMediaIDs($va_project_specimen["specimen_id"]);
							
							$vn_specimen_media_views = 0;
							$vn_specimen_media_downloads = 0;
							if(is_array($va_specimen_media_ids) && sizeof($va_specimen_media_ids)){
								$q_media_views = $o_db->query("SELECT count(*) c FROM ms_media_view_stats WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");
								$q_media_views->nextRow();
								$vn_specimen_media_views = $q_media_views->get("c");
								
								$q_media_downloads = $o_db->query("SELECT count(*) c FROM ms_media_download_stats WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");
								$q_media_downloads->nextRow();
								$vn_specimen_media_downloads = $q_media_downloads->get("c");								
							}
							$va_rows[] = array(
											"project_name" => $va_project["name"], 
											"specimen_number" => $this->opo_specimen->formatSpecimenNumber($va_project_specimen), 
											"specimen_taxonomy" => $this->opo_specimen->getSpecimenTaxonomy($va_project_specimen["specimen_id"]),
											"specimen_views" => $this->opo_specimen->numViews($va_project_specimen["specimen_id"]),
											"num_specimen_media" => sizeof($va_specimen_media_ids),
											"specimen_media_views" => $vn_specimen_media_views,
											"specimen_media_downloads" => $vn_specimen_media_downloads,
											"specimen_id" => $va_project_specimen["specimen_id"]
										);
						}
					}
				}
			}
			$this->view->setVar("rows", $va_rows);			
 			$this->render('Stats/stats_html.php');
 		}
 		# -------------------------------------------------------
 		function specimenInfo() {
 			$pn_specimen_id = $this->request->getParameter('specimen_id', pInteger);
			if($pn_specimen_id){
				$va_rows = array();
				$va_specimen_info = array();
				$this->opo_specimen->load($pn_specimen_id);
				$va_media_ids = $this->opo_specimen->getSpecimenMediaIDs();
				$va_specimen_info = array("specimen_name" => $this->opo_specimen->getSpecimenName(),
											"specimen_views" => $this->opo_specimen->numViews(),
											"num_specimen_media" => sizeof($va_media_ids)
										);
				$this->view->setVar("specimen_info", $va_specimen_info);
				$o_db = new Db();
				$q_media_views = $o_db->query("SELECT mvs.*, u.fname, u.lname, u.email, u.user_id 
										FROM ms_media_view_stats mvs 
										LEFT JOIN ca_users as u ON mvs.user_id = u.user_id
										WHERE mvs.media_id IN (".join(", ", $va_media_ids).")");
				if($q_media_views->numRows()){
					while($q_media_views->nextRow()){
						$va_rows[$q_media_views->get("media_id")]["views"][$q_media_views->get("view_id")] = array("date" => date("n/j/y G:i", $q_media_views->get("viewed_on")), "user_id" => $q_media_views->get("user_id"), "user_info" => date("n/j/y G:i", $q_media_views->get("viewed_on")).(($q_media_views->get("user_id")) ? ", ".$q_media_views->get("fname")." ".$q_media_views->get("lname").", ".$q_media_views->get("email") : ""));
					}
				}
				
				$q_media_downloads = $o_db->query("SELECT mds.*, u.fname, u.lname, u.email, u.user_id 
										FROM ms_media_download_stats mds 
										INNER JOIN ca_users as u ON mds.user_id = u.user_id
										WHERE mds.media_id IN (".join(", ", $va_media_ids).")");
				if($q_media_downloads->numRows()){
					while($q_media_downloads->nextRow()){
						$va_rows[$q_media_downloads->get("media_id")]["downloads"][$q_media_downloads->get("download_id")] = array("date" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")), "user_id" => $q_media_downloads->get("user_id"), "email" => $q_media_downloads->get("email"), "name" => trim($q_media_downloads->get("fname")." ".$q_media_downloads->get("lname")), "user_info" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")).", ".$q_media_downloads->get("fname")." ".$q_media_downloads->get("lname").", ".$q_media_downloads->get("email"));
					}
				}
				$this->view->setVar("rows", $va_rows);			
 				$this->render('Stats/specimen_info_html.php');
 			}
 		}
 		# -------------------------------------------------------
 		function userInfo() {
 			$pn_user_id = $this->request->getParameter('user_id', pInteger);
			$t_user = new ca_users();
			if($pn_user_id){
				$va_rows = array();
				$va_user_info = array();
				$t_user->load($pn_user_id);
				$va_user_info = array("user_id" => $t_user->get("user_id"),
										"name" => trim($t_user->get("fname")." ".$t_user->get("lname")),
										"email" => $t_user->get("fname"),
										"num_media_views" => $t_user->numMediaViews(),
										"num_specimen_views" => $t_user->numSpecimenViews(),
										"num_downloads" => $t_user->numDownloads()	
									);
				$this->view->setVar("user_info", $va_user_info);			
 				
				$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
				$va_project_ids = array();
				foreach($va_projects as $va_project){
					$va_project_ids[] = $va_project["project_id"];
				}
				$o_db = new Db();
				$qr = $o_db->query("
					SELECT mds.*, m.specimen_id, p.name, p.project_id
					FROM ms_media_download_stats mds
					INNER JOIN ms_media AS m ON mds.media_id = m.media_id
					INNER JOIN ms_projects AS p on m.project_id = p.project_id
					WHERE mds.user_id = ? and m.project_id IN (".join(", ", $va_project_ids).")
				", $pn_user_id);
				
				$va_downloads_for_user = array();
				$t_specimen = new ms_specimens();
				if($qr->numRows()){
					while($qr->nextRow()){
						$va_row = $qr->getRow();
						$va_row["specimen"] = $t_specimen->getSpecimenName($qr->get("specimen_id"));
						$va_downloads_for_user[$qr->get("media_id")][] = $va_row;
					}
				}
				
				$this->view->setVar("downloads", $va_downloads_for_user);			
 				$this->render('Stats/user_info_html.php');
 			}
 		}
 		# -------------------------------------------------------
 	}
 ?>