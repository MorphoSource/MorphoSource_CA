<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/institution_list_html.php : 
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
	$pn_browse_institution_id = $this->getVar("browse_institution_id");
	$q_institutions = $this->getVar("institutions");
?>
	<div id="browseList">
		<div class="tealRule"><!-- empty --></div>
		<H2><?php print _t("Institutions"); ?></H2>
		<div class='' style='padding-left: 10px;'>
			Filter: <input type='text' name='filter' value='' onkeyup='filterInstBrowse(this.value); return false;' size='20' style='border:1px solid #828282; margin-right: 10px; margin-left: 5px;'/>
			<span id='numberInstFiltered'></span>
			<br/><br/>
		</div><!-- end class list-filter -->
<?php
	if($q_institutions->numRows() > 0){
		print "<div class='browseListScrollContainer' style='padding-right: 10px; border-top: 1px solid #578686; margin-top: 5px; padding-top: 20px;'>";
		while($q_institutions->nextRow()){
			print "<div class='browseItem'><a href='#' onClick='highlightLink(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('institution_id' => $q_institutions->get("institution_id")))."\"); return false;' class='blueText".(($q_institutions->get("institution_id") == $pn_browse_institution_id) ? " browseItemSelected" : "")."'>".$q_institutions->get("name")."</a></div>";
			#print "<div class='browseItem'><a href='#' onclick='jQuery(\"#specimenResults\").smoothDivScroll(\"getAjaxContent\", \"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('institution_id' => $q_institutions->get("institution_id")))."\",\"replace\"); return false;'>".$q_institutions->get("name")."</a></div>";
					
		}
		print "</div>";
	}else{
		print "<p><b>"._t("There are no institutions available")."</b></p>";
	}
?>
	</div>
	<div id="specimenResults"><!-- load the specimen results here --></div>
<script type="text/javascript">
<?php
	if($pn_browse_institution_id){
?>
	jQuery(document).ready(function() {			
		jQuery('#specimenResults').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'specimenResults', array('institution_id' => $pn_browse_institution_id)); ?>'
		);
		return false;
	});
<?php	
	}
?>
	filterInstBrowse = function (searchText) {
		if (!searchText) {
			jQuery('#browseList').find('.browseItem').show();
			jQuery('#numberInstFiltered').text('');
			return;
		}
		jQuery('#browseList').find('.browseItem').hide();
		var filtered = jQuery('#browseList')
			.find('.browseItem:iContains('+searchText+')');
		filtered.show();
		jQuery('#numberInstFiltered').text(filtered.length + ' institutions found');
	}
</script>