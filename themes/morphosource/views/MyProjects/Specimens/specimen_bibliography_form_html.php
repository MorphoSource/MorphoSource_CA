<?php
	$pn_specimen_id = $this->getVar("item_id");
	$t_specimen = $this->getVar("item");
	$vs_message = $this->getVar("message");
	$vs_new_message = $this->getVar("new_message");
	$va_bib_citations = $this->getVar("bib_citations");
?>
<div id="formArea" class='specimenBibFormContainer'><div class="ltBlueTopRule"><br/>
<?php
	if($vs_message || $vs_new_message){
		print "<div class='formErrors'>".$vs_message.$vs_new_message."</div>";
	}
	print caFormTag($this->request, 'linkBibliography', 'specimenBibForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
	print "<div class='formLabel'>";
	print "Look up a bibliographic citation";
	if(!$pn_specimen_id){
		print " to link to all specimen created by your project";
	}
	print ":<br/>".caHTMLTextInput("bibliography_lookup", array("id" => 'msBibliograpnyID', 'class' => 'lookupBg'), array('width' => "200px", 'height' => 1));
	#print "&nbsp;&nbsp;&nbsp;Page(s): <input type='text' style='width:30px;' value='' name='page'>";
	print "&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#specimenBibForm\").submit(); return false;'>"._t("Save")."</a></div>";
	print "<input type='hidden' value='' name='bibliography_id' id='bibliography_id'>";
	print "<input type='hidden' value='".$pn_specimen_id."' name='specimen_id'>"
?>
</form></div><!-- end ltBlueTopRule -->
<?php
	# --- if there are exisitng linked bib citations, display them here
	if(sizeof($va_bib_citations)){
		print "<div class='ltBlueTopRule' style='max-height:300px; overflow:auto;'>";
		foreach($va_bib_citations as $vn_link_id => $va_bib_info){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onClick='jQuery(\".specimenBibFormContainer\").load(\"".caNavUrl($this->request, "MyProjects", "Specimens", "removeBibliography", array("specimen_id" => $pn_specimen_id, "link_id" => $va_bib_info["link_id"]))."\"); return false;'>Remove</a></div>";			
			print caNavLink($this->request, $va_bib_info["citation"], "", "MyProjects", "Bibliography", "form", array('bibref_id' => $va_bib_info["bibref_id"]));
			#if($va_bib_info["page"]){
			#	print "<br/>Page(s): ".$va_bib_info["page"];
			#}
			print "</div>";
		}
		print "</div>";
	}	
?>
</div><!-- end formArea -->

<script type='text/javascript'>
	jQuery('#msBibliograpnyID').autocomplete(
		{
<?php
		if($pn_specimen_id){
			print "source: '".caNavUrl($this->request, 'lookup', 'Bibliography', 'Get', array("max" => 500, "quickadd" => true))."',"; 
		}else{
			print "source: '".caNavUrl($this->request, 'lookup', 'Bibliography', 'Get', array("max" => 500, "quickadd" => false))."',"; 
		}
?>
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var bibliography_id = parseInt(ui.item.id);
				if (bibliography_id < 1) {
					// nothing found...
					//alert("Create new bibliography since returned id was " + bibliography_id);
					jQuery(".specimenBibFormContainer").load("<?php print caNavUrl($this->request, 'MyProjects', 'Bibliography', 'form', array('specimen_id' => $pn_specimen_id)); ?>");
				} else {
					// found an id
					jQuery('#bibliography_id').val(bibliography_id);
					//alert("bibliography id set to: " + jQuery('#bibliography_id').val());
				}
			}
		}
	).click(function() { this.select(); });

	jQuery(document).ready(function() {
		jQuery('#specimenBibForm').submit(function(e){		
			jQuery('#specimenBibliographyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Specimens', 'linkBibliography'); ?>',
				jQuery('#specimenBibForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>