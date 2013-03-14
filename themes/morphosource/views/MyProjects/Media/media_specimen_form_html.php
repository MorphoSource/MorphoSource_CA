<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$vs_message = $this->getVar("message");
	$vs_new_message = $this->getVar("new_message");
	$vs_specimen_name = $this->getVar("specimen_name");
?>
<div id="formArea" class="mediaSpecimensForm"><div class="ltBlueTopRule"><br/>
<?php
	if($vs_message || $vs_new_message){
		print "<div class='formErrors'>".$vs_message.$vs_new_message."</div>";
	}
	print caFormTag($this->request, 'linkSpecimen', 'mediaSpecimenForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
	print "<div class='formLabel'>";
	print $t_media->getDisplayLabel("ms_media.specimen_id").":<br/>".caHTMLTextInput("specimen_lookup", array("id" => 'msSpecimenID', 'class' => 'lookupBg', 'value' => $vs_specimen_name), array('width' => "200px", 'height' => 1));
	print "&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaSpecimenForm\").submit(); return false;'>"._t("Save")."</a></div>";
	print "<input type='hidden' value='".$t_media->get("specimen_id")."' name='specimen_id' id='specimen_id'>";
	print "<input type='hidden' value='".$pn_media_id."' name='media_id'>"
?>
</form></div><!-- end ltBlueTopRule --></div><!-- end formArea -->

<script type='text/javascript'>
	jQuery('#msSpecimenID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'Specimen', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var specimen_id = parseInt(ui.item.id);
				if (specimen_id < 1) {
					// nothing found...
					//alert("Create new specimen since returned id was " + specimen_id);
					jQuery(".mediaSpecimensForm").load("<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'form', array('media_id' => $pn_media_id)); ?>");
				} else {
					// found an id
					//alert("found specimen id: " + specimen_id);
					jQuery('#specimen_id').val(specimen_id);
					//alert("specimen id set to: " + jQuery('#specimen_id').val());
				}
			}
		}
	).click(function() { this.select(); });

	jQuery(document).ready(function() {
		jQuery('#mediaSpecimenForm').submit(function(e){		
			jQuery('#mediaSpecimenInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'linkSpecimen'); ?>',
				jQuery('#mediaSpecimenForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>