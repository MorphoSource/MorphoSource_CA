<?php
	$t_project = $this->getVar("project");
	$va_errors = $this->getVar("errors");
	$t_project_users = $this->getVar("project_users");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><a href="#" class="button buttonLarge" onclick="jQuery('#formArea').load('<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'lookUpMember'); ?>', function() { $('#formArea').show('slide'); }); return false;"><?php print _t("Add Member"); ?></a></div>
		<?php print _t("Project Members"); ?>
	</H1>
<?php
	# get project members
	$va_members = $t_project->getMembers();
	if(sizeof($va_members)){
		$i = 0;
		print "<div class='ltBlueTopRule'>";
		foreach($va_members as $va_member){
			$i++;
			print "<div class='listItemLtBlue ltBlueText'>";
			if($t_project->get("user_id") != $va_member["user_id"]){
				print "<div class='listItemRightCol'>".caNavLink($this->request, _t("Remove"), "button buttonSmall", "MyProjects", "Members", "Delete", array("user_id" => $va_member["user_id"]))."</div>";
			}
			print trim($va_member["fname"]." ".$va_member["lname"]).", ".$va_member["email"];
			if($t_project->get("user_id") == $va_member["user_id"]){
				print ", "._t("Project Administrator");
			}
			print "</div>";	
		}
		print "</div>";
	}
?>
<div id='formArea' style='display:none;'>
	
</div>