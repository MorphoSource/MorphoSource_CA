<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
	$t_specimen = new ms_specimens();
	$t_project = new ms_projects();
	$o_db = new Db();
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, "Back to Dashboard", "button buttonLarge", "MyProjects", "Dashboard", "dashboard"); ?></div>
		Media Publication Settings
	</H1>
<?php
	if($q_listings->numRows()){
		print caFormTag($this->request, 'batchPublicationSave', 'batchPublicationForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
		print "<p>Use the checkboxes to select media groups and/or files.  Choose a publication option from the drop down and hit save to update the publication setting of the selected media groups and/or files.</p>";
		print "<p><b>Please note: </b>Media files will inherit the publication setting of the group.  Only assign a publication setting to individual media files if you want to override the group level setting.</p>";
		print "<div class='formLabel'>".$t_item->htmlFormElement("published","Choose a publication status to apply to the selected files below: ^ELEMENT");
		print "<a href='#' name='save' class='button buttonSmall' style='margin-top:5px;' onclick='jQuery(\"#batchPublicationForm\").submit(); return false;'>"._t("Save")."</a></div>";
		print "<div class='tealRule'></div>";
		print '<div id="mediaListings">';
		while($q_listings->nextRow()){
			print "<div class='projectMediaListItem'>";
			print "<input type='checkbox' name='media_ids[]' value='".$q_listings->get("media_id")."'> <b>".caNavLink($this->request, "M".$q_listings->get("media_id"), "", "MyProjects", $this->request->getController(), "mediaInfo", array($ps_primary_key => $q_listings->get($ps_primary_key)))."</b>, ".$q_listings->get("title").", ".$t_specimen->getSpecimenName($q_listings->get("specimen_id")).(($q_listings->get("element")) ? ", ".$q_listings->get("element"): "").", <b>".$t_item->getChoiceListValue("published", $q_listings->get("published"))."</b>"; 
			$q_media_files = $o_db->query("SELECT media, media_file_id, use_for_preview, published, side, element, title, notes FROM ms_media_files where media_id = ?", $q_listings->get("media_id"));
			if($q_media_files->numRows()){
				while($q_media_files->nextRow()){
					print "<div class='projectMediaListFile'>";
					print "<input type='checkbox' name='media_file_ids[]' value='".$q_media_files->get("media_file_id")."'> <b>M".$q_listings->get("media_id")."-".$q_media_files->get("media_file_id")."</b>; ";
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
						print "Inherits publication setting from group: <b>".$t_item->getChoiceListValue("published", $q_listings->get("published"))."</b>";
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