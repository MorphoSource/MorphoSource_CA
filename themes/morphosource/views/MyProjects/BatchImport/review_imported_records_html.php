<?php
	$va_stats = $this->getVar("stats");
	$va_errors = $this->getVar("errors");
	$va_rows_not_imported = $this->getVar("rows_not_imported");
	$pn_project_id = $this->getVar("project_id");
?>
<h1>Batch Import: Summary</h1>
<?php
	if(is_array($va_errors) && sizeof($va_errors)){
		foreach($va_errors as $vs_row => $vs_error){
			print "<div class='formErrors'>Row ".$vs_row.": ".$vs_error."</div>";
		}
	}
?>
<div style="padding:15px; background-color:#EDEDED;">
	<H3>Summary</H3>
	<span class="blueText"><b>New Specimen:</b></span> <?php print $va_stats["new_specimen"]; ?><br/>
	<span class="blueText"><b>Linked Specimen (already existed in MorphoSource):</b></span> <?php print $va_stats["linked_specimen"]; ?><br/>
	<span class="blueText"><b>New Taxonomy:</b></span> <?php print $va_stats["new_taxonomy"]; ?><br/>
	<span class="blueText"><b>New Media Groups:</b></span> <?php print $va_stats["new_media_groups"]; ?><br/>
	<span class="blueText"><b>New Media Files:</b></span> <?php print $va_stats["new_media_files"]; ?><br/>
	<span class="<?php print (sizeof($va_rows_not_imported)) ? "formErrors" : "blueText"; ?>"><b>Worksheet rows not imported due to errors:</b></span> <?php print sizeof($va_rows_not_imported); ?><br/>
</div>
<?php
	if(is_array($va_stats["new_media_group_ids"]) && sizeof($va_stats["new_media_group_ids"])){
		$t_media = new ms_media();
		$t_specimen = new ms_specimens();
		$q_listings = caMakeSearchResult("ms_media", $va_stats["new_media_group_ids"]);
		print '<div id="mediaListings">';
		while($q_listings->nextHit()){
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia'>";
			$va_preview_file_info = $t_media->getPreviewMediaFile($q_listings->get("media_id"), array("preview190"));
			print "<span style='float:right'>".$va_preview_file_info["numFiles"]." file".(($va_preview_file_info["numFiles"] == 1) ? "" : "s")."</span>";
			print caNavLink($this->request, "M".$q_listings->get("media_id"), "", "MyProjects", "Media", "mediaInfo", array("media_id" => $q_listings->get("media_id")))."<br/>";
			print caNavLink($this->request, $va_preview_file_info["media"]["preview190"], "", "MyProjects", "Media", "mediaInfo", array("media_id" => $q_listings->get("media_id")));
			print $q_listings->get("title")."<br/>";
			print $t_specimen->getSpecimenName($q_listings->get("specimen_id"));
			print "<br/>".$t_media->formatPublishedText($q_listings->get("published"));
			print "</div>";
			print '</div><!-- end projectMediaContainer -->';
		}
		print '<div style="clear:right;"><!-- empty --></div></div><!-- end mediaListings -->';

	}
?>
