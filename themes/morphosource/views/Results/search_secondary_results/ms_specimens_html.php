<?php	
/* ----------------------------------------------------------------------
 * themes/default/views/Results/search_secondary_results/ms_specimens_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
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
	
	if ($this->request->config->get('do_secondary_search_for_ms_specimens')) {
		$t_specimens = new ms_specimens();
		$qr_specimens = $this->getVar('secondary_search_ms_specimens');
		#if (($vn_num_hits = $qr_specimens->numHits()) > 0) {
			$vn_num_hits_per_page 	= $this->getVar('secondaryItemsPerPage');
			$vn_page 				= $this->getVar('page_ms_specimens');
?>
			<div class="searchSec" id="specimensSecondaryResults">

				<h1><?php print _t('Specimen results'); ?></h1>
				<div class="searchSecNav">
<?php
					if ($vn_num_hits > $vn_num_hits_per_page) {
						print "<div class='nav'>";
						if ($vn_page > 0) {
							print "<a href='#' onclick='jQuery(\"#specimensSecondaryResults\").load(\"".caNavUrl($this->request, '', 'Search', 'secondarySearch', array('spage' => $vn_page - 1, 'type' => 'ms_specimens'))."\"); return false;'>&lsaquo; "._t("Previous")."</a>";
						}
						if (($vn_page > 0) && ($vn_page < (ceil($vn_num_hits/$vn_num_hits_per_page) - 1))) {
							print " | ";
						}
						if ($vn_page < (ceil($vn_num_hits/$vn_num_hits_per_page) - 1)) {
							print "<a href='#' onclick='jQuery(\"#specimensSecondaryResults\").load(\"".caNavUrl($this->request, '', 'Search', 'secondarySearch', array('spage' => $vn_page + 1, 'type' => 'ms_specimens'))."\"); return false;'>"._t("Next")." &rsaquo;</a>";
						}
						print "</div><!-- end nav -->\n";
					}
					print _t("%1 %2", $qr_specimens->numHits(), ($qr_specimens->numHits() == 1) ? _t('result') : _t('results'));
?>
				</div><!-- end searchSecNav -->
				<div class="results">
<?php
					$vn_c = 0;
					$vb_link_to_specimen_detail = (int)$this->request->config->get('allow_detail_for_ms_specimens') ? true : false;
					while($qr_specimens->nextHit()) {
						$vs_name = $t_specimens->getSpecimenName($qr_specimens->get("specimen_id"));
						if ($vb_link_to_specimen_detail) {
							print caNavLink($this->request, $vs_name, 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $qr_specimens->get("specimen_id")));
						} else {
							print caNavLink($this->request, $vs_name, 'blueText', '', 'Search', 'Index', array('search' => $vs_name));
						}
						print "<br/>\n";
						$vn_c++;
						
						if ($vn_c >= $vn_num_hits_per_page) { break; }
					}
?>
				</div><!-- end results -->
			</div><!-- end searchSecContainer -->
<?php
		#}
	}
?>