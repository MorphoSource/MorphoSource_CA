<?php
	require_once(__CA_MODELS_DIR__."/ms_scanners.php");
	require_once(__CA_MODELS_DIR__."/ms_scanner_modes.php");
	
	$t_item = $this->getVar("item");
	$t_scanner = new ms_scanners();
	$t_scanner_modes = new ms_scanner_modes();
	
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	
	# --- formatting variables
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("address1", "address2", "city", "stateprov", "postalcode", "country", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("address2", "stateprov", "country", "last_modified_on");
	
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Facility"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'itemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "MyProjects", $this->request->getController(), "listItems")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
<?php
}else{
?>
	<H2 class="ltBlueBottomRule" style="margin-bottom:10px;">
		<?php print _t("Facility Information"); ?>
	</H2>
	<div class="ltBlueBottomRule" style="margin-bottom:15px;">
<?php
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
			default:
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
if (!$this->request->isAjax()) {
	// Controls to add scanners
?>
	<H2 class="ltBlueBottomRule" style="margin-bottom:10px;">
		<?php print _t("Scanners at this facility"); ?>
	</H2>
	<div style="margin: 0 0 15px 0;">
	
	<div id="msFacilityScannerList">
		<textarea class='caItemTemplate' style='display: none;'>
			<div id="msFacilityScannerListItem_{n}" class="labelInfo">	
				<span class="formLabelError">{error}</span>

				<div style="float: right;">
					<a href="#" class="caDeleteItemButton"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__); ?></a>
				</div>
				
				<table>
					<tr>
						<td>
							<?php print str_replace("textarea", "textentry", $t_scanner->htmlFormElement('name', "<div class='formLabel'>^LABEL<br/>^ELEMENT</div>", array('name' => 'scanner_name_{n}', 'value' => '{name}'))); ?>		
						</td><td>
							<?php print str_replace("textarea", "textentry", $t_scanner->htmlFormElement('description', "<div class='formLabel'>^LABEL<br/>^ELEMENT</div>", array('name' => 'scanner_description_{n}', 'value' => '{description}'))); ?>		
						</td>
					</tr>
					<tr>
						<td>
							<?php
								$a = '{mode_1}'; 
								print_r($a); 
							?>
							<?php print str_replace("textarea", "textentry", $t_scanner_modes->htmlFormElement('modality', "<div class='formLabel'>^LABEL<br/>^ELEMENT</div>", array('name' => 'scanner_modality_{n}', 'value' => '7'))); ?>
						</td>
						<td>
							<?php print_r('{mode_2}'); ?>
							<?php print str_replace("textarea", "textentry", $t_scanner_modes->htmlFormElement('modality', "<div class='formLabel'>^LABEL<br/>^ELEMENT</div>", array('name' => 'scanner_modality_{n}', 'value' => '{mode_2}'))); ?>
						</td>
						<td>
							<?php print_r('{mode_3}'); ?>
							<?php print str_replace("textarea", "textentry", $t_scanner_modes->htmlFormElement('modality', "<div class='formLabel'>^LABEL<br/>^ELEMENT</div>", array('name' => 'scanner_modality_{n}', 'value' => '{mode_3}'))); ?>
						</td>
					</tr>
				</table>
			</div>
		</textarea>
	
		<div class="bundleContainer">
			<div class="caItemList">
		
			</div>

			<div class='labelInfo caAddItemButton'><a href='#'><?php print caNavIcon($this->request, __CA_NAV_BUTTON_ADD__); ?> <?php print _t('Add scanner'); ?></a></div>

		</div>

	</div>

	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
</form>
</div>

<script type="text/javascript">
	caUI.initBundle('#msFacilityScannerList', {
		fieldNamePrefix: 'msFacilityScannerList_',
		templateValues: ['name', 'description', 'scanner_id', 'mode_array'],
		initialValues: <?php print (is_array($va_scanner_list = $this->getVar('scannerList'))) ? json_encode($va_scanner_list) : "null"; ?>,
		forceNewValues: {},
		errors: <?php print json_encode($this->getVar('scannerErrors')); ?>,
		itemID: 'msFacilityScannerListItem_',
		templateClassName: 'caItemTemplate',
		itemListClassName: 'caItemList',
		addButtonClassName: 'caAddItemButton',
		deleteButtonClassName: 'caDeleteItemButton',
		minRepeats: 0,
		maxRepeats: 9999,
		showEmptyFormsOnLoad: 1,
		readonly: 0,
		disableUnsavedChangesWarning: true
	});
</script>

<?php
}else{
?>
	</div><!-- end ltBlueBottomRule -->
	<!-- form is loaded via AJAX as part of media form - check name of facility is entered to avoid errors when loaded in the media info form -->
<?php
	if($this->getVar("media_id")){
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#mediaForm').submit(function(){
				if($('#name').val() == ''){
					alert("Please enter the name of the facility");
					return false;
				}else{
					return true;
				}
			});
		});
	</script>
<?php
	}
}
?>