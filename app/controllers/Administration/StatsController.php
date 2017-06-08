<?php
/* ----------------------------------------------------------------------
 * app/controllers/Administration/StatsController.php :
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

 	class StatsController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			if(!$po_request->user->canDoAction("is_administrator")) { die("Insufficient privileges"); }
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 		}
 		# -------------------------------------------------------
 		public function ListStats() {
 			JavascriptLoadManager::register('tableList');
 			$t_project = new ms_projects();
 			$this->view->setVar('project', $t_project);
 			
 			$o_db = new Db();
 			$q_project_count = $o_db->query("SELECT count(*) c FROM ms_projects");
 			$q_project_count->nextRow();
 			$this->view->setVar('num_projects', $q_project_count->get("c"));
 			
 			$q_specimens_count = $o_db->query("SELECT count(*) c FROM ms_specimens");
 			$q_specimens_count->nextRow();
 			$this->view->setVar('num_specimens', $q_specimens_count->get("c"));
 			
 			$q_media_count = $o_db->query("SELECT count(*) c FROM ms_media");
 			$q_media_count->nextRow();
 			$this->view->setVar('num_media', $q_media_count->get("c"));
 			
 			$q_published_media_count = $o_db->query("SELECT count(*) c FROM ms_media where published > 0");
 			$q_published_media_count->nextRow();
 			$this->view->setVar('num_published_media', $q_published_media_count->get("c"));
 			
 			$q_not_published_media_count = $o_db->query("SELECT count(*) c FROM ms_media where published = 0");
 			$q_not_published_media_count->nextRow();
 			$this->view->setVar('num_not_published_media', $q_not_published_media_count->get("c"));
 			
 			$q_media_files_count = $o_db->query("SELECT count(*) c FROM ms_media_files");
 			$q_media_files_count->nextRow();
 			$this->view->setVar('num_media_files', $q_media_files_count->get("c"));
 			
 			$q_published_media_files_count = $o_db->query("SELECT count(*) c FROM ms_media_files mf INNER JOIN ms_media as m ON m.media_id = mf.media_id WHERE (mf.published > 0) OR ((mf.published IS null) AND (m.published > 0))");
 			$q_published_media_files_count->nextRow();
 			$this->view->setVar('num_published_media_files', $q_published_media_files_count->get("c"));
 			
 			$q_not_published_media_files_count = $o_db->query("SELECT count(*) c FROM ms_media_files mf INNER JOIN ms_media as m ON m.media_id = mf.media_id WHERE (mf.published = 0) OR ((mf.published IS null) AND (m.published = 0))");
 			$q_not_published_media_files_count->nextRow();
 			$this->view->setVar('num_not_published_media_files', $q_not_published_media_files_count->get("c"));
 			
 			# --- this is gonna me slow
 			$q_file_types = $o_db->query("SELECT media FROM ms_media_files");
 			$va_all_mimetypes = array();
 			$va_all_mimetype_counts = array();
 			if($q_file_types->numRows()){
 				while($q_file_types->nextRow()){
 					$va_properties = $q_file_types->getMediaInfo('media', 'original');
 					$va_all_mimetypes[$va_properties["MIMETYPE"]] = msGetMediaFormatDisplayString($q_file_types);
 					$va_all_mimetype_counts[$va_properties["MIMETYPE"]] = $va_all_mimetype_counts[$va_properties["MIMETYPE"]] + 1;
 				}
 			}
 			$this->view->setVar('all_mimetype_counts', $va_all_mimetype_counts);
 			$this->view->setVar('all_mimetypes', $va_all_mimetypes);
 			
 			$q_taxonomy_count = $o_db->query("SELECT count(*) c FROM ms_taxonomy_names");
 			$q_taxonomy_count->nextRow();
 			$this->view->setVar('num_taxonomy_names', $q_taxonomy_count->get("c"));
 			
 			$q_user_count = $o_db->query("SELECT count(*) c FROM ca_users WHERE userclass != 255");
 			$q_user_count->nextRow();
 			$this->view->setVar('num_users', $q_user_count->get("c"));
 			
 			$q_bib_count = $o_db->query("SELECT count(*) c FROM ms_bibliography");
 			$q_bib_count->nextRow();
 			$this->view->setVar('num_bibliography', $q_bib_count->get("c"));
 			
 			$q_facility_count = $o_db->query("SELECT count(*) c FROM ms_facilities");
 			$q_facility_count->nextRow();
 			$this->view->setVar('num_facilities', $q_facility_count->get("c"));
 			
 			$q_institution_count = $o_db->query("SELECT count(*) c FROM ms_institutions");
 			$q_institution_count->nextRow();
 			$this->view->setVar('num_institutions', $q_institution_count->get("c"));
 			
 			$q_download_count = $o_db->query("SELECT count(*) c FROM ms_media_download_stats");
 			$q_download_count->nextRow();
 			$this->view->setVar('num_downloads', $q_download_count->get("c"));
 			
 			$q_download_users_count = $o_db->query("SELECT DISTINCT user_id FROM ms_media_download_stats");
 			$this->view->setVar('num_downloads_users', $q_download_users_count->numRows());
 			
 			$q_download_media_count = $o_db->query("SELECT DISTINCT media_id FROM ms_media_download_stats");
 			$this->view->setVar('num_downloads_media', $q_download_media_count->numRows());
 			
 			$q_view_count = $o_db->query("SELECT count(*) c FROM ms_media_view_stats");
 			$q_view_count->nextRow();
 			$this->view->setVar('num_views', $q_view_count->get("c"));
 			
 			$q_users = $o_db->query("SELECT user_id from ca_users WHERE userclass != 255");
 			$this->view->setVar('users', $q_users);

 			$q_download_survey = $o_db->query("SELECT download_id, intended_use, intended_use_other, 3d_print FROM ms_media_download_stats WHERE (intended_use is not null) OR (intended_use_other is not null) OR (3d_print is not null)");
 			$this->view->setVar('download_survey', $q_download_survey);
 			
 			
 			$this->render('stats_list_html.php');
 		}
 		# -------------------------------------------------------
 		public function statsOverTime() {
 			JavascriptLoadManager::register('tableList');
 			$t_project = new ms_projects();
 			$this->view->setVar('project', $t_project);
 			
 			$o_db = new Db();
 			$va_counts = array();
 			# --- show counts every 4 months since Jan 1 2013
 			$vn_year = 2013;
 			$vn_current_year = intval(date("Y"));
 			$vn_current_month = intval(date("n"));
 			$vn_last_timestamp = 0;
 			while($vn_year <= $vn_current_year){
 				$vn_month = 1;
				while($vn_month < 12){
 					$va_tmp = array();
 					$vn_time_stamp = mktime(0, 0, 0, $vn_month, 1, $vn_year);
 					# --- do the queries
 					$q_project_count = $o_db->query("SELECT count(*) c FROM ms_projects WHERE created_on < ".$vn_time_stamp);
 					$q_project_count->nextRow();
 					$va_tmp["projects"] = $q_project_count->get("c");
 					
 					$q_specimen_count = $o_db->query("SELECT count(*) c FROM ms_specimens WHERE created_on < ".$vn_time_stamp);
 					$q_specimen_count->nextRow();
 					$va_tmp["specimen"] = $q_specimen_count->get("c");
 					
 					$q_media_count = $o_db->query("SELECT count(*) c FROM ms_media WHERE created_on < ".$vn_time_stamp);
 					$q_media_count->nextRow();
 					$va_tmp["media"] = $q_media_count->get("c");
 					
 					$q_media_files_count = $o_db->query("SELECT count(*) c FROM ms_media_files WHERE created_on < ".$vn_time_stamp);
 					$q_media_files_count->nextRow();
 					$va_tmp["media files"] = $q_media_files_count->get("c");
 					
 					$q_taxonomy_names_count = $o_db->query("SELECT count(*) c FROM ms_taxonomy_names WHERE created_on < ".$vn_time_stamp);
 					$q_taxonomy_names_count->nextRow();
 					$va_tmp["taxonomic names"] = $q_taxonomy_names_count->get("c");
 					
 					$q_users_count = $o_db->query("SELECT count(*) c FROM ca_users WHERE registered_on < ".$vn_time_stamp);
 					$q_users_count->nextRow();
 					$va_tmp["registered users"] = $q_users_count->get("c");
 					
 					$q_download_count_quarter = $o_db->query("SELECT count(*) c FROM ms_media_download_stats WHERE downloaded_on < ".$vn_time_stamp." AND  downloaded_on > ".$vn_last_timestamp);
 					$q_download_count_quarter->nextRow();
 					$va_tmp["total downloads this quarter"] = $q_download_count_quarter->get("c");
 			
 					$q_download_count = $o_db->query("SELECT count(*) c FROM ms_media_download_stats WHERE downloaded_on < ".$vn_time_stamp);
 					$q_download_count->nextRow();
 					$va_tmp["total downloads"] = $q_download_count->get("c");
 			
 					$q_download_users_count = $o_db->query("SELECT DISTINCT user_id FROM ms_media_download_stats WHERE downloaded_on < ".$vn_time_stamp);
 					$va_tmp["users downloaded media"] = $q_download_users_count->numRows();
 			
 					$q_download_media_count = $o_db->query("SELECT DISTINCT media_id FROM ms_media_download_stats WHERE downloaded_on < ".$vn_time_stamp);
 					$va_tmp["media downloaded"] = $q_download_media_count->numRows();	
 					
 					$va_counts[$vn_year][$vn_month] = $va_tmp;
 					
 					$vn_last_timestamp = $vn_time_stamp;
 					$vn_month = $vn_month + 3;
 					if(($vn_year == $vn_current_year) && ($vn_month > $vn_current_month)){
 						break;
 					}
 				}
 				$vn_year++;
 			}
 			
 			$this->view->setVar('counts', $va_counts);
 	
 	 		$this->render('stats_over_time_html.php');
 	 	}
 		# -------------------------------------------------------
 	}
 ?>