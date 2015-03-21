<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Stats/stats_html.php : 
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
 
	$va_rows = $this->getVar('rows');
?>
	<div class="blueRule"><!-- empty --></div>
	<p style="float:right; padding-top:15px;">See how your project Specimens and media have been viewed and downloaded</p>
	<H1>Usage Stats</H1>
<div id='formArea' style="margin-top:0px; padding-top:0px;">		
		<br style="clear: both"/>

<?php
	if (sizeof($va_rows)) {	
?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msProjectList').caFormatListTable();
	});
/* ]]> */
</script>
	<div style="margin-bottom:5px;">Filter: <input type="text" name="filter" value="" onkeyup="$('#msProjectList').caFilterTable(this.value); return false;" size="20" style="border:1px solid #828282;"/></div>
<div style="max-height:400px; overflow-y:auto; border-top:1px solid #DEDEDE;">
	<table id="msProjectList" class="listtable" border="0" cellpadding="0" cellspacing="1" style="margin-top:0px;">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Project'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen Public Views'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen Media'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen Media Public Views'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen Media Downloads'); ?>
				</th>
				<th class="{sorter: false} list-header-nosort">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($va_rows as $va_row) {
?>	
			<tr>
				<td>
					<?php print $va_row["project_name"]; ?>
				</td>
				<td>
					<?php print $va_row["specimen_number"].", ".join("; ", $va_row["specimen_taxonomy"]); ?>
				</td>
				<td>
					<?php print $va_row["specimen_views"]; ?>
				</td>
				<td>
					<?php print $va_row["num_specimen_media"]; ?>
				</td>
				<td>
					<?php print $va_row["specimen_media_views"]; ?>
				</td>
				<td>
					<?php print $va_row["specimen_media_downloads"]; ?>
				</td>
				
				<td>
					<?php print "<a href='#' class='button buttonSmall' onClick='jQuery(\"#specimenInfo\").load(\"".caNavUrl($this->request, "", "Stats", "specimenInfo", array('specimen_id' => $va_row["specimen_id"]))."\"); return false;'>"._t("More")."</a>"; ?>
				</td>
<?php
		}
?>
		
			</tr>
		</tbody>
	</table>
</div>	
<div id="specimenInfo"></div>

<?php
	} else {
?>
		<H2>
			<?php print _t('You have no project specimen'); ?>
		</H2>
<?php			
	}
?>
