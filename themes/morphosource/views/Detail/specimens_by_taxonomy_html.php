<?php
	$va_specimens_by_taxonomy = $this->getVar("specimens_by_taxomony");
	
	$vn_count = $va_specimens_by_taxonomy["numSpecimen"];
	$va_specimens = array_pop($va_specimens_by_taxonomy["specimen"]);
	$t_specimen = new ms_specimens();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<?php print sizeof($va_specimens["specimens"])." Specimen".((sizeof($va_specimens["specimens"]) == 1) ? "" : "s")." for <i>".$this->getVar("taxomony_term")."</i>"; ?>
	</H1>
	<div id="dashboardMedia">

<?php
			if(is_array($va_specimens["specimens"]) && (sizeof($va_specimens["specimens"]))){
				foreach($va_specimens["specimens"] as $vn_specimen_id => $va_specimen) {
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