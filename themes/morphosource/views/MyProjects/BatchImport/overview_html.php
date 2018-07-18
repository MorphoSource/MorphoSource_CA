<div class="blueRule"><!-- empty --></div>
<?php
	print "<div style='float:right; margin-top:15px;'>".caNavLink($this->request, _t("Next: Import Settings"), "button buttonLarge", "MyProjects", "BatchImport", "importSettingsForm")."</div>";
?>
<h1>Batch Import: Overview</h1>
<div class="textContent">
	<p>
		MorphoSource's batch import tool allows users to bulk add media and associate them with existing or new specimen records. The batch import steps are reviewed below. Please also read the additional instructions found below prior to downloading and completing the batch import worksheet.
	</p>
<?php
	if(!$this->request->user->getPreference('user_upload_directory')){
		print "<p class='formErrors'>WARNING:  You do not have an upload directory linked to your account.  Please contact xxx to request one.</p>";
	}
?>
	<p>	
		<ol>
			<li>
				<span class="blueText"><b>Upload media files to your upload directory.</b></span> If you do not have a directory, please contact <a href="mailto:info@morphosource.org">info@morphosource.org</a>.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Download and complete the batch import worksheet linked below.</b></span><br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Complete the Import Settings form</b></span> to select the specimen institution, facilty and scanner to associate with all records in your batch import worksheet.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Upload your worksheet.</b></span> The Batch Import Worksheet is an Excel file.  Each row represents a media group.  Columns are color coded into subgroups to represent different records associated with the media groups: Specimen, media group and media files.  The first 3 rows of the spreadsheet provide the subgroup, name and description of what should be entered in each column.  The final columns are for entering media files.  Up to 5 media files can be added to each media group.<br/><br/>
			</li>
			<li>
				<span class="blueText"><b>Review the data uploaded from your worksheet.</b></span>  If there are no errors identified, verify the data has been parsed properly and click <span class="blueText"><b>Import Batch</b></span> to save the records to your project. If you see an issue, correct it in your worksheet and reupload.  No records are saved till you click <span class="blueText"><b>Import Batch</b></span>.<br/><br/>
			</li>
		</ol>
	</p>
	<p>
		<H3>General Notes</H3>
		<ul>
			<li>
				All new specimen and media records will be associated with your current project.  If you have more than one project in MorphoSource make sure to select the appropriate project from your dashboard.<br/><br/>
			</li>
			<li>
				Specimen institution, facility and scanner will be selected through the <span class="blueText"><b>Import Settings</b></span> form prior to uploading your batch import worksheet.  All specimen and media will be associated with the selected institution, facility and scanner.  You can create multiple worksheets to batch import specimen and media from different institutions and facilities.<br/><br/>
			</li>
		</ul>
	</p>
	<p>
		<H3>Entering Specimen Information</H3>
		<ul>
			<li>
				If the specimen is already in MorphoSource, find the MorphoSource Identifier on a Specimen Detail page and enter it in the MorphoSource Identifier column of your batch import worksheet.  When the MorphoSource Identifier is entered there is no need to enter any additional specimen or taxonomy information for that specimen.  MorphoSource Identifiers are available on Specimen Detail pages and begin with "S" followed by a number.<br/><br/>
			</li>
			<li>
				If the specimen is not in MophoSource you can either complete the batch import worksheet's specimen and taxonomy columns or just enter the specimen's Occurrence ID as entered in iDigBio.org.  The Occurrence ID will be used to attempt to link your specimen record with a cooresponding specimen record on iDigBio.org.  If a match is found, no additional specimen or taxonomy information is necessary.<br/><br/>
			</li>
		</ul>
	</p>
	<p>
		<H3>Additional Information For Media Group Fields</H3>
		<ul>
			<li>
				<span><b><i>ms_media.published</i>:</b> Publication status. This is an optional drop-down field. If nothing is entered here, media will be unpublished by default. To enter a value here, either enter the exact text string from the MorphoSource drop-down (minus any quotes) or the numeric code associated with the publication status (minus any parentheses). Possible values for this field include: "Not published / Not available in public search" (0), "Published / available in public search and for download" (1), "Published / available in public search / users must request download permission" (2).</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.is_copyrighted</i>:</b> Is this media copyrighted? This field is a check box, it is either selected or unselected. Please enter "1", "true", or "yes" to select the check box for this field.</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.side</i>:</b> Side of specimen. Will be displayed when side has not been set at the file level. This is an optional drop-down field. To enter a value here, enter the exact text string from the MorphoSource drop-down (minus any quotes). Possible values for this field include: "Not Applicable", "Unknown", "Left", "Right", "Midline".</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.copyright_permission</i>:</b> Copyright permission. This is an optional drop-down field. If nothing is entered here, the copyright permission will be set to "Copyright permission not set". To enter a value here, either enter the exact text string from the MorphoSource drop-down (minus any quotes) or the numeric code associated with the copyright permission status (minus any parentheses). Possible values for this field include: "Copyright permission not set" (0), "Person loading media owns copyright and grants permission for use of media on MorphoSource" (1), "Permission to use media on MorphoSource granted by copyright holder" (2),"Permission pending" (3), "Copyright expired or work otherwise in public domain" (4), "Copyright permission not yet requested" (5).</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.copyright_license</i>:</b> Copyright license. This is an optional drop-down field. If nothing is entered here, the copyright license will be set to "Media reuse policy not set". To enter a value here, either enter the exact text string from the MorphoSource drop-down (minus any quotes) or the numeric code associated with the license status (minus any parentheses). Possible values for this field include: "Media reuse policy not set" (0), "CC0 - relinquish copyright" (1), "Attribution CC BY - reuse with attribution" (2), "Attribution-NonCommercial CC BY-NC - reuse but noncommercial" (3), "Attribution-ShareAlike CC BY-SA - reuse here and applied to future uses" (4), "Attribution- CC BY-NC-SA - reuse here and applied to future uses but noncommercial" (5), "Attribution-NoDerivs CC BY-ND - reuse but no changes" (6), "Attribution-NonCommercial-NoDerivs CC BY-NC-ND - reuse noncommerical no changes" (7), "Media released for onetime use, no reuse without permission" (8), "Unknown - Will set before project publication" (20).</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.scanner_calibration_shading_correction</i>:</b> When checked, indicates the scanner's shading correction was calibrated. This field is a check box, it is either selected or unselected. Please enter "1", "true", or "yes" to select the check box for this field.</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.scanner_calibration_flux_normalization</i>:</b> When checked, indicates the scanner's flux normalization was calibrated. This field is a check box, it is either selected or unselected. Please enter "1", "true", or "yes" to select the check box for this field.</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media.scanner_calibration_geometric_calibration</i>:</b> When checked, indicates the scanner's geometric calibration was calibrated. This field is a check box, it is either selected or unselected. Please enter "1", "true", or "yes" to select the check box for this field.</span><br/><br/>
			</li>
		</ul>
	</p>
	<p>
		<H3>Additional Information For Media File Fields</H3>
		<ul>
			<li>
				<span><b><i>ms_media_files.side</i>:</b> Side of specimen depicted by media, if different from what was entered in the general information for the media. This is an optional drop-down field. To enter a value here, enter the exact text string from the MorphoSource drop-down (minus any quotes). Possible values for this field include: "Not Applicable", "Unknown", "Left", "Right", "Midline".</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media_files.use_for_preview.1</i>:</b> Use this file as preview for entire media record? This field is a check box, it is either selected or unselected. Please enter "1", "true", or "yes" to select the check box for this field.</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media_files.file_type</i>:</b> File type. This is an optional drop-down field. If nothing is entered here, the file type will be unset by default. To enter a value here, either enter the exact text string from the MorphoSource drop-down (minus any quotes) or the numeric code associated with the file type status (minus any parentheses). Possible values for this field include: "Raw file of group" (1), "derivative file" (2).</span><br/><br/>
			</li>
			<li>
				<span><b><i>ms_media_files.published</i>:</b> Publication status. This is an optional drop-down field. If nothing is entered here, publication status will be inherited from the media group setting (unpublished by default). To enter a value here, either enter the exact text string from the MorphoSource drop-down (minus any quotes) or the numeric code associated with the publication status (minus any parentheses). Possible values for this field include: "Not published / Not available in public search" (0), "Published / available in public search and for download" (1), "Published / available in public search / users must request download permission" (2).</span><br/><br/>
			</li>
		</ul>
	</p>
	<p style="text-align:center;">
<?php
		print "<a href='".$this->request->getThemeUrlPath()."/static/MorphoSourceBatchImportWorksheet.xlsx' class='button buttonLarge'><i class='fa fa-download'></i> "._t("Download Batch Import Worksheet")."</a>";
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
