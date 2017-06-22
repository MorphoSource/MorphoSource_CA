<?php
	$t_media = new ms_media;
?>
<?php
	print "<div style='float:right; margin-top:15px;'>".caNavLink($this->request, _t("Back: Overview"), "button buttonLarge", "MyProjects", "BatchImport", "overview")."</div>";
?>
<h1>Batch Import: Settings</h1>

<p>
	Specimen and media created through the batch import will be linked to the project you're currently working in.  If you have access to multiple projects, make sure to select the appropriate project.
</p>
<p>
	Use the forms below to choose the specimen institution, scanner facility and scanner to associate with the imported records.  If you would like to upload records from various institutions or facilities, please create multiple spreadsheets and conduct multiple imports.
</p>
<div id='formArea'>
<?php
	print caFormTag($this->request, 'reviewImportSettings', 'importSettingsForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
		<H2 class='ltBlueBottomRule' style='margin: 30px 0px 10px 0px;'>Media Files</H2>
<?php
		$vb_dir_files_available = false;
		if (
			($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
			&&
			($vs_upload_base_directory = $t_media->getAppConfig()->get('upload_base_directory'))
			&&
			(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
		) {
			$va_files = caGetDirectoryContentsAsList($vs_user_upload_directory);
			print "<div class='formLabel'>Your File Directory: <span style='font-weight:normal;'>".str_replace($vs_upload_base_directory."/", "", $this->request->user->getPreference('user_upload_directory'))."</span></div>";						
			$i = 0;
			if(sizeof($va_files)){
				print "<div class='formLabel'>Available files: <span style='font-weight:normal;'>";
				foreach($va_files as $vs_path) {
					print preg_replace('!^'.$vs_user_upload_directory.'/!', '', $vs_path);
					$i++;
					if($i < sizeof($va_files)){
						print "; ";
					}
				}
				print "</span></div>";
				$vb_dir_files_available = true;
			}else{
				print "<div class='formErrors'>You have not uploaded any files to your directory.  Please upload your files before proceeding.</div>";
			}
			
		}else{
			print "<div class='formErrors'>You must upload your media files to be batch imported to your upload directory.  You do not have access to a directory! Please contact xxx.</div>";
		}
		
		if($vb_dir_files_available){					
?>		
			<H2 class='ltBlueBottomRule' style='margin: 30px 0px 10px 0px;'>Project</H2>
			<div class="formLabel">
				<b><?php print $this->getVar("project_name"); ?></b>
			</div>
			<H2 class='ltBlueBottomRule' style='margin: 30px 0px 10px 0px;'>Institution</H2>
			<div class="formLabel">
				<b>Specimen Institution</b>:<br/>
				<input name="institution_id_lookup" id="ms_institution_lookup" class="lookupBg ui-autocomplete-input" value="" rows="1" style="width: 354px;" size="" autocomplete="off" type="text">
				<input type="hidden" name="institution_id" id="institution_id" value="<?php print $this->getVar("institution_id"); ?>">
			</div>
			<H2 class='ltBlueBottomRule' style='margin: 30px 0px 10px 0px;'>Facility and Scanner Information</H2>
			<div class="formLabel">
<?php
				print $t_media->htmlFormElement("facility_id","<div class='formLabel'>Facility<br>^ELEMENT</div>", array('id' => 'msFacilityID', 'nullOption' => '-'));
?>
			</div>
			<div class="formLabel">
<?php
				print $t_media->htmlFormElement("scanner_id","<div class='formLabel'>^LABEL<br>^ELEMENT</div>", array('id' => 'msScannerID', 'nullOption' => '-'));

?>
			</div>
			<div style="clear:both; height:30px;"><!-- empty --></div>
				<div class="formButtons tealTopBottomRule">
					<a href="#" name="save" class="button buttonLarge" onclick="jQuery('#importSettingsForm').submit(); return false;"><?php print _t("Continue"); ?></a>
				</div>
			</div>
<?php
		}
?>
	</form>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery('#ms_institution_lookup').autocomplete(
			{ 
				source: '<?php print caNavUrl($this->request, 'lookup', 'Institution', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
				minLength: 3, delay: 800, html: true,
				select: function(event, ui) {
					var institution_id = parseInt(ui.item.id);
					if (institution_id < 1) {
						// nothing found...
						jQuery("#specimenInstitutionFormContainer").load("<?php print caNavUrl($this->request, 'MyProjects', 'Institutions', 'form', array('specimen_id' => $pn_specimen_id)); ?>");
					} else {
						// found an id
						jQuery('#institution_id').val(institution_id);
					}
				}
			}
		).click(function() { this.select(); });

		var scannerListByFacilityID = <?php print json_encode(ms_facilities::scannerListByFacilityID()); ?>;
	
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
		jQuery('#importSettingsForm').submit(function(e){		
			if(!jQuery('#institution_id').val() || !jQuery('#msFacilityID').val() || !jQuery('#msScannerID').val()){
				alert("Please select the institution, facility, and scanner for your batch import.");
				e.preventDefault();
				return false;
			}
		});
	});
</script>