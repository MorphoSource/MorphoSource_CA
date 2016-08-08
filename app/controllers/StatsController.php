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
					$va_project_specimens = $this->opo_project->getProjectSpecimens(null, null);
					if(sizeof($va_project_specimens)){
						foreach($va_project_specimens as $va_project_specimen){
							# --- only record the specimen info once - it can appear in multiple projects
							if(!$va_rows[$va_project_specimen["specimen_id"]]){
								$va_specimen_media_ids = $this->opo_specimen->getSpecimenMediaIDs($va_project_specimen["specimen_id"]);
								$va_specimen_media_ids_published = $this->opo_specimen->getSpecimenMediaIDs($va_project_specimen["specimen_id"], array("published" => true));
							
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
								# --- what projects do the published media belong to?
								$va_project_names = array();
								if(is_array($va_specimen_media_ids) && sizeof($va_specimen_media_ids)){
									$q_media_projects = $o_db->query("SELECT DISTINCT p.project_id, p.name from ms_media m INNER JOIN ms_projects as p ON p.project_id = m.project_id WHERE m.media_id IN (".join(", ", $va_specimen_media_ids).")");							
									if($q_media_projects->numRows()){
										while($q_media_projects->nextRow()){
											$va_project_names[] = $q_media_projects->get("name");
										}
									}
									$vn_media_projects = $q_media_projects->numRows();
								}
								$va_rows[$va_project_specimen["specimen_id"]] = array(
												"project_name" => join("; ", $va_project_names), 
												"specimen_number" => $this->opo_specimen->formatSpecimenNumber($va_project_specimen), 
												"specimen_taxonomy" => $this->opo_specimen->getSpecimenTaxonomy($va_project_specimen["specimen_id"]),
												"specimen_views" => $this->opo_specimen->numViews($va_project_specimen["specimen_id"]),
												"num_specimen_media" => sizeof($va_specimen_media_ids),
												"num_specimen_media_unpublished" => sizeof($va_specimen_media_ids) - sizeof($va_specimen_media_ids_published),
												"specimen_media_views" => $vn_specimen_media_views,
												"specimen_media_downloads" => $vn_specimen_media_downloads,
												"specimen_id" => $va_project_specimen["specimen_id"]
											);
							}
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
				$o_db = new Db();
				$t_project = new ms_projects();
				$va_rows = array();
				$va_specimen_info = array();
				$this->opo_specimen->load($pn_specimen_id);
				$va_all_media_ids = $this->opo_specimen->getSpecimenMediaIDs();
				if(sizeof($va_all_media_ids)){
					# --- filter out any unpublished media where user does not have access to project
					$q_media_check = $o_db->query("SELECT media_id, project_id, published from ms_media where media_id IN (".join(", ", $va_all_media_ids).")");
					$va_media_checked_ids = array();
					if($q_media_check->numRows()){
						while($q_media_check->nextRow()){
							if($q_media_check->get("published") || $t_project->isMember($this->request->user->get("user_id"), $q_media_check->get("project_id"))){
								$va_media_checked_ids[] = $q_media_check->get("media_id");
							}
						}
						$va_media_ids = $va_media_checked_ids;
					}
					$va_specimen_info = array("specimen_name" => $this->opo_specimen->getSpecimenName(),
												"specimen_views" => $this->opo_specimen->numViews(),
												"num_specimen_media" => sizeof($va_all_media_ids),
												"num_specimen_media_no_access" => sizeof($va_all_media_ids) - sizeof($va_media_checked_ids)
											);
					$this->view->setVar("specimen_info", $va_specimen_info);
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
					$va_downloads_by_file = array();
					if($q_media_downloads->numRows()){
						while($q_media_downloads->nextRow()){
							$va_rows[$q_media_downloads->get("media_id")]["downloads"][$q_media_downloads->get("download_id")] = array("media_file_id" => $q_media_downloads->get("media_file_id"), "date" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")), "user_id" => $q_media_downloads->get("user_id"), "email" => $q_media_downloads->get("email"), "name" => trim($q_media_downloads->get("fname")." ".$q_media_downloads->get("lname")), "user_info" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")).", ".$q_media_downloads->get("fname")." ".$q_media_downloads->get("lname").", ".$q_media_downloads->get("email"));
							$va_downloads_by_file[$q_media_downloads->get("media_id")][$q_media_downloads->get("media_file_id")][] = $q_media_downloads->get("user_id");
						}
					}
					$q_media_project = $o_db->query("SELECT p.name, m.media_id from ms_media m INNER JOIN ms_projects AS p ON m.project_id = p.project_id WHERE m.media_id IN (".join(",", $va_media_ids).")");
					$va_media_projects = array();
					if($q_media_project->numRows()){
						while($q_media_project->nextRow()){
							$va_media_projects[$q_media_project->get("media_id")] = $q_media_project->get("name");
						}
					}
					$this->view->setVar("media_projects", $va_media_projects);
					$this->view->setVar("rows", $va_rows);			
					$this->view->setVar("downloads_by_file", $va_downloads_by_file);			
				}	
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
										"email" => $t_user->get("email"),
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
 		 function downloadSummaryx() {
			$va_specimen_info = array();
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
							if(sizeof($va_specimen_media_ids)){
								$va_rows = array();
								$q_media_views = $o_db->query("SELECT mvs.*, u.fname, u.lname, u.email, u.user_id 
														FROM ms_media_view_stats mvs 
														LEFT JOIN ca_users as u ON mvs.user_id = u.user_id
														WHERE mvs.media_id IN (".join(", ", $va_specimen_media_ids).")");
								if($q_media_views->numRows()){
									while($q_media_views->nextRow()){
										$va_rows[$q_media_views->get("media_id")]["views"][$q_media_views->get("view_id")] = array("date" => date("n/j/y G:i", $q_media_views->get("viewed_on")), "user_id" => $q_media_views->get("user_id"), "user_info" => date("n/j/y G:i", $q_media_views->get("viewed_on")).(($q_media_views->get("user_id")) ? ", ".$q_media_views->get("fname")." ".$q_media_views->get("lname").", ".$q_media_views->get("email") : ""));
									}
								}
				
								$q_media_downloads = $o_db->query("SELECT mds.*, u.fname, u.lname, u.email, u.user_id 
														FROM ms_media_download_stats mds 
														INNER JOIN ca_users as u ON mds.user_id = u.user_id
														WHERE mds.media_id IN (".join(", ", $va_specimen_media_ids).")");
								$va_downloads_by_file = array();
								if($q_media_downloads->numRows()){
									while($q_media_downloads->nextRow()){
										$va_rows[$q_media_downloads->get("media_id")]["downloads"][$q_media_downloads->get("download_id")] = array("media_file_id" => $q_media_downloads->get("media_file_id"), "date" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")), "user_id" => $q_media_downloads->get("user_id"), "email" => $q_media_downloads->get("email"), "name" => trim($q_media_downloads->get("fname")." ".$q_media_downloads->get("lname")), "user_info" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")).", ".$q_media_downloads->get("fname")." ".$q_media_downloads->get("lname").", ".$q_media_downloads->get("email"));
										$va_downloads_by_file[$q_media_downloads->get("media_id")][$q_media_downloads->get("media_file_id")][] = $q_media_downloads->get("user_id");
									}
								}
							}
							$va_specimen_info[] = array(
											"project_name" => $va_project["name"], 
											"specimen_number" => $this->opo_specimen->formatSpecimenNumber($va_project_specimen), 
											"specimen_taxonomy" => $this->opo_specimen->getSpecimenTaxonomy($va_project_specimen["specimen_id"]),
											"specimen_views" => $this->opo_specimen->numViews($va_project_specimen["specimen_id"]),
											"num_specimen_media" => sizeof($va_specimen_media_ids),
											"specimen_media_views" => $vn_specimen_media_views,
											"specimen_media_downloads" => $vn_specimen_media_downloads,
											"specimen_id" => $va_project_specimen["specimen_id"],
											"mediaDownloadsViews" => $va_rows,
											"downloadByFile" => $va_downloads_by_file
										);
						}
					}
				}
			}
			if(sizeof($va_specimen_info)){
 				header("Content-Disposition: attachment; filename=MorphoSourceReport_".date("m-d-y").".txt");
				header("Content-type: application/vnd.ms-excel");
				$va_rows = array();
				$va_row = array("Project", "Specimen", "Specimen views", "Specimen media", "Specimen media public views", "Specimen media downloads");
				$va_rows[] = join("\t", $va_row);
				$vs_display_project = "";
				foreach($va_specimen_info as $va_info){					
					$va_row = array($va_info["project_name"], $va_info["specimen_number"].", ".join("; ", $va_info["specimen_taxonomy"]), $va_info["specimen_views"], $va_info["num_specimen_media"], $va_info["specimen_media_views"], $va_info["specimen_media_downloads"]);
					$va_rows[] = join("\t", $va_row);
					foreach($va_info["mediaDownloadsViews"] as $vn_media_id => $va_media_view) {
						$vs_media = "M".$vn_media_id;
						$vs_media_views = "";
						$vs_media_downloads = "";
						if(is_array($va_media_view["views"]) && sizeof($va_media_view["views"])){
							$vs_media_views = sizeof($va_media_view["views"])." media view".((sizeof($va_media_view["views"]) == 1) ? "" : "s").": ";
							$vn_anon = 0;
							foreach($va_media_view["views"] as $vn_view_id => $va_view_info){
								if($va_view_info["user_id"]){
									$vs_media_views .= $va_view_info["user_info"]."; ";
								}else{
									$vn_anon++;
								}
							}
							if($vn_anon){
								$vs_media_views .= $vn_anon." anonymous view".(($vn_anon == 1) ? "" : "s");
							}
						}
						$va_downloads_by_file = $va_info["downloadByFile"];
						if(is_array($va_media_view["downloads"]) && sizeof($va_media_view["downloads"])){
							$vs_media_downloads = sizeof($va_media_view["downloads"])." media download".((sizeof($va_media_view["downloads"]) == 1) ? "" : "s").": ";
							foreach($va_downloads_by_file[$vn_media_id] as $vn_file_id => $va_file_download_info){
								if($vn_file_id){
									$vs_media_downloads .= "M".$vn_media_id."-".$vn_file_id.": ".sizeof($va_file_download_info)." downloads; ";
								}
							}
							foreach($va_media_view["downloads"] as $vn_download_id => $va_download_info){
								$vs_media_downloads .= (($va_download_info["media_file_id"]) ? "M".$vn_media_id."-".$va_download_info["media_file_id"].": " : "").$va_download_info["date"].", ".$va_download_info["name"].", (".$va_download_info["email"]."); ";
							}
						}
						$va_row = array("", "", "", $vs_media, $vs_media_views, $vs_media_downloads);
						$va_rows[] = join("\t", $va_row);	
					}
										
				}
			}
			$this->response->addContent(join("\n", $va_rows), 'view');

 		}
 		# -------------------------------------------------------
 		 function downloadSummary() {
			$va_filtered_specimen_ids = explode(",", $this->request->getParameter('specimen_ids', pString));
			$va_specimen_info = array();
			# --- get list of available projects for user
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$o_db = new Db();
			$output_rows = array();
			if(sizeof($va_projects)){
				$output_rows = array();
				$output_row = array("Project", "Report Date", "Specimen Number", "Taxon", "Element", "Media", "Media public views", "Media view diversity", "Media view detail", "media downloads", "media download diversity", "media download detail");
				$output_rows[] = join("\t", $output_row);
				foreach($va_projects as $va_project){
					$va_media_info = array();
					$this->opo_project->load($va_project["project_id"]);
					$va_project_specimens = array();
					$q_project_media = $this->opo_project->getProjectMedia();
					$va_project_media_ids = array();
					$va_project_media_specimen_ids = array();
					if($q_project_media->numRows()){
							while($q_project_media->nextRow()){
								if(in_array($q_project_media->get("specimen_id"), $va_filtered_specimen_ids)){ # --- don't show specimen filtered out in browser filter table
									$va_project_media_ids[] = $q_project_media->get("media_id");
									if($q_project_media->get("specimen_id")){
										$va_project_media_specimen_ids[$q_project_media->get("media_id")] = $q_project_media->get("specimen_id");
									}
								}
							}
							if(is_array($va_project_media_specimen_ids) && sizeof($va_project_media_specimen_ids)){
								$q_project_specimen_info = $o_db->query("select * from ms_specimens where specimen_id IN (".join(", ", $va_project_media_specimen_ids).")");
								if($q_project_specimen_info->numRows()){
									while($q_project_specimen_info->nextRow()){
										$va_project_specimens[] = $q_project_specimen_info->getRow();
									}
								}
							}
					}
					if(sizeof($va_project_specimens)){
						# --- make array with specimen info
						foreach($va_project_specimens as $va_project_specimen){
							$va_specimen_info[$va_project_specimen["specimen_id"]] = array(
									"specimen_number" => $this->opo_specimen->formatSpecimenNumber($va_project_specimen), 
									"specimen_taxonomy" => $this->opo_specimen->getSpecimenTaxonomy($va_project_specimen["specimen_id"]),
									"specimen_views" => $this->opo_specimen->numViews($va_project_specimen["specimen_id"]),
									"num_specimen_media" => sizeof($va_specimen_media_ids),
									"specimen_id" => $va_project_specimen["specimen_id"],
									"element" => $va_project_specimen["element"]
								);

						}
					}
					if(sizeof($va_project_media_ids)){
						# --- get all the elements in one query for use later
						$q_elements = $o_db->query("SELECT element, media_id from ms_media where media_id IN (".join(", ", $va_project_media_ids).")");
						$va_elements = array();
						if($q_elements->numRows()){
							while($q_elements->nextRow()){
								$va_elements[$q_elements->get("media_id")] = $q_elements->get("element");
							}
						}
						# --- make array with media info
						if(sizeof($va_project_media_ids)){
							$va_rows = array();
							$q_media_views = $o_db->query("SELECT mvs.*, u.fname, u.lname, u.email, u.user_id 
													FROM ms_media_view_stats mvs 
													LEFT JOIN ca_users as u ON mvs.user_id = u.user_id
													WHERE mvs.media_id IN (".join(", ", $va_project_media_ids).")");
							if($q_media_views->numRows()){
								while($q_media_views->nextRow()){
									$va_rows[$q_media_views->get("media_id")]["views"][$q_media_views->get("view_id")] = array("date" => date("n/j/y G:i", $q_media_views->get("viewed_on")), "user_id" => $q_media_views->get("user_id"), "user_info" => date("n/j/y G:i", $q_media_views->get("viewed_on")).(($q_media_views->get("user_id")) ? ", ".$q_media_views->get("fname")." ".$q_media_views->get("lname").", ".$q_media_views->get("email") : ""));
								}
							}
			
							$q_media_downloads = $o_db->query("SELECT mds.*, u.fname, u.lname, u.email, u.user_id 
													FROM ms_media_download_stats mds 
													INNER JOIN ca_users as u ON mds.user_id = u.user_id
													WHERE mds.media_id IN (".join(", ", $va_project_media_ids).")");
							$va_downloads_by_file = array();
							if($q_media_downloads->numRows()){
								while($q_media_downloads->nextRow()){
									$va_rows[$q_media_downloads->get("media_id")]["downloads"][$q_media_downloads->get("download_id")] = array("media_file_id" => $q_media_downloads->get("media_file_id"), "date" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")), "user_id" => $q_media_downloads->get("user_id"), "email" => $q_media_downloads->get("email"), "name" => trim($q_media_downloads->get("fname")." ".$q_media_downloads->get("lname")), "user_info" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")).", ".$q_media_downloads->get("fname")." ".$q_media_downloads->get("lname").", ".$q_media_downloads->get("email"));
									$va_downloads_by_file[$q_media_downloads->get("media_id")][$q_media_downloads->get("media_file_id")][] = $q_media_downloads->get("user_id");
								}
							}
							foreach($va_project_media_ids as $vn_media_id){
								$va_media_info[$vn_media_id] = array(
											"media_id" => $vn_media_id,
											"project_name" => $va_project["name"], 
											"specimen_number" => $va_specimen_info[$va_project_media_specimen_ids[$vn_media_id]]["specimen_number"], 
											"specimen_taxonomy" => $va_specimen_info[$va_project_media_specimen_ids[$vn_media_id]]["specimen_taxonomy"],
											"element" => $va_elements[$vn_media_id],
											"specimen_id" => $va_project_specimen["specimen_id"],
											"mediaDownloadsViews" => $va_rows[$vn_media_id],
											"downloadByFile" => $va_downloads_by_file[$vn_media_id]
								);
							}
						}
					}
					if(sizeof($va_media_info)){
						foreach($va_media_info as $va_info){			
							$va_row = array();
							$va_media_views_download = $va_info["mediaDownloadsViews"];
							$vs_media = "M".$va_info["media_id"];
							$vn_media_id = $va_info["media_id"];
							$vs_media_views = "";
							$vs_media_downloads = "";
							$vs_view_diversity = "";
							$va_view_by_user = array();
							$vs_download_diversity = "";
							$va_download_by_user = array();
							if(is_array($va_media_views_download["views"]) && sizeof($va_media_views_download["views"])){
								$vs_media_views = sizeof($va_media_views_download["views"])." media view".((sizeof($va_media_views_download["views"]) == 1) ? "" : "s").": ";
								$vn_anon = 0;
								foreach($va_media_views_download["views"] as $vn_view_id => $va_view_info){
									if($va_view_info["user_id"]){
										$vs_media_views .= $va_view_info["user_info"]."; ";
										$va_view_by_user[$va_view_info["user_id"]] = $va_view_info["user_id"];
									}else{
										$vn_anon++;
									}
								}
								if(sizeof($va_view_by_user)){
									$vs_view_diversity = sizeof($va_view_by_user);
									if($vn_anon){
										$vs_view_diversity .= "+";
									}
								}else{
									$vs_view_diversity = "all anonymous";
								}			
								if($vn_anon){
									$vs_media_views .= $vn_anon." anonymous view".(($vn_anon == 1) ? "" : "s");
								}
							}
							$va_downloads_by_file = $va_info["downloadByFile"];
							if(is_array($va_media_views_download["downloads"]) && sizeof($va_media_views_download["downloads"])){
								$vs_media_downloads = sizeof($va_media_views_download["downloads"])." media download".((sizeof($va_media_views_download["downloads"]) == 1) ? "" : "s").": ";
								foreach($va_info["downloadByFile"] as $vn_file_id => $va_file_download_info){
									if($vn_file_id){
										$vs_media_downloads .= "M".$vn_media_id."-".$vn_file_id.": ".sizeof($va_file_download_info)." downloads; ";
									}
								}
								foreach($va_media_views_download["downloads"] as $vn_download_id => $va_download_info){
									$vs_media_downloads .= (($va_download_info["media_file_id"]) ? "M".$vn_media_id."-".$va_download_info["media_file_id"].": " : "").$va_download_info["date"].", ".$va_download_info["name"].", (".$va_download_info["email"]."); ";
									$va_download_by_user[$va_download_info["user_id"]] = $va_download_info["user_id"];
								}
								$vs_download_diversity = sizeof($va_download_by_user);
							}
					
							# --- shorten media views and downloads text to not break Excel
							if(mb_strlen($vs_media_views) > 31000){
								$vs_media_views = mb_substr($vs_media_views, 0, 31000)."... This info has been shortened to work with Excel";
							}
							if(mb_strlen($vs_media_downloads) > 31000){
								$vs_media_downloads = mb_substr($vs_media_downloads, 0, 31000)."... This info has been shortened to work with Excel";
							}
							$va_output_row = array($va_info["project_name"], date("n/j/y", time()), $va_info["specimen_number"], (is_array($va_info["specimen_taxonomy"])) ? join(", ", $va_info["specimen_taxonomy"]) : "", preg_replace("/\r|\n/", " ", $va_info["element"]), $vs_media, sizeof($va_media_views_download["views"]), $vs_view_diversity,  $vs_media_views, sizeof($va_media_views_download["downloads"]), $vs_download_diversity, $vs_media_downloads);
							$output_rows[] = join("\t", $va_output_row);
									
						}
					}
				}
				header("Content-Disposition: attachment; filename=MorphoSourceMediaReport_".date("m-d-y").".txt");
				header("Content-type: application/vnd.ms-excel");
				
				$this->response->addContent(join("\n", $output_rows), 'view');
			}
		}
 		# -------------------------------------------------------
 		 function downloadSpecimenSummary() {
 		 	$va_filtered_specimen_ids = explode(",", $this->request->getParameter('specimen_ids', pString));
			$va_specimen_info = array();
			# --- get list of available projects for user
			$va_projects = $this->opo_project->getProjectsForMember($this->request->user->get("user_id"));
			$o_db = new Db();
			if(sizeof($va_projects)){
				foreach($va_projects as $va_project){
					$this->opo_project->load($va_project["project_id"]);
					$va_project_specimens = $this->opo_project->getProjectSpecimens();
					if(sizeof($va_project_specimens)){
						foreach($va_project_specimens as $va_project_specimen){
							if(in_array($va_project_specimen["specimen_id"], $va_filtered_specimen_ids)){
								# --- has this specimen already been added to the array for export?
								if($va_specimen_info[$va_project_specimen["specimen_id"]]){
									# --- just add the project name to what is already there so it lists all project the specimen appears in
									$va_specimen_info[$va_project_specimen["specimen_id"]]["name"] = $va_specimen_info[$va_project_specimen["specimen_id"]]["name"]."; ".$va_project["name"];
								}else{
									$va_specimen_media_ids = $this->opo_specimen->getSpecimenMediaIDs($va_project_specimen["specimen_id"]);
									$va_specimen_media_ids_published = $this->opo_specimen->getSpecimenMediaIDs($va_project_specimen["specimen_id"], array("published" => true));
							
									$vn_specimen_media_views = 0;
									$vn_specimen_media_downloads = 0;
									if(is_array($va_specimen_media_ids) && sizeof($va_specimen_media_ids)){
										$q_media_views = $o_db->query("SELECT count(*) c FROM ms_media_view_stats WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");
										$q_media_views->nextRow();
										$vn_specimen_media_views = $q_media_views->get("c");
								
										$q_media_viewers = $o_db->query("SELECT DISTINCT user_id FROM ms_media_view_stats WHERE user_id > 0 AND media_id IN (".join(", ", $va_specimen_media_ids).")");
										$vn_specimen_media_viewers = $q_media_viewers->numRows()." registered users";
								
										$q_media_view_anon_users = $o_db->query("SELECT count(*) c FROM ms_media_view_stats WHERE user_id IS NULL AND media_id IN (".join(", ", $va_specimen_media_ids).")");
										$q_media_view_anon_users->nextRow();
										$vn_specimen_media_viewers .= ", ".$q_media_view_anon_users->get("c")." anonymous users";
								
										$q_media_downloads = $o_db->query("SELECT count(*) c FROM ms_media_download_stats WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");
										$q_media_downloads->nextRow();
										$vn_specimen_media_downloads = $q_media_downloads->get("c");	
								
										$q_media_downloaders = $o_db->query("SELECT DISTINCT user_id FROM ms_media_download_stats WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");
										$vn_specimen_media_downloaders = $q_media_downloaders->numRows()." users";
								
										# --- how many projects do the media belong in
										$q_media_projects = $o_db->query("SELECT DISTINCT project_id from ms_media WHERE media_id IN (".join(", ", $va_specimen_media_ids).")");							
										$vn_media_projects = $q_media_projects->numRows();
									}
		// 							if(sizeof($va_specimen_media_ids)){
		// 								$va_rows = array();
		// 								$q_media_views = $o_db->query("SELECT mvs.*, u.fname, u.lname, u.email, u.user_id 
		// 														FROM ms_media_view_stats mvs 
		// 														LEFT JOIN ca_users as u ON mvs.user_id = u.user_id
		// 														WHERE mvs.media_id IN (".join(", ", $va_specimen_media_ids).")");
		// 								if($q_media_views->numRows()){
		// 									while($q_media_views->nextRow()){
		// 										$va_rows[$q_media_views->get("media_id")]["views"][$q_media_views->get("view_id")] = array("date" => date("n/j/y G:i", $q_media_views->get("viewed_on")), "user_id" => $q_media_views->get("user_id"), "user_info" => date("n/j/y G:i", $q_media_views->get("viewed_on")).(($q_media_views->get("user_id")) ? ", ".$q_media_views->get("fname")." ".$q_media_views->get("lname").", ".$q_media_views->get("email") : ""));
		// 									}
		// 								}
		// 				
		// 								$q_media_downloads = $o_db->query("SELECT mds.*, u.fname, u.lname, u.email, u.user_id 
		// 														FROM ms_media_download_stats mds 
		// 														INNER JOIN ca_users as u ON mds.user_id = u.user_id
		// 														WHERE mds.media_id IN (".join(", ", $va_specimen_media_ids).")");
		// 								$va_downloads_by_file = array();
		// 								if($q_media_downloads->numRows()){
		// 									while($q_media_downloads->nextRow()){
		// 										$va_rows[$q_media_downloads->get("media_id")]["downloads"][$q_media_downloads->get("download_id")] = array("media_file_id" => $q_media_downloads->get("media_file_id"), "date" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")), "user_id" => $q_media_downloads->get("user_id"), "email" => $q_media_downloads->get("email"), "name" => trim($q_media_downloads->get("fname")." ".$q_media_downloads->get("lname")), "user_info" => date("n/j/y G:i", $q_media_downloads->get("downloaded_on")).", ".$q_media_downloads->get("fname")." ".$q_media_downloads->get("lname").", ".$q_media_downloads->get("email"));
		// 										$va_downloads_by_file[$q_media_downloads->get("media_id")][$q_media_downloads->get("media_file_id")][] = $q_media_downloads->get("user_id");
		// 									}
		// 								}
		// 							}
									$va_specimen_info[$va_project_specimen["specimen_id"]] = array(
													"project_name" => $va_project["name"], 
													"specimen_number" => $this->opo_specimen->formatSpecimenNumber($va_project_specimen), 
													"specimen_taxonomy" => $this->opo_specimen->getSpecimenTaxonomy($va_project_specimen["specimen_id"]),
													"specimen_description" => preg_replace("/\r|\n/", " ", $va_project_specimen["description"]),
													"specimen_views" => $this->opo_specimen->numViews($va_project_specimen["specimen_id"]),
													"num_specimen_media" => sizeof($va_specimen_media_ids),
													"num_specimen_media_unpublished" => sizeof($va_specimen_media_ids) - sizeof($va_specimen_media_ids_published),
													"specimen_media_views" => $vn_specimen_media_views,
													"specimen_media_downloads" => $vn_specimen_media_downloads,
													"specimen_id" => $va_project_specimen["specimen_id"],
													"media_projects" => $vn_media_projects,
													"specimen_media_viewers" => $vn_specimen_media_viewers,
													"specimen_media_downloaders" => $vn_specimen_media_downloaders
													#"mediaDownloadsViews" => $va_rows,
													#"downloadByFile" => $va_downloads_by_file,
												);
								}
							}
						}
					}
				}
			}
			if(sizeof($va_specimen_info)){
 				header("Content-Disposition: attachment; filename=MorphoSourceSpecimenReport_".date("m-d-y").".txt");
				header("Content-type: application/vnd.ms-excel");
				$va_rows = array();
				$va_row = array("Specimen", "Taxon", "Description", "Specimen media", "Specimen projects", "Specimen views", "Specimen media views", "diversity", "Specimen media downloads", "diversity");
				$va_rows[] = join("\t", $va_row);
				$vs_display_project = "";
				foreach($va_specimen_info as $va_info){					
					$va_row = array($va_info["specimen_number"], join("; ", $va_info["specimen_taxonomy"]), $va_info["specimen_description"], $va_info["num_specimen_media"].(($va_info["num_specimen_media_unpublished"]) ? ", (".$va_info["num_specimen_media_unpublished"]." unpublished)" : ""), $va_info["media_projects"], $va_info["specimen_views"], $va_info["specimen_media_views"], $va_info["specimen_media_viewers"], $va_info["specimen_media_downloads"], $va_info["specimen_media_downloaders"]);
					$va_rows[] = join("\t", $va_row);									
				}
			}
			$this->response->addContent(join("\n", $va_rows), 'view');

 		}
 		# ------------------------------------------------------- 		
 	}
 ?>