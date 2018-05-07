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
	$agreement_url = $this->request->getThemeUrlPath().
		'/static/MorphoSource_download_use_agreement.pdf';
	
?>
<div class="panelDialog">
	<a href="#" style="float:right;" onclick="msMediaPanel.hidePanel(); return false;">
		<img src="<?php print $x_img_url; ?>"></img>
	</a>
	<div style='text-align: center;'>
	<H1>
		Download Media
	</H1>
	</div>
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
			<div id='download_agreement' style='margin-top: 20px; margin-bottom: 20px; padding: 4px;'>
				<input type='checkbox' id='agreement_check'>
				<label for='agreement_check'>
					I agree to the conditions of this download as outlined in the 
					<a href="<?php print $agreement_url; ?>" target='_blank'>MorphoSource download use agreement</a>.
					<span id='required' style='color: red; font-weight: bold; display: none;'>You must agree to the conditions to download.</span>
				</label>
			</div>
			<div class="formButtons" style='text-align: center;'>
				<a href="#" name="save" class="button buttonLarge" onclick="submitDownloadForm(); return false;"><?php print _t("Download"); ?></a>
			</div>
		</form>
	</div>
</div>
<script>
	function submitDownloadForm() {
		if (!jQuery('#agreement_check').prop('checked')) {
			jQuery('#required').show();
			jQuery('#download_agreement').addClass('redShadow');
		} else {
			jQuery('#required').hide();
			jQuery('#download_agreement').removeClass('redShadow');
			jQuery('#downloadForm').submit(); 
			msMediaPanel.hidePanel(); 
		}
		return false;
	}
</script>