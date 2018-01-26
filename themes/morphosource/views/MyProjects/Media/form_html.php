<?php
	$vn_specimen_id = $this->request->getParameter("specimen_id", pInteger);
	$t_specimen = new ms_specimens();
	
	$t_project = $this->getVar("project");
	
	$t_item = $this->getVar("item");
	$vn_media_id = $t_item->get("media_id");
	
	if (!$vn_media_id) { // new media
		$t_item->set('specimen_id', $vn_specimen_id);	// if specimen_id is present on request for newly created media set it here
	}
	$o_db = new Db();
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Media Group Information"); ?>
	</H1>
<?php
}
# --- formatting variables
# --- all fields in float_fields array  will be floated to the left
$va_float_fields = array("element", "side", "copyright_permission", "copyright_license", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_exposure_time", "scanner_filter", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_shading_correction", "scanner_calibration_flux_normalization", "scanner_calibration_geometric_calibration", "created_on", "approval_status");
# --- all fields in clear_fields array  will have a clear output after them
$va_clear_fields = array("copyright_license", "side", "scanner_z_resolution", "scanner_watts", "scanner_filter", "scanner_wedge", "approval_status", "scanner_calibration_description");

?>
	<div id='formArea'>
<?php
if (!$this->request->isAjax() && !$t_item->get("media_id") && !$t_item->get("derived_from_media_id")) {
?>
	<div id="derivativeLookupContainer" style="background-color:#ededed; padding:10px; margin-bottom:20px;">
		<H2 class='ltBlueBottomRule' style='margin: 10px 0px 10px 0px;'>Derivative Information</H2>
		<div style="text-align:center;">
			<b>Are you creating a media group containing files derived from another MorphoSource media group?</b><br/>
			If so, search for the original media group to pre-populate your record with the appropriate information.<br/>
			When searching by media number, do not include the "M".  For example to find M1234, enter 1234:<br/>
		</div>
<?php
			print caFormTag($this->request, 'form', 'mediaDerivativeForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
			print "<div class='formLabel' style='text-align:center;'>";
			print caHTMLTextInput("derivative_lookup", array("id" => 'msMediaDerivativeID', 'class' => 'lookupBg', 'value' => ''), array('width' => "200px", 'height' => 1));
			print "</div>";
			print "<input type='hidden' value='' name='lookup_derived_from_media_id' id='media_derivative_id'>";
			print "</form>";		
?>
			<div id="derivativePreview"></div>
			<H2 class='ltBlueBottomRule' style='margin: 10px 0px 10px 0px;'>&nbsp;</H2>
			<div style="text-align:center;">
				<b>No, this media group is not a derivative.</b>
			</div>
			<p style="text-align:center;">
				<br/><a href="#" class="button buttonSmall" onClick="jQuery('#mediaForm').show(); jQuery('#derivativeLookupContainer').hide(); return false;">Continue With New Media Group</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
			print caNavLink($this->request, _t("Back to Project Page"), "button buttonSmall", "MyProjects", "Dashboard", "Dashboard");
?>
			</p>
	</div>
<script type='text/javascript'>
	jQuery('#msMediaDerivativeID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'Media', 'Get', array("max" => 500, "quickadd" => false)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var media_derivative_id = parseInt(ui.item.id);
				if (media_derivative_id > 0) {
					// found an id
					jQuery('#media_derivative_id').val(media_derivative_id);
					jQuery('#derivativePreview').load(
						'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'derivativePreview'); ?>/media_derivative_id/' + media_derivative_id
					);
				}
			}
		}
	).click(function() { this.select(); });
	jQuery(document).ready(function(){
		jQuery('#mediaForm').hide();
	});
</script>

<?php
}
print caFormTag($this->request, 'save', 'mediaForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back to Project Page"), "button buttonSmall", "MyProjects", "Dashboard", "Dashboard")."</div>";
}else{
	print "<div style='float:right;'>".caNavLink($this->request, _t("Cancel"), "button buttonSmall", "MyProjects", "Media", "MediaInfo", array("media_id" => $vn_media_id))."</div>";
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
	if($vn_media_id){
		print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $vn_media_id));
	}
}
?>
	</div><!-- end formButtons -->

