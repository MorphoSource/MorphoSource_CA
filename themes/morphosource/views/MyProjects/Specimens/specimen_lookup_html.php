<?php
	$va_lookup_fields = $this->getVar("lookup_fields");
	$va_errors = $this->getVar("errors");
	$va_results = $this->getVar("results");
	$qr_morphosource_results = $this->getVar("morphosource_results");
	$vn_num_morphosource_results = $this->getVar("num_morphosource_results");
?>
<div id='pageArea'>
<div class="blueRule"><!-- empty --></div>
<H1>Search Specimens</H1>
<div>
	Please search to locate your specimen in MorphoSource and iDigBio before entering it on your own.  If you're having trouble finding the specimen record, try entering different combinations of data such as Institution code and Species.  You can also use the search tools located at <a href="https://www.idigbio.org/portal/search" target="_blank">idigbio.org</a> to explore what specimen data is available.
	If you can't locate your specimen, <b><?php print caNavLink($this->request, _t("click here to enter it directly in MorphoSource"), "", "MyProjects", $this->request->getController(), "Form"); ?></b>.
</div>	
<form action="<?php print caNavUrl($this->request, 'MyProjects', $this->request->getController(), 'lookupSpecimen', array()); ?>" method="post" name="lookupForm">
<?php
	if(is_array($va_errors) && sizeof($va_errors)){
		print "<div class='formLabelError' style='margin:25px 0px 25px 0px; text-align: left;'>".join("<br/>", $va_errors)."<br/><br/></div>";
	}
	foreach($va_lookup_fields as $vs_label => $vs_field){
		print "<div class='formLabel' style='float:left; margin-right:20px;'>".$vs_label."<br/><input type='text' id='".$vs_field."' name='".$vs_field."' size='20' value='".$this->request->getParameter($vs_field, pString)."'/></div>";
	}
?>
	<div class="formButtons" style="float:left;">
		<br/><a href="#" name="search" class="button buttonSmall" onclick="document.forms.lookupForm.submit(); return false;"><?php print _t("Search"); ?></a>
	</div><!-- end formButtons -->
	<div style="clear:left;"></div>
	Can't find your specimen? <b><?php print caNavLink($this->request, _t("Enter your specimen directly in MorphoSource"), "", "MyProjects", $this->request->getController(), "Form"); ?></b>
	<br/><br/><input type="hidden" name="doLookup" value="1">

</form>
<?php
	if($vn_num_morphosource_results){
		if(is_array($va_results) && sizeof($va_results)){
			print "<div style='float:left; width:45%; margin-right:5%'>";
		}
		$t_specimen = new ms_specimens();
		print "<div class='blueRule'><!-- empty --></div><H1>MorphoSource Results</H1>";
		print "<b>Showing ".$vn_num_morphosource_results." MorphoSource Result".(($vn_num_morphosource_results == 1) ? "" : "s")."</b>";
		print "<div id='itemListings' style='overflow-y:auto; max-height:500px;'>";
		$i = 0;
		$col = 0;
		while($qr_morphosource_results->nextHit()){
			print "<div class='listItemLtBlue".(($col == 0) ? " bg" : "")."'>";
			print $t_specimen->getSpecimenName($qr_morphosource_results->get("specimen_id"));
			print "<div style='margin-top:5px;'>".caNavLink($this->request, _t("Add Media"), "button buttonSmall", "MyProjects", "Media", "form", array("specimen_id" => $qr_morphosource_results->get("specimen_id")));
			print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Link Specimen to Project"), "button buttonSmall", "MyProjects", "Specimens", "linkSpecimen", array("specimen_id" => $qr_morphosource_results->get("specimen_id")))."</div>";
			print "</div>";
			$col++;
			if($col == 2){
				$col = 0;
			}
			$i++;
		}
		print "</div><!-- end itemListings -->";
		if(is_array($va_results) && sizeof($va_results)){
			print "</div>";
		}		
	}
	if(is_array($va_results) && sizeof($va_results)){
		#print "<pre>";
		#print_r($va_results);
		#print "</pre>";
		$vn_num_hits = $va_results["itemCount"];
		$vn_num_hits_displayed = sizeof($va_results["items"]);
		if($vn_num_morphosource_results){
			print "<div style='float:left; width:45%; margin-right:5%'>";
		}
		print "<div class='blueRule'><!-- empty --></div><H1>iDigBio Results</H1>";
		print "<b>Showing ".(($vn_num_hits_displayed < $vn_num_hits) ? $vn_num_hits_displayed." of " : "").$vn_num_hits." iDigBio Result".(($vn_num_hits == 1) ? "" : "s")."</b>";
		print "<div id='itemListings' style='overflow-y:auto; max-height:500px;'>";
		$i = 0;
		$col = 0;
		foreach($va_results["items"] as $va_result){
			print "<div class='listItemLtBlue".(($col == 0) ? " bg" : "")."'>";
			print "<div class='listItemRightCol'>".caNavLink($this->request, _t("Import Specimen"), "button buttonSmall", "MyProjects", "Specimens", "importIDBSpecimen", array("uuid" => $va_result["uuid"]))."</div>";
			if($va_result["indexTerms"]["institutioncode"]){
				print $va_result["indexTerms"]["institutioncode"]."-";
			}
			if($va_result["indexTerms"]["collectioncode"]){
				print $va_result["indexTerms"]["collectioncode"]."-";
			}
			if($va_result["indexTerms"]["catalognumber"]){
				print $va_result["indexTerms"]["catalognumber"];
			}
			if($va_result["indexTerms"]["scientificname"]){
				print ", <i>".$va_result["indexTerms"]["scientificname"]."</i>";
			}
			print "<br/><a href='https://www.idigbio.org/portal/records/".$va_result["uuid"]."' target='_blank'>View on iDigBio</a>";
			
			print "</div>";
			$col++;
			if($col == 2){
				$col = 0;
			}
			$i++;
		}
		print "</div><!-- end itemListings -->";
		if($vn_num_morphosource_results){
			print "</div>";
		}
	}
?>
</div>