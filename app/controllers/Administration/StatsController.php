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
 			
 			$q_media_files_count = $o_db->query("SELECT count(*) c FROM ms_media_files");
 			$q_media_files_count->nextRow();
 			$this->view->setVar('num_media_files', $q_media_files_count->get("c"));
 			
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
 	}
 ?>