<div class="blueRule"><!-- empty --></div>
<?php
	print "<div style='float:right; margin-top:15px;'>".caNavLink($this->request, _t("Next: Import Settings"), "button buttonLarge", "MyProjects", "BatchImport", "importSettingsForm")."</div>";
?>
<h1>Batch Import: Overview</h1>
<div class="textContent">
	<p>
		MorphoSource's batch import tool allows users to bulk add media and associate them with existing or new specimen records.  Please review the <span class="blueText"><b>Keep in mind</b></span> and <span class="blueText"><b>How To</b></span> lists prior to downloading and completing the batch import worksheet.
	</p>
<?php
	if(!$this->request->user->getPreference('user_upload_directory')){
		print "<p class='formErrors'>WARNING:  You do not have an upload directory linked to your account.  Please contact xxx to request one.</p>";
	}
?>
	<p>
		<H3>Keep in mind</H3>
		<ul>
			<li>
				Media must be uploaded to your upload directory to conduct a batch import. If you do not have a directory, please contact xxx. 
			</li>
			<li>
				All new specimen and media records will be associated with your current project.  If you have more than one project in MorphoSource make sure to select the appropriate project from your dashboard.
			</li>
			<li>
				Specimen institution, facility and scanner will be selected through the <span class="blueText"><b>Import Settings</b></span> form prior to uploading your batch import worksheet.  All specimen and media will be associated with the selected institution, facility and scanner.  You can create multiple worksheets to batch import specimen and media from different institutions and facilities.
			</li>
			<li>
				<span class="blueText"><b>How to enter specimen information</b></span>
					<ul>
						<li>
							If the specimen is already in MorphoSource, lookup the MorphoSource identifier on the specimen's detail page and enter it in the MorphoSource Identifier column of your batch import worksheet.  When the MorphoSource Identifier is entered there is no need to enter any additional specimen information for that specimen.  MorphoSource Identifiers are available on Specimen Detail pages and begin with "S" followed by a number.
						</li>
						<li>
							If the specimen is not in MophoSource please complete the batch import worksheet's specimen record columns.  The Occurrence ID will be used to attempt to link your specimen record with a cooresponding specimen record on iDigBio.org.
						</li>
					</ul>
			</li>
		</ul>
	</p>
	<p>
		<H3>How to</H3>
		<ol>
			<li>
				<span class="blueText"><b>Upload media files to your upload directory.</b></span> If you do not have a directory, please contact xxx.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Download and complete the batch import worksheet linked to below.</b></span><br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Complete the Import Settings form</b></span> to select the specimen institution, facilty and scanner to associate with all records in your batch import worksheet.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Upload your worksheet.</b></span> The Batch Import Worksheet is an Excel file.  Each row represents a media group.  Columns are color coded into subgroups to represent different records associated with the media groups: Specimen, media group and media files.  The first 3 rows of the spreadsheet provide the subgroup, name and description of what should be entered in each column.  The final columns are for entering media files.  Up to 5 media files can be added to each media group.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Review the data uploaded from your worksheet.</b></span>  If there are no errors identified, verify the data has been parsed properly and click <span class="blueText"><b>Import Batch</b></span> to save the records to your project.<br/><br/>
				If you see an issue, correct it in your worksheet and reupload.  No records are saved till you click <span class="blueText"><b>Import Batch</b></span>.<br/><br/>
			</li>
		</ol>
	</p>
	<p style="text-align:center;">
<?php
		print "<a href='".$this->request->getThemeUrlPath()."/graphics/MorphoSourceBatchImportWorksheet.xlsx' class='button buttonLarge'><i class='fa fa-download'></i> "._t("Download Batch Import Worksheet")."</a>";
?>
	</p>
	<div style="clear:both; height:30px;"><!-- empty --></div>
		<div class="formButtons tealTopBottomRule" style="text-align:right;">

<?php
			print caNavLink($this->request, _t("Next: Import Settings"), "button buttonLarge", "MyProjects", "BatchImport", "importSettingsForm")."</div>";
?>
		</div>
	</div>
</div>
