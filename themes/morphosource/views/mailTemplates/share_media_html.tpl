<?php
/* ----------------------------------------------------------------------
 * default/views/mailTemplates/share_media_html.tpl
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2016 Whirl-i-Gig
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
 
	print "<p>Dear ".$vs_user_name.",<br/>";
	print "<p>".$vs_sharing_name." has shared the media record M".$this->opo_item->get("media_id")." ".$vs_specimen_info." with you. Access will expire in 30 days.</p>";
	if($vs_use_restrictions){
		print "<p>The following use restrictions apply: ".$vs_use_restrictions."</p>";
	}
	print "<p>Please login to MorphoSource and click on the Shared Media option in the site navigation to access this media.</p>";
	print "</p><p>Thank you,<br/> The MorphoSource system administrators</p>";


	print "<p>".$this->request->config->get("site_host")."</p>";
?>