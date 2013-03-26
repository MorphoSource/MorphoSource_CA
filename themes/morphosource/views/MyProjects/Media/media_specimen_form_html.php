<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$vs_message = $this->getVar("message");
	$vs_new_message = $this->getVar("new_message");
	$vs_taxonomy_message = $this->getVar("taxonomy_message");
	$vs_taxonomy_new_message = $this->getVar("taxonomy_new_message");
	$vs_specimen_name = $this->getVar("specimen_name");
	$va_specimen_taxonomy = $this->getVar("specimen_taxonomy");
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
	print "<input type='hidden' value='".$pn_media_id."' name='media_id'>";
?>
</form>
<?php
	print "<div class='mediaSpecimenTaxonomyForm'>";
	if($vs_taxonomy_message || $vs_taxonomy_new_message){
		print "<div class='formErrors'>".$vs_taxonomy_message.$vs_taxonomy_new_message."</div>";
	}
	if(is_array($va_specimen_taxonomy) && sizeof($va_specimen_taxonomy)){
		print "<div class='formLabel'>Specimen taxonomy for ".$vs_specimen_name.":<br/><span style='font-weight:normal;'>".join($va_specimen_taxonomy, "<br/>")."</span></div>";
	}elseif($t_media->get("specimen_id")){
		if($vs_taxon_message || $vs_new_taxon_message){
			print "<div class='formErrors'>".$vs_taxon_message.$vs_new_taxon_message."</div>";
		}
		print caFormTag($this->request, 'linkSpecimenTaxon', 'mediaSpecimenTaxonomyForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
		print "<div class='formLabel'>";
		print "Select a taxonomic name for this specimen:<br/>".caHTMLTextInput("specimen_taxonomy_lookup", array("id" => 'msSpecimenTaxonomyID', 'class' => 'lookupBg'), array('width' => "200px", 'height' => 1));
		print "&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaSpecimenTaxonomyForm\").submit(); return false;'>"._t("Save")."</a></div>";
		print "<input type='hidden' value='' name='alt_id' id='alt_id'>";
		print "<input type='hidden' value='".$t_media->get("specimen_id")."' name='specimen_id' id='specimen_id'>";
		print "<input type='hidden' value='".$pn_media_id."' name='media_id'>";
?>
	</div>
</form>
<?php		
	}
?>
</div><!-- end ltBlueTopRule --></div><!-- end formArea -->

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
					jQuery("#mediaSpecimenInfo").load("<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'form', array('media_id' => $pn_media_id)); ?>");
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
<?php
	if($t_media->get("specimen_id")){
?>
	jQuery('#msSpecimenTaxonomyID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'TaxonomicName', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var alt_id = parseInt(ui.item.id);
				if (alt_id < 1) {
					// nothing found...
					//alert("Create new taxon since returned id was " + alt_id);
					jQuery("#mediaSpecimenInfo").load("<?php print caNavUrl($this->request, 'MyProjects', 'Taxonomy', 'form', array('specimen_id' => $t_media->get("specimen_id"), 'media_id' => $pn_media_id)); ?>");
				} else {
					// found an id
					//alert("found alt id: " + aslt_id);
					jQuery('#alt_id').val(alt_id);
					//alert("alt id set to: " + jQuery('#alt_id').val());
				}
			}
		}
	).click(function() { this.select(); });

	jQuery(document).ready(function() {
		jQuery('#mediaSpecimenTaxonomyForm').submit(function(e){		
			jQuery('#mediaSpecimenInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'linkSpecimenTaxon'); ?>',
				jQuery('#mediaSpecimenTaxonomyForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
<?php
	}
?>
</script>