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
 
	print "Dear ".$t_new_admin->get("fname")." ".$t_new_admin->get("lname").",\n";
	print "You have been made the project adminisrator of MorphoSource project, P".$this->opn_project_id.", titled '".$this->opo_project->get("name")."'.\n\n";
	print "As the project administrator, you can manage project members and maintain the project's information.  Log on to the MorphoSource site at ".$this->request->config->get("site_host")." and select '".$this->opo_project->get("name")."' in the Dashboard section of the site to access the project.\n\n";
	print "Thank you,\n ".$t_old_admin->get("fname")." ".$t_old_admin->get("lname")."\n";


	print "\n\n".$this->request->config->get("site_host");

?>