<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
	$t_specimen = new ms_specimens();
	$t_project = new ms_projects();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("Add All Media to Cart"), "button buttonLarge", "", "MediaCart", "addProjectMediaToCart", array("project_id" => $pn_project_id)); ?>&nbsp;&nbsp;<?php print caNavLink($this->request, _t("Review Publication Settings"), "button buttonLarge", "MyProjects", "Media", "reviewPublicationSettings"); ?>&nbsp;&nbsp;<?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if($q_listings->numRows()){
?>
		<div id="mediaBibliographyInfo">
			<!-- load Bib form here -->
		</div><!-- end mediaBibliographyInfo -->
		<div id="mediaListCitationForm" style="padding:10px 0px 15px 0px;">
<?php
			print caFormTag($this->request, 'batchMediaCitationInstructions', 'mediaCitationInstructionsForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
			print "<div class='formLabel'>Apply the following Media Citation Instructions to all project media:<div style='font-weight:normal; padding:0px 0px 0px 15px;'>".$t_item->htmlFormElement("media_citation_instruction1", "^ELEMENT")." provided access to these data ".$t_item->htmlFormElement("media_citation_instruction2", "^ELEMENT").$t_item->htmlFormElement("media_citation_instruction3", "^ELEMENT").". The files were downloaded from www.MorphoSource.org, Duke University.";
			print "<br/><a href='#' name='save' class='button buttonSmall' style='margin-top:5px;' onclick='jQuery(\"#mediaCitationInstructionsForm\").submit(); return false;'>"._t("Save")."</a></div>";
?>
			</div></form>
		</div>
<?php
		$t_own_project = new ms_projects();
		print '<div id="mediaListings">';
		while($q_listings->nextRow()){
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia'>";
			$va_preview_file_info = $t_item->getPreviewMediaFile($q_listings->get("media_id"), array("preview190"));
			print "<span style='float:right'>".$va_preview_file_info["numFiles"]." file".(($va_preview_file_info["numFiles"] == 1) ? "" : "s")."</span>";
			if(($q_listings->get("project_id") != $pn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $q_listings->get("project_id")))){
				# --- read only access --- link to detail page
				print caNavLink($this->request, "M".$q_listings->get("media_id"), "", "Detail", "MediaDetail", "Show", array($ps_primary_key => $q_listings->get($ps_primary_key)))." - <b>READ ONLY ACCESS</b><br/>";
				print caNavLink($this->request, $va_preview_file_info["media"]["preview190"], "", "Detail", "MediaDetail", "Show", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			}else{
				print caNavLink($this->request, "M".$q_listings->get("media_id"), "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $q_listings->get($ps_primary_key)))."<br/>";
				print caNavLink($this->request, $va_preview_file_info["media"]["preview190"], "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			}
			if($q_listings->get("project_id") != $pn_project_id){
				$t_own_project->load($q_listings->get("project_id"));
				print "<b>Owned by</b> ".((strlen($t_own_project->get("name")) > 22) ? mb_substr($t_own_project->get("name"), 0, 22)."..." : $t_own_project->get("name"))."<br/>";
			}
			print $q_listings->get("title")."<br/>";
			print $t_specimen->getSpecimenName($q_listings->get("specimen_id"));
			print "<br/>".$t_item->formatPublishedText($q_listings->get("published"));
			print "</div>";
			print '</div><!-- end projectMediaContainer -->';
		}
		print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
?>
		<script type="text/javascript">
			jQuery(document).ready(function() {			
				jQuery('#mediaBibliographyInfo').load(
					'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'bibliographyLookup', array('media_id' => $pn_media_id)); ?>'
				);
				return false;
			});
		</script>
<?php
	}else{
		print "<br/><br/><H2>"._t("There are no %1 used by this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>