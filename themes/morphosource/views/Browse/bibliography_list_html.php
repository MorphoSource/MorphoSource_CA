<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/bibliography_list_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	$pn_browse_bibref_id = $this->getVar("browse_bibref_id");
	$q_bibliography = $this->getVar("bibliography");
	$t_bib = new ms_bibliography();
?>
	<div id="browseList">
		<div class="tealRule"><!-- empty --></div>
		<H2><?php print _t("Bibliographic Citations"); ?></H2>
		<div class='' style='padding-left: 10px;'>
			Filter: <input type='text' name='filter' value='' onkeyup='filterBiblioBrowse(this.value); return false;' size='20' style='border:1px solid #828282; margin-right: 10px; margin-left: 5px;'/>
			<span id='numberBiblioFiltered'></span>
			<br/><br/>
		</div><!-- end class list-filter -->
<?php
	if($q_bibliography->numRows() > 0){
		print "<div class='browseListScrollContainer' style='padding-right: 10px; border-top: 1px solid #578686; margin-top: 5px; padding-top: 20px;'>";
		while($q_bibliography->nextRow()){
			$t_bib->load($q_bibliography->get("bibref_id"));
			print "<div class='browseItem indent'><a href='#' onClick='highlightLink(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('bibref_id' => $q_bibliography->get("bibref_id")))."\"); return false;' class='blueText".(($q_bibliography->get("bibref_id") == $pn_browse_bibref_id) ? " browseItemSelected" : "")."'>".$t_bib->getCitationText()."</a></div>";		
		}
		print "</div>";
	}else{
		print "<p><b>"._t("There are no citations available")."</b></p>";
	}
?>
	</div>
	<div id="specimenResults"><!-- load the specimen results here --></div>
<script type="text/javascript">
<?php
	if($pn_browse_bibref_id){
?>
	jQuery(document).ready(function() {			
		jQuery('#specimenResults').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'specimenResults', array('bibref_id' => $pn_browse_bibref_id)); ?>'
		);
		return false;
	});
<?php	
	}
?>
	filterBiblioBrowse = function (searchText) {
		if (!searchText) {
			jQuery('#browseList').find('.browseItem').show();
			jQuery('#numberBiblioFiltered').text('');
			return;
		}
		jQuery('#browseList').find('.browseItem').hide();
		var filtered = jQuery('#browseList')
			.find('.browseItem:iContains('+searchText+')');
		filtered.show();
		jQuery('#numberBiblioFiltered').text(filtered.length + ' citations found');
	}
</script>