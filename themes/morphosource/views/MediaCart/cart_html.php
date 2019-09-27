<?php
	$q_items = $this->getVar("items");
	$vn_set_id = $this->getVar("set_id");
	$t_specimen = new ms_specimens();
	$t_media = new ms_media();
	$vs_errors = $this->getVar("errors");
?>
	<div class="blueRule"><!-- empty --></div>
<?php
	$vs_display = "";
	if($vs_errors){
		$vs_display .= "<H2>".$vs_errors."</H2>";
	}
	if($q_items->numRows()){
		$vs_display .= '<div id="mediaListings">';
		$va_all_mimetypes = array();
		while($q_items->nextRow()){
			if($t_media->userCanDownloadMediaFile($this->request->getUserID(), $q_items->get("media_id"), $q_items->get("media_file_id"))){
				$va_properties = $q_items->getMediaInfo('media', 'original');
				$vs_display .= "<div class='projectMediaContainer'>";
				$vs_display .= "<div class='mediaCartTools'>";
				$vs_display .= "<div style='float:right;'>".caNavLink($this->request, _t("<i class='fa fa-times-circle'></i>"), "", "", "MediaCart", "Remove", array("media_file_id" => $q_items->get("media_file_id")), array("title" => _t("remove from cart")))."</div>";
				#$vs_display .= caNavLink($this->request, _t("<i class='fa fa-download'></i>"), "", "Detail", "MediaDetail", "DownloadMedia", array("media_file_id" => $q_items->get("media_file_id"), "media_id" => $q_items->get("media_id"), "download" => 1), array("title" => _t("download")));
				$vs_display .= "<a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("media_id" => $q_items->get("media_id"), "media_file_id" => $q_items->get("media_file_id"), "download_action" => "DownloadMedia"))."\", true); return false;' title='download'>"._t("<i class='fa fa-download'></i>")."</a>";
		
				$vs_display .= "</div>";
				$vs_display .= "<div class='projectMedia'>";
				$vs_display .= caNavLink($this->request, $q_items->getMediaTag("media", "preview190"), '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $q_items->get("media_id")));
				$vs_display .= caNavLink($this->request, "M".$q_items->get("media_id")."-".$q_items->get("media_file_id"), '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $q_items->get("media_id")));
				$vs_display .= "<br/>".caNavLink($this->request, $t_specimen->getSpecimenName($q_items->get("specimen_id")), 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $q_items->get("specimen_id")));
				#$vs_display .= "<br/>".$t_media->formatPublishedText($q_items->get("published"));
				$vs_display .= "<br/>".msGetMediaFormatDisplayString($q_items).", ".caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize'])."<br/>";
				$vs_display .= "</div>";
				$vs_display .= '</div><!-- end projectMediaContainer -->';
				$va_all_mimetypes[$va_properties["MIMETYPE"]] = msGetMediaFormatDisplayString($q_items);
			}
		}
		$vs_display .= '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
	}else{
		$vs_display .= "<br/><br/><H2>"._t("<b>There are no files in your cart.</b><br/>Use the media cart to collect files to download in a single batch.  You can add files to your cart by clicking the cart icon <i class='fa fa-shopping-cart'></i> along side media throughout the site.")."</H2>";
	}
	if($q_items->numRows()){
?>
		<H1 class="capitalize">
			<?php print _t("Media Cart"); ?>
		</H1>
			<div style="float:right; width:250px; text-align:right; margin-top:-10px;">
<?php
			print caFormTag($this->request, 'removeByMimetype', 'removeMimetypeForm', 'mediaCart', 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
				<span style="font-size:12px;">Remove files from cart by type:
				<select name='mimetype' style='width:150px;'>
<?php
				foreach($va_all_mimetypes as $vs_mimetype => $vs_mimetype_display){
					print "<option value='$vs_mimetype'>".$vs_mimetype_display."</option>";
				}
?>
				</select>
				<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#removeMimetypeForm').submit(); return false;"><?php print _t("Remove"); ?></a>
		
			</form></div>

		<div>
<?php
			print caNavLink($this->request, _t("Clear Cart"), "button buttonLarge", "", "MediaCart", "clearCart", array("set_id" => $vn_set_id))." ";
			#print caNavLink($this->request, _t("Download all files & metadata"), "button buttonLarge", "", "MediaCart", "downloadCart", array("set_id" => $vn_set_id, "download" => 1))." ";
			print "<a href='#' class='button buttonLarge' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("set_id" => $vn_set_id))."\", true); return false;' title='download'>"._t("Download all files & metadata")."</a> ";
			print caNavLink($this->request, _t("Download only metadata"), "button buttonLarge", "", "MediaCart", "downloadCartMd", array("set_id" => $vn_set_id, "download" => 1));
?>
		</div>
			
<?php
	}
	print $vs_display;
?>