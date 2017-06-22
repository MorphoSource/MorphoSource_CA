<?php
	$va_batch_info = $this->getVar("batch_info");
	$t_specimen = new ms_specimens();
?>
<div style="float:right; margin-top:15px;">
<?php
	print caNavLink($this->request, _t("Back: Review Settings/Upload"), "button buttonLarge", "MyProjects", "BatchImport", "reviewImportSettings");
?>
</div>
<h1>Batch Import: Review and Import</h1>
<p>
	You must select "Import Batch" to save all records. 
</p>
<div class="tealTopBottomRule" id="continueContainer" style="padding:20px 0px 20px 0px;">
<div style="float:right;">
<?php
	print caNavLink($this->request, _t("Back: Upload Revised Worksheet"), "button buttonLarge", "MyProjects", "BatchImport", "reviewImportSettings");
?>
</div>
<?php
	print caNavLink($this->request, _t("Import Batch"), "button buttonLarge", "MyProjects", "BatchImport", "saveBatch");
?>
</div>
<div style="padding:15px; background-color:#EDEDED;">
	<H3>Summary</H3>
	<span class="blueText"><b>New Specimen:</b></span> <?php print $va_batch_info["stats"]["new_specimen"]; ?><br/>
	<span class="blueText"><b>Linked Specimen (already existed in MorphoSource):</b></span> <?php print $va_batch_info["stats"]["linked_specimen"]; ?><br/>
	<span class="blueText"><b>New Media Groups:</b></span> <?php print $va_batch_info["stats"]["new_media_groups"]; ?><br/>
	<span class="blueText"><b>New Media Files:</b></span> <?php print $va_batch_info["stats"]["new_media_files"]; ?><br/>
	<span class="<?php print ($va_batch_info["stats"]["errors"]) ? "formErrors" : "blueText"; ?>"><b>Rows with Errors (can not be imported):</b></span> <?php print $va_batch_info["stats"]["errors"]; ?><br/>
</div>
<?php
	if(is_array($va_batch_info["batch"]) && sizeof($va_batch_info["batch"])){
?>
		<script language="JavaScript" type="text/javascript">
			$(document).ready(function(){
				$('#batchImportReviewTable').caFormatListTable();
			});
		</script>
		<table id="batchImportReviewTable" class="listtable" border="0" cellpadding="0" cellspacing="1">
			<thead>
				<tr>
					<th class="{sorter: false} list-header-nosort">
						<?php print _t('Worksheet Row'); ?>
					</th>
					<th class="{sorter: false} list-header-nosort">
						<?php print _t('Status'); ?>
					</th>
					<th class="{sorter: false} list-header-nosort">
						<?php print _t('Linked Specimen'); ?>
					</th>
<?php
					foreach(array_keys($va_batch_info["batch"][4]) as $vs_field){
						if(!in_array($vs_field, array("errors", "hasData", "ms_specimens.specimen_id"))){
							print "<th class='{sorter: false} list-header-nosort'>".$vs_field."</th>";
						}
					}
?>
				</tr>
			</thead>
			<tbody>
<?php
		foreach($va_batch_info["batch"] as $vs_row_number => $va_record_info){
			print "<tr>";
			print "<td>".$vs_row_number."</td>";
			foreach($va_record_info as $vs_field => $vs_value){
				switch($vs_field){
					case "errors":
						print "<td style='white-space: nowrap;'>".(($vs_value) ? "<span class='formErrors'><i class='fa fa-times-circle'></i> ERROR</span>" : "<span style='color:#21c721;'><i class='fa fa-check-circle'></i> Success</span>")."</td>";
					break;
					# -------------------------------------
					case "hasData":
					break;
					# -------------------------------------
					case "ms_specimens.specimen_id":
						$t_specimen->load($vs_value);
						print "<td>".caNavLink($this->request, "<i class='fa fa-link-out'></i>".$t_specimen->getSpecimenName(), "blueText", "Detail", "SpecimenDetail", "Show", array("specimen_id" => $vs_value), array("target" => "_blank"))."</td>";
					break;
					# -------------------------------------
					default:
						print "<td>".$vs_value."</td>";
					break;
					# -------------------------------------
				}
			}
			print "</tr>";
		}
?>
			</tbody>
		</table>
<?php
	}
?>
<div class="tealTopBottomRule" style="padding:20px 0px 20px 0px;">
<div style="float:right;">
<?php
	print caNavLink($this->request, _t("Back: Upload Revised Worksheet"), "button buttonLarge", "MyProjects", "BatchImport", "reviewImportSettings");
?>
</div>
<?php
	print caNavLink($this->request, _t("Import Batch"), "button buttonLarge", "MyProjects", "BatchImport", "saveBatch");
?>
</div>