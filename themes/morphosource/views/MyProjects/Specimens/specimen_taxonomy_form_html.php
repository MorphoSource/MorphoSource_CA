<?php
	$vs_message = $this->getVar("message");
	$vs_new_message = $this->getVar("new_message");
	$va_specimen_taxonomy = $this->getVar("specimen_taxonomy");
	$vs_specimen_taxonomy = "";
	$vn_taxonomy_id = "";
	if(is_array($va_specimen_taxonomy) && sizeof($va_specimen_taxonomy)){
		foreach($va_specimen_taxonomy as $vn_taxonomy_id => $vs_specimen_taxonomy){
			break;
		}
	}
	$pn_specimen_id = $this->getVar("item_id");
?>
<div id="formArea" class="specimenTaxonomyForm"><div class="ltBlueTopRule"><br/>
<?php
	print "<div class='specimenTaxonomyForm'>";
	if($vs_message || $vs_new_message){
		print "<div class='formErrors'>".$vs_message.$vs_new_message."</div>";
	}
		if($vs_taxon_message || $vs_new_taxon_message){
			print "<div class='formErrors'>".$vs_taxon_message.$vs_new_taxon_message."</div>";
		}
		print caFormTag($this->request, 'linkSpecimenTaxon', 'specimenTaxonomyForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
		print "<div class='formLabel'>";
		print (($vs_specimen_taxonomy) ? "Current " : "Select a")." taxonomic name for this specimen:<br/>".caHTMLTextInput("specimen_taxonomy_lookup", array("id" => 'msSpecimenTaxonomyID', 'class' => 'lookupBg', 'value' => $vs_specimen_taxonomy), array('width' => "200px", 'height' => 1));
		print "&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#specimenTaxonomyForm\").submit(); return false;'>"._t("Save")."</a></div>";
		print "<input type='hidden' value='".$vn_taxonomy_id."' name='alt_id' id='alt_id'>";
		print "<input type='hidden' value='".$pn_specimen_id."' name='specimen_id' id='specimen_id'>";
?>
	</div>
</form>

</div><!-- end ltBlueTopRule --></div><!-- end formArea -->

<script type='text/javascript'>

	jQuery('#msSpecimenTaxonomyID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, "lookup", "TaxonomicName", "Get", array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var alt_id = parseInt(ui.item.id);
				if (alt_id < 1) {
					// nothing found...
					jQuery("#specimenTaxonomyInfo").load("<?php print caNavUrl($this->request, 'MyProjects', 'Taxonomy', 'form', array('specimen_id' => $pn_specimen_id)); ?>");
				} else {
					// found an id
					jQuery('#alt_id').val(alt_id);
				}
			}
		}
	).click(function() { this.select(); });

	jQuery(document).ready(function() {
		jQuery('#specimenTaxonomyForm').submit(function(e){		
			jQuery('#specimenTaxonomyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'linkSpecimenTaxon'); ?>',
				jQuery('#specimenTaxonomyForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>