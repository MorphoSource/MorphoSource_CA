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
 			$q_all_projects = $o_db->query("SELECT project_id from ms_projects order by project_id desc");

 			$this->view->setVar('project', $t_project);
 			$this->view->setVar('all_projects', $q_all_projects);

 			$this->render('project_list_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>