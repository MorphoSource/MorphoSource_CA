<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
?>
<div class="tealRule"><!-- empty --></div>
<H2>Media Specimen</H2>
<div class="tealTopBottomRule">				
<?php
	print $t_media->htmlFormElement("specimen_id","<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
?>
</div><!-- end tealTopBottomRule -->