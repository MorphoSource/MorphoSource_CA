<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$va_specimens = $this->getVar("specimens");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if(sizeof($va_specimens) > 0){
		print '<div id="itemListings">';
		foreach($va_specimens as $vn_specimen => $va_specimen_info){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'>";
			# --- only show edit/delete links for facilities created by this project
			if($va_specimen_info["project_id"] == $this->getVar("project_id")){
				print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $vn_specimen));
				print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $vn_specimen));
			}else{
				print "<div class='editMessage'>This specimen was created by the project, <b>".$va_specimen_info["project_name"]."</b><br/>If you need to edit this specimen please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
			}
			print "</div>";
			$i = 0;
			print $t_item->getSpecimenName($vn_specimen);
			
			print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 associated with this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
