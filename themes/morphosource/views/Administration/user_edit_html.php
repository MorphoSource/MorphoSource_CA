<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/user_edit_html.php :
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

	$t_user = $this->getVar('t_user');
	$vn_user_id = $this->getVar('user_id');
	
	$va_roles = $this->getVar('roles');
	$va_groups = $this->getVar('groups');
?>	
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print ($t_user->get("user_id")) ? _t("Edit User") : _t("New User"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'Save', 'UsersForm');	
?>
	<div class="formButtons tealTopBottomRule">
<?php
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "Administration", "Users", "ListUsers")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#UsersForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($vn_user_id){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Users", "Delete", array('user_id' => $vn_user_id));
		}
?>
	</div><!-- end formButtons -->

<div class="sectionBox">
<?php
		// ca_users fields
		foreach($t_user->getFormFields() as $vs_f => $va_user_info) {
			print $t_user->htmlFormElement($vs_f, "<div class='formErrors'>^ERRORS</div><div class='formLabel'>^LABEL<br>^ELEMENT</div>", array('field_errors' => $this->request->getActionErrors('field_'.$vs_f)));

			if ($vs_f == 'password') {
				// display password confirmation
				print $t_user->htmlFormElement($vs_f, "<div class='formLabel'>Confirm Password<br>^ELEMENT</div>", array('name' => 'password_confirm', 'LABEL' => 'Confirm password'));
			}
		}
?>
		<table style="width: 700px; border:0px;">
			<tr valign="top">
				<td>
<?php
		// roles
		print $t_user->roleListAsHTMLFormElement(array('name' => 'roles', 'size' => 6));
?>
				</td>
			</tr>
		</table><br/>
<?php
		// Output user profile settings if defined
		$va_user_profile_settings = $this->getVar('profile_settings');
		if (is_array($va_user_profile_settings) && sizeof($va_user_profile_settings)) {
			foreach($va_user_profile_settings as $vs_field => $va_info) {
				if($va_errors[$vs_field]){
					print "<div class='formErrors' style='text-align: left;'>".$va_errors[$vs_field]."</div>";
				}
				if($va_info["label"] == "Other"){
					print "<div class='formLabel other' style='font-weight:normal;'>&nbsp;".$va_info["label"].": ".$va_info["element"]."</div>";
				}else{
					print "<div class='formLabel'>".$va_info["label"]."<br/><span style='font-weight:normal;'>".$va_info["element"]."</span></div>";
				}
			}
		}
?>
	</form>
	</div>
	
	<div class="formButtons tealTopBottomRule">
<?php
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "Administration", "Users", "ListUsers")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#UsersForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($vn_user_id){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "Users", "Delete", array('user_id' => $vn_user_id));
		}
?>
	</div><!-- end formButtons -->
</div>