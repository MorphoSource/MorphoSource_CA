<?php
	$vs_taxomony_term = $this->getVar("taxomony_term");
	$vs_taxomony_term_display = $this->getVar("taxomony_term_display");
	$vn_project_id = $this->getVar("project_id");
	$t_project = $this->getVar("project");
	$va_specimens_by_taxonomy = $this->getVar("specimens_by_taxomony");
	
	$t_specimen = new ms_specimens();
?>
	<div class="blueRule"><!-- empty --></div>
<?php
	if($t_project->get("publication_status")){
		print "<div style='float:right; padding-top:20px;'>".caNavLink($this->request, _t("View public page"), "", "Detail", "ProjectDetail", "specimenWithoutTaxonomy", array("specimens_group_by" => $vs_taxomony_term, "project_id" => $vn_project_id))." or <a href='#' onClick='copyToClipboard(\"#specimenGroupLink\"); return false;' class='button buttonSmall' title='click to copy link to clipboard'>Copy <i class='fa fa-external-link'></i></a>";
		print "<div style='display:none;' id='specimenGroupLink'>".caNavUrl($this->request, "Detail", "ProjectDetail", "specimenWithoutTaxonomy", array("specimens_group_by" => $vs_taxomony_term, "project_id" => $vn_project_id))."</div>";
		print "</div>";
	}
?>
	<H1 class="capitalize">
		<?php print sizeof($va_specimens_by_taxonomy)." Specimen".((sizeof($va_specimens_by_taxonomy) == 1) ? "" : "s")." without <i>".$vs_taxomony_term_display."</i>"; ?>
	</H1>
	<div id="dashboardMedia">

<?php
			if(is_array($va_specimens_by_taxonomy) && (sizeof($va_specimens_by_taxonomy))){
				foreach($va_specimens_by_taxonomy as $vn_specimen_id => $va_specimen) {
					$vn_num_media = is_array($va_specimen['media']) ? sizeof($va_specimen['media']) : 0;
			
					print "<div class='projectMediaContainer'>";
					print "<div class='projectMedia".(($vn_num_media > 1) ? " projectMediaSlideCycle" : "")."'>";
			
					if (is_array($va_specimen['media']) && ($vn_num_media > 0)) {
						foreach($va_specimen['media'] as $vn_media_id => $va_media) {
							if (!($vs_media_tag = $va_media['tags']['preview190'])) {
								$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
							}
							print "<div class='projectMediaSlide'>".caNavLink($this->request, $vs_media_tag, "", "MyProjects", "Specimens", "form", array("specimen_id" => $vn_specimen_id))."</div>";
							//print "<span class='mediaID'>M{$vn_media_id}</span>";
						}
					} else {
						print "<div class='projectMediaPlaceholder'> </div>";
					}
					print "</div><!-- end projectMedia -->";
			
					$vs_specimen_taxonomy = join(" ", $t_specimen->getSpecimenTaxonomy($vn_specimen_id));
					print "<div class='projectMediaSlideCaption'>".caNavLink($this->request, $t_specimen->formatSpecimenName($va_specimen), '', "MyProjects", "Specimens", "form", array("specimen_id" => $vn_specimen_id));
					if ($vs_specimen_taxonomy) { print ", <em>{$vs_specimen_taxonomy}</em>"; }
							//print ($vs_element = $va_specimen['element']) ? " ({$vs_element})" : "";
					if($vs_uuid_id = $va_specimen["uuid"]){
						print "<div style='margin-top:3px; '><a href='https://www.idigbio.org/portal/records/".$vs_uuid_id."' target='_blank' class='blueText' style='text-decoration:none; font-weight:bold;'>iDigBio <i class='fa fa-external-link'></i></a></div>";
					}
					print "</div>\n";
					print "</div><!-- end projectMediaContainer -->";
				}
			}
?>	
		<div style="clear:both;"></div>
	</div><!-- end dashboardMedia -->
	
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.projectMediaSlideCycle').cycle();
	});
</script>
<?php