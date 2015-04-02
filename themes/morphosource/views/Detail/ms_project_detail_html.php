<?php
	$t_project = $this->getVar("item");
	
	# --- get all media files linked to this media record
	$o_db = new Db();
	
?>
<div class="blueRule"><!-- empty --></div>
<?php
	$vs_back_link = "";
	#switch(ResultContext::getLastFind($this->request, "ms_specimens")){
	#	case "specimen_browse":
			$vs_back_link = caNavLink($this->request, _t("Back"), 'button buttonLarge', '', 'Browse', 'Index', array(), array('id' => 'back'));
	#	break;
	#	# ----------------------------------
	#	case "basic_search":
	#		$vs_back_link = caNavLink($this->request, _t("Back"), 'button buttonLarge', '', 'Search', 'Index', array(), array('id' => 'back'));
	#	break;
	#	# ----------------------------------
	#}
	#if (($this->getVar('is_in_result_list'))) {
	#	if ($this->getVar('next_id') > 0) {
	#		print "<div style='float:right; padding:15px 0px 0px 15px;'>".caNavLink($this->request, _t("Next"), 'button buttonLarge', 'Detail', 'MediaDetail', 'Show', array('media_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
	#	}
		print "<div style='float:right; padding:15px 0px 0px 15px;'>".$vs_back_link."</div>";
	#	if ($this->getVar('previous_id')) {
	#		print "<div style='float:right; padding:15px 0px 0px 15px;'>".caNavLink($this->request, _t("Previous"), 'button buttonLarge', 'Detail', 'MediaDetail', 'Show', array('media_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
	#	}
	#}
?>
<H1>
<?php
	print _t("Project: ").$t_project->get("name");
?>
</H1>
<div id="projectDetail">
	<div class="tealRule"><!-- empty --></div>
<?php
	if($t_project->get("abstract")){
		print "<div class='InfoContainerRight'><H2>"._t("About the project")."</H2><div class='unit'>".$t_project->get("abstract")."</div></div>";
	}
	print "<div class='InfoContainerLeft'>";
	print "<H2>"._t("Members")."</H2><div class='unit'>";
	$va_members = array();
	$va_members = $t_project->getMembers();
	if(sizeof($va_members) > 0){
		$vni = 0;
		foreach($va_members as $va_member){
			$vni++;
			print $va_member["fname"]." ".$va_member["lname"];
			if($vni < sizeof($va_members)){
				print ", ";
			}
		}
	}
	print "</div><!-- end unit -->\n";
	print "<H2>"._t("Data")."</H2><div class='unit'>";
	$va_media_counts = $t_project->getProjectMediaCounts();
	print ((int)$va_media_counts[1] + (int)$va_media_counts[2])." published media<br/>";
	print $t_project->numSpecimens()." specimens";
	print "</div><!-- end unit -->\n";
	if($t_project->get("url")){
		print "<H2>"._t("More Information")."</H2><div class='unit'>";
		print "<a href='".$t_project->get("url")."' target='_blank'>".$t_project->get("url")."</a>";
		print "</div><!-- end unit -->\n";
	}
	print "</div>";
?>
	<div id="dashboardMedia">
	<div class="tealRule"><!-- empty --></div>
<?php
	$t_specimen = new ms_specimens();
	$vs_order_by = $this->request->getParameter('specimens_order_by', pString);
	if(!$vs_order_by){
		$vs_order_by = "number";
	}
	$va_specimens = $t_project->getProjectSpecimens(null, $vs_order_by, array("published_media_only" => true));
?>
	<H1><?php print sizeof($va_specimens)." Project Specimen".((sizeof($va_specimens) == 1) ? "" : "s"); ?></H1>
<?php
	if(is_array($va_specimens) && ($vn_num_media = sizeof($va_specimens))){
		print "<div style='text-align:right; margin:5px 0px 5px 0px;'><b>Order by:</b> ".(($vs_order_by == "number") ? "<b>" : "").caNavLink($this->request, "Specimen number", "", "Detail", "ProjectDetail", "Show", array("specimens_order_by" => "number", "project_id" => $t_project->get("project_id"))).(($vs_order_by == "number") ? "</b>" : "")." | ".(($vs_order_by == "taxon") ? "<b>" : "").caNavLink($this->request, "Taxonomic name", "", "Detail", "ProjectDetail", "Show", array("specimens_order_by" => "taxon", "project_id" => $t_project->get("project_id"))).(($vs_order_by == "taxon") ? "</b>" : "")."</div>";
		
		foreach($va_specimens as $vn_specimen_id => $va_specimen) {
			$vn_num_media = is_array($va_specimen['media']) ? sizeof($va_specimen['media']) : 0;
			
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia".(($vn_num_media > 1) ? " projectMediaSlideCycle" : "")."'>";
			
			if (is_array($va_specimen['media']) && ($vn_num_media > 0)) {
				foreach($va_specimen['media'] as $vn_media_id => $va_media) {
					if (!($vs_media_tag = $va_media['tags']['preview190'])) {
						$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
					}
					print "<div class='projectMediaSlide'>".caNavLink($this->request, $vs_media_tag, "", "Detail", "SpecimenDetail", "Show", array("specimen_id" => $vn_specimen_id))."</div>";
					//print "<span class='mediaID'>M{$vn_media_id}</span>";
				}
			} else {
				print "<div class='projectMediaPlaceholder'> </div>";
			}
			print "</div><!-- end projectMedia -->";
			
			$vs_specimen_taxonomy = join(" ", $t_specimen->getSpecimenTaxonomy($vn_specimen_id));
			print "<div class='projectMediaSlideCaption'>".caNavLink($this->request, $t_specimen->formatSpecimenName($va_specimen), '', "Detail", "SpecimenDetail", "Show", array("specimen_id" => $vn_specimen_id));
			if ($vs_specimen_taxonomy) { print ", <em>{$vs_specimen_taxonomy}</em>"; }
					//print ($vs_element = $va_specimen['element']) ? " ({$vs_element})" : "";
			print "</div>\n";
			print "</div><!-- end projectMediaContainer -->";
		}
	}else{
		print "<H2>"._t("This project has no published specimen/media")."</H2>";
	}
?>
</div><!-- end dashboardMedia -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.projectMediaSlideCycle').cycle();
	});
</script>

</div>