<?php

if (!$this->request->isAjax()) {
	print "<div style='float:left; width:430px; padding-top:10px;'>";
	if($vn_specimen_id){
		print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>Specimen Information</H2>";
?>
	<h2 style='width:390px; margin: 10px 0px 10px 0px;'><em>Will be added to specimen <strong><?php print $t_specimen->getSpecimenName($vn_specimen_id); ?></strong> upon save</em></h2>
<?php
	}
}
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f] && $vs_f != "facility_id"){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			# -----------------------------------------------
			case "derived_from_media_id":
				if($t_item->get("derived_from_media_id")){
					$t_parent = new ms_media($t_item->get("derived_from_media_id"));
					$vb_derivative_access = false;
					if($t_parent->get("published") > 0){
						$vb_derivative_access = true;
					}else{
						$t_project = new ms_projects();
						$vb_derivative_access = $t_project->isMember($this->request->user->get("user_id"), $t_parent->get("project_id"));
					}
					print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>Derived From</H2>";
					print "<div style='margin-bottom:20px; padding:10px; width:390px;'>";
					$va_parent_media = $t_parent->getPreviewMediaFile(null, array("icon"), (($vb_derivative_access) ? false : true));
					if(is_array($va_parent_media) && sizeof($va_parent_media)){
						print "<div style='float:left; padding-right:20px;'>".$va_parent_media["media"]["icon"]."</div>";
					}
					print "<b>M".$t_parent->get("media_id")."</b><br/>";
					print $t_parent->get("title")."<br/>";
					print $t_specimen->getSpecimenName($t_parent->get("specimen_id"));
					print "<div style='clear:both;'></div></div>";
				}
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>General Information</H2>";
			break;
			# -----------------------------------------------
			case "facility_id":
				#if (!$this->request->isAjax()) {
				#	print "</div><!-- end float --><div style='float:left; width:420px;'>";
				#}
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>Scanner Information</H2>";
				if($va_errors[$vs_f]){
					print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
				}
				print "<div id='facilityInfo'>"; //<div class='formLabel'>";
				//print "Enter the facility this file was created at:<br/>".caHTMLTextInput("media_facility_lookup", array("id" => 'msFacilityID', 'class' => 'lookupBg', 'value' => $this->getVar("facility_name")), array('width' => "375px", 'height' => 1));
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>", array('id' => 'msFacilityID', 'nullOption' => '-'));
				print "</div>";
				//print "<input type='hidden' id='facility_id' name='facility_id' value='".$t_item->get("facility_id")."'>";
				//print "</div>";
			break;
			# -----------------------------------------------
			case "scanner_id":
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>", array('id' => 'msScannerID', 'nullOption' => '-'));
			break;
			# -----------------------------------------------
			case "is_copyrighted":
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin:20px 0px 10px 0px;'>Copyright Information</H2>";
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				print "<div id='copyrightBlock' style='display:none;'>";
			break;
			# -----------------------------------------------
			case "copyright_info":
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				print "</div><!-- end copyrightBlock -->";
			break;
			# -----------------------------------------------
			case "created_on":
			case "last_modified_on":
				if($t_item->get($vs_f)){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."' style='padding-right:25px;'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			case "media":
				$vs_media_url = $t_item->getMediaUrl('media', 'original');
				
				
				
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>";
				
				print 'Select '.($vs_media_url ? 'replacement ' : '').'media from '.caHTMLSelect('mediaSource', array(
					'From your computer...' => 'upload',
					'From the server...' => 'server'
				), array('id' => 'mediaSource'), array('value' => $this->request->user->getVar('lastMediaSource')));
				print "<div id='mediaFormFileUpload'>".$t_item->htmlFormElement($vs_f, "^ELEMENT")."</div>";
				
				if (
					($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
					&&
					($vs_upload_base_directory = $t_item->getAppConfig()->get('upload_base_directory'))
					&&
					(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
				) {
					$va_files = caGetDirectoryContentsAsList($vs_user_upload_directory);
					$va_files_proc = array('[SELECT A FILE]' => '');
					foreach($va_files as $vs_path) {
						$va_files_proc[$vs_path_proc = preg_replace('!^'.$vs_user_upload_directory.'!', '', $vs_path)] = $vs_path_proc;
					}
					print "<div id='mediaFormFileSelect'>". caHTMLSelect('mediaServerPath', $va_files_proc, array(), array())."</div>";
				}
				//print $t_item->htmlFormElement($vs_f.'_preview', "Preview media".(($vs_media_url) ? " replacement" : "")."<br>^ELEMENT");
				print "<div id='mediaFormFilePreviews'>Image to use as preview:<br/><input type='file' name='mediaPreviews'/></div>";
				//if ($vs_media_url) {
				//	print "<div style='float: right; width: 125px;'>".caHTMLCheckboxInput("updatePreviews", array('value' => '1'))." Update preview icons only</div>";
				//}
				print "</div>";
?>
				<script type="text/javascript">
					function msSetMediaSource() {
						if (jQuery('#mediaSource').val() == 'upload') {
							jQuery('#mediaFormFileUpload').slideDown(250);
							jQuery('#mediaFormFileSelect').slideUp(250);
						} else {
							jQuery('#mediaFormFileSelect').slideDown(250);
							jQuery('#mediaFormFileUpload').slideUp(250);
						}
					}
					jQuery(document).ready(function() { 
						msSetMediaSource(); 
						jQuery('#mediaSource').on("change", function(e) { msSetMediaSource();  });
					});
				</script>
<?php
			break;
			# -----------------------------------------------
			case "scanner_calibration_shading_correction":
				# --- make a subheading over the calibration check boxes
				print "<div style='padding-left:10px;'><b>Calibration options</b></div>";
				$vs_suffix = $t_item->getFieldInfo($vs_f, 'SUFFIX');
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT {$vs_suffix}</div>");
			break;
			# -----------------------------------------------
			case "media_citation_instruction1":
				print "<div class='formLabel'>";
				print $t_item->htmlFormElement("media_citation_instruction1", "^LABEL<br>^ELEMENT")."<span style='font-weight:normal;'> provided access to these data ".$t_item->htmlFormElement("media_citation_instruction2", "^ELEMENT")." ".$t_item->htmlFormElement("media_citation_instruction3", "^ELEMENT").". The files were downloaded from www.MorphoSource.org, Duke University.";
				print "</span></div>";
			break;
			# -----------------------------------------------
			case "media_citation_instruction2":
			case "media_citation_instruction3":
				continue;
			break;
			# -----------------------------------------------
			case "reviewer_id":
				# --- when publish is set to download needs aproval, show drop down of project users to set optional override to project default for who should approve downloads
				$va_members = $t_project->getMembers();
				if(sizeof($va_members)){
					print "<div class='formLabel' id='downloadRequestReviewer' ".(($t_item->get("published") == 2) ? "" : "style='display:none;'").">"._t("Download requests reviewed by")."<br/>";
					print "<select name='reviewer_id' id='reviewer_id'>\n";
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
					  	$('#downloadRequestReviewer').show();
					  }else{
					  	$('#reviewer_id').val('');
					  	$('#downloadRequestReviewer').hide();
					  }
					});
				</script>
<?php
			break;
			# -----------------------------------------------
			default:
				$vs_suffix = $t_item->getFieldInfo($vs_f, 'SUFFIX');
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT {$vs_suffix}</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
if (!$this->request->isAjax()) {
	# --- only load the repeatable file bundle when not ajax load
	$t_media_file = new ms_media_files();
	
?>
</div><!-- end float -->
<div style='float:left; width:420px;'>
	<div class="mediaFilesContainer">
		<H2 class='ltBlueBottomRule' style='width:390px; margin:20px 0px 10px 0px;'>Media File(s)</H2>
		<div class="r-group">
<?php
				$vb_show_server_dir = false;
				if (
					($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
					&&
					($vs_upload_base_directory = $t_media_file->getAppConfig()->get('upload_base_directory'))
					&&
					(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
				) {
					$vb_show_server_dir = true;
				}
				foreach(array("title") as $vs_f){
					print $t_media_file->htmlFormElement($vs_f,"<div class='formLabel'><label for='media_0_'.$vs_f data-pattern-text='^LABEL ++:'>^LABEL</label><br>^ELEMENT</div>", array("name" => "media[0][".$vs_f."]", "id" => "media_0_".$vs_f, "data-pattern-name" => "media[++][".$vs_f."]", "data-pattern-id" => "media_++_".$vs_f));
				}
				print "<div class='formLabelFloat'>Select a file for upload".(($vb_show_server_dir) ? "<br/>" : " ")."from your computer";
				print "<br/>".$t_media_file->htmlFormElement("media", "^ELEMENT", array("name" => "media[0]", "id" => "media_0", "data-pattern-name" => "media[++]", "data-pattern-id" => "media_++"));
				print "</div>\n";
				
				if ($vb_show_server_dir) {
					print "<div class='formLabelFloat'><br/>OR from the server<br/>";
					$va_files = caGetDirectoryContentsAsList($vs_user_upload_directory);
					$va_files_proc = array('[SELECT A FILE]' => '');
					foreach($va_files as $vs_path) {
						$va_files_proc[$vs_path_proc = preg_replace('!^'.$vs_user_upload_directory.'!', '', $vs_path)] = $vs_path_proc;
					}
					print caHTMLSelect('media[0][mediaServerPath]', $va_files_proc, array("id" => "media_0_mediaServerPath", "data-pattern-name" => "media[++][mediaServerPath]", "data-pattern-id" => "media_++_mediaServerPath"), array());
					print "</div>\n";
				}
				print "<div class='formLabel' style='clear:both;'>";
				print "<label for='mediaPreviews_0' data-pattern-text='Image to use as preview ++:'>Image to use as preview:</label><br/><input type='file' name='mediaPreviews[0]' id='mediaPreviews_0' data-pattern-name='mediaPreviews[++]' data-pattern-id='mediaPreviews_++'/>";
				print "</div>\n";
			
			foreach(array("element", "side") as $vs_f){
				print $t_media_file->htmlFormElement($vs_f,"<div class='formLabelFloat'><label for='media_0_'.$vs_f data-pattern-text='^LABEL ++:'>^LABEL</label><br>^ELEMENT</div>", array("name" => "media[0][".$vs_f."]", "id" => "media_0_".$vs_f, "data-pattern-name" => "media[++][".$vs_f."]", "data-pattern-id" => "media_++_".$vs_f));
			}
			print "<div style='clear:both;'><!--empty--></div>";
			# --- file type set to derivative if the group is a derivative
			$va_derived_from_media_file_ids = array();
			if($t_item->get("derived_from_media_id")){
				# --- get the file numbers of the media group this group was derived from so can add to derived from file id dropdown
				$q_derived_from_media_file_ids = $o_db->query("select media_file_id, media_id, title from ms_media_files where media_id = ?", $t_item->get("derived_from_media_id"));
				if($q_derived_from_media_file_ids->numRows()){
					while($q_derived_from_media_file_ids->nextRow()){
						$va_derived_from_media_file_ids[$q_derived_from_media_file_ids->get("media_file_id")] = "M".$q_derived_from_media_file_ids->get("media_id")."-".$q_derived_from_media_file_ids->get("media_file_id")."; ".$q_derived_from_media_file_ids->get("title");
					}
				}
				# --- file is a derivative
				print "<div class='formLabel'>File Type: Derivative File</div>";
				print "<input type='hidden' value='2'  name='media[0][file_type]' id='media_0_file_type' data-pattern-name='media[++][file_type]' data-pattern-id='media_++_file_type'>";
			}else{
				$vs_f = "file_type";
				print $t_media_file->htmlFormElement($vs_f,"<div class='formLabel'><label for='media_0_'.$vs_f data-pattern-text='^LABEL ++:'>^LABEL</label><br>^ELEMENT</div>", array("name" => "media[0][".$vs_f."]", "id" => "media_0_".$vs_f, "data-pattern-name" => "media[++][".$vs_f."]", "data-pattern-id" => "media_++_".$vs_f));
			
			}
			if(is_array($va_derived_from_media_file_ids) && sizeof($va_derived_from_media_file_ids)){
				$vs_derived_from_file_select = "<div class='formLabel'><label for='media_0_derived_from_media_file_id' data-pattern-text='Derived from file ++:'>Derived from file</label><br/><select name='media[0][derived_from_media_file_id]' id='media_0_derived_from_media_file_id' data-pattern-name='media[++][derived_from_media_file_id]' data-pattern-id='media_++_derived_from_media_file_id'><option value=''>-</option>";
				foreach($va_derived_from_media_file_ids as $vn_derived_from_id => $vs_derived_from_title){
					$vs_derived_from_file_select .= "<option value='".$vn_derived_from_id."'>".$vs_derived_from_title."</option>";	
				
				}
				$vs_derived_from_file_select .= "</select></div>";
			}
			print $vs_derived_from_file_select;
			
			print $t_media_file->htmlFormElement("published","<div class='formLabel'><label for='media_0_'.published data-pattern-text='^LABEL ++:'>^LABEL</label><br>^ELEMENT</div>", array("name" => "media[0][published]", "id" => "media_0_published", "data-pattern-name" => "media[++][published]", "data-pattern-id" => "media_++_published"));

			print $t_media_file->htmlFormElement("notes","<div class='formLabel'><label for='media_0_'.notes data-pattern-text='^LABEL ++:'>^LABEL</label><br>^ELEMENT</div>", array("width"=> "368px", "name" => "media[0][notes]", "id" => "media_0_published", "data-pattern-name" => "media[++][notes]", "data-pattern-id" => "media_++_published"));
?>

		<p>
		  <!-- Manually a remove button for the item. -->
		  <!-- If one didn't exist, it would be added to overall group -->
		  <div class="r-btnRemove button buttonSmall"><i class='fa fa-remove'></i></div>
		</p>
	  </div><!-- end r-group -->
	
	  <!-- The add button -->
	  <div class="r-btnAdd button buttonSmall">Add another media file +</div>
	</div><!-- end mediaFilesContainer -->
<script>
	jQuery(document).ready(function() {
		$('.mediaFilesContainer').repeater({
		  btnAddClass: 'r-btnAdd',
		  btnRemoveClass: 'r-btnRemove',
		  groupClass: 'r-group',
		  minItems: 1,
		  maxItems: 3,
		  startingIndex: 0,
		  reindexOnDelete: true,
		  repeatMode: 'append',
		  animation: null,
		  animationSpeed: 400,
		  animationEasing: 'swing',
		  clearValues: true
		});


		jQuery('#mediaForm').submit(function(e){		
			if(!jQuery('#msFacilityID').val() || !jQuery('#title').val()){
				alert("Please enter the title and facility");
				e.preventDefault();
				return false;
			}else{
				return true;
			}
		});
	});

</script>
<?php
	
	print "</div><!-- end float -->";
	print "<div style='clear:both;'><!--empty--></div>";
}
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($vn_media_id){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $vn_media_id));
		}
}
?>
	</div><!-- end formButtons -->
</form>
</div>
<script type='text/javascript'>
	jQuery(document).ready(function(){
		if ($('input[name="is_copyrighted"]').attr('checked')) {
			$('#copyrightBlock').slideDown(200);
		}
		$('input[name="is_copyrighted"]').click(function() {
			if ($('input[name="is_copyrighted"]').attr('checked')) {
				$('#copyrightBlock').slideDown(200);
			} else {
				$('#copyright_permission').val('0');
				$('#copyright_license').val('0');
				$('#copyright_info').val('');
				$('#copyrightBlock').slideUp(200);
			}
		});
	});

	var scannerListByFacilityID = <?php print json_encode($this->getVar('scannerListByFacilityID')); ?>;
	
	jQuery('#msFacilityID').bind('change', function(event) {
				var facility_id = jQuery('#msFacilityID').val();
				console.log("facilityID", facility_id);
				if (facility_id < 1) {
					return;
				} else {
					// Load scanner list into drop-down
					var optionsAsString = "<option value=''>-</option>";
					var scannerList = scannerListByFacilityID[facility_id];
					console.log(scannerList);
					if (scannerList) {
						for(var scanner_id in scannerList) {
							optionsAsString += "<option value='" + scanner_id + "'>" + scannerList[scanner_id].name + "</option>";
						}
					}
					jQuery("select#msScannerID").find('option').remove().end().append(jQuery(optionsAsString));
				}
			}
	);
</script>