<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/role_edit_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2009 Whirl-i-Gig
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
		<?php print ($t_role->get("role_id")) ? _t("Edit Role") : _t("New Role"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'Save', 'RolesForm');	
?>
	<div class="formButtons tealTopBottomRule">
<?php
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "Administration", "Roles", "ListRoles")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#RolesForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($vn_role_id){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Roles", "Delete", array('role_id' => $vn_role_id));
		}
?>
	</div><!-- end formButtons -->
<?php
	print caFormTag($this->request, 'Save', 'RolesForm');

		foreach($t_role->getFormFields() as $vs_f => $va_role_info) {
			print $t_role->htmlFormElement($vs_f, "<div class='formErrors'>^ERRORS</div><div class='formLabel'>^LABEL<br>^ELEMENT</div>", array('field_errors' => $this->request->getActionErrors('field_'.$vs_f)));
		}
		
		$va_actions = $t_role->getRoleActionList();
		
		$vn_num_cols = 5;
		$vn_c = 0;
		
		$va_tooltips = array();
?>
		<H2><?php print _t('User actions'); ?></h2>
		<div style='margin-left:6px;'>
			<table>
<?php
			$va_current_actions = $t_role->getRoleActions();
			
			foreach($va_actions as $vs_group => $va_group_info) {
				$vs_check_all_link = '<a href="#" onclick="jQuery(\'.role_action_group_'.$vs_group.'\').attr(\'checked\', true); return false;" class="roleCheckAllNoneButton">'._t('All').'</a>';
				$vs_check_none_link = '<a href="#" onclick="jQuery(\'.role_action_group_'.$vs_group.'\').attr(\'checked\', false); return false;" class="roleCheckAllNoneButton">'._t('None').'</a>';
				
				print "<tr><td colspan='".($vn_num_cols * 2)."' class='formLabel roleCheckGroupHeading'><span id='group_label_".$vs_group."'>".$va_group_info['label']."</span> <span class='roleCheckAllNoneButtons'>{$vs_check_all_link}/{$vs_check_none_link}</span></td></tr>\n";
				TooltipManager::add('#group_label_'.$vs_group, "<h3>".$va_group_info['label']."</h3>".$va_group_info['description']);

				foreach($va_group_info['actions'] as $vs_action => $va_action_info) {
					if ($vn_c == 0) {
						print "<tr valign='top'>";
					} 
					$va_attributes = array('value' => 1);
					if (in_array($vs_action, $va_current_actions)) {
						$va_attributes['checked'] = 1;
					}
					$va_attributes['class'] = 'role_action_group_'.$vs_group;
					
					print "<td>".caHTMLCheckboxInput($vs_action, $va_attributes)."</td><td width='120'><span id='role_label_".$vs_action."'>".$va_action_info['label']."</span></td>";
					TooltipManager::add('#role_label_'.$vs_action, "<h3>".$va_action_info['label']."</h3>".$va_action_info['description']);
					
					$vn_c++;
					
					if ($vn_c >= $vn_num_cols) {
						$vn_c = 0;
						print "</tr>\n";
					}
				}
									
				if ($vn_c > 0) {
					print "</tr>\n";
				}
				$vn_c = 0;
			}
?>
			</table>
		</div><br/>
	<div class="formButtons tealTopBottomRule">
<?php
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "Administration", "Roles", "ListRoles")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#RolesForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($vn_role_id){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Roles", "Delete", array('role_id' => $vn_role_id));
		}
?>
	</div><!-- end formButtons -->
	</form>
</div>