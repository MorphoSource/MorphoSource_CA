<?php
/* ----------------------------------------------------------------------
 * default/views/mailTemplates/invite_member.tpl
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2013 Whirl-i-Gig
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
 
	print "Dear ".$vs_member_name.",\n";
	print "You have been invited to participate in a MorphoSource project, P".$this->opn_project_id.", titled '".$this->opo_project->get("name")."' by $vs_from_name (".$vs_from_email.").";
	if($ps_member_message){
		print "\n\n$vs_from_name wrote:\n".$ps_member_message;
	}
	print "\n\nIf you are not interested in participating in this project please contact $vs_from_name (".$vs_from_email.").  Otherwise log on to the MorphoSource site at ".$this->request->config->get("site_host")." and select '".$this->opo_project->get("name")."' in the Dashboard section of the site. Your login name is your email address.";
	if($vn_password){
		print " Your password is $vn_password";
	}
	print "\n\nThank you,\n The MorphoSource system administrators";


	print "\n\n".$this->request->config->get("site_host");

?>