<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
	$t_specimen = new ms_specimens();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if($q_listings->numRows()){
		print '<div id="mediaListings">';
		while($q_listings->nextRow()){
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia'>";
			print caNavLink($this->request, "M".$q_listings->get("media_id"), "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $q_listings->get($ps_primary_key)))."<br/>";
			print caNavLink($this->request, $q_listings->getMediaTag("media", "preview190"), "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			print $t_specimen->getSpecimenName($q_listings->get("specimen_id"));
			print "</div>";
			print '</div><!-- end projectMediaContainer -->';
		}
		print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
	}else{
		print "<br/><br/><H2>"._t("There are no %1 used by this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
