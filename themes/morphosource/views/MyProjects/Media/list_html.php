<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$va_media_listings = $this->getVar("listings");
	$t_specimen = new ms_specimens();
	$t_project = new ms_projects();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("Add All Media to Cart"), "button buttonLarge", "", "MediaCart", "addProjectMediaToCart", array("project_id" => $pn_project_id)); ?>&nbsp;&nbsp;<?php print caNavLink($this->request, _t("Review Publication Settings"), "button buttonLarge", "MyProjects", "Media", "reviewPublicationSettings"); ?>&nbsp;&nbsp;<?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if(sizeof($va_media)){
?>
		<H2 style="font-size:25px; margin-top:15px; margin-bottom:0px; padding-bottom:0px;" class="ltBlueBottomRule">Batch options</H2>
		<div id="mediaBibliographyInfo">
			<!-- load Bib form here -->
		</div><!-- end mediaBibliographyInfo -->
		<div id="mediaListCitationForm" style="padding:10px 0px 0px 0px;">
<?php
			print caFormTag($this->request, 'batchMediaCitationInstructions', 'mediaCitationInstructionsForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
			print "<div class='formLabel'>Apply the following Media Citation Instructions to all project media:<div style='font-weight:normal; padding:0px 0px 0px 15px;'>".$t_item->htmlFormElement("media_citation_instruction1", "^ELEMENT")." provided access to these data ".$t_item->htmlFormElement("media_citation_instruction2", "^ELEMENT").$t_item->htmlFormElement("media_citation_instruction3", "^ELEMENT").". The files were downloaded from www.MorphoSource.org, Duke University.";
			print "<br/><a href='#' name='save' class='button buttonSmall' style='margin-top:5px;' onclick='jQuery(\"#mediaCitationInstructionsForm\").submit(); return false;'>"._t("Save")."</a></div>";
?>
			</div></form>
		</div>
		<div id="mediaListcopyrightForm" style="padding:10px 0px 25px 0px;">
<?php
			print caFormTag($this->request, 'batchMediaCopyright', 'mediaCopyrightForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
			print "<div><b>Apply the following Copyright settings to all project media:</b></div>";
			print "<div style='padding:0px 0px 0px 15px;'>Permission: ".$t_item->htmlFormElement("copyright_permission", "^ELEMENT", array("width" => "150px"))."&nbsp;&nbsp;";
			print "License: ".$t_item->htmlFormElement("copyright_license", "^ELEMENT", array("width" => "150px"))."&nbsp;&nbsp;";
			print "Copyright Holder: ".$t_item->htmlFormElement("copyright_info", "^ELEMENT", array("width" => "150px"))."&nbsp;&nbsp;";
			print "<a href='#' name='save' class='button buttonSmall' style='margin-top:5px;' onclick='jQuery(\"#mediaCopyrightForm\").submit(); return false;'>"._t("Save")."</a></div>";
?>
			</form>
		</div>
<?php
		$t_own_project = new ms_projects();
		print '<div id="mediaListings"><H2 style="font-size:25px; margin-top:15px; margin-bottom:25px;" class="ltBlueBottomRule">Media Groups</H2>';
		foreach ($va_media_listings as $vn_media_id => $va_media) {
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia'>";
			$va_preview_file_info = $t_item->getPreviewMediaFile($vn_media_id, array("preview190"));
			print "<span style='float:right'>".$va_preview_file_info["numFiles"]." file".(($va_preview_file_info["numFiles"] == 1) ? "" : "s")."</span>";
			if(($va_media["project_id"] != $pn_project_id) && (!$t_project->isFullAccessMember($this->request->user->get("user_id"), $va_media["project_id"]))){
				# --- read only access --- link to detail page
				print caNavLink($this->request, "M".$vn_media_id, "", "Detail", "MediaDetail", "Show", array($ps_primary_key => $vn_media_id))." - <b>READ ONLY ACCESS</b><br/>";
				print caNavLink($this->request, $va_preview_file_info["media"]["preview190"], "", "Detail", "MediaDetail", "Show", array($ps_primary_key => $vn_media_id));
			}else{
				print caNavLink($this->request, "M".$vn_media_id, "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $vn_media_id))."<br/>";
				print caNavLink($this->request, $va_preview_file_info["media"]["preview190"], "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $vn_media_id));
			}
			if($va_media["project_id"] != $pn_project_id){
				$t_own_project->load($va_media["project_id"]);
				print "<b>Owned by</b> ".((strlen($t_own_project->get("name")) > 22) ? mb_substr($t_own_project->get("name"), 0, 22)."..." : $t_own_project->get("name"))."<br/>";
			}
			print $va_media["title"]."<br/>";
			print $t_specimen->getSpecimenName($va_media["specimen_id"]);
			print "<br/>".$t_item->formatPublishedText($va_media["published"]);
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