<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/browse_main_html.php : 
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
	$pn_browse_bibref_id = $this->getVar("browse_bibref_id");
	$pn_browse_project_id = $this->getVar("browse_project_id");
	$pn_browse_taxon_id = $this->getVar("browse_taxon_id");
	$ps_last_browse = $this->getVar("last_browse");
?>
<div id="browse">
	<div class="blueRule"><!-- empty --></div>
	<H1 style="float:left;">
		Start Browsing By:
	</H1>
	<div style="float:left; padding:15px 0px 0px 40px;">
		<a href="#" onClick='jQuery("#browseArea").load("<?php print caNavUrl($this->request, '', 'Browse', 'institutionList'); ?>"); return false;' class="button buttonLarge"><?php print _t("Institution"); ?></a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onClick='jQuery("#browseArea").load("<?php print caNavUrl($this->request, '', 'Browse', 'taxonList'); ?>"); return false;' class="button buttonLarge"><?php print _t("Taxonomy"); ?></a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onClick='jQuery("#browseArea").load("<?php print caNavUrl($this->request, '', 'Browse', 'bibliographyList'); ?>"); return false;' class="button buttonLarge"><?php print _t("Bibliography"); ?></a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onClick='jQuery("#browseArea").load("<?php print caNavUrl($this->request, '', 'Browse', 'projectList'); ?>"); return false;' class="button buttonLarge"><?php print _t("Project"); ?></a>
	</div>
	<div id="browseArea"><!-- load the specimen results here --></div></div>
</div>
<script type="text/javascript">
	function highlightLink(link) {
		$('.browseItem a').removeClass('browseItemSelected');
		$(link).addClass("browseItemSelected");
	}
	function highlightLinkSpecies(link) {
		$('#browseSubList .browseItem a').removeClass('browseItemSelected');
		$(link).addClass("browseItemSelected");
	}
<?php
	switch($ps_last_browse){
		case "institutions":
?>
	jQuery(document).ready(function() {			
		jQuery('#browseArea').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'institutionList', array("institution_id" => $pn_browse_institution_id)); ?>'
		);
		return false;
	});
<?php
		break;
		# ---------------------------------
		default:
		case "taxonomy":
?>
	jQuery(document).ready(function() {			
		jQuery('#browseArea').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'taxonList', array("taxon_id" => $pn_browse_taxon_id)); ?>'
		);
		return false;
	});
<?php	
		break;
		# ---------------------------------
		case "bibliography":
?>
	jQuery(document).ready(function() {			
		jQuery('#browseArea').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'bibliographyList', array("bibref_id" => $pn_browse_bibref_id)); ?>'
		);
		return false;
	});
<?php
		break;
		# ---------------------------------
		case "projects":
?>
	jQuery(document).ready(function() {			
		jQuery('#browseArea').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'projectList', array("project_id" => $pn_browse_project_id)); ?>'
		);
		return false;
	});
<?php
		break;
		# ---------------------------------
	}
?>
</script>