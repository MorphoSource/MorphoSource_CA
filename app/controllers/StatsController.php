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
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/core/Parsers/ZipStream.php');
 
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
				$va_pub_media_ids = $this->opo_specimen->getSpecimenMediaIDs(
					$pn_specimen_id, 
					array("published" => true));
				$vn_num_unpub_media = 
					sizeof($va_all_media_ids) - sizeof($va_pub_media_ids);
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
												"num_specimen_media_unpublished" => $vn_num_unpub_media,
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
 			# essentially copy media cart md but for all media user has access to 
 			$va_projects = $this->opo_project->getProjectsForMember(
				$this->request->user->get("user_id"));
 			$t_proj = new ms_projects();
 			$t_media = new ms_media();
 			$t_media_file = new ms_media_files();
 			$va_media_file_ids = [];
 			foreach ($va_projects as $va_proj) {
 				$t_proj->load($va_proj["project_id"]);
 				$qr_media = $t_proj->getProjectMedia(true);
				while($qr_media->nextRow()){
					$va_media_files = 
						$t_media->getMediaFiles($qr_media->get("media_id"));
					foreach ($va_media_files as $t_mf) {
						$va_media_file_ids[] = $t_mf->get("media_file_id");
					}
				}						
 			}

 			if(sizeof($va_media_file_ids)){
 				$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'media_statistics');
 				$vs_text_file_name = "morphosourceUserMediaStatistics_".date('m_d_y_His');
 				$va_text_file_text = 
 					$t_media_file->mediaMdText($va_media_file_ids, null, True);
 				if(sizeof($va_text_file_text)){
 					$vo_file = fopen($vs_tmp_file_name, "w");
					foreach($va_text_file_text as $va_row){
						fputcsv($vo_file, $va_row);			
					}
					fclose($vo_file);

					$o_zip = new ZipStream();
					$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name.".csv");

					$this->view->setVar('zip_stream', $o_zip);
					$this->view->setVar('version_download_name', $vs_text_file_name.".zip");
					
					$this->response->sendHeaders();
					$vn_rc = $this->render('Detail/media_download_binary.php');
					$this->response->sendContent();
						
					@unlink($vs_tmp_file_name);

					return $vn_rc;
 				}
 			}
 		}
 		# -------------------------------------------------------
 		function downloadSpecimenSummary() {
 			$va_projects = $this->opo_project->getProjectsForMember(
				$this->request->user->get("user_id"));
 			$t_proj = new ms_projects();
 			$t_media = new ms_media();
 			$t_media_file = new ms_media_files();
 			$t_specimen = new ms_specimens();
 			$va_media_file_ids = [];
 			foreach ($va_projects as $va_proj) {
 				$t_proj->load($va_proj["project_id"]);
 				$va_specimens = $t_proj->getProjectSpecimens();
 				foreach ($va_specimens as $vn_specimen_id => $va_spec) {
 					$va_media_ids = $t_specimen->getSpecimenMediaIDs(
 						$vn_specimen_id, 
 						[
 							'published' => true, 
 							'user_id' => $this->request->user->get("user_id")
 						]
 					);
 					foreach ($va_media_ids as $vn_media_id) {
 						$va_media_files = $t_media->getMediaFiles($vn_media_id);
 						foreach ($va_media_files as $t_mf) {
 							$va_media_file_ids[] = $t_mf->get("media_file_id");
 						}
 					}
 				}
 			}

 			if(sizeof($va_media_file_ids)){
 				$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'specimen_statistics');
 				$vs_text_file_name = "morphosourceUserSpecimenMediaStatistics_".date('m_d_y_His');
 				$va_text_file_text = 
 					$t_media_file->mediaMdText($va_media_file_ids, null, True);
 				if(sizeof($va_text_file_text)){
 					$vo_file = fopen($vs_tmp_file_name, "w");
					foreach($va_text_file_text as $va_row){
						fputcsv($vo_file, $va_row);			
					}
					fclose($vo_file);

					$o_zip = new ZipStream();
					$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name.".csv");

					$this->view->setVar('zip_stream', $o_zip);
					$this->view->setVar('version_download_name', $vs_text_file_name.".zip");
					
					$this->response->sendHeaders();
					$vn_rc = $this->render('Detail/media_download_binary.php');
					$this->response->sendContent();
						
					@unlink($vs_tmp_file_name);

					return $vn_rc;
 				}
 			}
 		}
 		# ------------------------------------------------------- 		
 	}
 ?>