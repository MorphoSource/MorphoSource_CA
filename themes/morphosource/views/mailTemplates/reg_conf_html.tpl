<?php
/* ----------------------------------------------------------------------
 * default/views/mailTemplates/reg_conf_html.tpl
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
 
print "<p>You have successfully created a MorphoSource user account. We are glad to have you as a member!</p>

<p>Your username is the email address you used to create the account. With this account you can begin to download 3D media from MorphoSource. If you're not sure where to begin, we can suggest <a href='https://www.morphosource.org/Detail/MediaDetail/Show/media_id/7300'>a</a> <a href='https://www.morphosource.org/Detail/MediaDetail/Show/media_id/11590'>few</a> <a href='https://www.morphosource.org/Detail/MediaDetail/Show/media_id/21428'>examples</a>. If you want to begin uploading 3D media to MorphoSource, you can <a href='https://www.morphosource.org/MyProjects/Dashboard/projectList'>request contributor access</a> to start creating projects and adding media.</p>

<p>For further information, see our <a href='https://www.morphosource.org/About/userGuide'>user guide</a>, our <a href='https://www.youtube.com/channel/UCusG--ELmxbSHNuTIcVL5mQ/featured'>YouTube channel</a> with tutorials, and don't hesitate to contact us directly at this email address for comments or questions.</p>

<p>Thanks,<br/>
MorphoSource Team</p>";

print "<p>".$this->request->config->get("site_host")."</p>";
?>