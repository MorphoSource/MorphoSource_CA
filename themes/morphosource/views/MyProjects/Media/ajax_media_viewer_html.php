<?php
	$t_media = $this->getVar('t_media');
	$t_media_file = $this->getVar('t_media_file');
	
	$vs_media_id = "M".$t_media->getPrimaryKey();
	$vs_side = $t_media->getChoiceListValue("side", $t_media->get('side'));
	$vs_title = $t_media->get('title');
	$vs_element = $t_media->get('element');
	$va_media_info = $t_media_file->getMediaInfo("media");
	
	$vs_media_class = caGetMediaClassForDisplay($va_media_info['INPUT']['MIMETYPE']); 
	$vs_mimetype_name = caGetDisplayNameForMimetype($va_media_info['INPUT']['MIMETYPE']);
	
	$vn_filesize = $va_media_info['INPUT']['FILESIZE'];
	$vs_filesize = caFormatFilesize($vn_filesize);
	
	$t_specimen = new ms_specimens($t_media->get('specimen_id'));
	if(is_array($va_taxa = $t_specimen->getSpecimenTaxonomy()) && sizeof($va_taxa)) {
		$vs_taxonomy = join(", ", $va_taxa);
	} else {
		$vs_taxonomy = '';
	}
?>
<div id="msMediaOverlayContent">
	<div class="msMediaOverlayProgress" id="msMediaOverlayProgress">
		<div class="msMediaOverlayProgressContent">
	 	</div>
	</div>
	<div class="msMediaOverlayControls">
		<div id='msMediaOverlayWebGLWarning'></div>
<?php
	print "<span class='mediaID'>{$vs_media_id}-".$t_media_file->get("media_file_id")."</span>";
	print " <strong>{$vs_title}</strong>".(($vs_side && (strtolower($vs_side) != 'unknown')) ? " ({$vs_side})" : "").($vs_element ? " ({$vs_element})" : "");
	if ($vs_taxonomy) { print "; <em>{$vs_taxonomy}</em>"; }
	print "; {$vs_media_class} ({$vs_mimetype_name}); {$vs_filesize}";
?>
		<div class='close'><a href="#" onclick="msMediaPanel.hidePanel(); return false;" title="close">&nbsp;&nbsp;&nbsp;</a></div>
	</div>
<?php
	$vb_force_resize = false;
	$vb_show_progress_bar = false;
	$vb_would_like_webgl = false;
	switch($vs_mimetype = $t_media_file->getMediaInfo("media", "original", "MIMETYPE")) {
		case 'application/stl':
		case 'application/surf':
		case 'text/prs.wavefront-obj':
			print "<div id='msMediaOverlayLegend'><b>tip:</b> Shift scroll to zoom in/out</div>";
			print $t_media_file->getMediaTag('media', 'ctm', array('viewer_width' => '1000', 'viewer_height' => '800', 'background_color' => '#cccccc', 'id' => 'msMediaViewer', 'progress_id' => 'msMediaOverlayProgress'));
			$vb_would_like_webgl = true;
			$vb_show_progress_bar = true;
			break;
		case 'application/ply':		// We could also load the original PLY here but the 3d viewer won't render textures for it so we'll use STL instead
			print "<div id='msMediaOverlayLegend'><b>tip:</b> Shift scroll to zoom in/out</div>";
			print $t_media_file->getMediaTag('media', 'ctm', array('viewer_width' => '1000', 'viewer_height' => '800', 'background_color' => '#cccccc', 'id' => 'msMediaViewer', 'progress_id' => 'msMediaOverlayProgress'));
			$vb_would_like_webgl = true;
			$vb_show_progress_bar = true;
			break;
		case 'application/pdf':
			print "<div style='height: 100%; width: 100%; min-height: 100%; padding: 20% 40% 200% 40%;' onclick='msMediaPanel.hidePanel(); return false;'><div style='color: white; background-color: black; opacity: 0.7; width: 200px; padding: 10px;' onclick='msMediaPanel.hidePanel(); return false;'>Preview unavailable for this content</div></div>";
			break;
		case 'video/mp4':
			$vb_force_resize = true;
			print $t_media_file->getMediaTag('media', 'h264_hi', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer', 'progress_id' => 'msMediaOverlayProgress', 'progress_total_filesize' => $vn_filesize));
			break;
		default:
			if (preg_match("!^video!", $vs_mimetype)) {
				$vb_force_resize = true;
				print $t_media_file->getMediaTag('media', 'h264_hi', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer', 'progress_id' => 'msMediaOverlayProgress', 'progress_total_filesize' => $vn_filesize));
			} else {
				$vb_force_resize = true;
				print $t_media_file->getMediaTag('media', 'tilepic', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer', 'progress_id' => 'msMediaOverlayProgress', 'progress_total_filesize' => $vn_filesize));
			}
			break;
	}
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
<?php
	if ($vb_force_resize) {
?>
		jQuery("#msMediaViewer").width(jQuery("#msMediaViewer").parent().width()).height(jQuery("#msMediaViewer").parent().height());
<?php
	}
	
	if ($vb_show_progress_bar) {
?>
		jQuery("#msMediaOverlayProgress").css("display", "block");	
<?php
	}
		
	if ($vb_would_like_webgl) {
?>
		if (Detector && !Detector.webgl) {
			jQuery('#msMediaOverlayWebGLWarning').html("<a href='http://get.webgl.org'>WebGL</a> is not available. <a href='http://get.webgl.org' class='msMediaOverlayGetWebGLLink'>Get it</a> to improve rendering speed.");
		}
<?php
	}
?>
	});
</script>