		<div style="clear:both;">&nbsp;</div>
	</div><!-- end pageArea -->
</div><!-- end contentArea -->
<div id="footer">
	<div style="float: right;"><em>Commercial use of MorphoSource media is strictly prohibited.</em></div>
	<ul>
<?php
		if($this->request->isLoggedIn()){
			print "<li>";
		}else{
			print "<li class='last'>";
		}
		print caNavLink($this->request, _t("Contact"), "", "", "About", "contact")."</li>";
		if($this->request->isLoggedIn()){
			print "<li class='last'>".caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout")."</li>";
		}
?>
	</ul>
</div><!-- end footer -->
<?php
print TooltipManager::getLoadHTML();
?>
	<div id="msMediaPanel"> 
		<div id="msMediaPanelContentArea">
		
		</div>
	</div>
	<script type="text/javascript">
	/*
		Set up the "msMediaPanel" panel that will be triggered by links in object detail
		Note that the actual <div>'s implementing the panel are located here in views/pageFormat/pageFooter.php
	*/
	var msMediaPanel;
	jQuery(document).ready(function() {
		if (caUI.initPanel) {
			msMediaPanel = caUI.initPanel({ 
				panelID: 'msMediaPanel',										/* DOM ID of the <div> enclosing the panel */
				panelContentID: 'msMediaPanelContentArea',		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: '#000000',						/* color (in hex notation) of background masking out page content; include the leading '#' in the color spec */
				exposeBackgroundOpacity: 0.8,							/* opacity of background color masking out page content; 1.0 is opaque */
				panelTransitionSpeed: 400, 									/* time it takes the panel to fade in/out in milliseconds */
				allowMobileSafariZooming: true,
				mobileSafariViewportTagID: '_msafari_viewport',
				closeButtonSelector: '.close'					/* anything with the CSS classname "close" will trigger the panel to close */
			});
		}
	});
	</script>
	</body>
</html>