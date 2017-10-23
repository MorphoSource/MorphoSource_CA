<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/role_list_html.php :
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
	$va_role_list = $this->getVar('role_list');

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#caRoleList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><?php print caNavLink($this->request, _t("New role"), 'button buttonLarge', 'Administration', 'Roles', 'Edit', array('role_id' => 0)); ?></div>
		<?php print _t("Manage User Roles"); ?>
	</H1>
	
	<?php 
		print caFormControlBox(
			'', 
			'',
			'<div class="list-filter">'._t('Filter').': <input type="text" name="filter" value="" onkeyup="$(\'#caRoleList\').caFilterTable(this.value); return false;" size="20"/></div>'
		); 
	?>
<div id='formArea'>		
		<br style="clear: both"/>
	<table id="caRoleList" class="listtable" border="0" cellpadding="0" cellspacing="1">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Name'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Code'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Description'); ?>
				</th>
				<th class="{sorter: false} list-header-nosort">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
	if (sizeof($va_role_list)) {
		foreach($va_role_list as $va_role) {
?>
			<tr>
				<td>
					<?php print $va_role['name']; ?>
				</td>
				<td>
					<?php print $va_role['code']; ?>
				</td>
				<td>
					<?php print $va_role['description']; ?>
				</td>
				<td width="100" align="center">
<?php
					#print caNavButton($this->request, '', _t("Edit"), 'Administration', 'Roles', 'Edit', array('role_id' => $va_role['role_id']), array(), array('icon_position' => __CA_NAV_BUTTON_ICON_POS_LEFT__, 'use_class' => 'button buttonSmall', 'no_background' => true, 'dont_show_content' => true));
					#print caNavButton($this->request, '', _t("Delete"), 'Administration', 'Roles', 'Delete', array('role_id' => $va_role['role_id']), array(), array('icon_position' => __CA_NAV_BUTTON_ICON_POS_LEFT__, 'use_class' => 'button buttonSmall', 'no_background' => true, 'dont_show_content' => true));
					print caNavLink($this->request, _t("Edit"), "button buttonSmall", "Administration", "Roles", "Edit", array('role_id' => $va_role['role_id']));
					print "&nbsp;&nbsp;";
					print caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Roles", "Delete", array('role_id' => $va_role['role_id']));
					
?>
				</td>
			</tr>
<?php
		}
	} else {
?>
		<tr>
			<td colspan='4'>
				<div align="center">
					<?php print _t('No roles have been configured'); ?>
				</div>
			</td>
		</tr>
<?php			
	}
?>
		</tbody>
	</table>
</div>