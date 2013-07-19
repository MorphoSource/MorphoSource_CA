<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if($q_listings->numRows()){
		print '<div id="itemListings">';
		while($q_listings->nextRow()){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'>";
			if($q_listings->get("project_id") == $this->getVar("project_id")){
				print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $q_listings->get($ps_primary_key)));
				print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			}else{
				print "<div class='editMessage'>This record was created by the project, <b>".$q_listings->get("name")."</b><br/>If you need to edit this record please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
			}
			print "</div>";
			$i = 0;
			foreach($pa_list_fields as $vs_field){
				$i++;
				if($q_listings->get($vs_field)){
					print $q_listings->get($vs_field);
					if($i < sizeof($pa_list_fields)){
						print ' '; //$t_item->getProperty("LIST_DELIMITER");
					}
				}
			}
			print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 associated with this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
