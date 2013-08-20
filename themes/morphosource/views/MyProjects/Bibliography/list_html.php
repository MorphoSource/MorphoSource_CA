<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$q_listings = $this->getVar("listings");
	$vn_num_listings = $this->getVar("num_listings");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print _t("Bibliography"); ?>
	</H1>
<?php
	if($vn_num_listings){
		if($q_listings->numRows()){
			print '<div id="itemListings">';
			while($q_listings->nextRow()){
				print "<div class='listItemLtBlue'>";
				print "<div class='listItemRightCol'>";
				$t_project = new ms_projects();
				if(($q_listings->get("project_id") == $this->getVar("project_id")) || $t_project->isMember($this->request->user->get("user_id"), $q_listings->get("project_id"))){
					print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $q_listings->get($ps_primary_key)));
					print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $q_listings->get($ps_primary_key)));
				}else{
					print "<div class='editMessage'>This record was created by the project, <b>".$q_listings->get("name")."</b><br/>If you need to edit this record please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
				}
				print "</div>";
				print $t_item->getCitationText($q_listings->getRow())."</div>";
			}
			print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 associated with this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
