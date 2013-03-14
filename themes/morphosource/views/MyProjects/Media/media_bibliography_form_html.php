<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$vs_message = $this->getVar("message");
	$vs_new_message = $this->getVar("new_message");
	$va_bib_citations = $this->getVar("bib_citations");
?>
<div id="formArea" class='mediaBibFormContainer'><div class="ltBlueTopRule"><br/>
<?php
	if($vs_message || $vs_new_message){
		print "<div class='formErrors'>".$vs_message.$vs_new_message."</div>";
	}
	print caFormTag($this->request, 'linkBibliography', 'mediaBibForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
	print "<div class='formLabel'>";
	print "Look up a bibliographic citation".":<br/>".caHTMLTextInput("bibliography_lookup", array("id" => 'msBibliograpnyID', 'class' => 'lookupBg'), array('width' => "200px", 'height' => 1));
	print "&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaBibForm\").submit(); return false;'>"._t("Save")."</a></div>";
	print "<input type='hidden' value='' name='bibliography_id' id='bibliography_id'>";
	print "<input type='hidden' value='".$pn_media_id."' name='media_id'>"
?>
</form></div><!-- end ltBlueTopRule -->
<?php
	# --- if there are exisitng linked bib citations, display them here
	if(sizeof($va_bib_citations)){
		print "<div class='ltBlueTopRule'>";
		foreach($va_bib_citations as $vn_link_id => $vs_citaiton){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onClick='jQuery(\".mediaBibFormContainer\").load(\"".caNavUrl($this->request, "MyProjects", "Media", "removeBibliography", array("media_id" => $pn_media_id, "link_id" => $vn_link_id))."\"); return false;'>Remove</a></div>";			
			print $vs_citaiton."</div>";
		}
		print "</div>";
	}	
?>
</div><!-- end formArea -->

<script type='text/javascript'>
	jQuery('#msBibliograpnyID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'Bibliography', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var bibliography_id = parseInt(ui.item.id);
				if (bibliography_id < 1) {
					// nothing found...
					//alert("Create new bibliography since returned id was " + bibliography_id);
					jQuery(".mediaBibFormContainer").load("<?php print caNavUrl($this->request, 'MyProjects', 'Bibliography', 'form', array('media_id' => $pn_media_id)); ?>");
				} else {
					// found an id
					jQuery('#bibliography_id').val(bibliography_id);
					//alert("bibliography id set to: " + jQuery('#bibliography_id').val());
				}
			}
		}
	).click(function() { this.select(); });

	jQuery(document).ready(function() {
		jQuery('#mediaBibForm').submit(function(e){		
			jQuery('#mediaBibliographyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'linkBibliography'); ?>',
				jQuery('#mediaBibForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>