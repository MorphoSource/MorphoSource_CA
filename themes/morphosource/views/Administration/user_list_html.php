<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/user_list_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2010 Whirl-i-Gig
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
	$va_user_list = $this->getVar('user_list');
	$t_user = new ca_users();

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#caUserList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<div style="float:right; margin-top:15px;"><?php print caNavLink($this->request, _t("New user"), 'button buttonLarge', 'Administration', 'Users', 'Edit', array('user_id' => 0)); ?>&nbsp;&nbsp;&nbsp;<?php print caNavLink($this->request, _t("Manage Roles"), 'button buttonLarge', 'Administration', 'Roles', 'ListRoles'); ?></div>
	<H1>
		<?php print _t("Manage Users"); ?>
	</H1>
	<div id='formArea'>
<div class="sectionBox">
	<?php 
		print caFormTag($this->request, 'ListUsers', 'caUserListForm', NULL, NULL, NULL, NULL, array("disableUnsavedChangesWarning" => true));
		
		print caFormControlBox(
			'<div class="list-filter">'._t('Filter').': <input type="text" name="filter" value="" onkeyup="$(\'#caUserList\').caFilterTable(this.value); return false;" size="20"/></div>', 
			null,
			_t('Show %1 users', caHTMLSelect('userclass', $this->request->user->getFieldInfo('userclass', 'BOUNDS_CHOICE_LIST'), array('onchange' => 'jQuery("#caUserListForm").submit();'), array('value' => $this->getVar('userclass'))))
		); 
?>	
		<h2 class="userList"><?php print sizeof($va_user_list)." "._t('%1 %2', ucfirst($this->getVar('userclass_displayname')), (sizeof($va_user_list) == 1) ? "User" : "Users"); ?></h2>
		
		<table id="caUserList" class="listtable" border="0" cellpadding="0" cellspacing="1">
			<thead>
				<tr>
					<th class="list-header-unsorted">
						<?php print _t('Login name'); ?>
					</th>
					<th class="list-header-unsorted">
						<?php print _t('Name'); ?>
					</th>
					<th class="list-header-unsorted">
						<?php print _t('Active?'); ?>
					</th>
					<th class="list-header-unsorted">
						<?php print _t('Downloads'); ?>
					</th>
					<th class="list-header-unsorted">
						<?php print _t('Last login'); ?>
					</th>
					<th class="{sorter: false} list-header-nosort">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?php
	$o_tep = new TimeExpressionParser();
	foreach($va_user_list as $va_user) {
		if ($va_user['last_login'] > 0) {
			$o_tep->setUnixTimestamps($va_user['last_login'], $va_user['last_login']);
		}
?>
			<tr>
				<td>
					<?php 
						if ($va_user['email'] != $va_user['user_name']) {
							print $va_user['user_name']." (email address is ".$va_user['email'].")"; 
						} else {
							print $va_user['user_name']; 
						}
					?>
				</td>
				<td>
					<?php print $va_user['lname'].', '.$va_user['fname']; ?>
				</td>
				<td>
					<?php print $va_user['active'] ? _t('Yes') : _t('No'); ?>
				</td>
				<td>
<?php
					if($va_user['num_downloads']){
						print "<a href='#' title='More info' onClick='jQuery(\"#downloadInfo".$va_user['user_id']."\").load(\"".caNavUrl($this->request, 'Administration', 'Users', 'downloadInfo', array('user_id' => $va_user['user_id']))."\"); return false;'>".$va_user['num_downloads']."</a>";
						print "<div id='downloadInfo".$va_user['user_id']."'></div>";
					}else{
						print $va_user['num_downloads'];
					}
?>		
				</td>
				<td>
					<?php print ($va_user['last_login'] > 0) ? $o_tep->getText() : '-'; ?>
				</td>
				<td <?php print (($this->getVar('userclass') == 50) ? "" : 'width="100"'); ?> align="center">
<?php
					if($this->getVar('userclass') == 50){
						print caNavLink($this->request, _t("Approve"), "button buttonSmall", "Administration", "Users", "approveRequest", array('user_id' => $va_user['user_id']));
						print "&nbsp;&nbsp;".caNavLink($this->request, _t("Deny"), "button buttonSmall", "Administration", "Users", "denyRequest", array('user_id' => $va_user['user_id']))."&nbsp;&nbsp;";
					}
					print caNavLink($this->request, _t("Edit"), "button buttonSmall", "Administration", "Users", "Edit", array('user_id' => $va_user['user_id']));
					print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Users", "Delete", array('user_id' => $va_user['user_id']));
?>
				</td>
			</tr>
<?php
	}
?>
			</tbody>
		</table>
	</form>
</div>
</div>