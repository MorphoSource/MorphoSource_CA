<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$va_media_listings = $this->getVar("listings");
	$t_specimen = new ms_specimens();
	$t_project = new ms_projects($pn_project_id);
	$o_db = new Db();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, "Back to Dashboard", "button buttonLarge", "MyProjects", "Dashboard", "dashboard"); ?></div>
		Media Publication Settings
	</H1>
<?php
	if(sizeof($va_media_listings)){
		print caFormTag($this->request, 'batchPublicationSave', 'batchPublicationForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
		print "<p>Use the checkboxes to select media groups and/or files.  Choose a publication option from the drop down and hit save to update the publication setting of the selected media groups and/or files.</p>";
		print "<p><b>Please note: </b>Media files will inherit the publication setting of the group.  Only assign a publication setting to individual media files if you want to override the group level setting. Selecting a user to review download requests ONLY applies to published media groups, not separately published media files.</p>";
		print "<div class='formLabel' style='text-align: center;'>";
		print $t_item->htmlFormElement("published","Choose a publication status to apply to the selected files below:</br> ^ELEMENT");

		$va_members = $t_project->getMembers();
		if(sizeof($va_members)){
			print "<div id='downloadRequestReviewer'></br>"._t("If users must request download permission, requests reviewed by:</br> ");
			print "<select name='reviewer_id' id='reviewer_id' style='width: 450px;'".(($t_item->get("published") == 2) ? "" : "disabled").">\n";
			print "<option value=''>"._t("Use project default")."</option>\n";
			foreach($va_members as $va_member){
				if($va_member["membership_type"] == 1){
					print "<option value='".$va_member["user_id"]."' ".(($t_item->get("reviewer_id") == $va_member["user_id"]) ? "selected" : "").">".$va_member["fname"]." ".$va_member["lname"].", ".$va_member["email"]."</option>\n";
				}
			}
			print "</select>\n";
			print "</div>\n";
		}
?>
		<script>
			$('#published').on('change', function() {
			  if($(this).val() == 2){
			  	$('#reviewer_id').prop("disabled", false);
			  }else{
			  	$('#reviewer_id').val('');
			  	$('#reviewer_id').prop("disabled", true);
			  }
			});
		</script>
<?php

		print "</br><a href='#' name='save' class='button buttonLarge' style='margin-top:5px;' onclick='jQuery(\"#batchPublicationForm\").submit(); return false;'>"._t("Save")."</a>";
		print "</div>";
		print "<div style=''>";
		print "<a href='#' name='selectAll' class='button buttonSmall' style='margin:5px 10px 10px 0px;' onclick='jQuery(\"input:checkbox\").prop(\"checked\", true); return false;'>"._t("Select All")."</a>";
		print "<a href='#' name='selectNone' class='button buttonSmall' style='margin:5px 10px 10px 0px;' onclick='jQuery(\"input:checkbox\").prop(\"checked\", false); return false;'>"._t("Select None")."</a>";
		print "<a href='#' name='selectAllPub' class='button buttonSmall' style='margin:5px 10px 10px 0px;' onclick='jQuery(\"input:checkbox\").prop(\"checked\", false); jQuery(\".pub\").prop(\"checked\", true); return false;'>"._t("Select Media Groups Currently Published")."</a>";
		print "<a href='#' name='selectUnPub' class='button buttonSmall' style='margin:5px 10px 10px 0px;' onclick='jQuery(\"input:checkbox\").prop(\"checked\", false); jQuery(\".unpub\").prop(\"checked\", true); return false;'>"._t("Select Media Groups Currently Unpublished")."</a>";
		print "</div>";
		
		print "<div class='tealRule'></div>";
		print '<div id="mediaListings">';
		foreach ($va_media_listings as $vn_media_id => $va_media) {
			print "<div class='projectMediaListItem'>";
			print "<input type='checkbox' name='media_ids[]' class='".
				((($va_media["published"] == 1) || ($va_media["published"] == 2)) ? "pub" : "unpub").
				"' value='".$vn_media_id."'> <b>".
				caNavLink($this->request, "M".$vn_media_id, "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $vn_media_id))."</b>, ".$va_media["title"].", ".$t_specimen->getSpecimenName($va_media["specimen_id"]).(($va_media["element"]) ? ", ".$va_media["element"]: "").", <b>".$t_item->getChoiceListValue("published", $va_media["published"])."</b>";
			if ($va_media["published"] == 2){
				print "<b>;</b> Download requests reviewed by ";
				if($vn_reviewer_id = $va_media["reviewer_id"]){
					$t_reviewer = new ca_users($vn_reviewer_id);
					print $t_reviewer->get('fname')." ".$t_reviewer->get('lname');
				} else {
					print "all project members";
				}
			} 
			$q_media_files = $o_db->query("SELECT media, media_file_id, use_for_preview, published, side, element, title, notes FROM ms_media_files where media_id = ?", $vn_media_id);
			if($q_media_files->numRows()){
				while($q_media_files->nextRow()){
					print "<div class='projectMediaListFile'>";
					print "<input type='checkbox' name='media_file_ids[]' value='".$q_media_files->get("media_file_id")."'> <b>M".$$vn_media_id."-".$q_media_files->get("media_file_id")."</b>; ";
					$vs_file_info = msGetMediaFormatDisplayString($q_media_files)."; ";
					$va_versions = $q_media_files->getMediaVersions('media');
					$va_properties = $q_media_files->getMediaInfo('media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
					$vs_file_info .= caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize']);
					if($q_media_files->get("title")){
						print (mb_strlen($q_media_files->get("title")) > 60) ? mb_substr($q_media_files->get("title"), 0, 60)."..." : $q_media_files->get("title");
						print "; ";
					}
					if($q_media_files->get("element")){
						print $q_media_files->get("element")."; ";
					}
					print ($q_media_files->get("use_for_preview") == 1) ? " <i>used for media preview</i>; " : "";
					print $vs_file_info."<br/>";
					if($q_media_files->get("published") == null){
						# --- get the pub setting from the group
						print "Inherits publication setting from group: <b>".$t_item->getChoiceListValue("published", $va_media["published"])."</b>";
					}else{
						print "<b>".$t_item->getChoiceListValue("published", $q_media_files->get("published"))."</b>";
					}
					print "</div>";
				}
			}
			print "</div><!-- end projectMediaListItem -->";
		}
		print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		print "</form>";
	}else{
		print "<br/><br/><H2>"._t("There are no %1 used by this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>