	<div id='deleteConfirmBox'>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Delete %1", $this->getVar("name_singular")); ?>
	</H1>
<?php
	print "<p>"._t("Really delete %1: <i>%2</i>?", $this->getVar("name_singular"), $this->getVar("item_name"))."</p>";
	
	print caNavLink($this->request, _t("Yes"), "button buttonLarge", "Administration", "List", "Delete", array("table" => $this->getVar("table"), "delete_confirm" => 1, $this->getVar("primary_key") => $this->getVar($this->getVar("primary_key"))));
	print "&nbsp;&nbsp;&nbsp;&nbsp;";
	print caNavLink($this->request, _t("No"), "button buttonLarge", "Administration", "List", "listItems", array("table" => $this->getVar("table")));
	print "</div><!-- end deleteConfirmBox -->"
?>