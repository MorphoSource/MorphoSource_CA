		<div style="clear:both;">&nbsp;</div>
	</div><!-- end pageArea -->
</div><!-- end contentArea -->
<div id="footer">
	<div style="float: right;">
<?php
		if($this->request->getController() == "Splash"){
?>
			<div id="hpFunding" class="fundingText">
				<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/nsf-logo-small.jpg">
<?php
				 TooltipManager::add(
					".fundingText", "<div style='padding:10px; font-size:11px; width:250px;'>This project is funded by Duke University Trinity College of Arts and Sciences and by NSF grants to D. M. Boyer, E. R. Seiffert, and E. M. StClair (NSF BCS 1317525, 1304045, BCS 1552848)</div>"
				);
?>
				<div style="clear:both;"><!-- empty --></div>
			</div><!-- end hpFunding -->
<?php
		}
?>
	</div>
	<ul>
<?php
		print "<li>".caNavLink($this->request, _t("About"), "", "", "About", "home")."</li>";
		print "<li>".caNavLink($this->request, _t("Contact"), "", "", "About", "contact")."</li>";
		if($this->request->isLoggedIn()){
			print "<li>".caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout")."</li>";
		}
		print "<li>".caNavLink($this->request, _t("Terms and Conditions"), "", "", "About", "termsAndConditions")."</li>";
		print "<li class='last'>".caNavLink($this->request, _t("User Guide"), "", "", "About", "userGuide")."</li>";
		
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
	
		function copyToClipboard(element) {
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val("http://www.morphosource.org" + $(element).text()).select();
			document.execCommand("copy");
			$temp.remove();
		}
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
