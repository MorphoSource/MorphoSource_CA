<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/role_delete_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008 Whirl-i-Gig
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

	$t_role = $this->getVar('t_role');
	$vn_role_id = $this->getVar('role_id');
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Delete Role"); ?>
	</H1>
	<div id='formAreaDeleteForm'>
<?php
	print caDeleteWarningBox($this->request, $t_role, $t_role->getName(), 'Administration', 'roles', 'ListRoles', array('role_id' => $vn_role_id));
?>
	</div>