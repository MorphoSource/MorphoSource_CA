<?php
	$t_item = $this->getVar("item");
	$t_media = new ms_media();
	
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	# --- media means this is a quick add for the media form being loaded via ajax
	$pn_media_id = $this->getVar("media_id");
	
	# --- formatting variables
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("institution_code", "collection_code", "catalog_number", "collector", "collected_on", "element", "side", "type", "sex", "relative_age", "absolute_age", "body_mass", "body_mass_comments", "locality_coordinates", "locality_northing_coordinate", "locality_easting_coordinate", "locality_datum_zone", "locality_absolute_age", "locality_relative_age", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("catalog_number", "collected_on", "sex", "absolute_age", "body_mass_comments", "locality_datum_zone", "locality_easting_coordinate", "locality_relative_age", "last_modified_on");
	$vs_delete_msg = "";
	
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
<?php
	if($t_item->get("specimen_id")){
		print "<div style='float:right; padding-top:20px;'>".caNavLink($this->request, _t("View public page"), "", "Detail", "SpecimenDetail", "Show", array("specimen_id" => $t_item->get("specimen_id")))." or <a href='#' onClick='copyToClipboard(\"#specimenGroupLink\"); return false;' class='button buttonSmall' title='click to copy link to clipboard'>Copy <i class='fa fa-external-link'></i></a>";
		print "<div style='display:none;' id='specimenGroupLink'>".caNavUrl($this->request, "Detail", "SpecimenDetail", "Show", array("specimen_id" => $t_item->get("specimen_id")))."</div>";
		print "</div>";
	}
?>
	<H1>
		<?php print _t("Specimen Information"); ?>
	</H1>
<?php
	// if(!$t_item->get("specimen_id")){
// 		# --- display a look up for people to find an existing record before entering their own
// 		print "<div class='formLabel' id='specimenLookUpContainer'>";
// 		print "<span style='font-weight:normal;'>Before creating a new specimen, please check if a specimen record already exists.</span><br/><b>Enter any <i>individual</i> part of the catalog number, either the institution code prefix, collection code modifier or the alphanumeric suffix:</b><br/>";
// 		print caHTMLTextInput("specimenLookUp", array("id" => 'specimenLookUp', 'class' => 'lookupBg', 'value' => ''), array('width' => "200px", 'height' => 1));
// 		print "<span id='addOrLinkButtons' style='display:none;'><a href='#' class='button buttonSmall' id='addMedia'>ADD MEDIA</a>&nbsp;&nbsp;or&nbsp;&nbsp;<a href='#' class='button buttonSmall' id='linkSpecimen'>LINK SPECIMEN TO PROJECT</a></span><!-- end addOrLinkButtons -->";
// 	
// 		print "</div>";
// 	}
}
?>
	<!--<div id='formArea' <?php print ((!$this->request->isAjax()) && (!$t_item->get("specimen_id"))) ? "style='display:none;'" : ""; ?>>-->
	<div id='formArea'>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
		print "<div style='float:right;'>Back to: ";
		$vs_specimens_group_by = $this->request->session->getVar('specimens_group_by');
		# --- get the taxon_id from this specimen so can pass to the species/genus list
		$o_db = new Db();
		$q_taxonomy = $o_db->query("SELECT t.taxon_id
									FROM ms_specimens s
									RIGHT JOIN ms_specimens_x_taxonomy AS sxt ON sxt.specimen_id = s.specimen_id
									RIGHT JOIN ms_taxonomy_names AS t ON sxt.alt_id = t.alt_id
									WHERE s.specimen_id = ?
									", $t_item->get("specimen_id"));
		if($q_taxonomy->numRows()){
			$q_taxonomy->nextRow();
			if(in_array($vs_specimens_group_by, array("genus", "species"))){
				print caNavLink($this->request, _t("%1 Specimens", $vs_specimens_group_by), "button buttonSmall", "MyProjects", "Dashboard", "specimenByTaxonomy", array("taxon_id" => $q_taxonomy->get("taxon_id")))." ";
			}
		}
		
		print caNavLink($this->request, _t("Dashboard"), "button buttonSmall", "MyProjects", "Dashboard", "dashboard");
		print "</div>";
}else{
	if($pn_media_id){
		print "<div style='float:right;'><a href='#' class='button buttonSmall' onclick='jQuery(\"#mediaSpecimenInfo\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'specimenLookup', array('media_id' => $pn_media_id))."\");'>"._t("Cancel")."</a></div>";
	}
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#specimenItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get($ps_primary_key)){
			if(!$t_item->getSpecimenMediaIDs()){
				print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
			}else{
				$vs_delete_msg = "<b>* You cannot delete specimen with media</b>";
			}
		}
}
?>
	</div><!-- end formButtons -->

