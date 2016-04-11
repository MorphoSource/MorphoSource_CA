<?php
	$ps_action = $this->getVar("download_action");
	$pn_media_id = $this->getVar("media_id");
	$pn_media_file_id = $this->getVar("media_file_id");
	$pn_set_id = $this->getVar("set_id");
	if($pn_set_id){
		$vs_url = caNavUrl($this->request, "", "MediaCart", "downloadCart");
	}else{
		$vs_url = caNavUrl($this->request, "Detail", "MediaDetail", $ps_action);
	}
?>
<div id="surveyFormOverlay">
	<div class="button buttonSmall" style="float:right;" onclick="msMediaPanel.hidePanel(); return false;">X</div>
	<H1>
		Download Media
	</H1>
	<div class="blueRule"><!-- empty --></div>
	<div id="formArea">
	<p>Please provide the following information before download.</p>
	<form id="downloadForm" action="<?php print $vs_url; ?>" class="form-horizontal" role="form" method="POST">
<?php
		$t_dl_stats = new ms_media_download_stats();
		print $t_dl_stats->htmlFormElement("intended_use","<div class='formLabel'>^LABEL<br><span style='font-weight:normal;'>^ELEMENT</span></div>", ['useTable' => 1, 'numTableColumns' => 2]);
		print $t_dl_stats->htmlFormElement("intended_use_other","<div class='formLabel' style='margin-top:-10px; font-weight:normal; margin-left:15px;'>^LABEL: ^ELEMENT</div>");
		print $t_dl_stats->htmlFormElement("3d_print","<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
		
		if($pn_media_id){
			print "<input type='hidden' name='media_id' value='".$pn_media_id."'>";
		}
		if($pn_media_file_id){
			print "<input type='hidden' name='media_file_id' value='".$pn_media_file_id."'>";
			print "<input type='hidden' name='download' value='1'>";
		}
		if($pn_set_id){
			print "<input type='hidden' name='set_id' value='".$pn_set_id."'>";
			print "<input type='hidden' name='download' value='1'>";
		}
		
		
?>
			<div class="formButtons tealTopBottomRule">
				<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#downloadForm').submit(); msMediaPanel.hidePanel(); return false;"><?php print _t("Download"); ?></a>
			</div>
		</form>
	</div>
</div>