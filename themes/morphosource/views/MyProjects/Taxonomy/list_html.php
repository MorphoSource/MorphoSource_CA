<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");
	$o_db = new Db();
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
			$t_project = new ms_projects();
			if(($q_listings->get("project_id") == $this->getVar("project_id")) || $t_project->isMember($this->request->user->get("user_id"), $q_listings->get("project_id"))){
				print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", $this->request->getController(), "form", array($ps_primary_key => $q_listings->get($ps_primary_key)));
				print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $q_listings->get($ps_primary_key)));
			}else{
				print "<div class='editMessage'>This record was created by the project, <b>".$q_listings->get("name")."</b><br/>If you need to edit this record please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
			}
			print "</div>";
			$i = 0;
			foreach($pa_list_fields as $vs_field){
				$i++;
				if($q_listings->get($vs_field)){
					print $q_listings->get($vs_field);
					if($i < sizeof($pa_list_fields)){
						print ' '; //$t_item->getProperty("LIST_DELIMITER");
					}
				}
			}
			# --- get specimen and media counts linked to this record
			$q_specimens = $o_db->query("SELECT specimen_id FROM ms_specimens_x_taxonomy WHERE alt_id = ?", $q_listings->get("alt_id"));
			if($q_specimens->numRows()){
				print "<br/>&nbsp;&nbsp;&nbsp;&nbsp;".$q_specimens->numRows()." specimen".(($q_specimens->numRows() == 1) ? "" : "s")." reference".(($q_specimens->numRows() == 1) ? "s" : "")." this taxon in MorphoSource.";
			}
			$q_media = $o_db->query("
				SELECT m.media_id, m.project_id
				FROM ms_media m
				INNER JOIN ms_specimens_x_taxonomy AS s ON m.specimen_id = s.specimen_id
				WHERE s.alt_id = ?
				", $q_listings->get("alt_id"));
			$va_project_media = array();
			$va_other_project_media = array();
			if($q_media->numRows()){
				while($q_media->nextRow()){
					if($q_media->get("project_id") == $this->getVar("project_id")){
						$va_project_media[] = $q_media->get("media_id");
					}else{
						$va_other_project_media[] = $q_media->get("media_id");
					}
				}
			}
			if(sizeof($va_project_media) || sizeof($va_other_project_media)){
				print "<br/>&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if(sizeof($va_project_media)){
				print sizeof($va_project_media)." media within your project";
			}
			if(sizeof($va_project_media) && sizeof($va_other_project_media)){
				print " and ";
			}
			if(sizeof($va_other_project_media)){
				print sizeof($va_other_project_media)." media in other projects";
			}
			if(sizeof($va_project_media) || sizeof($va_other_project_media)){
				print " reference this taxon.";
			}
			print '<div style="clear:right;"><!-- empty --></div></div><!-- end itemListings -->';
		}
	}else{
		print "<br/><br/><H2>"._t("There are no %1 associated with this project.  Use the button above to enter a %2.", $this->getVar("name_plural"), $this->getVar("name_singular"))."</H2>";
	}
?>