<?php
if (!$this->request->isAjax() && $t_item->get("specimen_id")) {
?>
		<div id="rightCol">
			<div class="tealRule" style="margin-top:20px;"><!-- empty --></div>
			<H2>Specimen Taxonomy</H2>
			<div id="specimenTaxonomyInfo">
				<!-- load Specimen taxonomy form/info here -->
			</div><!-- end specimenTaxonomyInfo -->
			<div class="tealRule" style="margin-top:40px;"><!-- empty --></div>
			<H2>Specimen Bibliography</H2>
			<div id="specimenBibliographyInfo">
				<!-- load Specimen bibliography form/info here -->
			</div><!-- end specimenBibliographyInfo -->
			
<?php
	// Media list
?>
			<div class="tealRule" style="margin-top:40px;"><!-- empty --></div>
<?php
	print caNavLink($this->request, _t("Add media"), "button buttonSmall", "MyProjects", 'Media', "form", array('specimen_id' => $t_item->get('specimen_id')), array('style' => 'float: right;'));
?>
			<H2>Specimen Media</H2>
			<div id="specimenMediaList">
<?php
			$va_media_list = $t_item->getSpecimenMedia(null, array('versions' => array('preview190')));
			if (is_array($va_media_list) && sizeof($va_media_list)) {
				foreach($va_media_list as $vn_media_id => $va_media_info) {
					$t_media = new ms_media($vn_media_id);
					$vs_side = $t_media->getChoiceListValue("side", $va_media_info['side']);
			
					// print '<div class="specimenMediaListContainer">';

					$vb_access = false;
					$vb_read_only_access = false;
					$vs_media_html = '<div class="specimenMediaListContainer" style="height: 280px;">';
					if (!($vs_media_tag = $va_media_info['media']['preview190'])) {
						$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
					}
					$t_project = new ms_projects($va_media_info['project_id']);
					$vs_project_name = 
						((strlen($t_project->get('name')) > 25) ? 
							substr($t_project->get('name'), 0, 25).'...' : 
							$t_project->get('name'));
					if(($va_media_info['project_id'] == $this->getVar("project_id")) || ($t_project->isMember($this->request->user->get("user_id")))){
						# --- media owned by same project as specimen or user has edit access to media
						$vb_access = true;
						$vs_media_html .= "<div class='specimenMediaListSlide' style='height: 190px;'>".caNavLink($this->request, $vs_media_tag, "", "MyProjects", "Media", "mediaInfo", array("media_id" => $vn_media_id))."</div>";
						$vs_media_html .= "<span class='mediaID'>".caNavLink($this->request, "M".$vn_media_id, "", "MyProjects", "Media", "mediaInfo", array("media_id" => $vn_media_id))."</span>, ";
						$vs_project_tag = caNavLink($this->request, $vs_project_name, "", "MyProjects", "Dashboard", "dashboard", array("select_project_id" => $t_project->get("project_id")));
					}else{
						if($t_media->userHasReadOnlyAccessToMedia($this->request->user->get("user_id"))){
							$vb_read_only_access = true;
						}
						if(($t_media->get("published") > 0) || $vb_read_only_access){
							# --- media owned by another project, but is published or user has read only access to media - so link to the public detail page
							$vb_access = true;
							$vs_media_html .= "<div class='specimenMediaListSlide'>".caNavLink($this->request, $vs_media_tag, "", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id))."</div>";
							$vs_media_html .= "<span class='mediaID'>".caNavLink($this->request, "M".$vn_media_id, "", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id))."</span>, ";
							$vs_project_tag = caNavLink($this->request, $vs_project_name, "", "Detail", "ProjectDetail", "Show", array("project_id" => $t_project->get("project_id")));
						}
					}
					
					if($vb_access){
						print $vs_media_html;
						print $va_media_info["numFiles"]." file".(($va_media_info["numFiles"] == 1) ? "" : "s");;

						# Media description
						$vs_title = ((strlen($va_media_info['title']) > 50) ? 
							substr($va_media_info['title'], 0, 50).'...' : 
							$va_media_info['title'] );
						$vs_element = ((strlen($va_media_info['element']) > 20) ? 
							substr($va_media_info['element'], 0, 20).'...' : 
							$va_media_info['element'] );
						print "<br/>{$vs_title}".(($vs_side && (strtolower($vs_side) != 'unknown')) ? " ({$vs_side})" : "").(($vs_element) ? " ({$vs_element})" : "");
						print "<br><br>".$vs_project_tag;
						print "<br>".$t_media->formatPublishedText();
						if($vb_read_only_access){
							print "<br><br><b>READ ONLY ACCESS</b>";
						}
						print "</div><!-- end specimenMediaListContainer -->\n";
					}	
				}
			} else {
				print "<H2>"._t("This specimen has no media.  Use the \"ADD MEDIA\" button to add media files for this specimen.")."</H2>";
			}?>
			</div><!-- end specimenMediaListContainer -->
		
		
