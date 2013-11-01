<?php
	$t_project = $this->getVar("project");
	$va_errors = $this->getVar("errors");
	$t_project_users = $this->getVar("project_users");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><a href="#" class="button buttonLarge" onclick="jQuery('#formArea').load('<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'lookUpMember'); ?>', function() { $('#formArea').show('slide'); }); return false;"><?php print _t("Add Members"); ?></a></div>
		<?php print _t("Project Members"); ?>
	</H1>
<?php
	# get project members
	$va_members = $t_project->getMembers();
	$t_user = new ca_users();
	if(sizeof($va_members)){
		$i = 0;
		print "<div class='ltBlueTopRule'>";
		foreach($va_members as $va_member){
			$t_user->load($va_member["user_id"]);
			$i++;
			print "<div class='listItemLtBlue ltBlueText'>";
			print "<div class='listItemRightCol'>";
			if($t_project->get("user_id") != $va_member["user_id"]){
				print caNavLink($this->request, _t("Remove From Project"), "button buttonSmall", "MyProjects", "Members", "Delete", array("user_id" => $va_member["user_id"]))."&nbsp;&nbsp;&nbsp;";
				if($va_member["membership_type"] == 1){
					# --- only full access users can be project admin
					print caNavLink($this->request, _t("Make Project Admin"), "button buttonSmall makeAdmin", "MyProjects", "Members", "setNewAdmin", array("new_admin_id" => $va_member["user_id"]));
				}
			}
			if((!$t_user->hasRole("downloads")) && ($va_member["membership_type"] == 1)){
				# --- only full access users can manage downloads
				print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Manage Downloads"), "button buttonSmall manageDownloads", "MyProjects", "Members", "addUserManageDownloads", array("user_id" => $va_member["user_id"]));
			}	
			print "</div>";
			print trim($va_member["fname"]." ".$va_member["lname"]).", ".$va_member["email"];
			if($va_member["membership_type"] == 2){
				print ", <b>Read Only</b>";
			}
			if($t_project->get("user_id") == $va_member["user_id"]){
				print ", <b>"._t("Project Administrator")."</b>";
			}
			if($t_user->hasRole("downloads")){
				print ", <b>"._t("User is notified of new download requests")."</b> ";
				print caNavLink($this->request, _t("[Stop Download Request notifications]"), "button buttonSmall", "MyProjects", "Members", "removeUserManageDownloads", array("user_id" => $va_member["user_id"]))."&nbsp;&nbsp;&nbsp;";
			}
			print "</div>";	
		}
		# --- add tooltip
		TooltipManager::add(
			".makeAdmin", "<H5>Reassigning the project administrator means you will no longer be able to manage members and project information.</H5>"
		);
		TooltipManager::add(
			".manageDownloads", "<H5>User will be notified by email when there is a new download request for any project media.</H5>"
		);
			
		print "</div>";
	}
?>
<div id='formArea' style='display:none;'>
	
</div>