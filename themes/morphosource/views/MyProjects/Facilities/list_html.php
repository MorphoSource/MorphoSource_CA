<?php
	$pn_project_id = $this->getVar("project_id");
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1 class="capitalize">
		<div style="float:right;"><?php print caNavLink($this->request, _t("New %1", $this->getVar("name_singular")), "button buttonLarge", "MyProjects", $this->request->getController(), "form"); ?></div>
		<?php print $this->getVar("name_plural"); ?>
	</H1>
<?php
	if($q_listings->numRows()){
		print '<div id="itemListings">';
		while($q_listings->nextRow()){
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'>";
			# --- only show edit/delete links for records created by this project or projects the user has access to
			$t_project = new ms_projects();
			if(($q_listings->get("project_id") == $this->getVar("project_id")) || $t_project->isMember($this->request->user->get("user_id"), $q_listings->get("project_id"))){
				print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $q_listings->get($ps_primary_key)));
				print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			}else{
				print "<div class='editMessage'>This facility was created by the project, <b>".$q_listings->get("name")."</b><br/>If you need to edit this facility please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
			}
			print "</div>";
			$i = 0;
			foreach($pa_list_fields as $vs_field){
				$i++;
				if($q_listings->get($vs_field)){
					print "<b>".$q_listings->get($vs_field)."</b>";
					if($i < sizeof($pa_list_fields)){
						print $t_item->getProperty("LIST_DELIMITER");
					}
				}
			}
			print "<span style='font-size:11px; line-height:1.1em;'>";
			if($q_listings->get("description")){
				print "<br/>".$q_listings->get("description");
			}
			if($q_listings->get("address1")){
				print "<br/>".$q_listings->get("address1");
			}
			if($q_listings->get("address2")){
				print "<br/>".$q_listings->get("address2");
			}
			$va_address_parts = array();
			if($q_listings->get("city")){
				$va_address_parts[] = $q_listings->get("city");
			}
			if($q_listings->get("stateprov")){
				$va_address_parts[] = $q_listings->get("stateprov");
			}
			if($q_listings->get("postalcode")){
				$va_address_parts[] = $q_listings->get("postalcode");
			}
			if($q_listings->get("country")){
				$va_address_parts[] = $q_listings->get("country");
			}
			if(sizeof($va_address_parts) > 0){
				print "<br/>".join(", ", $va_address_parts);
			}
			if($q_listings->get("contact")){
				print "<br/>Contact: ".$q_listings->get("contact");
			}
			print "</span>";
			# --- list the scanners for the facility
			$va_scanners = array();
			$va_scanners = $t_item->scannerList($q_listings->get("facility_id"));
			if(sizeof($va_scanners)){
				print "<br/><b>Scanners:</b> ";
				$iscanners = 0;
				foreach($va_scanners as $va_scanner){
					if($va_scanner["name"]){
						print $va_scanner["name"];
					}
					if($va_scanner["description"]){
						if($va_scanner["name"]){
							print ", ";
						}
						print $va_scanner["description"];
					}
					$iscanners++;
					if($iscanners < sizeof($va_scanners)){
						print "<br/>";
					}
				}
			}
			print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 used by this project.  If a facility used in your project is not already present in the facilities drop-down menu of the media form you may add it to MorphoSource here using the button above.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
