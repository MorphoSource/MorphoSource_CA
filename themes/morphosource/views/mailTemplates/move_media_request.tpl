<?php
/* ----------------------------------------------------------------------
 * default/views/mailTemplates/move_media_request.tpl
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2015 Whirl-i-Gig
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
 
	print "Dear ".$vs_move_project_admin_name.",\n";
	print $vs_user_name." (".$vs_user_email.") a member of P".$this->opn_project_id.", titled '".$this->opo_project->get("name")."' has requested to ".(($t_media_movement_requests->get("type") == 1) ? "move" : "share")." the media record M".$this->opo_item->get("media_id")." ".$vs_specimen_info." ".(($t_media_movement_requests->get("type") == 1) ? "to" : "with")." your project, ".$vs_move_project_name.".\n";
	print "Please login to your MorphoSource project dashboard to approve or reject this request.</p>";
	if($vs_comment){
		print "\n\n$vs_user_name wrote:\n".$vs_comment."\n";
	}
	print "\nThank you,\n The MorphoSource system administrators\n\n";


	print $this->request->config->get("site_host");
?>