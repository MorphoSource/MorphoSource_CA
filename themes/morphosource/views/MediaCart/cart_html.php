<?php
	$q_items = $this->getVar("items");
	$vn_set_id = $this->getVar("set_id");
	$t_specimen = new ms_specimens();
	$t_media = new ms_media();
	$vs_errors = $this->getVar("errors");
?>
	<div class="blueRule"><!-- empty --></div>
<?php
	if($q_items->numRows()){
?>
		<H1 class="capitalize">
			<div style="float:right;"><?php print caNavLink($this->request, _t("Clear Cart"), "button buttonLarge", "", "MediaCart", "clearCart", array("set_id" => $vn_set_id)); ?> <?php print caNavLink($this->request, _t("Download all"), "button buttonLarge", "", "MediaCart", "downloadCart", array("set_id" => $vn_set_id)); ?></div>
			<?php print _t("Media Cart"); ?>
		</H1>
<?php
	}
	if($vs_errors){
		print "<H2>".$vs_errors."</H2>";
	}
	if($q_items->numRows()){
		print '<div id="mediaListings">';
		while($q_items->nextRow()){
			print "<div class='projectMediaContainer'>";
			print "<div class='mediaCartTools'>";
			print "<div style='float:right;'>".caNavLink($this->request, _t("<i class='fa fa-times-circle'></i>"), "", "", "MediaCart", "Remove", array("media_id" => $q_items->get("media_id")), array("title" => _t("remove from cart")))."</div>";
			print caNavLink($this->request, _t("<i class='fa fa-download'></i>"), "", "Detail", "MediaDetail", "DownloadMedia", array("media_id" => $q_items->get("media_id")), array("title" => _t("download")));
			print "</div>";
			print "<div class='projectMedia'>";
			print $q_items->getMediaTag("media", "preview190");
			print $t_specimen->getSpecimenName($q_items->get("specimen_id"));
			print "<br/>".$t_media->formatPublishedText($q_items->get("published"));
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
		print "<br/><br/><H2>"._t("<b>There are no media in your cart.</b><br/>Use the media cart to collect media to download in a single batch.  You can add media to your cart by clicking the cart icon <i class='fa fa-shopping-cart'></i> along side media throughout the site.")."</H2>";
	}
?>