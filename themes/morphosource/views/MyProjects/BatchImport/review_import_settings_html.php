<div style="float:right; margin-top:15px;">
<?php
	print caNavLink($this->request, _t("Back: Import Settings"), "button buttonLarge", "MyProjects", "BatchImport", "importSettingsForm");
?>
</div>
<?php
	if($va_errors = $this->getVar("errors")){
		print "<div class='formErrors'>".join("; ", $va_errors)."</div>";
	}
?>
<h1>Batch Import: Review Settings & Upload File</h1>

<H2 class="ltBlueBottomRule" style="margin: 10px 0px 10px 0px;">Import Settings</H2>
<p>
	Please confirm your batch import settings are accurate before uploading your worksheet.  The following project, institution, facility and scanner will be applied to <span class="blueText"><i><b>all</b></i></span> batch imported specimen and media.
</p>
<p>
	<b>Project:</b> <?php print $this->getVar("project_name"); ?><br/>
	<b>Specimen Institution:</b> <?php print $this->getVar("institution_name"); ?><br/>
	<b>Facility:</b> <?php print $this->getVar("facility_name"); ?><br/>
	<b>Scanner:</b> <?php print $this->getVar("scanner_name"); ?><br/>
</p>
<p>
	
</p>
<div id="uploadContainer" style="padding:30px 0px 30px 0px; display:none;">
	<H2 class='ltBlueBottomRule' style='margin: 10px 0px 10px 0px;'>Upload Import File</H2>
<?php
	print caFormTag($this->request, 'uploadFile', 'fileUploadForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
		<div class="formLabel">
			Import Worksheet<br/>
			<input name="spreadsheet[0]" id="spreadsheet" type="file"> <a href="#" onClick="jQuery('#spreadsheet').val(''); return false;">Clear file</a>
		</div>
		<input type="hidden" name="institution_id" value="<?php print $this->getVar("institution_id"); ?>">
		<input type="hidden" name="facility_id" value="<?php print $this->getVar("facility_id"); ?>">
		<input type="hidden" name="scanner_id" value="<?php print $this->getVar("scanner_id"); ?>">
		<div class="formLabel">
			<a href="#" name="save" class="button buttonLarge" onclick="jQuery('#fileUploadForm').submit(); return false;"><?php print _t("Import Batch"); ?></a>
		</div>
	</form>
</div>
<div class="tealTopBottomRule" id="continueContainer" style="padding:20px 0px 20px 0px;">
	<div style="float:right;">
<?php
	print caNavLink($this->request, _t("Back: Import Settings"), "button buttonLarge", "MyProjects", "BatchImport", "importSettingsForm");
?>
	</div>
	<a href="#" class="button buttonLarge" onclick="jQuery('#continueContainer').hide(); jQuery('#uploadContainer').slideToggle(); return false;"><?php print _t("Continue"); ?></a>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('#fileUploadForm').submit(function(e){		
			if(!jQuery('#spreadsheet').val()){
				alert("Please select your completed import worksheet.");
				e.preventDefault();
				return false;
			}
		});
	});
</script>