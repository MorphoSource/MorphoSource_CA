<?php
/** ---------------------------------------------------------------------
 * app/helpers/morphoSourceHelpers.php : utility functions for checking user access
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010-2013 Whirl-i-Gig
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
 * @package CollectiveAccess
 * @subpackage utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */ 
 
  /**
   *
   */
   
 require_once(__CA_LIB_DIR__.'/core/Configuration.php');
 
	 # --------------------------------------------------------------------------------------------
	 /**
	  * checks access and sets session variable to select project as current working project
	  * this is for logged in users who are accessing the 'Dashboard' section of the site for doing cataloguing
	  */
	function msSelectProject($o_controller, $o_request, $pn_project_id="") {
		if(!$pn_project_id){
			$pn_project_id = $o_request->getParameter('project_id', pInteger);
		}
		$t_project = new ms_projects($pn_project_id);
		if (!$t_project->getPrimaryKey()) {
			$o_controller->notification->addNotification("Project does not exist!", __NOTIFICATION_TYPE_ERROR__);
			$o_controller->response->setRedirect(caNavUrl($o_controller->request, "", "", ""));
		}
		if(!$t_project->isMember($o_request->user->get("user_id"))){
			$o_controller->notification->addNotification("You do not have access to the project", __NOTIFICATION_TYPE_ERROR__);
			$o_controller->response->setRedirect(caNavUrl($o_controller->request, "", "", ""));
		}
		
		if ($t_project->getPrimaryKey()) {
			$o_request->session->setVar('current_project_id', $pn_project_id);
			$o_request->session->setVar('current_project_unpublished', (($t_project->get("publication_status")) ? "0" : "1"));
			
			$vs_title = "P".$pn_project_id.': '.$t_project->get('name');
			if (strlen($vs_title) > 50) { $vs_title = substr($vs_title, 0, 47)."..."; }
			$o_request->session->setVar('current_project_name', $vs_title);
			
			$t_project->setUserAccessTime($o_request->user->get("user_id"));
		}
	}
	# --------------------------------------------------------------------------------------------
 ?>