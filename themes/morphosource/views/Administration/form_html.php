<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	$ps_table = $this->getVar("table");
	
	# --- formatting varibales
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("location_city", "location_state", "location_country");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("location_country");

?>
	<div class="blueRule"><!-- empty --></div>
	<H1 style='text-transform:capitalize;'>
		<?php print "Manage ".$this->getVar("name_plural"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'listForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "Administration", "List", "listItems", array("table" => $this->getVar("table")))."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#listForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "List", "Delete", array("table" => $this->getVar("table"), $ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
<?php
	if($ps_table == "ms_specimens"){
		$o_datamodel = $this->getVar("o_datamodel");
		$t_bib = $o_datamodel->getInstanceByTableName("ms_bibliography", true);
		$t_institution = $o_datamodel->getInstanceByTableName("ms_institutions", true);
	}
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
			case "approval_status":
				if($t_item->get($vs_f)){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			case "body_mass_bibref_id":
			case "locality_absolute_age_bibref_id":
			case "locality_relative_age_bibref_id":
				if($ps_table == "ms_specimens"){
					$vs_name = "";
					if($t_item->get($vs_f)){
						$t_bib->load($t_item->get($vs_f));
						$vs_name = strip_tags($t_bib->getCitationText());
					}
					print "<div class='formLabel'>";
					print $vs_field_info["LABEL"].":<br/>".caHTMLTextInput($vs_f."_lookup", array("id" => 'ms_'.$vs_f.'_lookup', 'class' => 'lookupBg', 'value' => $vs_name), array('width' => '354px', 'height' => 1, 'paadding-right' => '15px'));
					print "</div>";
					print "<input type='hidden' id='".$vs_f."' name='".$vs_f."' value='".$t_item->get($vs_f)."'>";
				}
			break;
			# -----------------------------------------------
			case "institution_id":
				if($ps_table == "ms_specimens"){
					$vs_name = "";
					if($t_item->get($vs_f)){
						$t_institution->load($t_item->get($vs_f));
						$vs_name = strip_tags($t_institution->get("name"));
					}
					print "<div id='specimenInstitutionFormContainer'><div class='formLabel'>";
					print $vs_field_info["LABEL"].":<br/>".caHTMLTextInput($vs_f."_lookup", array("id" => 'ms_institution_lookup', 'class' => 'lookupBg', 'value' => $vs_name), array('width' => '354px', 'height' => 1, 'paadding-right' => '15px'));
					print "</div>";
					print "<input type='hidden' id='".$vs_f."' name='".$vs_f."' value='".$t_item->get($vs_f)."'></div>";
				}elseif($ps_table == "ms_institutions"){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			default:
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
	switch($ps_table){
		case "ms_facilities":
			$va_scanners = $this->getVar("scannerList");
			if(is_array($va_scanners) && sizeof($va_scanners)){
				print "<div class='formLabel'>Scanners:<br/><span style='font-weight:normal;'>";
				$i = 0;
				foreach($va_scanners as $va_scanner){
					print $va_scanner["name"];
					$i++;
					if($i < sizeof($va_scanners)){
						print ", ";
					}
				}
				print "</span></div>";
			}
		break;
		# -----------------------------------------------
	}
	
	
	
	
	
	print "<input type='hidden' name='table' value='".$this->getVar("table")."'>";
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#listForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", $this->request->getController(), "Delete", array("table" => $this->getVar("table"), $ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
</form>
</div>


<?php
	if($ps_table == "ms_specimens"){
?>
		<script type='text/javascript'>
<?php
			foreach(array("body_mass_bibref_id", "locality_absolute_age_bibref_id", "locality_relative_age_bibref_id") as $vs_field){
?>
				jQuery(document).ready(function() {
					jQuery('#ms_<?php print $vs_field; ?>_lookup').autocomplete(
						{ 
							source: '<?php print caNavUrl($this->request, 'lookup', 'Bibliography', 'Get', array("max" => 500)); ?>', 
							minLength: 3, delay: 800, html: true,
							select: function(event, ui) {
								var bib_id = parseInt(ui.item.id);
								if (bib_id < 1) {
									// nothing found...
								} else {
									// found an id
									jQuery('#<?php print $vs_field; ?>').val(bib_id);
								}
							}
						}
					).click(function() { this.select(); });
				});
<?php
			}
?>
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
			});
		</script>
<?php
	}
?>