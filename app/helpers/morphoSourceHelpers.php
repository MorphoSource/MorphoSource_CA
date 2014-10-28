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
 require_once(__CA_MODELS_DIR__."/ms_media_sets.php");
 
	 # --------------------------------------------------------------------------------------------
	 /**
	  * checks access and sets session variable to select project as current working project
	  * this is for logged in users who are accessing the 'Dashboard' section of the site for doing cataloguing
	  */
	function msSelectProject($o_controller, $o_request, $pn_project_id="") {
		if(!$pn_project_id){
			$pn_project_id = $o_request->getParameter('select_project_id', pInteger);
		}
		$t_project = new ms_projects($pn_project_id);
		if (!$t_project->getPrimaryKey()) {
			$o_controller->notification->addNotification("Project does not exist!", __NOTIFICATION_TYPE_ERROR__);
			$o_controller->response->setRedirect(caNavUrl($o_controller->request, "", "", ""));
		}
		if(!$o_request->user->canDoAction("is_administrator") && !$t_project->isMember($o_request->user->get("user_id"))){
			$o_controller->notification->addNotification("You do not have access to the project", __NOTIFICATION_TYPE_ERROR__);
			$o_controller->response->setRedirect(caNavUrl($o_controller->request, "", "", ""));
			return;
		}
		if(!$o_request->user->canDoAction("is_administrator") && !$t_project->isFullAccessMember($o_request->user->get("user_id"))){
			$o_controller->notification->addNotification("You do not have FULL access to the project, you only have read only access", __NOTIFICATION_TYPE_ERROR__);
			$o_controller->response->setRedirect(caNavUrl($o_controller->request, "", "", ""));
			return;
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
	 /**
	  * returns add or remove link to media cart
	  * 
	  */
	function addToCartLink($o_request, $pn_media_id, $pn_user_id = null, $va_cart_media_ids = null, $va_options = array()) {
		if(!$pn_media_id){
			return false;
		}else{
			if(!is_array($va_cart_media_ids)){
				if(!$pn_user_id){
					return false;
				}
				$t_media_cart = new ms_media_sets();
 				$va_cart_media_ids = $t_media_cart->getCartMediaIdsForUser($pn_user_id);
			}
			if(is_array($va_cart_media_ids)){
				$vs_class = "button buttonLarge";
				if($va_options["class"]){
					$vs_class = $va_options["class"];
				}
				if(in_array($pn_media_id, $va_cart_media_ids)){
					$vs_link = "<a href='#' onClick='$(this).parent().load(\"".caNavUrl($o_request, '', 'MediaCart', 'Remove', array('media_id' => $pn_media_id, "class" => $vs_class))."\"); return false;' class='".$vs_class."'>"._t("remove <i class='fa fa-shopping-cart'></i>")."</a>";
				}else{
					$vs_link = "<a href='#' onClick='$(this).parent().load(\"".caNavUrl($o_request, '', 'MediaCart', 'Add', array('media_id' => $pn_media_id, "class" => $vs_class))."\"); return false;' class='".$vs_class."'>"._t("add <i class='fa fa-shopping-cart'></i>")."</a>";
				}
			}
			return $vs_link;
		}
	}
	# --------------------------------------------------------------------------------------------
 ?>