<?php
			$t_projects = new ms_projects();
			$t_projects->load($t_item->get("project_id"));
			$o_db = new Db();
			$q_projects = $o_db->query("SELECT project_id, name from ms_projects WHERE project_id != ? ORDER BY name", $t_item->get("project_id"));
			if($q_projects->numRows()){
?>
				<div class="tealRule"><!-- empty --></div>
				<H2>Move Specimen</H2>
				<div id="specimenMove">
					This specimen is owned by <i><b><?php print $t_projects->get("name"); ?></b></i>.<br/>Move specimen to 
					<select id="move_project_id" name="move_project_id" style="width:250px;">
					<option></option>
<?php
				while($q_projects->nextRow()){
					print "<option value='".$q_projects->get("project_id")."'>".$q_projects->get("name").", P".$q_projects->get("project_id")."</option>";
				}

				print "</select>&nbsp;&nbsp;";
				print "<a href='#' id='moveSpecimenButton' class='button buttonSmall'>Move</a>";
?>
						
				</div><!-- end specimenMove -->
				<div style="font-size:10px; font-style:italic;">If you transfer your specimen to a project you are not a member of, you will no longer be able to edit the specimen.</div>
				<br/><br/>
<?php
			}	
?>	
		</div>
<?php
}
?>
	<div id="leftCol">
