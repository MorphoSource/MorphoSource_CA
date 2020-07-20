<?php
	$pa_response = $this->getVar('response');
	header("Content-type: text/json");
	print caFormatJson(json_encode($pa_response));
?>