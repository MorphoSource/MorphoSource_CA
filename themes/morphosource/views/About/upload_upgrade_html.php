<div class='blueRule'></div>
<div>
	<h1 style='margin-bottom:10px;'>File Upload Tool Information</h1>
	<div>
		Some aspects of the MorphoSource file upload tool are changing. This will allow much larger files to be uploaded, going from an approximate single file limit of 8 gigabytes to 20+ gigabytes. Also, if file uploads are interrupted for any reason, it is now possible to resume the upload by refreshing the browser page or navigating back to the original media upload page.<br/><br/> 

		As part of this upgrade, files must be completely uploaded before the page is submitted. If the Save button is selected before files are completely uploaded, you can choose to have the files automatically submitted after all files have uploaded or opt to review uploaded files before submitting them.<br/><br/>
	</div>
</div>

<div class='content'>
	<div class='text'>
		This is the default look of the file upload pane. Select the Upload From Computer button to begin uploading. Alternately, if your account is associated with a server SFTP account, you can upload a file directly from your SFTP directory. Be sure not to mix these file upload styles at the same time.
	</div>
	<div >
		<img class='img-pane1' style=' margin: 5px; box-shadow:0px 0px 2px 0px #000000' src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/upload.png' />
	</div>
</div>

<div class='content'>
	<div >
		<img class='img-pane1' style=' margin: 5px; box-shadow:0px 0px 2px 0px #000000' src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/in_progress.png' />
	</div>
	<div class='text'>
		When a file is actively uploading, current progress is shown. Uploads can be canceled at any time. This does not delete other files uploaded. If creating a new media group, you can also start a new file upload to the same media group at any time.
	</div>
</div>

<div class='content'>
	<div class='text'>
		If an upload is interrupted, redirect your browser to the media upload page and resume the media upload.
	</div>
	<div >
		<img class='img-pane1' style=' margin: 5px; box-shadow:0px 0px 2px 0px #000000' src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/interrupt.png' />
	</div>
</div>

<div class='content' style='margin-bottom: 20px;'>
	<div >
		<img class='img-pane2' style=' margin: 5px; box-shadow:0px 0px 2px 0px #000000' src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/submit.png' />
	</div>
	<div class='text'>
		If a media group or file is submitted while a file is still uploading, you can choose to automatically submit after the file is finished uploading. Otherwise, the Save button should be clicked after all files are uploaded.
	</div>
</div>

<style media="screen" type="text/css">

.content
{
 margin-bottom: -40px;
 padding: 0;
 overflow:hidden;
 display: flex;
}

.text {
 overflow:hidden;
 margin: auto;
 padding: 30px;
}

.img-pane1 {
	width: 300px;
}

.img-pane2 {
	width: 400px;
}

</style>
