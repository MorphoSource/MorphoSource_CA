<?php
	$t_media = $this->getVar('t_media');
?>
<div class='close'><a href="#" onclick="caMediaPanel.hidePanel(); return false;" title="close">&nbsp;&nbsp;&nbsp;</a></div>
<div style="background-color: #ffffff; text-align: center; height:100%;">
<?php
	$vb_force_resize = false;
	switch($t_media->getMediaInfo("media", "original", "MIMETYPE")) {
		case 'application/stl':
		case 'application/surf':
			print $t_media->getMediaTag('media', 'original', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer'));
		case 'application/ply':
			print $t_media->getMediaTag('media', 'stl', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer'));
			break;
		default:
			$vb_force_resize = true;
			print $t_media->getMediaTag('media', 'tilepic', array('viewer_width' => '1000', 'viewer_height' => '800', 'id' => 'msMediaViewer'));
			break;
	}
?>
</div>
<?php
	if ($vb_force_resize) {
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#msMediaViewer").width(jQuery("#msMediaViewer").parent().width()).height(jQuery("#msMediaViewer").parent().height());
	});
</script>
<?php
	}
?>