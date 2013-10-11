<?php
/* ----------------------------------------------------------------------
 * default/views/mailTemplates/request_full_access_html.tpl
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
 
	print "<p>".$t_user->get("fname")." ".$t_user->get("lname")." has requested to become a contributor to MorphoSource with the following message:</p>";
	print "<blockquote>".$vs_message."</blockquote>";
	print "<p>Please login to MorphoSource and click the link to Manage Users to approve or deny this request.</p>";

	print "<p>".$this->request->config->get("site_host")."</p>";
?>