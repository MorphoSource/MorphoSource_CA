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
	$va_counts = $this->getVar('counts');
	krsort($va_counts);
?>

<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Site Stats Over Time"); ?>
	</H1>
<?php
	foreach($va_counts as $vn_year => $va_counts_for_year){
		print "<h2><b>".$vn_year."</b></h2>";
		print "<div style='margin-left:15px;'>";
		foreach($va_counts_for_year as $vn_month => $va_stats){
			print "<b>".date('F', mktime(0, 0, 0, $vn_month, 10))." 1st</b><br/>";
			foreach($va_stats as $vs_label => $vn_count){
				print $vs_label.": <span class='ltBlueText'>".$vn_count."</span><br/>";
			}
			print "<br/>";
		}
		print "</div>";
	}
	
?>