<?php
/* ----------------------------------------------------------------------
 * themes/default/views/Results/ms_media_result_tooltip_html.php :
 * 		thumbnail search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
?>
<div>
	<H2>M<?php print $this->getVar('tooltip_id'); ?></H2>
<?php 
	print $this->getVar('tooltip_representation');
	print "<div class='mediaSearchTooltipText'>";
	if($this->getVar('tooltip_specimen')){
		print $this->getVar('tooltip_specimen')."<br/>";
	}
	if($this->getVar('tooltip_element')){
		print $this->getVar('tooltip_element')."<br/>";
	}
	if($this->getVar('tooltip_facility')){
		print $this->getVar('tooltip_facility')."<br/>";
	}
	if($this->getVar('tooltip_file_format')){
		print $this->getVar('tooltip_file_format').", ";
	}
	if($this->getVar('tooltip_file_size')){
		print $this->getVar('tooltip_file_size').", ";
	}
	print "</div>";
?>
</div>