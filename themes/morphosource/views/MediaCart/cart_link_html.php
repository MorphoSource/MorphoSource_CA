<?php
	print addToCartLink($this->request, $this->getVar("media_id"), $this->request->user->get("user_id"), null, array("class" => $this->getVar("linkClass")));
?>