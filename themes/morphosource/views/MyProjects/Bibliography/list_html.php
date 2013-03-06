<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$q_listings = $this->getVar("listings");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print _t("Bibliography"); ?>
	</H1>
<?php
	if($q_listings->numRows()){
		print '<div id="itemListings">';
		while($q_listings->nextRow()){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'>";
			print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			print "</div>";
			print $t_item->getCitationText($q_listings->getRow())."</div>";
			print '</div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 associated with this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