<?php
	print caFormTag($this->request, 'save', 'specimenItemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
	$t_bib = new ms_bibliography();
	$t_institution = new ms_institutions();
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
				if($t_item->get($vs_f)){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			case "body_mass_bibref_id":
			case "locality_absolute_age_bibref_id":
			case "locality_relative_age_bibref_id":
				$vs_name = "";
				if($t_item->get($vs_f)){
					$t_bib->load($t_item->get($vs_f));
					$vs_name = strip_tags($t_bib->getCitationText());
				}
				print "<div class='formLabel'>";
				print $vs_field_info["LABEL"].":<br/>".caHTMLTextInput($vs_f."_lookup", array("id" => 'ms_'.$vs_f.'_lookup', 'class' => 'lookupBg', 'value' => $vs_name), array('width' => '354px', 'height' => 1, 'paadding-right' => '15px'));
				print "</div>";
				print "<input type='hidden' id='".$vs_f."' name='".$vs_f."' value='".$t_item->get($vs_f)."'>";
			break;
			# -----------------------------------------------
			case "institution_id":
				$vs_name = "";
				if($t_item->get($vs_f)){
					$t_institution->load($t_item->get($vs_f));
					$vs_name = strip_tags($t_institution->get("name"));
				}
				print "<div id='specimenInstitutionFormContainer'><div class='formLabel'>";
				print $vs_field_info["LABEL"].":<br/>".caHTMLTextInput($vs_f."_lookup", array("id" => 'ms_institution_lookup', 'class' => 'lookupBg', 'value' => $vs_name), array('width' => '354px', 'height' => 1, 'paadding-right' => '15px'));
				print "</div>";
				print "<input type='hidden' id='".$vs_f."' name='".$vs_f."' value='".$t_item->get($vs_f)."'></div>";
			break;
			# -----------------------------------------------
			case "occurrence_id":
				# --- don't allow editing of occ id when there is an idigbio uuid
				if($t_item->get("uuid")){
					print $t_item->htmlFormElement("occurrence_id","<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL").": <span style='font-weight:normal;'>".$t_item->get("occurrence_id")."</span></div>";
					print "<input type='hidden' name='occurrence_id' value='".$t_item->get("occurrence_id")."'>";
				}else{
					print $t_item->htmlFormElement("occurrence_id","<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			#case "catalog_number":
			#	print "<div class='formLabelFloat'>".$t_item->getDisplayLabel("ms_specimens.catalog_number").":<br/>".caHTMLTextInput("catalog_number", array("id" => 'catalog_number', 'class' => 'lookupBg', 'value' => $t_item->get("catalog_number")), array('width' => "100px", 'height' => 1))."</div>";
	#print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			#break;
			# -----------------------------------------------
			default:
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
		
		if($vs_f == 'catalog_number') {
			print "<div class='formLabel'>Specimen identifier: <span id='specimen_identifier_preview'></span></div>";
		}
		if($vs_f == 'uuid' && $t_item->get("uuid")) {
			print "<div class='formLabel'><a href='https://www.idigbio.org/portal/records/".$t_item->get("uuid")."' target='_blank' class='button buttonSmall'>View on iDigBio</a></div>";
		}
		
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		} 
	}
	if($pn_media_id){
		print "<input type='hidden' value='".$pn_media_id."' name='media_id'>";
	}
?>
	</div>
	<div style="clear:both;"><!-- empty --></div>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#specimenItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if(!$vs_delete_msg && $t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
}
?>
	</div><!-- end formButtons -->
<?php
	print $vs_delete_msg;
?>
</form>
</div>
<script type='text/javascript'>	
	jQuery(document).ready(function() {
		jQuery('#institution_code, #collection_code, #catalog_number').on('keyup', function(e) {
			_updateSpecimenIdentifierPrefix();
		});
		
		function _updateSpecimenIdentifierPrefix() {
			var v = [] 
			if (t = jQuery("#institution_code").val()) { v.push(t); }
			if (t = jQuery("#collection_code").val()) { v.push(t); }
			if (t = jQuery("#catalog_number").val()) { v.push(t); }
			
			jQuery("#specimen_identifier_preview").html(v.join(':'));
		}
		_updateSpecimenIdentifierPrefix();
		
		jQuery('#specimenItemForm').submit(function(e){		
			if(!jQuery('#institution_code').val() || !jQuery('#catalog_number').val() || (!jQuery('#institution_id').val() && !jQuery('#name').val())){
				alert("Please enter the specimen institution code, catalog number and institution");
				e.preventDefault();
				return false;
			}else{
<?php
	if($pn_media_id){
?>
			jQuery('#mediaSpecimenInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'save'); ?>',
				jQuery('#specimenItemForm').serialize()
			);
			e.preventDefault();
			return false;
<?php
	}else{
?>
				return true;
<?php
	}
?>				
			}
		});
	});
	
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
		
	jQuery('#specimenLookUp').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'Specimen', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var specimen_id = parseInt(ui.item.id);
				if (specimen_id < 1) {
					// nothing found...
					//alert("Create new specimen since returned id was " + specimen_id);
					//jQuery("#mediaSpecimenInfo").load("<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'form', array('media_id' => $pn_media_id)); ?>");
					jQuery('#formArea').show();
					jQuery('#catalog_number').val(jQuery('#specimenLookUp').val());
					jQuery('#specimenLookUpContainer').hide();
				} else {
					// found an id
					//alert("found specimen id: " + specimen_id);
					//window.location.href = "<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'form'); ?>/specimen_id/" + specimen_id;
					//jQuery('#specimen_id').val(specimen_id);
					//alert("specimen id set to: " + jQuery('#specimen_id').val());
					jQuery('#addOrLinkButtons').show();
					$('#addMedia').attr('href', '<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'form'); ?>/specimen_id/' + specimen_id);
					$('#linkSpecimen').attr('href', '<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'linkSpecimen'); ?>/specimen_id/' + specimen_id);
				}
			}
		}
	).click(function() { this.select(); });
<?php
	if($t_item->get("specimen_id")){
?>
	jQuery(document).ready(function() {			
		jQuery('#specimenTaxonomyInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'specimenTaxonomyLookup', array('specimen_id' => $t_item->get("specimen_id"))); ?>'
		);
		return false;
	});
	jQuery(document).ready(function() {			
		jQuery('#specimenBibliographyInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'bibliographyLookup', array('specimen_id' => $t_item->get("specimen_id"))); ?>'
		);
		return false;
	});
<?php
	}
?>

	jQuery('#moveSpecimenButton').click(function () {
		var url = "<?php
			print caNavUrl($this->request, 'MyProjects', 'Specimens', 'confirmMove', 
				array(
					"specimen_id" => $t_item->getPrimaryKey(), 
					"proj_from" => (isset($t_project) ? $t_projects->get("project_id") : null ), 
					"proj_to" => "placeholdertext"));
			?>";
		var proj_to = jQuery('#move_project_id').val();
		if (proj_to) {
			url = url.replace("placeholdertext", proj_to);
			msMediaPanel.showPanel(url);
		}
		return false;
	});

</script>