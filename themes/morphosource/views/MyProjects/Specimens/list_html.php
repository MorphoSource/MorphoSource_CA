<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$va_specimens = $this->getVar("specimens");
	$t_specimen = new ms_specimens();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "lookupSpecimen"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if(sizeof($va_specimens) > 0){
?>
		<div id="specimenBibliographyInfo">
			<!-- load Bib form here -->
		</div><!-- end specimenBibliographyInfo -->
		<script type="text/javascript">
			jQuery(document).ready(function() {			
				jQuery('#specimenBibliographyInfo').load(
					'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'bibliographyLookup'); ?>'
				);
				return false;
			});
		</script>
<?php
		$vs_order_by = $this->getVar("specimens_order_by");
		print "<div style='margin:15px 0px 0px 10px;'><b>Order by:</b> ".(($vs_order_by == "number") ? "<b>" : "").caNavLink($this->request, "Specimen number", "", "MyProjects", "Specimens", "listItems", array("specimens_order_by" => "number")).(($vs_order_by == "number") ? "</b>" : "")." | ".(($vs_order_by == "taxon") ? "<b>" : "").caNavLink($this->request, "Taxonomic name", "", "MyProjects", "Specimens", "listItems", array("specimens_order_by" => "taxon")).(($vs_order_by == "taxon") ? "</b>" : "")."</div>";
		print '<div id="itemListings">';
		foreach($va_specimens as $vn_specimen => $va_specimen_info){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'>";
			# --- only show edit/delete links for records created by this project or projects the user has access to
			# --- do not allow delete of records with media
			$t_project = new ms_projects();
			if(($va_specimen_info["project_id"] == $this->getVar("project_id")) || $t_project->isFullAccessMember($this->request->user->get("user_id"), $va_specimen_info["project_id"])){
				print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $vn_specimen));
				if(!$t_specimen->getSpecimenMediaIDs($vn_specimen)){
					print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $vn_specimen));
				}else{
					print "<br/><br/><div class='editMessage'>* You cannot delete specimen with media</div>";
				}
			}else{
				print "<div class='editMessage'>This specimen was created by the project, <b>".$va_specimen_info["project_name"]."</b>.  If you need to edit this specimen please contact ".trim($va_specimen_info["fname"]." ".$va_specimen_info["lname"])." at <a href='mailto:".$va_specimen_info["email"]."'>".$va_specimen_info["email"]."</a></div>";
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
