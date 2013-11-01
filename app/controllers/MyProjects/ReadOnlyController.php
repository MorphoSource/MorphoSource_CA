<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/ReadOnly.php
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
 
 	class ReadOnlyController extends ActionController {
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
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Read Only Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
				return;
 			}
 			if(!$this->request->user->isFullAccessUser()){
 				$this->notification->addNotification("You must be a full access user to access a project", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
				return;
 			}
 			
 			$this->opo_project = new ms_projects();
 			$vn_project_id = $this->request->getParameter('project_id', pInteger);
 			
 			if($vn_project_id && ($this->request->user->canDoAction("is_administrator") || $this->opo_project->isMember($this->request->user->get("user_id"), $vn_project_id))){
 				$this->opn_project_id = $vn_project_id;
				$this->opo_project->load($this->opn_project_id);
				$this->ops_project_name = $this->opo_project->get("name");
				$this->view->setvar("project_id", $this->opn_project_id);
				$this->view->setvar("project_name", $this->ops_project_name);
				$this->view->setvar("project", $this->opo_project);
 			}else{
 				if(!$vn_project_id){
 					$this->notification->addNotification("You must select a project", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
 				}else{
 					$this->notification->addNotification("You do not have access to the project", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
 				}
 				return;
 			}
 		}
 		# -------------------------------------------------------
 		function dashboard() {
 			if($this->opn_project_id){
 				JavascriptLoadManager::register("cycle");
				$this->render('ReadOnly/read_only_dashboard_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>