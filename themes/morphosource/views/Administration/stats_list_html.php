<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/stats_list_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008 Whirl-i-Gig
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
	$t_project = $this->getVar('project');
	$q_all_projects = $this->getVar('all_projects');
	$t_media = new ms_media();

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msStatsList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Site Stats"); ?>
	</H1>
	<div class="stats">Projects: <span class="ltBlueText"><?php print $this->getVar("num_projects"); ?></span></div>
	<div class="stats">Downloads: <span class="ltBlueText"><?php print $this->getVar("num_downloads"); ?> download<?php print (($this->getVar("num_downloads") == 1) ? "" : "s"); ?> of <?php print $this->getVar("num_downloads_media"); ?> media files by <?php print $this->getVar("num_downloads_users"); ?> user<?php print (($this->getVar("num_downloads_users") == 1) ? "" : "s"); ?>.</span></div>
	<div class="stats">Users: <span class="ltBlueText"><?php print $this->getVar("num_users"); ?></span></div>
	<div class="stats">Specimens: <span class="ltBlueText"><?php print $this->getVar("num_specimens"); ?></span></div>
	<div class="stats">Media: <span class="ltBlueText"><?php print $this->getVar("num_media"); ?></span></div>
	<div class="stats">Media files: <span class="ltBlueText"><?php print $this->getVar("num_media_files"); ?></span></div>
	<div class="stats">Taxonomic names: <span class="ltBlueText"><?php print $this->getVar("num_taxonomy_names"); ?></span></div>
	<div class="stats">Bibliographic citations: <span class="ltBlueText"><?php print $this->getVar("num_bibliography"); ?></span></div>
	<div class="stats">Facilities: <span class="ltBlueText"><?php print $this->getVar("num_facilities"); ?></span></div>
	<div class="stats">Institutions: <span class="ltBlueText"><?php print $this->getVar("num_institutions"); ?></span></div>
	
	


</div>